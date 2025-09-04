<?php
namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class PrintController
{
    private \PDO $pdo;
    private $userModel;
    private $logger;

    public function __construct(\PDO $pdo, $userModel, $logger)
    {
        $this->pdo = $pdo;
        $this->userModel = $userModel;
        $this->logger = $logger;
    }

public function printOrder(Request $request, Response $response, array $args): Response
{

    $orderId = $args['id'] ?? null;
    if (!$orderId) {
        $response->getBody()->write(json_encode(['error' => 'Pedido não encontrado']));
        $this->logger->error('Erro ao imprimir', ['order_id' => $orderId]);
        return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
    }

    // Busca pedido na tabela orders
    $stmt = $this->pdo->prepare("SELECT * FROM orders WHERE id = ?");
    $stmt->execute([$orderId]);
    $order = $stmt->fetch(\PDO::FETCH_ASSOC);

    if (!$order) {
        $response->getBody()->write(json_encode(['error' => 'Pedido não encontrado']));
        $this->logger->error('Erro ao imprimir *Pedido não encontrado*', ['order_id' => $orderId]);
        return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
    }

    $store = $this->userModel->getById($order['user_id']);

    // Busca itens do pedido
    $stmt = $this->pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
    $stmt->execute([$orderId]);
    $items = $stmt->fetchAll(\PDO::FETCH_ASSOC);

    // Monta array para impressão
    $printData = [
        ["type"=>0,"content"=>strtoupper($store['store_name']),"bold"=>1,"align"=>1,"format"=>2],
        ["type"=>0,"content"=>"PEDIDO #".$order['id'],"bold"=>1,"align"=>1],
        ["type"=>0,"content"=>"Cliente: ".$order['customer_name'],"bold"=>0,"align"=>0],
        ["type"=>0,"content"=>"Endereço: ".$order['customer_address'],"bold"=>0,"align"=>0],
        ["type"=>0,"content"=>"------------------------------","bold"=>0,"align"=>1]
    ];

    foreach ($items as $item) {
        // Busca nome do produto
        $stmtProd = $this->pdo->prepare("SELECT name FROM products WHERE id = ?");
        $stmtProd->execute([$item['product_id']]);
        $product = $stmtProd->fetch(\PDO::FETCH_ASSOC);

        $linha = $item['quantity']."x ".$product['name']." - R$ ".number_format($item['unit_price'],2,',','.');
        $printData[] = ["type"=>0,"content"=>$linha,"bold"=>0,"align"=>0];

        // Busca ingredientes/adicionais do item
        $stmtIng = $this->pdo->prepare("SELECT oi.*, i.name FROM order_item_ingredients oi JOIN ingredients i ON oi.ingredient_id = i.id WHERE oi.order_item_id = ?");
        $stmtIng->execute([$item['id']]);
        $ingredients = $stmtIng->fetchAll(\PDO::FETCH_ASSOC);

        if (!empty($ingredients)) {
            foreach ($ingredients as $ing) {
                $printData[] = ["type"=>0,"content"=>"  + ".$ing['name']." (".$ing['quantity']."x)","bold"=>0,"align"=>0];
            }
        }
    }

    $printData[] = ["type"=>0,"content"=>"------------------------------","bold"=>0,"align"=>1];
    $printData[] = ["type"=>0,"content"=>"TOTAL: R$ ".number_format($order['total_amount'],2,',','.'),"bold"=>1,"align"=>2];
    $printData[] = ["type"=>0,"content"=>" ","bold"=>0,"align"=>0];
    // Adiciona QR Code PIX no final
    $pixQrCode = '00020126460014br.gov.bcb.pix0124fortalecai2025@gmail.com5204000053039865802BR5925Alefe Augusto Lima Da Sil6014RIO DE JANEIRO622805242bf17e522526e2838a7b30ac6304993A';
    $printData[] = ["type"=>3,"value"=>$pixQrCode,"size"=>30,"align"=>1];

    $response->getBody()->write(json_encode($printData, JSON_UNESCAPED_UNICODE));

    $this->logger->info('Pedido enviado para impressão', ['order_id' => $orderId]);
    return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
}
}