<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Ingredient;
use App\Models\Order;
use App\Models\User;
use App\Services\TemplateService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Controlador para o painel administrativo
 */
class AdminController
{
    private Product $productModel;
    private Category $categoryModel;
    private Ingredient $ingredientModel;
    private Order $orderModel;
    private User $userModel;
    private TemplateService $templateService;

    public function __construct(
        Product $productModel,
        Category $categoryModel,
        Ingredient $ingredientModel,
        Order $orderModel,
        User $userModel,
        TemplateService $templateService
    ) {
        $this->productModel = $productModel;
        $this->categoryModel = $categoryModel;
        $this->ingredientModel = $ingredientModel;
        $this->orderModel = $orderModel;
        $this->userModel = $userModel;
        $this->templateService = $templateService;
    }

    /**
     * Dashboard do admin
     */
    public function dashboard(Request $request, Response $response): Response
    {
        $userId = $_SESSION['user_id'];
        
        $data = [
            'pageTitle' => 'Painel Administrativo',
            'store' => $this->userModel->getById($userId),
            'totalProducts' => count($this->productModel->getAll($userId)),
            'totalCategories' => count($this->categoryModel->getAll($userId)),
            'totalOrders' => count($this->orderModel->getByUserId($userId)),
            'recentOrders' => array_slice($this->orderModel->getByUserId($userId), 0, 5),
            'totalIngredients' => count($this->ingredientModel->getAll($userId))
        ];

        return $this->templateService->renderResponse($response, 'admin.dashboard', $data);
    }

    // === PRODUTOS ===

    /**
     * Lista produtos
     */
    public function listProducts(Request $request, Response $response): Response
    {
        $userId = $_SESSION['user_id'];
        $products = $this->productModel->getAll($userId);
        
        $data = [
            'pageTitle' => 'Gerenciar Produtos',
            'products' => $products
        ];

        return $this->templateService->renderResponse($response, 'admin/products/list', $data);
    }

    /**
     * Exibe formulário de novo produto
     */
    public function newProduct(Request $request, Response $response): Response
    {
        $userId = $_SESSION['user_id'];
        $categories = $this->categoryModel->getAll($userId);

        // Buscar tipos únicos de ingredientes do usuário
        $ingredients = $this->ingredientModel->getAll($userId);
        $ingredientTypes = [];
        foreach ($ingredients as $ingredient) {
            if (!empty($ingredient['type'])) {
                // Suporta múltiplos tipos separados por vírgula
                foreach (explode(',', $ingredient['type']) as $type) {
                    $type = trim($type);
                    if ($type && !in_array($type, $ingredientTypes)) {
                        $ingredientTypes[] = $type;
                    }
                }
            }
        }

        $data = [
            'pageTitle' => 'Novo Produto',
            'categories' => $categories,
            'ingredientTypes' => $ingredientTypes
        ];

        return $this->templateService->renderResponse($response, 'admin/products/form', $data);
    }

    /**
     * Processa upload de imagem
     */
    private function handleImageUpload($uploadedFile, string $directory = 'products'): ?string
    {
        // Suporte tanto para PSR-7 quanto para array do PHP
        if (!$uploadedFile) {
            return null;
        }

        // PSR-7 UploadedFileInterface
        if (is_object($uploadedFile) && method_exists($uploadedFile, 'getError')) {
            if ($uploadedFile->getError() !== UPLOAD_ERR_OK) {
                return null;
            }
            $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
            $filename = uniqid() . '.' . $extension;
            $uploadPath = __DIR__ . '/../../public/uploads/' . $directory;
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }
            $filepath = $uploadPath . '/' . $filename;
            $uploadedFile->moveTo($filepath);
            return '/uploads/' . $directory . '/' . $filename;
        }

