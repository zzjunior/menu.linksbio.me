<?php

declare(strict_types=1);

namespace App\Controllers;

	use App\Models\Order;
	use Psr\Http\Message\ResponseInterface as Response;
	use Psr\Http\Message\ServerRequestInterface as Request;

	class OrderController
	{
		protected $orderModel;

		public function __construct(Order $orderModel)
		{
			$this->orderModel = $orderModel;
		}

		/**
		 * Retorna o último ID de pedido criado
		 */
		public function getLastOrderId(Request $request, Response $response, $args = [])
		{
			$pdo = $this->orderModel->getConnection();
			$stmt = $pdo->query("SELECT MAX(id) as last_order_id FROM orders");
			$row = $stmt->fetch(\PDO::FETCH_ASSOC);
			$response->getBody()->write(json_encode(['last_order_id' => (int)($row['last_order_id'] ?? 0)]));
			return $response->withHeader('Content-Type', 'application/json');
		}

		public function getLastOrderByPhone(Request $request, Response $response, $args = [])
		{
			$phone = $request->getQueryParams()['phone'] ?? '';
			$pdo = $this->orderModel->getConnection();
			$stmt = $pdo->prepare("SELECT customer_name, customer_address, notes FROM orders WHERE customer_phone = ? ORDER BY id DESC LIMIT 1");
			$stmt->execute([$phone]);
			$row = $stmt->fetch(\PDO::FETCH_ASSOC);
			$response->getBody()->write(json_encode($row ?: []));
			return $response->withHeader('Content-Type', 'application/json');
		}
	}
