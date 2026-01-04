<?php

namespace App\Models;

class Order extends BaseModel
{
    /**
     * Retorna a conexão DBAL (Doctrine Connection)
     */
    public function getConnection()
    {
        return $this->db;
    }

    /**
     * Buscar pedidos por telefone do cliente
     */
    public function getOrdersByPhone($phone)
    {
        $sql = "SELECT * FROM orders WHERE customer_phone = ? ORDER BY created_at DESC";
        $stmt = $this->db->prepare($sql);
        $result = $stmt->executeQuery([$phone]);
        return $result->fetchAllAssociative();
    }
    public function create($data)
    {
        return $this->insert('orders', $data);
    }

    /**
     * Criar pedido com cliente associado
     */
    public function createWithCustomer($orderData, $customerData)
    {
        // Primeiro, criar ou atualizar o cliente
        $customerModel = new \App\Models\Customer($this->db);
        $customerId = $customerModel->createOrUpdate($customerData);
        
        // Adicionar customer_id aos dados do pedido
        $orderData['customer_id'] = $customerId;
        
        // Criar o pedido
        $orderId = $this->insert('orders', $orderData);
        
        // Atualizar estatísticas do cliente
        $customerModel->updateOrderStats($customerId, $orderData['total_amount']);
        
        return $orderId;
    }

    /**
     * Buscar pedido com dados do cliente
     */
    public function getOrderWithCustomer($orderId)
    {
        $sql = "
            SELECT o.*, c.name as customer_name, c.phone as customer_phone, 
                   c.email as customer_email, c.address as customer_address,
                   c.total_orders as customer_total_orders, c.total_spent as customer_total_spent,
                   c.loyalty_points as customer_loyalty_points
            FROM orders o
            LEFT JOIN customers c ON o.customer_id = c.id
            WHERE o.id = ?
        ";
        
        $stmt = $this->db->prepare($sql);
        $result = $stmt->executeQuery([$orderId]);
        return $result->fetchAssociative();
    }

    public function getByUserId($userId)
    {
        return $this->findBy('orders', ['user_id' => $userId], 'created_at DESC');
    }

    public function getById($id)
    {
        return $this->findById('orders', $id);
    }

    /**
     * Busca pedido por ID verificando se pertence ao usuário/loja
     */
    public function getByIdAndUserId($id, $userId)
    {
        $sql = "SELECT * FROM orders WHERE id = ? AND user_id = ?";
        $stmt = $this->db->prepare($sql);
        $result = $stmt->executeQuery([$id, $userId]);
        return $result->fetchAssociative();
    }

    public function updateStatus($id, $status)
    {
        return $this->update('orders', $id, ['status' => $status]);
    }

    public function getOrderWithItems($orderId)
    {
        // Busca o pedido com dados do cliente
        $sql = "
            SELECT o.*, c.name as customer_name, c.phone as customer_phone, 
                   c.email as customer_email, c.address as customer_address,
                   c.total_orders as customer_total_orders, c.total_spent as customer_total_spent,
                   c.loyalty_points as customer_loyalty_points
            FROM orders o
            LEFT JOIN customers c ON o.customer_id = c.id
            WHERE o.id = ?
        ";
        $stmt = $this->db->prepare($sql);
        $result = $stmt->executeQuery([$orderId]);
        $order = $result->fetchAssociative();
        
        if (!$order) {
            return null;
        }

        // Busca os itens do pedido
        $sql = "
            SELECT oi.*, p.name as product_name, p.image as product_image
            FROM order_items oi
            JOIN products p ON oi.product_id = p.id
            WHERE oi.order_id = ?
            ORDER BY oi.id
        ";
        $stmt = $this->db->prepare($sql);
        $result = $stmt->executeQuery([$orderId]);
        $items = $result->fetchAllAssociative();

        // Para cada item, busca os ingredientes/adicionais
        foreach ($items as &$item) {
            $sql = "
                SELECT oii.*, i.name as ingredient_name
                FROM order_item_ingredients oii
                JOIN ingredients i ON oii.ingredient_id = i.id
                WHERE oii.order_item_id = ?
            ";
            $stmt = $this->db->prepare($sql);
            $result = $stmt->executeQuery([$item['id']]);
            $item['ingredients'] = $result->fetchAllAssociative();
        }

        $order['items'] = $items;
        return $order;
    }

