<?php

declare(strict_types=1);

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\Product;
use App\Models\Ingredient;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Services\TemplateService;

class CartController
{
    private Product $productModel;
    private Ingredient $ingredientModel;
    private Order $orderModel;
    private OrderItem $orderItemModel;
    private User $userModel;
    private TemplateService $templateService;

    public function __construct(
        Product $productModel,
        Ingredient $ingredientModel,
        Order $orderModel,
        OrderItem $orderItemModel,
        User $userModel,
        TemplateService $templateService
    ) {
        $this->productModel = $productModel;
        $this->ingredientModel = $ingredientModel;
        $this->orderModel = $orderModel;
        $this->orderItemModel = $orderItemModel;
        $this->userModel = $userModel;
        $this->templateService = $templateService;
    }

    /**
     * Exibe o carrinho
     */
    public function index(Request $request, Response $response, array $args): Response
    {
        $storeSlug = $args['store'] ?? '';
        $store = $this->userModel->getStoreBySlug($storeSlug);
        
        if (!$store) {
            return $response->withStatus(404);
        }

        $cart = $_SESSION['cart_' . $store['id']] ?? [];
        $cartItems = [];
        $total = 0;

        foreach ($cart as $item) {
            $product = $this->productModel->getById($item['product_id']);
            if ($product) {
                $itemTotal = $item['price'] * $item['quantity'];
                
                // Adicionar preço dos ingredientes
                $ingredients = [];
                foreach ($item['ingredients'] as $ingredientId => $quantity) {
                    $ingredient = $this->ingredientModel->getById($ingredientId);
                    if ($ingredient) {
                        $ingredients[] = [
                            'name' => $ingredient['name'],
                            'quantity' => $quantity,
                            'price' => $ingredient['additional_price']
                        ];
                        $itemTotal += $ingredient['additional_price'] * $quantity * $item['quantity'];
                    }
                }

                $cartItems[] = [
                    'cart_id' => $item['cart_id'],
                    'product' => $product,
                    'quantity' => $item['quantity'],
                    'size' => $item['size'],
                    'price' => $item['price'],
                    'ingredients' => $ingredients,
                    'notes' => $item['notes'],
                    'total' => $itemTotal
                ];
                
                $total += $itemTotal;
            }
        }

        $data = [
            'title' => 'Carrinho - ' . $store['store_name'],
            'store' => $store,
            'cart_items' => $cartItems,
            'total' => $total,
            'store_slug' => $storeSlug
        ];

        return $this->templateService->renderResponse($response, 'cart/index', $data);
    }

    /**
     * Adiciona produto ao carrinho
     */
    public function addItem(Request $request, Response $response, array $args): Response
    {
        $storeSlug = $args['store'] ?? '';
        $store = $this->userModel->getStoreBySlug($storeSlug);
        
        if (!$store) {
            return $response->withStatus(404);
        }

        $data = $request->getParsedBody();
        $productId = (int) ($data['product_id'] ?? 0);
        $quantity = (int) ($data['quantity'] ?? 1);
        $size = $data['size'] ?? '';
        $notes = $data['notes'] ?? '';
        $ingredients = $data['ingredients'] ?? [];

        $product = $this->productModel->getById($productId);
        if (!$product || $product['user_id'] != $store['id']) {
            return $response->withStatus(404);
        }

        // Determinar preço baseado no tamanho
        $price = $product['price'];
        if ($size && isset($product['sizes'])) {
            $sizes = json_decode($product['sizes'], true);
            if (isset($sizes[$size])) {
                $price = $sizes[$size]['price'];
            }
        }

        $cartItem = [
            'cart_id' => uniqid(),
            'product_id' => $productId,
            'quantity' => $quantity,
            'size' => $size,
            'price' => $price,
            'notes' => $notes,
            'ingredients' => $ingredients
        ];

        if (!isset($_SESSION['cart_' . $store['id']])) {
            $_SESSION['cart_' . $store['id']] = [];
        }

        $_SESSION['cart_' . $store['id']][] = $cartItem;

        return $response->withHeader('Location', '/' . $storeSlug . '/carrinho')->withStatus(302);
    }

    /**
     * Remove item do carrinho
     */
    public function removeItem(Request $request, Response $response, array $args): Response
    {
        $storeSlug = $args['store'] ?? '';
        $store = $this->userModel->getStoreBySlug($storeSlug);
        
        if (!$store) {
            return $response->withStatus(404);
        }

        $cartId = $args['cart_id'] ?? '';
        
        if (isset($_SESSION['cart_' . $store['id']])) {
            $_SESSION['cart_' . $store['id']] = array_filter(
                $_SESSION['cart_' . $store['id']],
                fn($item) => $item['cart_id'] !== $cartId
            );
        }

        return $response->withHeader('Location', '/' . $storeSlug . '/carrinho')->withStatus(302);
    }

