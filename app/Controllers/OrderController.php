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

		public function __construct(Order $orderModel, TemplateService $templateService = null)
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
	}