    public function generateWhatsAppMessage($order)
    {
        $message = "🛍️ *NOVO PEDIDO* \n\n";
        $message .= "👤 *Cliente:* {$order['customer_name']}\n";
        $message .= "📱 *Telefone:* {$order['customer_phone']}\n";
        $message .= "📍 *Endereço:* {$order['customer_address']}\n\n";
        
        $message .= "🍓 *Itens do Pedido:*\n";
        
        foreach ($order['items'] as $item) {
            $message .= "▫️ {$item['quantity']}x {$item['product_name']}";
            
            if ($item['size']) {
                $message .= " ({$item['size']})";
            }
            
            $message .= " - R$ " . number_format($item['unit_price'], 2, ',', '.') . "\n";
            
            if (!empty($item['ingredients'])) {
                $message .= "   *Adicionais:* ";
                $ingredients = [];
                foreach ($item['ingredients'] as $ingredient) {
                    $ingredientText = $ingredient['ingredient_name'];
                    if ($ingredient['quantity'] > 1) {
                        $ingredientText .= " ({$ingredient['quantity']}x)";
                    }
                    if ($ingredient['price'] > 0) {
                        $ingredientText .= " +R$ " . number_format($ingredient['price'], 2, ',', '.');
                    }
                    $ingredients[] = $ingredientText;
                }
                $message .= implode(', ', $ingredients) . "\n";
            }
            
            if ($item['notes']) {
                $message .= "   *Obs:* {$item['notes']}\n";
            }
            $message .= "\n";
        }
        
        $message .= "💰 *Total: R$ " . number_format($order['total_amount'], 2, ',', '.') . "*\n\n";
        
        if ($order['notes']) {
            $message .= "📝 *Observações:* {$order['notes']}\n\n";
        }
        
        $message .= "🕒 *Pedido realizado em:* " . date('d/m/Y H:i', strtotime($order['created_at']));
        
        return $message;
    }

    /**
     * Lista pedidos com paginação e filtros
     */
    public function getAllOrdersPaginated($page = 1, $perPage = 20, $search = '', $status = '', $userId = null)
    {
        $offset = ($page - 1) * $perPage;
        
        $sql = "
            SELECT o.*, 
                   COUNT(oi.id) as items_count
            FROM orders o
            LEFT JOIN order_items oi ON o.id = oi.order_id
            WHERE 1=1
        ";
        
        $params = [];
        
        // Filtro por loja/usuário
        if ($userId) {
            $sql .= " AND o.user_id = ?";
            $params[] = $userId;
        }
        
        // Filtro de busca
        if (!empty($search)) {
            $sql .= " AND (o.customer_name LIKE ? OR o.customer_phone LIKE ? OR o.id = ?)";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
            $params[] = $search;
        }
        
        // Filtro de status
        if (!empty($status)) {
            $sql .= " AND o.status = ?";
            $params[] = $status;
        }
        
        $sql .= " GROUP BY o.id ORDER BY o.created_at DESC LIMIT {$perPage} OFFSET {$offset}";
        
        $stmt = $this->db->prepare($sql);
        $result = $stmt->executeQuery($params);
        return $result->fetchAllAssociative();
    }

    /**
     * Conta total de pedidos com filtros
     */
    public function getTotalOrdersCount($search = '', $status = '', $userId = null)
    {
        $sql = "SELECT COUNT(DISTINCT o.id) as total FROM orders o WHERE 1=1";
        $params = [];
        
        // Filtro por loja/usuário
        if ($userId) {
            $sql .= " AND o.user_id = ?";
            $params[] = $userId;
        }
        
        // Filtro de busca
        if (!empty($search)) {
            $sql .= " AND (o.customer_name LIKE ? OR o.customer_phone LIKE ? OR o.id = ?)";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
            $params[] = $search;
        }
        
        // Filtro de status
        if (!empty($status)) {
            $sql .= " AND o.status = ?";
            $params[] = $status;
        }
        
        $stmt = $this->db->prepare($sql);
        $result = $stmt->executeQuery($params);
        $row = $result->fetchAssociative();
        return (int) $row['total'];
    }
}
