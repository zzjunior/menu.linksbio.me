#!/usr/bin/env php
<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/bootstrap.php';

use App\Services\MigrationService;

// Verificar argumentos
$command = $argv[1] ?? 'help';

try {
    // Criar instância do MigrationService
    $migrationService = new MigrationService($container->get('db'));
    
    switch ($command) {
        case 'run':
            // Executar todas as migrations pendentes
            echo "Executando todas as migrations pendentes...\n";
            $results = $migrationService->runAllMigrations();
            
            if (empty($results)) {
                echo "✅ Nenhuma migration pendente.\n";
            } else {
                foreach ($results as $result) {
                    echo "✅ $result\n";
                }
            }
            break;
            
        case 'status':
            // Mostrar status das migrations
            echo "Status das migrations:\n";
            echo str_repeat('-', 50) . "\n";
            
            $availableMigrations = $migrationService->getAvailableMigrations();
            
            foreach ($availableMigrations as $migration) {
                $status = $migration['executed'] ? '✅ EXECUTADA' : '⏳ PENDENTE';
                printf("%-30s %s\n", $migration['name'], $status);
            }
            break;
            
        case 'create':
            // Criar nova migration
            $name = $argv[2] ?? null;
            if (!$name) {
                echo "❌ Erro: Nome da migration é obrigatório.\n";
                echo "Uso: php bin/migrate.php create nome_da_migration\n";
                exit(1);
            }
            
            $filename = date('Y_m_d_His') . '_' . $name . '.sql';
            $filepath = __DIR__ . '/../database/migrations/' . $filename;
            
            $template = "-- Migration: $name\n-- Created: " . date('Y-m-d H:i:s') . "\n\n";
            $template .= "-- Adicione seus comandos SQL aqui\n";
            $template .= "-- Exemplo:\n";
            $template .= "-- CREATE TABLE IF NOT EXISTS exemplo (\n";
            $template .= "--     id INT AUTO_INCREMENT PRIMARY KEY,\n";
            $template .= "--     nome VARCHAR(255) NOT NULL\n";
            $template .= "-- );\n";
            
            file_put_contents($filepath, $template);
            echo "✅ Migration criada: $filename\n";
            break;
            
        case 'rollback':
            echo "❌ Rollback não implementado ainda.\n";
            break;
            
        default:
            echo "Sistema de Migrations - Cardápio Base\n";
            echo str_repeat('=', 40) . "\n";
            echo "Comandos disponíveis:\n\n";
            echo "  run      Executar todas as migrations pendentes\n";
            echo "  status   Mostrar status das migrations\n";
            echo "  create   Criar nova migration\n";
            echo "  rollback Reverter última migration (não implementado)\n";
            echo "  help     Mostrar esta ajuda\n\n";
            echo "Exemplos:\n";
            echo "  php bin/migrate.php run\n";
            echo "  php bin/migrate.php status\n";
            echo "  php bin/migrate.php create add_new_column\n";
            break;
    }
    
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
    exit(1);
}