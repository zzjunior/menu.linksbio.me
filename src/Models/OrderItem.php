<?php

namespace App\Models;

class OrderItem extends BaseModel
{
    public function create($data)
    {
        return $this->insert('order_items', $data);
    }

    public function addIngredient($orderItemId, $ingredientId, $quantity = 1, $price = 0.00)
    {
        return $this->insert('order_item_ingredients', [
            'order_item_id' => $orderItemId,
            'ingredient_id' => $ingredientId,
            'quantity' => $quantity,
            'price' => $price
        ]);
    }

    public function getByOrderId($orderId)
    {
        return $this->findBy('order_items', ['order_id' => $orderId]);
    }
}
