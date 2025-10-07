<?php

namespace App\Models;

class Customer extends BaseModel
{
    /**
     * Criar ou atualizar cliente
     */
    public function createOrUpdate($data)
    {
        $existing = $this->findByPhone($data['phone']);
        
        if ($existing) {
            // Atualizar dados do cliente existente
            $this->update('customers', $existing['id'], [
                'name' => $data['name'],
                'email' => $data['email'] ?? $existing['email'],
                'address' => $data['address'] ?? $existing['address'],
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            return $existing['id'];
        } else {
            // Criar novo cliente
            return $this->insert('customers', [
                'name' => $data['name'],
                'phone' => $data['phone'],
                'email' => $data['email'] ?? null,
                'address' => $data['address'] ?? null,
                'total_orders' => 0,
                'total_spent' => 0.00,
                'loyalty_points' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }
    }

    /**
     * Buscar cliente por telefone
     */
    public function findByPhone($phone)
    {
        $sql = "SELECT * FROM customers WHERE phone = ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $result = $stmt->executeQuery([$phone]);
        return $result->fetchAssociative();
    }

    /**
     * Buscar cliente por ID
     */
    public function getById($id)
    {
        return $this->findById('customers', $id);
    }

    /**
     * Atualizar estatísticas do cliente após novo pedido
     */
    public function updateOrderStats($customerId, $orderAmount)
    {
        $sql = "
            UPDATE customers 
            SET 
                total_orders = total_orders + 1,
                total_spent = total_spent + ?,
                loyalty_points = loyalty_points + FLOOR(?),
                updated_at = NOW()
            WHERE id = ?
        ";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->executeStatement([$orderAmount, $orderAmount, $customerId]);
    }

    /**
     * Verificar se cliente tem direito a desconto por fidelidade
     */
    public function getLoyaltyDiscount($customerId)
    {
        $customer = $this->getById($customerId);
        if (!$customer) return 0;

        // Desconto baseado no número de pedidos
        $totalOrders = $customer['total_orders'];
        
        if ($totalOrders >= 20) {
            return 15; // 15% para clientes VIP (20+ pedidos)
        } elseif ($totalOrders >= 10) {
            return 10; // 10% para clientes fiéis (10+ pedidos)
        } elseif ($totalOrders >= 5) {
            return 5;  // 5% para clientes frequentes (5+ pedidos)
        }
        
        return 0; // Sem desconto
    }

    /**
     * Obter status de fidelidade do cliente
     */
    public function getLoyaltyStatus($customerId)
    {
        $customer = $this->getById($customerId);
        if (!$customer) return 'new';

        $totalOrders = $customer['total_orders'];
        
        if ($totalOrders >= 20) {
            return 'vip';
        } elseif ($totalOrders >= 10) {
            return 'loyal';
        } elseif ($totalOrders >= 5) {
            return 'frequent';
        }
        
        return 'regular';
    }

    /**
     * Listar todos os clientes com paginação
     */
    public function getAllCustomers($page = 1, $perPage = 20, $search = '')
    {
        $offset = ($page - 1) * $perPage;
        
        $sql = "
            SELECT c.*, 
                   COUNT(o.id) as orders_count,
                   MAX(o.created_at) as last_order_date
            FROM customers c
            LEFT JOIN orders o ON c.id = o.customer_id
            WHERE 1=1
        ";
        
        $params = [];
        
        if (!empty($search)) {
            $sql .= " AND (c.name LIKE ? OR c.phone LIKE ? OR c.email LIKE ?)";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
        }
        
        $sql .= " GROUP BY c.id ORDER BY c.total_spent DESC LIMIT {$perPage} OFFSET {$offset}";
        
        $stmt = $this->db->prepare($sql);
        $result = $stmt->executeQuery($params);
        return $result->fetchAllAssociative();
    }

    /**
     * Contar total de clientes
     */
    public function getTotalCustomersCount($search = '')
    {
        $sql = "SELECT COUNT(*) as total FROM customers WHERE 1=1";
        $params = [];
        
        if (!empty($search)) {
            $sql .= " AND (name LIKE ? OR phone LIKE ? OR email LIKE ?)";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
        }
        
        $stmt = $this->db->prepare($sql);
        $result = $stmt->executeQuery($params);
        $row = $result->fetchAssociative();
        return (int)$row['total'];
    }

    /**
     * Obter clientes mais valiosos
     */
    public function getTopCustomers($limit = 10)
    {
        $sql = "
            SELECT c.*, 
                   COUNT(o.id) as orders_count,
                   MAX(o.created_at) as last_order_date
            FROM customers c
            LEFT JOIN orders o ON c.id = o.customer_id
            GROUP BY c.id
            ORDER BY c.total_spent DESC
            LIMIT ?
        ";
        
        $stmt = $this->db->prepare($sql);
        $result = $stmt->executeQuery([$limit]);
        return $result->fetchAllAssociative();
    }

    /**
     * Obter estatísticas de clientes
     */
    public function getCustomerStats()
    {
        $sql = "
            SELECT 
                COUNT(*) as total_customers,
                SUM(total_spent) as total_revenue,
                AVG(total_spent) as avg_spent_per_customer,
                AVG(total_orders) as avg_orders_per_customer,
                COUNT(CASE WHEN total_orders >= 10 THEN 1 END) as loyal_customers,
                COUNT(CASE WHEN total_orders >= 20 THEN 1 END) as vip_customers
            FROM customers
        ";
        
        $stmt = $this->db->prepare($sql);
        $result = $stmt->executeQuery();
        return $result->fetchAssociative();
    }
}