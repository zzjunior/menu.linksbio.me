<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Modelo para gerenciar ingredientes disponíveis para o açaí
 */
class Ingredient extends BaseModel
{
    private const TABLE = 'ingredients';

    /**
     * Busca todos os ingredientes ativos de um usuário
     */
    public function getAll(int $userId = null): array
    {
        $sql = "SELECT * FROM ingredients WHERE active = 1";
        $params = [];
        
        if ($userId) {
            $sql .= " AND user_id = ?";
            $params[] = $userId;
        }
        
        $sql .= " ORDER BY type, name";
        
        $stmt = $this->db->prepare($sql);
        $result = $stmt->executeQuery($params);
        
        return $result->fetchAllAssociative();
    }

    /**
     * Busca todos os ingredientes de um usuário específico (para admin)
     */
    public function getAllByUser(int $userId): array
    {
        $sql = "SELECT * FROM ingredients WHERE user_id = ? ORDER BY type, name";
        $stmt = $this->db->prepare($sql);
        $result = $stmt->executeQuery([$userId]);
        
        return $result->fetchAllAssociative();
    }

    /**
     * Busca ingredientes por tipo de um usuário
     */
    public function getByType(string $type, int $userId = null): array
    {
        $sql = "SELECT * FROM ingredients WHERE type = ? AND active = 1";
        $params = [$type];
        
        if ($userId) {
            $sql .= " AND user_id = ?";
            $params[] = $userId;
        }
        
        $sql .= " ORDER BY name";
        
        $stmt = $this->db->prepare($sql);
        $result = $stmt->executeQuery($params);
        
        return $result->fetchAllAssociative();
    }

    /**
     * Busca ingredientes agrupados por tipo
     */
    public function getAllGroupedByType(): array
    {
        $sql = "SELECT * FROM ingredients WHERE active = 1 ORDER BY type, name";
        $stmt = $this->db->prepare($sql);
        $result = $stmt->executeQuery();
        $ingredients = $result->fetchAllAssociative();
        
        $grouped = [];
        foreach ($ingredients as $ingredient) {
            $grouped[$ingredient['type']][] = $ingredient;
        }
        
        return $grouped;
    }

    /**
     * Busca um ingrediente por ID
     */
    public function getById(int $id): ?array
    {
        return $this->findById(self::TABLE, $id);
    }

    /**
     * Cria um novo ingrediente
     */
    public function create(array $data): int
    {
        $ingredientData = [
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'type' => $data['type'] ?? '',
            'price' => $data['price'] ?? 0.00,
            'additional_price' => $data['additional_price'] ?? 0.00,
            'image' => $data['image'] ?? null,
            'image_url' => $data['image_url'] ?? null,
            'is_active' => $data['is_active'] ?? 1,
            'is_free' => $data['is_free'] ?? 0,
            'max_quantity' => $data['max_quantity'] ?? 5,
            'sort_order' => $data['sort_order'] ?? 0,
            'user_id' => $data['user_id'],
            'active' => $data['active'] ?? 1,
            'created_at' => date('Y-m-d H:i:s')
        ];

        return $this->insert(self::TABLE, $ingredientData);
    }

    /**
     * Atualiza um ingrediente
     */
    public function updateIngredient(int $id, array $data): bool
    {
        $ingredientData = [
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'type' => $data['type'] ?? '',
            'price' => $data['price'] ?? 0.00,
            'additional_price' => $data['additional_price'] ?? 0.00,
            'image' => $data['image'] ?? null,
            'image_url' => $data['image_url'] ?? null,
            'is_active' => $data['is_active'] ?? 1,
            'is_free' => $data['is_free'] ?? 0,
            'max_quantity' => $data['max_quantity'] ?? 5,
            'sort_order' => $data['sort_order'] ?? 0,
            'active' => $data['active'] ?? 1,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        return $this->update(self::TABLE, $id, $ingredientData);
    }

    /**
     * Atualiza um ingrediente (método público)
     */
    public function updateById(int $id, array $data): bool
    {
        return $this->updateIngredient($id, $data);
    }

    /**
     * Remove um ingrediente (soft delete)
     */
    public function deleteIngredient(int $id): bool
    {
        return $this->update(self::TABLE, $id, [
            'active' => 0,
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Remove um ingrediente (método público)
     */
    public function deleteById(int $id): bool
    {
        return $this->deleteIngredient($id);
    }

    /**
     * Busca ingredientes para admin (incluindo inativos)
     */
    public function getAllForAdmin(): array
    {
        $sql = "SELECT * FROM ingredients ORDER BY active DESC, type, name";
        $stmt = $this->db->prepare($sql);
        $result = $stmt->executeQuery();
        
        return $result->fetchAllAssociative();
    }

    /**
     * Busca tipos de ingredientes únicos
     */
    public function getTypes(): array
    {
        $sql = "SELECT DISTINCT type FROM ingredients WHERE active = 1 ORDER BY type";
        $stmt = $this->db->prepare($sql);
        $result = $stmt->executeQuery();
        
        return array_column($result->fetchAllAssociative(), 'type');
    }

        /**
     * Busca regras de máximo de ingredientes por produto e tipo
     * @param int[] $productIds
     * @return array [product_id][type] => max_quantity
     */
    public function getMaxIngredientsRules(array $productIds): array
    {
        if (empty($productIds)) return [];
        $placeholders = implode(',', array_fill(0, count($productIds), '?'));
        $sql = "SELECT product_id, type, max_quantity FROM max_ingredients_product WHERE product_id IN ($placeholders)";
        $stmt = $this->db->prepare($sql);
        $result = $stmt->executeQuery($productIds);
        $rules = [];
        foreach ($result->fetchAllAssociative() as $row) {
            $rules[$row['product_id']][$row['type']] = (int)$row['max_quantity'];
        }
        return $rules;
    }
}