    /**
     * Exibe formulário de checkout
     */
    public function checkout(Request $request, Response $response, array $args): Response
    {
        $storeSlug = $args['store'] ?? '';
        $store = $this->userModel->getStoreBySlug($storeSlug);
        
        if (!$store) {
            return $response->withStatus(404);
        }

        $cart = $_SESSION['cart_' . $store['id']] ?? [];
        
        if (empty($cart)) {
            return $response->withHeader('Location', '/' . $storeSlug)->withStatus(302);
        }

        $data = [
            'title' => 'Finalizar Pedido - ' . $store['store_name'],
            'store' => $store,
            'store_slug' => $storeSlug,
            'error' => $_SESSION['error'] ?? null
        ];

        unset($_SESSION['error']);

        return $this->templateService->renderResponse($response, 'cart/checkout', $data);
    }

    /**
     * Processa o pedido e envia para WhatsApp
     */
    public function processOrder(Request $request, Response $response, array $args): Response
    {
        $storeSlug = $args['store'] ?? '';
        $store = $this->userModel->getStoreBySlug($storeSlug);
        
        if (!$store) {
            return $response->withStatus(404);
        }

        $cart = $_SESSION['cart_' . $store['id']] ?? [];
        
        if (empty($cart)) {
            return $response->withHeader('Location', '/' . $storeSlug)->withStatus(302);
        }

        $data = $request->getParsedBody();
        $customerName = trim($data['customer_name'] ?? '');
        $customerPhone = preg_replace('/[^0-9]/', '', $data['customer_phone'] ?? '');
        $customerAddress = trim($data['customer_address'] ?? '');
        $notes = trim($data['notes'] ?? '');

        // Validações
        if (empty($customerName) || empty($customerPhone) || empty($customerAddress)) {
            $_SESSION['error'] = 'Todos os campos são obrigatórios';
            return $response->withHeader('Location', '/' . $storeSlug . '/checkout')->withStatus(302);
        }

        if (strlen($customerPhone) < 10) {
            $_SESSION['error'] = 'Telefone inválido';
            return $response->withHeader('Location', '/' . $storeSlug . '/checkout')->withStatus(302);
        }

        // Calcular total
        $total = 0;
        foreach ($cart as $item) {
            $itemTotal = $item['price'] * $item['quantity'];
            
            foreach ($item['ingredients'] as $ingredientId => $quantity) {
                $ingredient = $this->ingredientModel->getById($ingredientId);
                if ($ingredient) {
                    $itemTotal += $ingredient['additional_price'] * $quantity * $item['quantity'];
                }
            }
            
            $total += $itemTotal;
        }

        try {
            // Criar pedido
            $orderId = $this->orderModel->create([
                'user_id' => $store['id'],
                'customer_name' => $customerName,
                'customer_phone' => $customerPhone,
                'customer_address' => $customerAddress,
                'total_amount' => $total,
                'notes' => $notes
            ]);

            // Criar itens do pedido
            foreach ($cart as $item) {
                $orderItemId = $this->orderItemModel->create([
                    'order_id' => $orderId,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['price'],
                    'size' => $item['size'],
                    'notes' => $item['notes']
                ]);

                // Adicionar ingredientes
                foreach ($item['ingredients'] as $ingredientId => $quantity) {
                    $ingredient = $this->ingredientModel->getById($ingredientId);
                    if ($ingredient) {
                        $this->orderItemModel->addIngredient(
                            $orderItemId,
                            $ingredientId,
                            $quantity,
                            $ingredient['additional_price']
                        );
                    }
                }
            }

            // Buscar pedido completo e gerar mensagem WhatsApp
            $order = $this->orderModel->getOrderWithItems($orderId);
            $whatsappMessage = $this->orderModel->generateWhatsAppMessage($order);
            
            // Montar URL do WhatsApp
            $whatsappUrl = "https://wa.me/{$store['whatsapp']}?text=" . urlencode($whatsappMessage);

            // Limpar carrinho
            unset($_SESSION['cart_' . $store['id']]);

            $data = [
                'title' => 'Pedido Enviado - ' . $store['store_name'],
                'store' => $store,
                'store_slug' => $storeSlug,
                'order' => $order,
                'whatsapp_url' => $whatsappUrl
            ];

            return $this->templateService->renderResponse($response, 'cart/success', $data);

        } catch (\Exception $e) {
            $_SESSION['error'] = 'Erro ao processar pedido. Tente novamente.';
            return $response->withHeader('Location', '/' . $storeSlug . '/checkout')->withStatus(302);
        }
    }
}
