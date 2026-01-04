<?php

declare(strict_types=1);

namespace App\Models;

class StoreSettings extends BaseModel
{
    /**
     * Obter configurações da loja (da tabela stores relacionada ao user)
     */
    public function getSettings(): array
    {
        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            throw new \Exception('Usuário não autenticado');
        }

        // Verificar se a coluna store_banner existe na tabela users
        $columnsResult = $this->db->executeQuery("SHOW COLUMNS FROM users LIKE 'store_banner'");
        $storeBannerExists = $columnsResult->fetchOne() !== false;
        
        // Verificar se a coluna store_banner existe na tabela stores (se a tabela existir)
        $storeBannerStoreExists = false;
        try {
            $storesColumnsResult = $this->db->executeQuery("SHOW COLUMNS FROM stores LIKE 'store_banner'");
            $storeBannerStoreExists = $storesColumnsResult->fetchOne() !== false;
        } catch (\Exception $e) {
            // Tabela stores não existe ou erro ao consultar
        }
        
        // Construir o SELECT dinamicamente
        $userBannerField = $storeBannerExists ? 'u.store_banner,' : '';
        $storeBannerField = $storeBannerStoreExists ? 's.store_banner as store_banner_store,' : '';
        
        // Buscar dados do usuário e da loja relacionada
        $sql = "SELECT 
                    u.id as user_id,
                    u.name as user_name,
                    u.email,
                    u.store_name as user_store_name,
                    u.store_slug,
                    u.whatsapp,
                    u.address,
                    u.logo,
                    {$userBannerField}
                    u.is_active,
                    s.id as store_id,
                    s.store_name,
                    s.store_description,
                    s.store_address,
                    s.store_phone,
                    s.store_email,
                    s.store_logo,
                    {$storeBannerField}
                    s.delivery_fee,
                    s.loyalty_enabled,
                    s.loyalty_orders_required,
                    s.loyalty_discount_percent,
                    s.business_hours,
                    s.is_open,
                    s.closed_message,
                    s.created_at,
                    s.updated_at
                FROM users u
                LEFT JOIN stores s ON u.store_id = s.id
                WHERE u.id = ? 
                LIMIT 1";
        
        $stmt = $this->db->prepare($sql);
        $result = $stmt->executeQuery([$userId]);
        $data = $result->fetchAssociative();
        
        if (!$data) {
            throw new \Exception('Usuário não encontrado');
        }
        
        // Se não tem store associada, criar valores padrão baseados no user
        if (!$data['store_id']) {
            return [
                'user_id' => $data['user_id'],
                'store_name' => $data['user_store_name'] ?? 'Minha Loja',
                'store_description' => '',
                'store_address' => $data['address'] ?? '',
                'store_phone' => $data['whatsapp'] ?? '',
                'store_email' => $data['email'] ?? '',
                'store_logo' => $data['logo'] ?? '',
                'store_banner' => $data['store_banner'] ?? '',
                'delivery_fee' => 0.00,
                'loyalty_enabled' => false,
                'loyalty_orders_required' => 10,
                'loyalty_discount_percent' => 10.00,
                // Campos do user para compatibilidade
                'whatsapp' => $data['whatsapp'],
                'address' => $data['address'],
                'logo' => $data['logo']
            ];
        }
        
