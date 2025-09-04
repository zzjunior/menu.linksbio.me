<?php
namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class PrintController
{
    private \PDO $pdo;
    private $userModel;

    public function __construct(\PDO $pdo, $userModel)
    {
        $this->pdo = $pdo;
        $this->userModel = $userModel;
    }

    public function printOrder(Request $request, Response $response, array $args): Response
    {
        $pedidoId = $args['id'] ?? null;
        if (!$pedidoId) {
            $response->getBody()->write(json_encode(['error' => 'Pedido não encontrado']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }

        // Busca pedido no banco
        $stmt = $this->pdo->prepare("SELECT * FROM pedidos WHERE id = ?");
        $stmt->execute([$pedidoId]);
        $pedido = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$pedido) {
            $response->getBody()->write(json_encode(['error' => 'Pedido não encontrado']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }

        $store = $this->userModel->getStoreById($pedido['store_id']);
        $cart = json_decode($pedido['cart'], true);

        // Monta array para impressão
        $printData = [
            ["type"=>0,"content"=>strtoupper($store['store_name']),"bold"=>1,"align"=>1,"format"=>2],
            ["type"=>0,"content"=>"PEDIDO #".$pedido['id'],"bold"=>1,"align"=>1],
            ["type"=>0,"content"=>"Cliente: ".$pedido['customer_name'],"bold"=>0,"align"=>0],
            ["type"=>0,"content"=>"Endereço: ".$pedido['customer_address'],"bold"=>0,"align"=>0],
            ["type"=>0,"content"=>"------------------------------","bold"=>0,"align"=>1]
        ];

        foreach ($cart as $item) {
            $linha = $item['quantity']."x ".$item['product_name']." - R$ ".number_format($item['unit_price'],2,',','.');
            $printData[] = ["type"=>0,"content"=>$linha,"bold"=>0,"align"=>0];
            if (!empty($item['ingredients'])) {
                foreach ($item['ingredients'] as $ing) {
                    $printData[] = ["type"=>0,"content"=>"  + ".$ing['name']." (".$ing['quantity']."x)","bold"=>0,"align"=>0];
                }
            }
        }

        $printData[] = ["type"=>0,"content"=>"------------------------------","bold"=>0,"align"=>1];
        $printData[] = ["type"=>0,"content"=>"TOTAL: R$ ".number_format($pedido['total_amount'],2,',','.'),"bold"=>1,"align"=>2];
        $printData[] = ["type"=>0,"content"=>" ","bold"=>0,"align"=>0];
        // Adiciona QR Code PIX no final
        $pixQrCode = '00020126460014br.gov.bcb.pix0124fortalecai2025@gmail.com5204000053039865802BR5925Alefe Augusto Lima Da Sil6014RIO DE JANEIRO622805242bf17e522526e2838a7b30ac6304993A';
        $printData[] = ["type"=>3,"value"=>$pixQrCode,"size"=>30,"align"=>1];

        $response->getBody()->write(json_encode($printData, JSON_UNESCAPED_UNICODE));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }
}