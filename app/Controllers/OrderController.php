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
	 * Normalizar telefone removendo caracteres especiais
	 */
	private function normalizePhone($phone)
	{
		// Remove todos os caracteres não numéricos
		return preg_replace('/[^0-9]/', '', $phone);
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
		$status = $queryParams['status'] ?? null;
		$userId = $queryParams['user_id'] ?? null;
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

		// Verificar se o cliente já forneceu o telefone
		$customerPhone = $_SESSION['customer_phone_' . $store['store_id']] ?? null;
		$customerPhoneNormalized = $_SESSION['customer_phone_normalized_' . $store['store_id']] ?? null;
		
		if (!$customerPhone || !$customerPhoneNormalized) {
			// Mostrar formulário para inserir telefone
			$data = [
				'title' => 'Acompanhar Pedido - ' . $store['store_name'],
				'store' => $store,
				'store_slug' => $storeSlug,
				'show_phone_form' => true
			];
			
			if (!$this->templateService) {
				$this->templateService = new TemplateService();
			}
			
			return $this->templateService->renderResponse($response, 'cart/track', $data);
		}

		// Buscar apenas pedidos do cliente usando o telefone normalizado
		$ordersData = $this->orderModel->getByStoreIdAndPhoneNormalized($store['store_id'], $customerPhoneNormalized);
		
		// Buscar dados do cliente do primeiro pedido (se existir)
		$customerData = null;
		if (!empty($ordersData)) {
			$firstOrder = reset($ordersData);
			$customerData = [
				'name' => $firstOrder['customer_name'],
				'phone' => $firstOrder['customer_phone'],
				'address' => $firstOrder['customer_address'] ?? null,
				'total_orders' => count($ordersData)
			];
		}
		
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
			'orders' => $orders,
			'customer_phone' => $customerPhone,
			'customer_data' => $customerData,
			'show_phone_form' => false
		];

		if (!$this->templateService) {
			$this->templateService = new TemplateService();
		}

		return $this->templateService->renderResponse($response, 'cart/track', $data);
	}

	/**
	 * Processar telefone do cliente para acompanhar pedidos
	 */
	public function validateCustomerPhone(Request $request, Response $response, array $args): Response
	{
		$storeSlug = $args['store'] ?? '';
		$requestData = $request->getParsedBody();
		$customerPhone = trim($requestData['customer_phone'] ?? '');
		
		if (empty($customerPhone)) {
			return $response->withHeader('Location', '/order/' . $storeSlug . '?error=phone_required')->withStatus(302);
		}
		
		// Normalizar telefone (remover caracteres especiais)
		$normalizedPhone = $this->normalizePhone($customerPhone);
		
		if (empty($normalizedPhone)) {
			return $response->withHeader('Location', '/order/' . $storeSlug . '?error=phone_invalid')->withStatus(302);
		}
		
		// Buscar a loja pelo slug
		$userModel = new \App\Models\User($this->orderModel->getConnection());
		$store = $userModel->getStoreBySlug($storeSlug);
		
		if (!$store) {
			return $response->withHeader('Location', '/order/' . $storeSlug . '?error=store_not_found')->withStatus(302);
		}
		
		// Verificar se existe pelo menos um pedido com esse telefone nesta loja (normalizado)
		$hasOrders = $this->orderModel->hasOrdersByStoreAndPhoneNormalized($store['store_id'], $normalizedPhone);
		
		if (!$hasOrders) {
			return $response->withHeader('Location', '/order/' . $storeSlug . '?error=no_orders_found')->withStatus(302);
		}
		
		// Salvar telefone original na sessão específica da loja (manter formatação original)
		$_SESSION['customer_phone_' . $store['store_id']] = $customerPhone;
		$_SESSION['customer_phone_normalized_' . $store['store_id']] = $normalizedPhone;
		
		return $response->withHeader('Location', '/order/' . $storeSlug)->withStatus(302);
	}

	/**
	 * Limpar telefone do cliente (logout)
	 */
	public function clearCustomerPhone(Request $request, Response $response, array $args): Response
	{
		$storeSlug = $args['store'] ?? '';
		
		// Buscar a loja pelo slug
		$userModel = new \App\Models\User($this->orderModel->getConnection());
		$store = $userModel->getStoreBySlug($storeSlug);
		
		if ($store) {
			unset($_SESSION['customer_phone_' . $store['store_id']]);
			unset($_SESSION['customer_phone_normalized_' . $store['store_id']]);
		}
		
		return $response->withHeader('Location', '/order/' . $storeSlug)->withStatus(302);
	}

	/**
	 * Exibir formulário de confirmação para repetir pedido
	 */
	public function repeatOrderForm(Request $request, Response $response, array $args): Response
	{
		$storeSlug = $args['store'] ?? '';
		$orderId = (int)($args['id'] ?? 0);
		
		if (!$orderId) {
			$response->getBody()->write('Pedido não encontrado');
			return $response->withStatus(404);
		}

		// Buscar a loja pelo slug
		$userModel = new \App\Models\User($this->orderModel->getConnection());
		$store = $userModel->getStoreBySlug($storeSlug);
		
		if (!$store) {
			$response->getBody()->write('Loja não encontrada');
			return $response->withStatus(404);
		}

		// Buscar pedido completo
		$order = $this->orderModel->getOrderWithItems($orderId);
		
		if (!$order) {
			$response->getBody()->write('Pedido não encontrado');
			return $response->withStatus(404);
		}

		// Verificar se o pedido pertence a esta loja
		$orderUser = $userModel->getById($order['user_id']);
		if (!$orderUser || $orderUser['store_slug'] !== $storeSlug) {
			$response->getBody()->write('Pedido não pertence a esta loja');
			return $response->withStatus(403);
		}

		// Verificar se o cliente já forneceu o telefone para esta loja
		$customerPhone = $_SESSION['customer_phone_' . $store['store_id']] ?? null;
		$customerPhoneNormalized = $_SESSION['customer_phone_normalized_' . $store['store_id']] ?? null;
		
		if (!$customerPhone || !$customerPhoneNormalized) {
			// Redirecionar para página de acompanhamento para autenticar
			return $response->withHeader('Location', '/order/' . $storeSlug . '?error=phone_required&return_to=' . urlencode('/querodenovo/' . $storeSlug . '/' . $orderId))->withStatus(302);
		}

		// Verificar se o pedido pertence ao cliente autenticado (telefone normalizado)
		$orderPhoneNormalized = $this->normalizePhone($order['customer_phone']);
		if ($orderPhoneNormalized !== $customerPhoneNormalized) {
			$response->getBody()->write('Você não tem autorização para repetir este pedido');
			return $response->withStatus(403);
		}

		$data = [
			'title' => 'Repetir Pedido - ' . $store['store_name'],
			'store' => $store,
			'store_slug' => $storeSlug,
			'order' => $order
		];

		if (!$this->templateService) {
			$this->templateService = new TemplateService();
		}

		return $this->templateService->renderResponse($response, 'cart/repeat-order', $data);
	}

	/**
	 * Confirmar e executar repetição do pedido
	 */
	public function repeatOrderConfirm(Request $request, Response $response, array $args): Response
	{
		$storeSlug = $args['store'] ?? '';
		$orderId = (int)($args['id'] ?? 0);
		
		if (!$orderId) {
			$response->getBody()->write('Pedido não encontrado');
			return $response->withStatus(404);
		}

		// Buscar a loja pelo slug
		$userModel = new \App\Models\User($this->orderModel->getConnection());
		$store = $userModel->getStoreBySlug($storeSlug);
		
		if (!$store) {
			$response->getBody()->write('Loja não encontrada');
			return $response->withStatus(404);
		}

		// Verificar autenticação do cliente
		$customerPhoneNormalized = $_SESSION['customer_phone_normalized_' . $store['store_id']] ?? null;
		if (!$customerPhoneNormalized) {
			return $response->withHeader('Location', '/order/' . $storeSlug . '?error=phone_required')->withStatus(302);
		}

		// Buscar pedido completo
		$order = $this->orderModel->getOrderWithItems($orderId);
		
		if (!$order) {
			$response->getBody()->write('Pedido não encontrado');
			return $response->withStatus(404);
		}

		// Verificar se o pedido pertence ao cliente autenticado
		$orderPhoneNormalized = $this->normalizePhone($order['customer_phone']);
		if ($orderPhoneNormalized !== $customerPhoneNormalized) {
			$response->getBody()->write('Você não tem autorização para repetir este pedido');
			return $response->withStatus(403);
		}

		// Recriar carrinho com os itens do pedido (código original)
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
		$_SESSION['cart_' . $store['store_id']] = $cart;

		// Redirecionar para carrinho
		return $response->withHeader('Location', '/' . $storeSlug . '/carrinho?repeated=1')->withStatus(302);
	}

	/**
	 * Repetir pedido (Quero de Novo) - MÉTODO LEGADO - manter para compatibilidade
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
