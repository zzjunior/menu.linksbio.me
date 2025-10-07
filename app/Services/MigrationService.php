<?php

namespace App\Services;

use Doctrine\DBAL\Connection;

class MigrationService
{
    private Connection $db;
    
    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    /**
     * Criar tabela de migrations se não existir
     */
    private function createMigrationsTable()
    {
        $sql = "
            CREATE TABLE IF NOT EXISTS migrations (
                id INT PRIMARY KEY AUTO_INCREMENT,
                migration VARCHAR(255) NOT NULL UNIQUE,
                executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ";
        
        $this->db->executeStatement($sql);
    }

    /**
     * Verificar se migration já foi executada
     */
    private function isMigrationExecuted($migrationName)
    {
        $this->createMigrationsTable();
        
        $sql = "SELECT COUNT(*) as count FROM migrations WHERE migration = ?";
        $stmt = $this->db->prepare($sql);
        $result = $stmt->executeQuery([$migrationName]);
        $row = $result->fetchAssociative();
        
        return $row['count'] > 0;
    }

    /**
     * Marcar migration como executada
     */
    private function markMigrationAsExecuted($migrationName)
    {
        $sql = "INSERT INTO migrations (migration) VALUES (?)";
        $stmt = $this->db->prepare($sql);
        $stmt->executeStatement([$migrationName]);
    }

    /**
     * Executar uma migration específica
     */
    public function runMigration($migrationFile)
    {
        $migrationPath = __DIR__ . '/../../database/migrations/' . $migrationFile;
        
        if (!file_exists($migrationPath)) {
            throw new \Exception("Migration file not found: {$migrationFile}");
        }

        $migrationName = pathinfo($migrationFile, PATHINFO_FILENAME);
        
        if ($this->isMigrationExecuted($migrationName)) {
            return "Migration {$migrationName} already executed.";
        }

        try {
            // Ler o arquivo SQL
            $sql = file_get_contents($migrationPath);
            
            // Limpar comentários e dividir em statements
            $lines = explode("\n", $sql);
            $cleanedLines = [];
            
            foreach ($lines as $line) {
                $line = trim($line);
                // Pular linhas vazias e comentários
                if (!empty($line) && !preg_match('/^--/', $line)) {
                    $cleanedLines[] = $line;
                }
            }
            
            $cleanedSql = implode(' ', $cleanedLines);
            $statements = array_filter(
                array_map('trim', explode(';', $cleanedSql)), 
                function($stmt) { 
                    return !empty($stmt); 
                }
            );

            echo "DEBUG: Executando " . count($statements) . " statements:\n";
            
            // Executar cada statement
            $this->db->beginTransaction();
            
            foreach ($statements as $index => $statement) {
                if (trim($statement)) {
                    echo "DEBUG: Statement " . ($index + 1) . ": " . substr($statement, 0, 100) . "...\n";
                    try {
                        $this->db->executeStatement($statement);
                        echo "✅ Statement " . ($index + 1) . " executado com sucesso\n";
                    } catch (\Exception $e) {
                        echo "❌ Erro no statement " . ($index + 1) . ": " . $e->getMessage() . "\n";
                        
                        // Se for erro de coluna já existe ou constraint já existe, continuar
                        if (strpos($e->getMessage(), 'Duplicate column name') !== false ||
                            strpos($e->getMessage(), 'already exists') !== false ||
                            strpos($e->getMessage(), 'Duplicate key name') !== false ||
                            strpos($e->getMessage(), 'Duplicate entry') !== false) {
                            // Ignorar erro, coluna/constraint já existe
                            echo "⚠️  Ignorando erro (já existe)\n";
                            continue;
                        } else {
                            // Re-lançar outros erros
                            throw $e;
                        }
                    }
                }
            }
            
            // Marcar como executada (dentro da mesma transação)
            echo "DEBUG: Marcando migration como executada...\n";
            $sql = "INSERT INTO migrations (migration) VALUES ('" . $migrationName . "')";
            $this->db->executeStatement($sql);
            echo "✅ Migration marcada como executada\n";
            
            $this->db->commit();
            echo "✅ Transação commitada\n";
            
            return "Migration {$migrationName} executed successfully.";
            
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw new \Exception("Error executing migration {$migrationName}: " . $e->getMessage());
        }
    }

    /**
     * Executar todas as migrations pendentes
     */
    public function runAllMigrations()
    {
        $migrationsDir = __DIR__ . '/../../database/migrations/';
        $migrationFiles = glob($migrationsDir . '*.sql');
        sort($migrationFiles);
        
        $results = [];
        
        foreach ($migrationFiles as $migrationPath) {
            $migrationFile = basename($migrationPath);
            try {
                $result = $this->runMigration($migrationFile);
                $results[] = $result;
            } catch (\Exception $e) {
                $results[] = "ERROR: " . $e->getMessage();
                break; // Parar na primeira migration com erro
            }
        }
        
        return $results;
    }

    /**
     * Listar migrations executadas
     */
    public function getExecutedMigrations()
    {
        $this->createMigrationsTable();
        
        $sql = "SELECT migration, executed_at FROM migrations ORDER BY executed_at DESC";
        $stmt = $this->db->prepare($sql);
        $result = $stmt->executeQuery();
        
        return $result->fetchAllAssociative();
    }

    /**
     * Listar migrations disponíveis
     */
    public function getAvailableMigrations()
    {
        $migrationsDir = __DIR__ . '/../../database/migrations/';
        $migrationFiles = glob($migrationsDir . '*.sql');
        
        $migrations = [];
        foreach ($migrationFiles as $migrationPath) {
            $migrationFile = basename($migrationPath);
            $migrationName = pathinfo($migrationFile, PATHINFO_FILENAME);
            
            $migrations[] = [
                'file' => $migrationFile,
                'name' => $migrationName,
                'executed' => $this->isMigrationExecuted($migrationName)
            ];
        }
        
        return $migrations;
    }
}