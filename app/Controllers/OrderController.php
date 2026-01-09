<?php

declare(strict_types=1);

namespace App\Controllers;

	use App\Models\Order;
	use App\Services\TemplateService;
	use Psr\Http\Message\ResponseInterface as Response;
	use Psr\Http\Message\ServerRequestInterface as Request;

	class OrderController
	{
		protected $orderModel;
		protected $templateService;

		public function __construct(Order $orderModel, ?TemplateService $templateService = null)
		{
			$this->orderModel = $orderModel;
			$this->templateService = $templateService;
		}

		/**
		 * Lista todos os pedidos com paginação
		 */
		public function listOrders(Request $request, Response $response, array $args): Response
		{
			$queryParams = $request->getQueryParams();
			$page = (int)($queryParams['page'] ?? 1);
			$perPage = 20;
			$search = $queryParams['search'] ?? '';
			$status = $queryParams['status'] ?? '';
			
			// Filtrar apenas pedidos da loja atual
			$userId = $_SESSION['user_id'] ?? null;
			if (!$userId) {
				return $response->withHeader('Location', '/admin/login')->withStatus(302);
			}
			
			$orders = $this->orderModel->getAllOrdersPaginated($page, $perPage, $search, $status, $userId);
			$totalOrders = $this->orderModel->getTotalOrdersCount($search, $status, $userId);
			$totalPages = ceil($totalOrders / $perPage);
			
			$data = [
				'orders' => $orders,
				'currentPage' => $page,
				'totalPages' => $totalPages,
				'totalOrders' => $totalOrders,
				'search' => $search,
				'status' => $status,
				'perPage' => $perPage
			];

			$html = $this->templateService->render('admin.pedidos.list', $data);
			$response->getBody()->write($html);
			return $response;
		}

		/**
		 * Exibe detalhes completos de um pedido
		 */
		public function viewOrder(Request $request, Response $response, array $args): Response
		{
			$orderId = (int)$args['id'];
			$order = $this->orderModel->getOrderWithItems($orderId);
			
			if (!$order) {
				$response->getBody()->write('Pedido não encontrado');
				return $response->withStatus(404);
			}

			$data = ['order' => $order];

			$html = $this->templateService->render('admin.pedidos.view', $data);
			$response->getBody()->write($html);
			return $response;
		}

		/**
		 * Retorna o último ID de pedido criado
		 */
		public function getLastOrderId(Request $request, Response $response, $args = [])
		{
			$pdo = $this->orderModel->getConnection();
			$result = $pdo->executeQuery("SELECT MAX(id) as last_order_id FROM orders");
			$row = $result->fetchAssociative();
			$response->getBody()->write(json_encode(['last_order_id' => (int)($row['last_order_id'] ?? 0)]));
			return $response->withHeader('Content-Type', 'application/json');
		}

		public function getLastOrderByPhone(Request $request, Response $response, $args = [])
		{
			$phone = $request->getQueryParams()['phone'] ?? '';
			$pdo = $this->orderModel->getConnection();
			$stmt = $pdo->prepare("SELECT customer_name, customer_address, notes FROM orders WHERE customer_phone = ? ORDER BY id DESC LIMIT 1");
			$result = $stmt->executeQuery([$phone]);
			$row = $result->fetchAssociative();
			$response->getBody()->write(json_encode($row ?: []));
			return $response->withHeader('Content-Type', 'application/json');
		}

	/**
	 * Rastrear pedido (Track Order)
	 */
	public function trackOrder(Request $request, Response $response, array $args): Response
	{
		$storeSlug = $args['store'] ?? '';
		
		// Buscar a loja pelo slug
		$userModel = new \App\Models\User($this->orderModel->getConnection());
		$store = $userModel->getStoreBySlug($storeSlug);
		
		if (!$store) {
			$response->getBody()->write('Loja não encontrada');
			return $response->withStatus(404);
		}

		// Buscar pedidos da loja com itens detalhados (usando store_id)
		$ordersData = $this->orderModel->getByStoreId($store['store_id']);
		
		// Enriquecer cada pedido com seus itens
		$orders = [];
		foreach ($ordersData as $order) {
			$orderWithItems = $this->orderModel->getOrderWithItems($order['id']);
			if ($orderWithItems) {
				$orders[] = $orderWithItems;
			}
		}

		$data = [
			'title' => 'Acompanhar Pedido - ' . $store['store_name'],
			'store' => $store,
			'store_slug' => $storeSlug,
			'orders' => $orders
		];

		if (!$this->templateService) {
			$this->templateService = new TemplateService();
		}

		return $this->templateService->renderResponse($response, 'cart/track', $data);
	}

	/**
	 * Repetir pedido (Quero de Novo)
	 */
	public function repeatOrder(Request $request, Response $response, array $args): Response
	{
		$orderId = (int)($args['id'] ?? 0);
		
		if (!$orderId) {
			$response->getBody()->write('Pedido não encontrado');
			return $response->withStatus(404);
		}

		// Buscar pedido completo
		$order = $this->orderModel->getOrderWithItems($orderId);
		
		if (!$order) {
			$response->getBody()->write('Pedido não encontrado');
			return $response->withStatus(404);
		}

		// Buscar loja pelo user_id do pedido
		$userModel = new \App\Models\User($this->orderModel->getConnection());
		$user = $userModel->getById($order['user_id']);
		
		if (!$user || empty($user['store_slug'])) {
			$response->getBody()->write('Loja não encontrada');
			return $response->withStatus(404);
		}

		// Usar getStoreBySlug para pegar store_id
		$store = $userModel->getStoreBySlug($user['store_slug']);
		
		if (!$store || empty($store['store_id'])) {
			$response->getBody()->write('Loja não encontrada');
			return $response->withStatus(404);
		}

		$storeSlug = $user['store_slug'];
		$storeId = $store['store_id'];

		// Recriar carrinho com os itens do pedido
		$cart = [];
		foreach ($order['items'] as $item) {
			$ingredients = [];
			if (!empty($item['ingredients'])) {
				foreach ($item['ingredients'] as $ingredient) {
					$ingredients[$ingredient['ingredient_id']] = $ingredient['quantity'];
				}
			}

			$cart[] = [
				'cart_id' => uniqid(),
				'product_id' => $item['product_id'],
				'name' => $item['product_name'],
				'category_name' => $item['category_name'] ?? '',
				'quantity' => (int)$item['quantity'],
				'size' => $item['size'] ?? '',
				'price' => (float)$item['unit_price'],
				'notes' => $item['notes'] ?? '',
				'ingredients' => $ingredients
			];
		}

		// Salvar carrinho na sessão usando store_id
		$_SESSION['cart_' . $storeId] = $cart;

		// Redirecionar para carrinho
		return $response->withHeader('Location', '/' . $storeSlug . '/carrinho')->withStatus(302);
	}
}
