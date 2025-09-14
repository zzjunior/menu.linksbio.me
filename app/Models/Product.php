<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Modelo para gerenciar produtos do cardápio
 */
class Product extends BaseModel
{
    /**
     * Busca os limites de ingredientes por tipo para um produto
     * @param int $productId
     * @return array Ex: ['cremes' => 2, 'frutas' => 3]
     */
    private const TABLE = 'products';

    /**
     * Busca todos os produtos de um usuário
     */
    public function getAll(int $userId = null): array
    {
        $sql = "
            SELECT p.*, c.name as category_name 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            WHERE p.active = 1 
        ";
        
        $params = [];
        if ($userId) {
            $sql .= " AND p.user_id = ?";
            $params[] = $userId;
        }
        
        $sql .= " ORDER BY c.name, p.name";
        
        $stmt = $this->db->prepare($sql);
        $result = $stmt->executeQuery($params);
        
        return $result->fetchAllAssociative();
    }

    /**
     * Busca produtos por categoria de um usuário
     */
    public function getByCategory(int $categoryId, int $userId = null): array
    {
        $sql = "
            SELECT p.*, c.name as category_name 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            WHERE p.category_id = ? AND p.active = 1 
        ";
        
        $params = [$categoryId];
        if ($userId) {
            $sql .= " AND p.user_id = ?";
            $params[] = $userId;
        }
        
        $sql .= " ORDER BY p.name";
        
        $stmt = $this->db->prepare($sql);
        $result = $stmt->executeQuery($params);
        
        return $result->fetchAllAssociative();
    }

    /**
     * Busca um produto por ID
     */
    public function getById(int $id): ?array
    {
        $sql = "
            SELECT p.*, c.name as category_name 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            WHERE p.id = ?
        ";
        
        $stmt = $this->db->prepare($sql);
        $result = $stmt->executeQuery([$id]);
        $data = $result->fetchAssociative();
        
        return $data ?: null;
    }

    /**
     * Busca produtos de açaí com tamanhos
     */
    public function getAcaiProducts(): array
    {
        $sql = "
            SELECT p.*, c.name as category_name 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            WHERE c.name = 'Açaí' AND p.active = 1 
            ORDER BY p.size_order, p.name
        ";
        
        $stmt = $this->db->prepare($sql);
        $result = $stmt->executeQuery();
        
        return $result->fetchAllAssociative();
    }

    /**
     * Cria um novo produto
     */
    public function create(array $data): int
    {
        $productData = [
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'price' => $data['price'],
            'category_id' => $data['category_id'],
            'user_id' => $data['user_id'],
            'image_url' => $data['image_url'] ?? null,
            'size_ml' => $data['size_ml'] ?? null,
            'max_ingredients' => $data['max_ingredients'] ?? null,
            'size_order' => $data['size_order'] ?? 0,
            'active' => $data['active'] ?? 1,
            'created_at' => date('Y-m-d H:i:s')
        ];

    return $this->insert('products', $productData);
    }

    /**
     * Atualiza um produto
     */
    public function updateProduct(int $id, array $data): bool
    {
        $productData = [
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'price' => $data['price'],
            'category_id' => $data['category_id'],
            'image_url' => $data['image_url'] ?? null,
            'size_ml' => $data['size_ml'] ?? null,
            'max_ingredients' => $data['max_ingredients'] ?? null,
            'size_order' => $data['size_order'] ?? 0,
            'active' => $data['active'] ?? 1,
            'updated_at' => date('Y-m-d H:i:s')
        ];

    return $this->update('products', $id, $productData);
    }

    /**
     * Remove um produto (soft delete)
     */
    public function deleteProduct(int $id): bool
    {
    return $this->update('products', $id, [
            'active' => 0,
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Busca produtos para admin (incluindo inativos)
     */
    public function getAllForAdmin(): array
    {
        $sql = "
            SELECT p.*, c.name as category_name 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            ORDER BY p.active DESC, c.name, p.name
        ";
        
        $stmt = $this->db->prepare($sql);
        $result = $stmt->executeQuery();
        
        return $result->fetchAllAssociative();
    }
    /**
     * Salva os limites de ingredientes por tipo para um produto
     * @param int $productId
     * @param array $limits Ex: ['cremes' => 2, 'frutas' => 3]
     */
    public function saveMaxIngredientsProduct(int $productId, array $limits): void
    {
        // Remove antigos
        $sqlDelete = "DELETE FROM max_ingredients_product WHERE product_id = ?";
        $this->db->prepare($sqlDelete)->executeStatement([$productId]);

        // Insere novos
        $sqlInsert = "INSERT INTO max_ingredients_product (product_id, ingredient_type, max_quantity) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($sqlInsert);
        foreach ($limits as $type => $max) {
            if ($type !== '' && is_numeric($max)) {
                $stmt->executeStatement([$productId, $type, (int)$max]);
            }
        }
    }

    /**
     * Busca os limites de ingredientes por tipo para um produto
     * @param int $productId
     * @return array Ex: ['cremes' => 2, 'frutas' => 3]
     */
    public function getMaxIngredientsType(int $productId): array
    {
        $sql = "SELECT ingredient_type, max_quantity FROM max_ingredients_product WHERE product_id = ?";
        $stmt = $this->db->prepare($sql);
        $result = $stmt->executeQuery([$productId]);
        $out = [];
        foreach ($result->fetchAllAssociative() as $row) {
            $out[$row['ingredient_type']] = $row['max_quantity'];
        }
        return $out;
    }
}
