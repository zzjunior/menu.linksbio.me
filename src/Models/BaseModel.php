<?php

declare(strict_types=1);

namespace App\Models;

use Doctrine\DBAL\Connection;

/**
 * Classe base para todos os modelos
 */
abstract class BaseModel
{
    protected Connection $db;

    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    /**
     * Busca um registro por ID
     */
    protected function findById(string $table, int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$table} WHERE id = ?");
        $result = $stmt->executeQuery([$id]);
        $data = $result->fetchAssociative();
        
        return $data ?: null;
    }

    /**
     * Busca todos os registros de uma tabela
     */
    protected function findAll(string $table, string $orderBy = 'id'): array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$table} ORDER BY {$orderBy}");
        $result = $stmt->executeQuery();
        
        return $result->fetchAllAssociative();
    }

    /**
     * Insere um novo registro
     */
    protected function insert(string $table, array $data): int
    {
        $columns = implode(',', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        
        $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
        $stmt = $this->db->prepare($sql);
        $stmt->executeStatement($data);
        
        return (int) $this->db->lastInsertId();
    }

    /**
     * Atualiza um registro
     */
    protected function update(string $table, int $id, array $data): bool
    {
        $setParts = [];
        foreach (array_keys($data) as $column) {
            $setParts[] = "{$column} = :{$column}";
        }
        $setClause = implode(', ', $setParts);
        
        $sql = "UPDATE {$table} SET {$setClause} WHERE id = :id";
        $data['id'] = $id;
        
        $stmt = $this->db->prepare($sql);
        return $stmt->executeStatement($data) > 0;
    }

    /**
     * Remove um registro
     */
    protected function delete(string $table, int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM {$table} WHERE id = ?");
        return $stmt->executeStatement([$id]) > 0;
    }

    /**
     * Busca um registro por condições
     */
    protected function findOne(string $table, array $conditions): ?array
    {
        $whereParts = [];
        foreach (array_keys($conditions) as $column) {
            $whereParts[] = "{$column} = :{$column}";
        }
        $whereClause = implode(' AND ', $whereParts);
        
        $sql = "SELECT * FROM {$table} WHERE {$whereClause} LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $result = $stmt->executeQuery($conditions);
        $data = $result->fetchAssociative();
        
        return $data ?: null;
    }

    /**
     * Busca registros por condições
     */
    protected function findBy(string $table, array $conditions, string $orderBy = 'id'): array
    {
        $whereParts = [];
        foreach (array_keys($conditions) as $column) {
            $whereParts[] = "{$column} = :{$column}";
        }
        $whereClause = implode(' AND ', $whereParts);
        
        $sql = "SELECT * FROM {$table} WHERE {$whereClause} ORDER BY {$orderBy}";
        $stmt = $this->db->prepare($sql);
        $result = $stmt->executeQuery($conditions);
        
        return $result->fetchAllAssociative();
    }
}
