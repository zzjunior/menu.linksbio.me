<?php

declare(strict_types=1);

namespace App\Models;

class Store extends BaseModel
{
    /**
     * Criar nova store
     */
    public function create(array $data): int
    {
        $fields = [
            'store_name',
            'store_description',
            'store_address',
            'store_phone',
            'store_email',
            'store_logo',
            'delivery_fee',
            'loyalty_enabled',
            'loyalty_orders_required',
            'loyalty_discount_percent'
        ];
        
        $insertData = [];
        $placeholders = [];
        
        foreach ($fields as $field) {
            if (array_key_exists($field, $data)) {
                $insertData[] = $data[$field];
                $placeholders[] = '?';
            }
        }
        
        $sql = "INSERT INTO stores (" . implode(', ', array_keys($data)) . ") VALUES (" . implode(', ', $placeholders) . ")";
        $stmt = $this->db->prepare($sql);
        $stmt->executeStatement($insertData);
        
        return (int)$this->db->lastInsertId();
    }
    
    /**
     * Criar store baseada nos dados do usuário
     */
    public function createFromUser(array $userData): int
    {
        $storeData = [
            'store_name' => $userData['store_name'] ?? 'Minha Loja',
            'store_description' => 'Faça seus pedidos online de forma rápida e prática!',
            'store_address' => $userData['address'] ?? '',
            'store_phone' => $userData['whatsapp'] ?? '',
            'store_email' => $userData['email'] ?? '',
            'store_logo' => $userData['logo'] ?? null,
            'delivery_fee' => 0.00,
            'loyalty_enabled' => 0,
            'loyalty_orders_required' => 10,
            'loyalty_discount_percent' => 10.00
        ];
        
        $fields = array_keys($storeData);
        $placeholders = array_fill(0, count($fields), '?');
        
        $sql = "INSERT INTO stores (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $placeholders) . ")";
        $stmt = $this->db->prepare($sql);
        $stmt->executeStatement(array_values($storeData));
        
        return (int)$this->db->lastInsertId();
    }
    
    /**
     * Buscar store por ID
     */
    public function getById(int $id): ?array
    {
        $sql = "SELECT * FROM stores WHERE id = ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $result = $stmt->executeQuery([$id]);
        $store = $result->fetchAssociative();
        
        return $store ?: null;
    }
    
    /**
     * Atualizar store
     */
    public function updateStore(int $id, array $data): bool
    {
        $allowedFields = [
            'store_name',
            'store_description',
            'store_address',
            'store_phone',
            'store_email',
            'store_logo',
            'delivery_fee',
            'loyalty_enabled',
            'loyalty_orders_required',
            'loyalty_discount_percent'
        ];
        
        $updateData = [];
        $updateFields = [];
        
        foreach ($allowedFields as $field) {
            if (array_key_exists($field, $data)) {
                $updateFields[] = "{$field} = ?";
                $updateData[] = $data[$field];
            }
        }
        
        if (empty($updateFields)) {
            return true;
        }
        
        $updateFields[] = "updated_at = NOW()";
        $updateData[] = $id;
        
        $sql = "UPDATE stores SET " . implode(', ', $updateFields) . " WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        
        return $stmt->executeStatement($updateData) > 0;
    }
}