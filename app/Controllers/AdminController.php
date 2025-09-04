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

        return $this->templateService->renderResponse($response, 'admin/dashboard', $data);
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
        
        $data = [
            'pageTitle' => 'Novo Produto',
            'categories' => $categories
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
            $this->productModel->create($data);
            
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

        $categories = $this->categoryModel->getAll($_SESSION['user_id']);
        
        $data = [
            'pageTitle' => 'Editar Produto',
            'product' => $product,
            'categories' => $categories
        ];

        $html = $this->templateService->render('admin/products/form', $data);
        $response->getBody()->write($html);
        
        return $response->withHeader('Content-Type', 'text/html');
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

}
