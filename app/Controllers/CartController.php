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
use App\Models\Customer;
use App\Services\TemplateService;

class CartController
{
    /**
     * Sincroniza carrinho do localStorage para a sessão
     */
    public function syncCart(Request $request, Response $response, array $args): Response
    {
        $storeSlug = $args['store'] ?? '';
        $store = $this->userModel->getStoreBySlug($storeSlug);
        if (!$store) {
            return $response->withStatus(404);
        }

        $data = $request->getParsedBody();
        $cart = $data['cart'] ?? [];
        if (!is_array($cart)) {
            $cart = [];
        }
        $_SESSION['cart_' . $store['id']] = $cart;

    $response->getBody()->write(json_encode(['success' => true]));
    return $response->withHeader('Content-Type', 'application/json')
        ->withStatus(200);
}

    private Product $productModel;
    private Ingredient $ingredientModel;
    private Order $orderModel;
    private OrderItem $orderItemModel;
    private User $userModel;
    private Customer $customerModel;
    private TemplateService $templateService;
    private \PDO $pdo;

    public function __construct(
        Product $productModel,
        Ingredient $ingredientModel,
        Order $orderModel,
        OrderItem $orderItemModel,
        User $userModel,
        Customer $customerModel,
        TemplateService $templateService,
        \PDO $pdo
    ) {
        $this->productModel = $productModel;
        $this->ingredientModel = $ingredientModel;
        $this->orderModel = $orderModel;
        $this->orderItemModel = $orderItemModel;
        $this->userModel = $userModel;
        $this->customerModel = $customerModel;
        $this->templateService = $templateService;
        $this->pdo = $pdo;
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
        $cartItems = [];
        $total = 0;

        foreach ($cart as $item) {
            $product = $this->productModel->getById($item['product_id']);
            if ($product) {
                $itemTotal = $item['price'] * $item['quantity'];
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
            'title' => 'Checkout - ' . $store['store_name'],
            'store' => $store,
            'cart_items' => $cartItems,
            'total' => $total,
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
        return $response->withHeader('Location', '/' . $storeSlug . '/checkout')->withStatus(302);
    }

    $data = $request->getParsedBody();
    $customerName = trim($data['customer_name'] ?? '');
    $customerPhone = preg_replace('/[^0-9]/', '', $data['customer_phone'] ?? '');
    $customerAddress = trim($data['customer_address'] ?? '');
    $notes = trim($data['notes'] ?? '');

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
        // Preparar dados do cliente
        $customerData = [
            'name' => $customerName,
            'phone' => $customerPhone,
            'address' => $customerAddress
        ];
        
        // Preparar dados do pedido
        $orderData = [
            'user_id' => $store['id'],
            'customer_name' => $customerName,
            'customer_phone' => $customerPhone,
            'customer_address' => $customerAddress,
            'notes' => $notes,
            'total_amount' => $total,
            'status' => 'pendente',
            'created_at' => date('Y-m-d H:i:s')
        ];

        // Criar pedido com cliente associado
        $orderId = $this->orderModel->createWithCustomer($orderData, $customerData);

        // Salvar itens do pedido
        foreach ($cart as $item) {
            $orderItemId = $this->orderItemModel->create([
                'order_id' => $orderId,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['price'],
                'size' => $item['size'] ?? '',
                'notes' => $item['notes'] ?? '',
                'created_at' => date('Y-m-d H:i:s')
            ]);

            // Salvar ingredientes/adicionais do item
            if (!empty($item['ingredients'])) {
                foreach ($item['ingredients'] as $ingredientId => $quantity) {
                    $ingredient = $this->ingredientModel->getById($ingredientId);
                    $price = $ingredient ? $ingredient['additional_price'] : 0;
                    $this->orderItemModel->addIngredient($orderItemId, $ingredientId, $quantity, $price);
                }
            }
        }

        // Montar mensagem WhatsApp simples
        $whatsappMessage = "Olá, novo pedido recebido!\nCliente: $customerName\nTelefone: $customerPhone\nEndereço: $customerAddress\nTotal: R$ " . number_format($total, 2, ',', '.') . "\nPedido #$orderId";
        $whatsappUrl = "https://wa.me/55{$store['whatsapp']}?text=" . urlencode($whatsappMessage);

        // Limpar carrinho
        unset($_SESSION['cart_' . $store['id']]);

        // Buscar pedido completo para template de sucesso
        $order = $this->orderModel->getOrderWithItems($orderId);

        // Enriquecer ingredientes/adicionais dos itens do pedido com o campo 'name'
        if (!empty($order['items'])) {
            foreach ($order['items'] as &$item) {
                if (!empty($item['ingredients'])) {
                    foreach ($item['ingredients'] as &$ingredient) {
                        if (empty($ingredient['name']) && !empty($ingredient['ingredient_id'])) {
                            $ingredientData = $this->ingredientModel->getById($ingredient['ingredient_id']);
                            if ($ingredientData && !empty($ingredientData['name'])) {
                                $ingredient['name'] = $ingredientData['name'];
                            } else {
                                $ingredient['name'] = 'Adicional';
                            }
                        }
                    }
                    unset($ingredient);
                }
            }
            unset($item);
        }

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