        // Array do PHP ($_FILES)
        if (is_array($uploadedFile) && isset($uploadedFile['tmp_name']) && $uploadedFile['error'] === UPLOAD_ERR_OK) {
            $extension = pathinfo($uploadedFile['name'], PATHINFO_EXTENSION);
            $filename = uniqid() . '.' . $extension;
            $uploadPath = __DIR__ . '/../../public/uploads/' . $directory;
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }
            $filepath = $uploadPath . '/' . $filename;
            move_uploaded_file($uploadedFile['tmp_name'], $filepath);
            return '/uploads/' . $directory . '/' . $filename;
        }

        return null;
    }

    /**
     * Cria um novo produto
     */
    public function createProduct(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        $uploadedFiles = $request->getUploadedFiles();
        
        try {
            // Processar upload de imagem
            if (isset($uploadedFiles['image'])) {
                $imageUrl = $this->handleImageUpload($uploadedFiles['image'], 'products');
                if ($imageUrl) {
                    $data['image_url'] = $imageUrl;
                }
            }

            $data['user_id'] = $_SESSION['user_id'];
            $productId = $this->productModel->create($data); // Precisa retornar o ID do produto criado

            // Salvar limites de ingredientes por tipo, se enviados
            if (isset($data['max_ingredients_product']) && is_array($data['max_ingredients_product'])) {
                $this->productModel->saveMaxIngredientsProduct($productId, $data['max_ingredients_product']);
            }

            return $response
                ->withHeader('Location', '/admin/products')
                ->withStatus(302);
        } catch (\Exception $e) {
            // Retorna ao formulário com erro
            $categories = $this->categoryModel->getAllByUser($_SESSION['user_id']);
            
            $templateData = [
                'pageTitle' => 'Novo Produto',
                'categories' => $categories,
                'error' => 'Erro ao criar produto: ' . $e->getMessage(),
                'formData' => $data
            ];

            return $this->templateService->renderResponse($response, 'admin/products/form', $templateData);
        }
    }

    /**
     * Exibe formulário de edição de produto
     */
    public function editProduct(Request $request, Response $response, array $args): Response
    {
        $productId = (int) $args['id'];
        $product = $this->productModel->getById($productId);

        if (!$product) {
            return $response->withStatus(404);
        }

        $userId = $_SESSION['user_id'];
        $categories = $this->categoryModel->getAll($userId);

        // Buscar tipos únicos de ingredientes do usuário
        $ingredients = $this->ingredientModel->getAll($userId);
        $ingredientTypes = [];
        foreach ($ingredients as $ingredient) {
            if (!empty($ingredient['type'])) {
                // Suporta múltiplos tipos separados por vírgula
                foreach (explode(',', $ingredient['type']) as $type) {
                    $type = trim($type);
                    if ($type && !in_array($type, $ingredientTypes)) {
                        $ingredientTypes[] = $type;
                    }
                }
            }
        }

        // Buscar limites atuais de ingredientes por tipo para o produto
        $maxIngredientsType = $this->productModel->getMaxIngredientsType($productId);

        $data = [
            'pageTitle' => 'Editar Produto',
            'product' => $product,
            'categories' => $categories,
            'ingredientTypes' => $ingredientTypes,
            'maxIngredientsType' => $maxIngredientsType
        ];

        return $this->templateService->renderResponse($response, 'admin/products/form', $data);
    }

    /**
     * Atualiza um produto
     */
    public function updateProduct(Request $request, Response $response, array $args): Response
    {
        $productId = (int) $args['id'];
        $data = $request->getParsedBody();
        $uploadedFiles = $request->getUploadedFiles();
        
        try {
            // Processar upload de imagem
            if (isset($uploadedFiles['image'])) {
                $imageUrl = $this->handleImageUpload($uploadedFiles['image'], 'products');
                if ($imageUrl) {
                    $data['image_url'] = $imageUrl;
                }
            }

            $this->productModel->updateProduct($productId, $data);

            // Atualizar limites de ingredientes por tipo, se enviados
            if (isset($data['max_ingredients_product']) && is_array($data['max_ingredients_product'])) {
                $this->productModel->saveMaxIngredientsProduct($productId, $data['max_ingredients_product']);
            }

            return $response
                ->withHeader('Location', '/admin/products')
                ->withStatus(302);
        } catch (\Exception $e) {
            $product = $this->productModel->getById($productId);
            $categories = $this->categoryModel->getAllByUser($_SESSION['user_id']);
            
            $templateData = [
                'pageTitle' => 'Editar Produto',
                'product' => $product,
                'categories' => $categories,
                'error' => 'Erro ao atualizar produto: ' . $e->getMessage(),
                'formData' => $data
            ];

            return $this->templateService->renderResponse($response, 'admin/products/form', $templateData);
        }
    }

    /**
     * Remove um produto
     */
    public function deleteProduct(Request $request, Response $response, array $args): Response
    {
        $productId = (int) $args['id'];
        
        $this->productModel->deleteProduct($productId);
        
        return $response
            ->withHeader('Location', '/admin/products')
            ->withStatus(302);
    }

    // === CATEGORIAS ===

    /**
     * Lista categorias
     */
    public function listCategories(Request $request, Response $response): Response
    {
        $userId = $_SESSION['user_id'];
        $categories = $this->categoryModel->getAllByUser($userId);
        
        $data = [
            'pageTitle' => 'Gerenciar Categorias',
            'categories' => $categories
        ];

        return $this->templateService->renderResponse($response, 'admin/categories/list', $data);
    }

    /**
     * Formulário para nova categoria
     */
    public function newCategory(Request $request, Response $response): Response
    {
        $data = [
            'pageTitle' => 'Nova Categoria'
        ];

        return $this->templateService->renderResponse($response, 'admin/categories/form', $data);
    }

    /**
     * Cria nova categoria
     */
    public function createCategory(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        
        try {
            // Forçar uso do $_FILES para upload
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $imageUrl = $this->handleImageUpload($_FILES['image'], 'categories');
                if ($imageUrl) {
                    $data['image_url'] = $imageUrl;
                    $data['image'] = $imageUrl;
                }
            }

            // Debug - verificar dados recebidos
            error_log('Dados da categoria: ' . print_r($data, true));
            error_log('FILES: ' . print_r($_FILES, true));

            // Converter checkbox para boolean
            $data['has_customization'] = isset($data['has_customization']) ? 1 : 0;
            $data['is_active'] = isset($data['is_active']) ? 1 : 0;
            $data['user_id'] = $_SESSION['user_id'];
            
            $this->categoryModel->create($data);
            
            return $response
                ->withHeader('Location', '/admin/categories')
                ->withStatus(302);
        } catch (\Exception $e) {
            $templateData = [
                'pageTitle' => 'Nova Categoria',
                'error' => 'Erro ao criar categoria: ' . $e->getMessage(),
                'formData' => $data
            ];

            return $this->templateService->renderResponse($response, 'admin/categories/form', $templateData);
        }
    }

    /**
     * Formulário para editar categoria
     */
    public function editCategory(Request $request, Response $response, array $args): Response
    {
        $categoryId = (int) $args['id'];
        $category = $this->categoryModel->getById($categoryId);
        
        if (!$category || $category['user_id'] !== $_SESSION['user_id']) {
            return $response->withStatus(404);
        }
        
        $data = [
            'pageTitle' => 'Editar Categoria',
            'category' => $category
        ];

        return $this->templateService->renderResponse($response, 'admin/categories/form', $data);
    }

    /**
     * Atualiza categoria
     */
    public function updateCategory(Request $request, Response $response, array $args): Response
    {
        $categoryId = (int) $args['id'];
        $data = $request->getParsedBody();
        
        try {
            // Forçar uso do $_FILES para upload
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $imageUrl = $this->handleImageUpload($_FILES['image'], 'categories');
                if ($imageUrl) {
                    $data['image_url'] = $imageUrl;
                    $data['image'] = $imageUrl;
                }
            }

            // Debug - verificar dados recebidos
            error_log('Update categoria - Dados: ' . print_r($data, true));
            error_log('Update categoria - FILES: ' . print_r($_FILES, true));

            // Converter checkbox para boolean
            $data['has_customization'] = isset($data['has_customization']) ? 1 : 0;
            $data['is_active'] = isset($data['is_active']) ? 1 : 0;
            
            $this->categoryModel->updateById($categoryId, $data);
            
            return $response
                ->withHeader('Location', '/admin/categories')
                ->withStatus(302);
        } catch (\Exception $e) {
            $category = $this->categoryModel->getById($categoryId);
            
            $templateData = [
                'pageTitle' => 'Editar Categoria',
                'category' => $category,
                'error' => 'Erro ao atualizar categoria: ' . $e->getMessage(),
                'formData' => $data
            ];

            return $this->templateService->renderResponse($response, 'admin/categories/form', $templateData);
        }
    }

    /**
     * Excluir categoria
     */
    public function deleteCategory(Request $request, Response $response, array $args): Response
    {
        $categoryId = (int) $args['id'];
        $category = $this->categoryModel->getById($categoryId);
        
        if (!$category || $category['user_id'] !== $_SESSION['user_id']) {
            return $response->withStatus(404);
        }
        
        try {
            // Remover imagem se existir
            if ($category['image'] && file_exists($category['image'])) {
                unlink($category['image']);
            }
            
            $this->categoryModel->deleteById($categoryId);
            return $response->withHeader('Location', '/admin/categories')->withStatus(302);
        } catch (\Exception $e) {
            // Redirecionar com erro
            return $response->withHeader('Location', '/admin/categories?error=' . urlencode($e->getMessage()))->withStatus(302);
        }
    }

    // === INGREDIENTES ===

    /**
     * Lista ingredientes
     */
    public function listIngredients(Request $request, Response $response): Response
    {
        $userId = $_SESSION['user_id'];
        $ingredients = $this->ingredientModel->getAllByUser($userId);
        
        $data = [
            'pageTitle' => 'Gerenciar Ingredientes',
            'ingredients' => $ingredients
        ];

        return $this->templateService->renderResponse($response, 'admin/ingredients/list', $data);
    }

    /**
     * Formulário para novo ingrediente
     */
    public function newIngredient(Request $request, Response $response): Response
    {
        $data = [
            'pageTitle' => 'Novo Ingrediente'
        ];

        return $this->templateService->renderResponse($response, 'admin/ingredients/form', $data);
    }

    /**
     * Cria novo ingrediente
     */
    public function createIngredient(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();

        try {
            // Forçar uso do $_FILES para upload
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $imageUrl = $this->handleImageUpload($_FILES['image'], 'ingredients');
                if ($imageUrl) {
                    $data['image_url'] = $imageUrl;
                    $data['image'] = $imageUrl;
                }
            }

            // Mapear TODOS os campos corretamente
            $mappedData = [
                'name' => $data['name'] ?? '',
                'description' => $data['description'] ?? '',
                'type' => $data['type'] ?? '',
                'price' => $data['price'] ?? 0,
                'additional_price' => $data['price'] ?? 0,
                'active' => isset($data['is_active']) ? 1 : 0,
                'is_active' => isset($data['is_active']) ? 1 : 0,
                'is_free' => isset($data['is_free']) ? 1 : 0,
                'max_quantity' => $data['max_quantity'] ?? 5,
                'sort_order' => $data['sort_order'] ?? 0,
                'image' => $data['image'] ?? null,
                'image_url' => $data['image_url'] ?? null,
                'user_id' => $_SESSION['user_id']
            ];
            
            // Se é gratuito, força preço = 0
            if ($mappedData['is_free']) {
                $mappedData['price'] = 0;
                $mappedData['additional_price'] = 0;
            }

            error_log('Create ingrediente - Dados MAPEADOS: ' . print_r($mappedData, true));
            
            $this->ingredientModel->create($mappedData);
            
            return $response
                ->withHeader('Location', '/admin/ingredients')
                ->withStatus(302);
        } catch (\Exception $e) {
            $templateData = [
                'pageTitle' => 'Novo Ingrediente',
                'error' => 'Erro ao criar ingrediente: ' . $e->getMessage(),
                'formData' => $data
            ];

            return $this->templateService->renderResponse($response, 'admin/ingredients/form', $templateData);
        }
    }

    /**
     * Formulário para editar ingrediente
     */
    public function editIngredient(Request $request, Response $response, array $args): Response
    {
        $ingredientId = (int) $args['id'];
        $ingredient = $this->ingredientModel->getById($ingredientId);
        
        if (!$ingredient || $ingredient['user_id'] !== $_SESSION['user_id']) {
            return $response->withStatus(404);
        }
        
        $data = [
            'pageTitle' => 'Editar Ingrediente',
            'ingredient' => $ingredient
        ];

        return $this->templateService->renderResponse($response, 'admin/ingredients/form', $data);
    }

    /**
     * Atualiza ingrediente
     */
    public function updateIngredient(Request $request, Response $response, array $args): Response
    {
        $ingredientId = (int) $args['id'];
        $data = $request->getParsedBody();

        try {
            // Forçar uso do $_FILES para upload
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $imageUrl = $this->handleImageUpload($_FILES['image'], 'ingredients');
                if ($imageUrl) {
                    $data['image_url'] = $imageUrl;
                    $data['image'] = $imageUrl;
                }
            }

            // Debug completo
            error_log('=== DEBUG INGREDIENTE ===');
            error_log('POST data: ' . print_r($_POST, true));
            error_log('Parsed data: ' . print_r($data, true));
            error_log('FILES: ' . print_r($_FILES, true));
            error_log('Campo type específico: ' . ($data['type'] ?? 'NÃO EXISTE'));

            // Mapear TODOS os campos corretamente
            $mappedData = [
                'name' => $data['name'] ?? '',
                'description' => $data['description'] ?? '',
                'type' => $data['type'] ?? '',  // FORÇAR ESTE CAMPO
                'price' => $data['price'] ?? 0,
                'additional_price' => $data['price'] ?? 0,
                'active' => isset($data['is_active']) ? 1 : 0,
                'is_active' => isset($data['is_active']) ? 1 : 0,
                'is_free' => isset($data['is_free']) ? 1 : 0,
                'max_quantity' => $data['max_quantity'] ?? 5,
                'sort_order' => $data['sort_order'] ?? 0,
                'image' => $data['image'] ?? null,
                'image_url' => $data['image_url'] ?? null
            ];
            
            // Se é gratuito, força preço = 0
            if ($mappedData['is_free']) {
                $mappedData['price'] = 0;
                $mappedData['additional_price'] = 0;
            }

            error_log('Dados mapeados finais: ' . print_r($mappedData, true));
            error_log('Vai atualizar ingrediente ID: ' . $ingredientId);
            
            // Executar update e capturar resultado
            $result = $this->ingredientModel->updateById($ingredientId, $mappedData);
            error_log('Resultado do update: ' . print_r($result, true));
            
            // Verificar se salvou mesmo
            $updated = $this->ingredientModel->getById($ingredientId);
            error_log('Ingrediente após update: ' . print_r($updated, true));
            
            return $response
                ->withHeader('Location', '/admin/ingredients')
                ->withStatus(302);
        } catch (\Exception $e) {
            error_log('ERRO ao atualizar ingrediente: ' . $e->getMessage());
            error_log('Stack trace: ' . $e->getTraceAsString());
            $ingredient = $this->ingredientModel->getById($ingredientId);
            
            $templateData = [
                'pageTitle' => 'Editar Ingrediente',
                'ingredient' => $ingredient,
                'error' => 'Erro ao atualizar ingrediente: ' . $e->getMessage(),
                'formData' => $data
            ];

            return $this->templateService->renderResponse($response, 'admin/ingredients/form', $templateData);
        }
    }

    /**
     * Excluir ingrediente
     */
    public function deleteIngredient(Request $request, Response $response, array $args): Response
    {
        $ingredientId = (int) $args['id'];
        $ingredient = $this->ingredientModel->getById($ingredientId);
        
        if (!$ingredient || $ingredient['user_id'] !== $_SESSION['user_id']) {
            return $response->withStatus(404);
        }
        
        try {
            // Fazer soft delete (marcar como inativo)
            $this->ingredientModel->deleteById($ingredientId);
            
            $_SESSION['success'] = 'Ingrediente excluído com sucesso!';
            return $response->withHeader('Location', '/admin/ingredients')->withStatus(302);
        } catch (\Exception $e) {
            error_log('Erro ao excluir ingrediente: ' . $e->getMessage());
            $_SESSION['error'] = 'Erro ao excluir ingrediente: ' . $e->getMessage();
            return $response->withHeader('Location', '/admin/ingredients')->withStatus(302);
        }
    }

    /**
     * Faz upload do logo da loja
     */
    public function uploadLogo(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();

        try {
            // Forçar uso do $_FILES para upload
            if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
                $logoUrl = $this->handleImageUpload($_FILES['logo'], 'stores');
                if ($logoUrl) {
                    $data['logo'] = $logoUrl;
                }
            }

            // Atualiza o usuário (loja) com o logo
            $userId = $_SESSION['user_id'];
            $mappedData = [
                'logo' => $data['logo'] ?? null
            ];

            $this->userModel->updateById($userId, $mappedData);

            return $response
                ->withHeader('Location', '/admin')
                ->withStatus(302);
        } catch (\Exception $e) {
            error_log('ERRO ao fazer upload do logo: ' . $e->getMessage());
            error_log('Stack trace: ' . $e->getTraceAsString());

            return $this->templateService->renderResponse($response, 'admin/dashboard', [
                'pageTitle' => 'Dashboard',
                'error' => 'Erro ao fazer upload do logo: ' . $e->getMessage()
            ]);
        }
    }

    // === PEDIDOS ===

    /**
     * Atualizar status do pedido
     */
    public function updateOrderStatus(Request $request, Response $response, array $args): Response
    {
        $orderId = (int) $args['id'];
        $data = $request->getParsedBody();
        $newStatus = $data['status'] ?? '';
        
        $order = $this->orderModel->getById($orderId);
        if (!$order || $order['user_id'] !== $_SESSION['user_id']) {
            return $response->withStatus(404);
        }

        $validStatuses = ['pending', 'confirmed', 'preparing', 'ready', 'delivered', 'cancelled'];
        if (!in_array($newStatus, $validStatuses)) {
            return $response->withStatus(400);
        }

        $this->orderModel->updateStatus($orderId, $newStatus);
        
        return $response->withHeader('Location', '/admin/pedidos')->withStatus(302);
    }

    /**
     * Formulário para criar pedido de mesa
     */
    public function newTableOrder(Request $request, Response $response): Response
    {
        $userId = $_SESSION['user_id'];
        $products = $this->productModel->getAll($userId);
        $categories = $this->categoryModel->getAll($userId);
        $ingredients = $this->ingredientModel->getAll($userId);

        // Agrupar ingredientes por tipo
        $ingredientsByType = [];
        foreach ($ingredients as $ingredient) {
            $ingredientsByType[$ingredient['type']][] = $ingredient;
        }

        $data = [
            'pageTitle' => 'Novo Pedido de Mesa',
            'products' => $products,
            'categories' => $categories,
            'ingredients' => $ingredientsByType
        ];

        return $this->templateService->renderResponse($response, 'admin/orders/table-form', $data);
    }

    /**
     * Criar pedido de mesa
     */
    public function createTableOrder(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        $userId = $_SESSION['user_id'];
        
        try {
            // Validar dados obrigatórios
            $required = ['table_number', 'customer_name', 'items'];
            foreach ($required as $field) {
                if (empty($data[$field])) {
                    $_SESSION['error'] = 'Todos os campos obrigatórios devem ser preenchidos';
                    return $response->withHeader('Location', '/admin/pedidos/novo')->withStatus(302);
                }
            }

            $items = json_decode($data['items'], true);
            if (empty($items)) {
                $_SESSION['error'] = 'Adicione pelo menos um item ao pedido';
                return $response->withHeader('Location', '/admin/pedidos/novo')->withStatus(302);
            }

            // Calcular total
            $totalAmount = 0;
            foreach ($items as $item) {
                $product = $this->productModel->getById($item['product_id']);
                if ($product && $product['user_id'] == $userId) {
                    $itemTotal = $product['price'] * $item['quantity'];
                    
                    // Adicionar preço dos ingredientes extras
                    if (!empty($item['ingredients'])) {
                        foreach ($item['ingredients'] as $ingredientId => $quantity) {
                            $ingredient = $this->ingredientModel->getById($ingredientId);
                            if ($ingredient && $ingredient['user_id'] == $userId) {
                                $itemTotal += ($ingredient['additional_price'] ?? 0) * $quantity;
                            }
                        }
                    }
                    
                    $totalAmount += $itemTotal;
                }
            }

            // Criar pedido
            $orderData = [
                'user_id' => $userId,
                'customer_phone' => $data['customer_phone'] ?? '',
                'customer_name' => $data['customer_name'],
                'customer_address' => $data['customer_address'] ?? '',
                'table_number' => $data['table_number'],
                'total_amount' => $totalAmount,
                'status' => 'pendente',
                'order_type' => 'mesa',
                'notes' => $data['notes'] ?? '',
                'created_at' => date('Y-m-d H:i:s')
            ];

            $orderId = $this->orderModel->create($orderData);

            // Criar itens do pedido
            foreach ($items as $item) {
                $product = $this->productModel->getById($item['product_id']);
                if ($product && $product['user_id'] == $userId) {
                    $itemData = [
                        'order_id' => $orderId,
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'unit_price' => $product['price'],
                        'notes' => $item['notes'] ?? ''
                    ];
                    
                    $orderItemId = $this->insert('order_items', $itemData);
                    
                    // Adicionar ingredientes se houver
                    if (!empty($item['ingredients']) && $orderItemId) {
                        foreach ($item['ingredients'] as $ingredientId => $quantity) {
                            $ingredient = $this->ingredientModel->getById($ingredientId);
                            if ($ingredient && $ingredient['user_id'] == $userId && $quantity > 0) {
                                $this->insert('order_item_ingredients', [
                                    'order_item_id' => $orderItemId,
                                    'ingredient_id' => $ingredientId,
                                    'quantity' => $quantity,
                                    'price' => $ingredient['additional_price'] ?? 0
                                ]);
                            }
                        }
                    }
                }
            }

            $_SESSION['success'] = 'Pedido de mesa criado com sucesso!';
            return $response->withHeader('Location', '/admin/pedidos/' . $orderId)->withStatus(302);

        } catch (\Exception $e) {
            error_log('Erro ao criar pedido de mesa: ' . $e->getMessage());
            $_SESSION['error'] = 'Erro ao criar pedido. Tente novamente.';
            return $response->withHeader('Location', '/admin/pedidos/novo')->withStatus(302);
        }
    }

    /**
     * Helper para inserir dados nas tabelas
     */
    private function insert(string $table, array $data): int
    {
        $fields = array_keys($data);
        $placeholders = array_fill(0, count($fields), '?');
        
        $sql = "INSERT INTO {$table} (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $placeholders) . ")";
        $stmt = $this->orderModel->getConnection()->prepare($sql);
        $stmt->executeStatement(array_values($data));
        
        return (int)$this->orderModel->getConnection()->lastInsertId();
    }

}