        // Retornar dados da store com fallbacks do user
        return [
            'user_id' => $data['user_id'],
            'store_id' => $data['store_id'],
            'store_name' => $data['store_name'] ?? $data['user_store_name'],
            'store_description' => $data['store_description'] ?? '',
            'store_address' => $data['store_address'] ?? $data['address'],
            'store_phone' => $data['store_phone'] ?? $data['whatsapp'],
            'store_email' => $data['store_email'] ?? $data['email'],
            'store_logo' => $data['store_logo'] ?? $data['logo'],
            'store_banner' => $data['store_banner_store'] ?? $data['store_banner'] ?? '',
            'delivery_fee' => $data['delivery_fee'] ?? 0.00,
            'loyalty_enabled' => $data['loyalty_enabled'] ?? false,
            'loyalty_orders_required' => $data['loyalty_orders_required'] ?? 10,
            'loyalty_discount_percent' => $data['loyalty_discount_percent'] ?? 10.00,
            'business_hours' => $data['business_hours'] ?? null,
            'is_open' => $data['is_open'] ?? 1,
            'closed_message' => $data['closed_message'] ?? 'No momento estamos fechados. Volte em breve!',
            // Campos do user para compatibilidade (quando o template ainda usa)
            'whatsapp' => $data['whatsapp'],
            'address' => $data['address'],
            'logo' => $data['logo']
        ];
    }
    
    /**
     * Atualizar configurações da loja (na tabela users OU stores)
     */
    public function updateSettings(array $data): bool
    {
        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            throw new \Exception('Usuário não autenticado');
        }
        
        // Verificar se a tabela stores existe
        $tablesResult = $this->db->executeQuery("SHOW TABLES LIKE 'stores'");
        $storesTableExists = $tablesResult->fetchOne() !== false;
        
        // Se a tabela stores não existe, salvar na tabela users
        if (!$storesTableExists) {
            return $this->updateUserSettings($userId, $data);
        }
        
        // Buscar store_id do usuário
        $userSql = "SELECT store_id FROM users WHERE id = ?";
        $userStmt = $this->db->prepare($userSql);
        $userResult = $userStmt->executeQuery([$userId]);
        $userData = $userResult->fetchAssociative();
        
        if (!$userData) {
            throw new \Exception('Usuário não encontrado');
        }
        
        $storeId = $userData['store_id'];
        
        // Se não tem store_id, criar uma nova store
        if (!$storeId) {
            $storeId = $this->createStoreForUser($userId, $data);
        }
        
        // Campos permitidos para atualização na tabela stores
        $allowedFields = [
            'store_name',
            'store_description', 
            'store_address',
            'store_phone',
            'store_email',
            'store_logo',
            'store_banner',
            'delivery_fee',
            'loyalty_enabled',
            'loyalty_orders_required',
            'loyalty_discount_percent',
            'business_hours',
            'is_open',
            'closed_message'
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
            return true; // Nada para atualizar
        }
        
        // Adicionar timestamp de atualização
        $updateFields[] = "updated_at = NOW()";
        $updateData[] = $storeId; // Para o WHERE
        
        $sql = "UPDATE stores SET " . implode(', ', $updateFields) . " WHERE id = ?";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->executeStatement($updateData) > 0;
    }
    
    /**
     * Atualizar configurações na tabela users (quando não há tabela stores)
     */
    private function updateUserSettings(int $userId, array $data): bool
    {
        // Mapeamento de campos stores para campos users
        $fieldMapping = [
            'store_banner' => 'store_banner',
            'store_logo' => 'logo',
            'store_name' => 'store_name',
            'store_address' => 'address',
            'store_phone' => 'whatsapp'
        ];
        
        $updateData = [];
        $updateFields = [];
        
        foreach ($fieldMapping as $storeField => $userField) {
            if (array_key_exists($storeField, $data)) {
                $updateFields[] = "{$userField} = ?";
                $updateData[] = $data[$storeField];
            }
        }
        
        if (empty($updateFields)) {
            return true; // Nada para atualizar
        }
        
        $updateData[] = $userId; // Para o WHERE
        
        $sql = "UPDATE users SET " . implode(', ', $updateFields) . " WHERE id = ?";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->executeStatement($updateData) > 0;
    }
    
    /**
     * Criar nova store para o usuário
     */
    private function createStoreForUser(int $userId, array $data): int
    {
        // Buscar dados do usuário para criar a store
        $userSql = "SELECT store_name, email, whatsapp, address, logo, store_banner FROM users WHERE id = ?";
        $userStmt = $this->db->prepare($userSql);
        $userResult = $userStmt->executeQuery([$userId]);
        $userData = $userResult->fetchAssociative();
        
        // Criar nova store com dados do usuário + dados enviados
        $storeData = [
            'store_name' => $data['store_name'] ?? $userData['store_name'] ?? 'Minha Loja',
            'store_description' => $data['store_description'] ?? '',
            'store_address' => $data['store_address'] ?? $userData['address'] ?? '',
            'store_phone' => $data['store_phone'] ?? $userData['whatsapp'] ?? '',
            'store_email' => $data['store_email'] ?? $userData['email'] ?? '',
            'store_logo' => $data['store_logo'] ?? $userData['logo'] ?? '',
            'store_banner' => $data['store_banner'] ?? $userData['store_banner'] ?? '',
            'delivery_fee' => $data['delivery_fee'] ?? 0.00,
            'loyalty_enabled' => $data['loyalty_enabled'] ?? false,
            'loyalty_orders_required' => $data['loyalty_orders_required'] ?? 10,
            'loyalty_discount_percent' => $data['loyalty_discount_percent'] ?? 10.00
        ];
        
        $fields = array_keys($storeData);
        $placeholders = array_fill(0, count($fields), '?');
        
        $sql = "INSERT INTO stores (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $placeholders) . ")";
        $stmt = $this->db->prepare($sql);
        $stmt->executeStatement(array_values($storeData));
        
        $storeId = (int)$this->db->lastInsertId();
        
        // Atualizar usuário com o store_id
        $updateUserSql = "UPDATE users SET store_id = ? WHERE id = ?";
        $updateUserStmt = $this->db->prepare($updateUserSql);
        $updateUserStmt->executeStatement([$storeId, $userId]);
        
        return $storeId;
    }
    
    /**
     * Verificar se cliente tem direito à fidelidade
     */
    public function checkCustomerLoyalty(int $customerId): array
    {
        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            throw new \Exception('Usuário não autenticado');
        }
        
        // Buscar configurações de fidelidade da loja
        $storeSettings = $this->getSettings();
        
        if (!$storeSettings['loyalty_enabled']) {
            return [
                'eligible' => false,
                'reason' => 'Programa de fidelidade não ativo'
            ];
        }
        
        // Contar pedidos do cliente nesta loja específica
        $sql = "SELECT COUNT(*) as total_orders 
                FROM orders 
                WHERE customer_phone = ? 
                AND user_id = ?
                AND status = 'completed'";
        
        $stmt = $this->db->prepare($sql);
        $result = $stmt->executeQuery([$customerId, $userId]);
        $orderCount = $result->fetchOne();
        
        $ordersRequired = (int)$storeSettings['loyalty_orders_required'];
        $discountPercent = (float)$storeSettings['loyalty_discount_percent'];
        
        if ($orderCount >= $ordersRequired) {
            return [
                'eligible' => true,
                'orders_count' => $orderCount,
                'orders_required' => $ordersRequired,
                'discount_percent' => $discountPercent,
                'message' => "Cliente tem direito a {$discountPercent}% de desconto ({$orderCount} pedidos)"
            ];
        }
        
        $ordersLeft = $ordersRequired - $orderCount;
        return [
            'eligible' => false,
            'orders_count' => $orderCount,
            'orders_required' => $ordersRequired,
            'orders_left' => $ordersLeft,
            'message' => "Faltam {$ordersLeft} pedidos para o desconto de {$discountPercent}%"
        ];
    }
    
    /**
     * Upload de logo (mantém compatibilidade com campo logo da users)
     */
    public function uploadLogo(array $file): string
    {
        // Validações
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new \Exception('Erro no upload do arquivo');
        }
        
        $imageInfo = getimagesize($file['tmp_name']);
        if (!$imageInfo) {
            throw new \Exception('O arquivo deve ser uma imagem válida');
        }
        
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($file['type'], $allowedTypes)) {
            throw new \Exception('Tipo de arquivo não permitido');
        }
        
        if ($file['size'] > 2 * 1024 * 1024) {
            throw new \Exception('O arquivo deve ter no máximo 2MB');
        }
        
        // Diretório de upload (compatível com estrutura existente)
        $uploadDir = __DIR__ . '/../../public/uploads/stores/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        // Nome único
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $fileName = 'logo_' . time() . '.' . $extension;
        $filePath = $uploadDir . $fileName;
        
        if (!move_uploaded_file($file['tmp_name'], $filePath)) {
            throw new \Exception('Erro ao mover arquivo');
        }
        
        return '/uploads/stores/' . $fileName;
    }
}