<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Ingredient;
use App\Models\User;
use App\Services\TemplateService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Controlador para o cardápio público
 */
class MenuController
{
    private Product $productModel;
    private Category $categoryModel;
    private Ingredient $ingredientModel;
    private User $userModel;
    private TemplateService $templateService;

    public function __construct(
        Product $productModel,
        Category $categoryModel,
        Ingredient $ingredientModel,
        User $userModel,
        TemplateService $templateService
    ) {
        $this->productModel = $productModel;
        $this->categoryModel = $categoryModel;
        $this->ingredientModel = $ingredientModel;
        $this->userModel = $userModel;
        $this->templateService = $templateService;
    }

    /**
     * Exibe o cardápio da loja
     */
    public function index(Request $request, Response $response, array $args): Response
    {
        $storeSlug = $args['store'] ?? '';
        $store = $this->userModel->getStoreBySlug($storeSlug);
        
        if (!$store) {
            return $response->withStatus(404);
        }

        $queryParams = $request->getQueryParams();
        $categoryFilter = $queryParams['category'] ?? null;

        // Busca categorias da loja
        $categories = $this->categoryModel->getAll($store['id']);

        // Busca produtos da loja
        if ($categoryFilter) {
            $products = $this->productModel->getByCategory((int) $categoryFilter, $store['id']);
        } else {
            $products = $this->productModel->getAll($store['id']);
        }

        // Busca ingredientes da loja para personalização
        $ingredients = $this->ingredientModel->getAll($store['id']);

        // Agrupar ingredientes por tipo
        $ingredientsByType = [];
        foreach ($ingredients as $ingredient) {
            $ingredientsByType[$ingredient['type']][] = $ingredient;
        }

        $data = [
            'store' => $store,
            'store_slug' => $storeSlug,
            'categories' => $categories,
            'products' => $products,
            'ingredients' => $ingredientsByType,
            'currentCategory' => $categoryFilter,
            'pageTitle' => $store['store_name']
        ];

        return $this->templateService->renderResponse($response, 'menu/index', $data);
    }

    /**
     * API para buscar detalhes de um produto (AJAX)
     */
    public function getProduct(Request $request, Response $response, array $args): Response
    {
        $storeSlug = $args['store'] ?? '';
        $productId = (int) $args['id'];
        
        $store = $this->userModel->getStoreBySlug($storeSlug);
        if (!$store) {
            return $response->withStatus(404);
        }
        
        $product = $this->productModel->getById($productId);

        if (!$product || $product['user_id'] != $store['id']) {
            $response->getBody()->write(json_encode(['error' => 'Produto não encontrado']));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(404);
        }

        // Buscar ingredientes disponíveis da loja
        $ingredients = $this->ingredientModel->getAll($store['id']);
        $ingredientsByType = [];
        foreach ($ingredients as $ingredient) {
            $ingredientsByType[$ingredient['type']][] = $ingredient;
        }
        $product['ingredients'] = $ingredientsByType;

        $response->getBody()->write(json_encode($product));
        return $response->withHeader('Content-Type', 'application/json');
    }

    /**
     * Calcula o preço total do produto com ingredientes
     */
    public function calculatePrice(Request $request, Response $response, array $args): Response
    {
        $storeSlug = $args['store'] ?? '';
        $store = $this->userModel->getStoreBySlug($storeSlug);
        
        if (!$store) {
            return $response->withStatus(404);
        }
        
        $data = json_decode($request->getBody()->getContents(), true);
        
        $productId = $data['product_id'] ?? 0;
        $ingredientIds = $data['ingredients'] ?? [];
        $size = $data['size'] ?? '';

        $product = $this->productModel->getById($productId);
        if (!$product || $product['user_id'] != $store['id']) {
            $response->getBody()->write(json_encode(['error' => 'Produto não encontrado']));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(404);
        }

        $totalPrice = (float) $product['price'];

        // Se tem tamanhos definidos, usar preço do tamanho
        if ($size && isset($product['sizes'])) {
            $sizes = json_decode($product['sizes'], true);
            if (isset($sizes[$size])) {
                $totalPrice = $sizes[$size]['price'];
            }
        }

        // Adiciona preço dos ingredientes extras
        if (!empty($ingredientIds)) {
            foreach ($ingredientIds as $ingredientId => $quantity) {
                $ingredient = $this->ingredientModel->getById($ingredientId);
                if ($ingredient && $ingredient['user_id'] == $store['id'] && $ingredient['additional_price'] > 0) {
                    $totalPrice += (float) $ingredient['additional_price'] * (int) $quantity;
                }
            }
        }

        $response->getBody()->write(json_encode([
            'total_price' => $totalPrice,
            'formatted_price' => 'R$ ' . number_format($totalPrice, 2, ',', '.')
        ]));
        
        return $response->withHeader('Content-Type', 'application/json');
    }
}
