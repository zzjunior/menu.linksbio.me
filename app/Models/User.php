<?php

namespace App\Models;

class User extends BaseModel
{
    private const TABLE = 'users';

    public function getById($id)
    {
        return $this->findById('users', $id);
    }

    public function findByEmail($email)
    {
        return $this->findOne('users', ['email' => $email]);
    }

    public function findBySlug($slug)
    {
        return $this->findOne('users', ['store_slug' => $slug]);
    }

    public function create($data)
    {
        return $this->insert('users', $data);
    }

    public function updateUser($id, $data)
    {
        return $this->update('users', $id, $data);
    }

    public function updateById(int $id, array $data): bool
    {
        return $this->update(self::TABLE, $id, $data);
    }

    public function verifyPassword($password, $hash)
    {
        return password_verify($password, $hash);
    }

    public function hashPassword($password)
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    public function generateSlug($storeName)
    {
        $slug = strtolower(trim($storeName));
        $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
        $slug = preg_replace('/-+/', '-', $slug);
        $slug = trim($slug, '-');
        
        // Verificar se slug já existe
        $counter = 1;
        $originalSlug = $slug;
        while ($this->findBySlug($slug)) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
        
        return $slug;
    }

    public function getStoreBySlug(string $slug): ?array
    {
        $sql = "SELECT 
                    u.id,
                    u.name,
                    u.email,
                    u.store_name,
                    u.store_slug,
                    u.whatsapp,
                    u.address,
                    u.logo,
                    u.is_active,
                    s.id as store_id,
                    s.store_name as store_store_name,
                    s.store_description,
                    s.store_address,
                    s.store_phone,
                    s.store_email,
                    s.store_logo,
                    s.delivery_fee,
                    s.loyalty_enabled,
                    s.loyalty_orders_required,
                    s.loyalty_discount_percent
                FROM users u
                LEFT JOIN stores s ON u.store_id = s.id
                WHERE u.store_slug = ? AND u.is_active = 1 
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $result = $stmt->executeQuery([$slug]);
        $data = $result->fetchAssociative();
        
        if (!$data) {
            return null;
        }
        
        // Retornar dados combinados com fallbacks
        return [
            'id' => $data['id'], // user_id para produtos/categorias
            'user_id' => $data['id'],
            'store_id' => $data['store_id'],
            'store_name' => $data['store_store_name'] ?? $data['store_name'],
            'store_description' => $data['store_description'] ?? '',
            'store_address' => $data['store_address'] ?? $data['address'],
            'store_phone' => $data['store_phone'] ?? $data['whatsapp'],
            'store_email' => $data['store_email'] ?? $data['email'],
            'store_logo' => $data['store_logo'] ?? $data['logo'],
            'delivery_fee' => $data['delivery_fee'] ?? 0.00,
            'loyalty_enabled' => $data['loyalty_enabled'] ?? 0,
            'loyalty_orders_required' => $data['loyalty_orders_required'] ?? 10,
            'loyalty_discount_percent' => $data['loyalty_discount_percent'] ?? 10.00,
            'store_slug' => $data['store_slug']
        ];
    }

    public function isActive($userId)
    {
        $user = $this->findById('users', $userId);
        return $user && $user['is_active'] == 1;
    }
}
