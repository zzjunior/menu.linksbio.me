<?php

namespace App\Models;

class Order extends BaseModel
{
    public function create($data)
    {
        return $this->insert('orders', $data);
    }

    public function getByUserId($userId)
    {
        return $this->findBy('orders', ['user_id' => $userId], 'created_at DESC');
    }

    public function getById($id)
    {
        return $this->findById('orders', $id);
    }

    public function updateStatus($id, $status)
    {
        return $this->update('orders', $id, ['status' => $status]);
    }

    public function getOrderWithItems($orderId)
    {
        // Busca o pedido
        $order = $this->getById($orderId);
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
}
