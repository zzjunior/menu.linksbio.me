<?php

namespace App\Models;

class User extends BaseModel
{
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

    public function getStoreBySlug($slug)
    {
        return $this->findBySlug($slug);
    }

    public function isActive($userId)
    {
        $user = $this->findById('users', $userId);
        return $user && $user['is_active'] == 1;
    }
}
