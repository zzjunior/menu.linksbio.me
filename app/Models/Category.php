<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Modelo para gerenciar categorias dos produtos
 */
class Category extends BaseModel
{
    private const TABLE = 'categories';

    /**
     * Busca todas as categorias de um usuário
     */
    public function getAll(int $userId = null): array
    {
        if ($userId) {
            return $this->findBy(self::TABLE, ['user_id' => $userId], 'name');
        }
        return $this->findAll(self::TABLE, 'name');
    }

    /**
     * Busca todas as categorias de um usuário específico
     */
    public function getAllByUser(int $userId): array
    {
        return $this->findBy(self::TABLE, ['user_id' => $userId], 'name');
    }

    /**
     * Busca uma categoria por ID
     */
    public function getById(int $id): ?array
    {
        return $this->findById(self::TABLE, $id);
    }

    /**
     * Cria uma nova categoria
     */
    public function create(array $data): int
    {
        $categoryData = [
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'image' => $data['image'] ?? null,
            'image_url' => $data['image_url'] ?? null,
            'has_customization' => $data['has_customization'] ?? 0,
            'is_active' => $data['is_active'] ?? 1,
            'sort_order' => $data['sort_order'] ?? 0,
            'user_id' => $data['user_id'],
            'created_at' => date('Y-m-d H:i:s')
        ];

        return $this->insert(self::TABLE, $categoryData);
    }

    /**
     * Atualiza uma categoria
     */
    public function updateCategory(int $id, array $data): bool
    {
        $categoryData = [
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'image' => $data['image'] ?? null,
            'image_url' => $data['image_url'] ?? null,
            'has_customization' => $data['has_customization'] ?? 0,
            'is_active' => $data['is_active'] ?? 1,
            'sort_order' => $data['sort_order'] ?? 0,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        return $this->update(self::TABLE, $id, $categoryData);
    }

    /**
     * Atualiza uma categoria (método público)
     */
    public function updateById(int $id, array $data): bool
    {
        return $this->updateCategory($id, $data);
    }

    /**
     * Remove uma categoria
     */
    public function deleteCategory(int $id): bool
    {
        return $this->delete(self::TABLE, $id);
    }

    /**
     * Remove uma categoria (método público)
     */
    public function deleteById(int $id): bool
    {
        return $this->deleteCategory($id);
    }

    /**
     * Verifica se uma categoria tem produtos associados
     */
    public function hasProducts(int $id): bool
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM products WHERE category_id = ?");
        $result = $stmt->executeQuery([$id]);
        $data = $result->fetchAssociative();
        
        return $data['count'] > 0;
    }
}
