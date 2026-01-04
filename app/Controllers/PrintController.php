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

    // Endpoint para QZ Tray com comandos ESC/POS
    public function printOrder(Request $request, Response $response, array $args): Response
    {
        $orderId = $args['id'] ?? null;
        if (!$orderId) {
            $response->getBody()->write("Pedido não encontrado");
            return $response->withStatus(404);
        }

        $orderData = $this->getOrderData($orderId);
        if (!$orderData) {
            $response->getBody()->write("Pedido não encontrado");
            return $response->withStatus(404);
        }

        // Gerar string com comandos ESC/POS para impressão direta
        $printString = $this->generateESCPOSString($orderData);
        
        $response->getBody()->write($printString);
        return $response
            ->withHeader('Content-Type', 'text/plain; charset=utf-8')
            ->withHeader('Content-Disposition', 'inline; filename="pedido_' . $orderId . '.txt"');
    }

    // Endpoint para QZ Tray com comandos ESC/POS
    public function printDataJson(Request $request, Response $response, array $args): Response
    {
        $orderId = $args['id'] ?? null;
        if (!$orderId) {
            $response->getBody()->write(json_encode(['error' => 'Pedido não encontrado']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }

        $orderData = $this->getOrderData($orderId);
        if (!$orderData) {
            $response->getBody()->write(json_encode(['error' => 'Pedido não encontrado']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }

        // Gerar string com comandos ESC/POS para formatação correta
        $printString = $this->generateESCPOSString($orderData);
        
        $response->getBody()->write(json_encode(['printData' => $printString], JSON_UNESCAPED_UNICODE));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    // Endpoint para gerar PDF térmico
    public function printOrderPDF(Request $request, Response $response, array $args): Response
    {
        $orderId = $args['id'] ?? null;
        if (!$orderId) {
            $response->getBody()->write('<h1>Pedido não encontrado</h1>');
            return $response->withStatus(404)->withHeader('Content-Type', 'text/html');
        }

        $orderData = $this->getOrderData($orderId);
        if (!$orderData) {
            $response->getBody()->write('<h1>Pedido não encontrado</h1>');
            return $response->withStatus(404)->withHeader('Content-Type', 'text/html');
        }

        $html = $this->generateThermalHTML($orderData);
        
        $response->getBody()->write($html);
        return $response->withHeader('Content-Type', 'text/html')->withStatus(200);
    }

    private function generateESCPOSString($orderData)
    {
        $order = $orderData['order'];
        $store = $orderData['store'];
        $items = $orderData['items'];
        
        $output = "";
        
        // Comandos ESC/POS para formatação
        $output .= "\x1B\x40"; // Inicializar impressora
        $output .= "\x1B\x61\x01"; // Centralizar
        $output .= "\x1B\x21\x08"; // Fonte dupla altura
        $output .= strtoupper($store['store_name']) . "\n";
        $output .= "\x1B\x21\x00"; // Fonte normal
        $output .= "\x1B\x61\x00"; // Alinhar à esquerda
        $output .= "\n";
        if (!empty($store['store_phone'])) {
            $output .= "Tel: " . $store['store_phone'] . "\n";
        }
        if (!empty($store['store_email'])) {
            $output .= $store['store_email'] . "\n";
        }
        $output .= "\n";
        $output .= str_repeat("=", 32) . "\n";
        $output .= "\n";
        
        // Informações do pedido
        $output .= "\x1B\x61\x01"; // Centralizar
        $output .= "PEDIDO #" . $order['id'] . "\n";
        $output .= date('d/m/Y H:i', strtotime($order['created_at'])) . "\n";
        $output .= "\x1B\x61\x00"; // Alinhar à esquerda
        $output .= "\n";
        
        // Dados do cliente
        $output .= "CLIENTE:\n";
        $output .= $order['customer_name'] . "\n";
        $output .= "Tel: " . $order['customer_phone'] . "\n";
        $output .= "\n";
        $output .= "ENDERECO:\n";
        $output .= $order['customer_address'] . "\n";
        $output .= "\n";
        $output .= str_repeat("-", 32) . "\n";
        $output .= "ITENS:\n";
        $output .= "\n";
        
        // Itens
        foreach ($items as $item) {
            $output .= $item['quantity'] . "x " . $item['product_name'] . "\n";
            $output .= "\x1B\x61\x02"; // Alinhar à direita
            $output .= "R$ " . number_format($item['unit_price'], 2, ',', '.') . "\n";
            $output .= "\x1B\x61\x00"; // Alinhar à esquerda
            
            if (!empty($item['ingredients'])) {
                foreach ($item['ingredients'] as $ing) {
                    $output .= "  +" . $ing['name'] . "\n";
                }
            }
            $output .= "\n";
        }
        
        // Total
        $output .= str_repeat("-", 32) . "\n";
        $output .= "\x1B\x61\x01"; // Centralizar
        $output .= "\x1B\x21\x08"; // Fonte dupla altura
        $output .= "TOTAL: R$ " . number_format($order['total_amount'], 2, ',', '.') . "\n";
        $output .= "\x1B\x21\x00"; // Fonte normal
        $output .= "\x1B\x61\x00"; // Alinhar à esquerda
        $output .= "\n\n";
        
        // PIX
        $output .= str_repeat("=", 32) . "\n";
        $output .= "\x1B\x61\x01"; // Centralizar
        $output .= "PAGUE VIA PIX\n";
        $output .= "\x1B\x61\x00"; // Alinhar à esquerda
        $output .= "\n";
        if (!empty($store['pix_key'])) {
            $output .= "Chave: " . $store['pix_key'] . "\n";
        }
        $output .= "\n\n";
        $output .= "\x1B\x61\x01"; // Centralizar
        $output .= "Obrigado!\n";
        $output .= "\x1B\x61\x00"; // Alinhar à esquerda
        
        // Cortar papel
        $output .= "\n\n\n";
        $output .= "\x1D\x56\x42\x00"; // Comando de corte
        
        return $output;
    }

    private function generateThermalHTML($orderData)
    {
        $order = $orderData['order'];
        $store = $orderData['store'];
        $items = $orderData['items'];
        
        $html = '<!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Pedido #' . $order['id'] . '</title>
            <style>
                @page { 
                    size: 58mm auto; 
                    margin: 0; 
                    padding: 2mm;
                }
                body { 
                    font-family: "Courier New", monospace; 
                    font-size: 9px; 
                    margin: 0; 
                    padding: 0; 
                    width: 54mm;
                    line-height: 1.2;
                }
                .center { text-align: center; }
                .right { text-align: right; }
                .bold { font-weight: bold; }
                .large { font-size: 11px; }
                .item { margin: 3px 0; }
                .separator { border-top: 1px dashed #000; margin: 5px 0; }
                .total { font-size: 12px; font-weight: bold; }
                @media print {
                    body { -webkit-print-color-adjust: exact; }
                }
            </style>
            <script>
                window.onload = function() {
                    window.print();
                }
            </script>
        </head>
        <body>
            <div class="center bold large">' . strtoupper($store['store_name']) . '</div>
            <br>';
        
        if (!empty($store['store_phone'])) {
            $html .= '<div class="center">Tel: ' . htmlspecialchars($store['store_phone']) . '</div>';
        }
        if (!empty($store['store_email'])) {
            $html .= '<div class="center">' . htmlspecialchars($store['store_email']) . '</div>';
        }
        
        $html .= '
            <br>
            <div class="center">================================</div>
            <br>
            
            <div class="center bold">PEDIDO #' . $order['id'] . '</div>
            <div class="center">' . date('d/m/Y H:i', strtotime($order['created_at'])) . '</div>
            <br>
            
            <div class="bold">CLIENTE:</div>
            <div>' . $order['customer_name'] . '</div>
            <div>Tel: ' . $order['customer_phone'] . '</div>
            <br>
            
            <div class="bold">ENDERECO:</div>
            <div>' . $order['customer_address'] . '</div>
            <br>
            
            <div class="separator"></div>
            <div class="center bold">ITENS</div>
            <br>';
        
        foreach ($items as $item) {
            $html .= '<div class="item">
                <div>' . $item['quantity'] . 'x ' . $item['product_name'] . '</div>
                <div class="right">R$ ' . number_format($item['unit_price'], 2, ',', '.') . '</div>';
            
            if (!empty($item['ingredients'])) {
                foreach ($item['ingredients'] as $ing) {
                    $html .= '<div>  +' . $ing['name'] . '</div>';
                }
            }
            
            $html .= '</div>';
        }
        
        $html .= '<br>
            <div class="separator"></div>
            <br>
            <div class="center total">TOTAL: R$ ' . number_format($order['total_amount'], 2, ',', '.') . '</div>
            <br><br>
            
            <div class="center">================================</div>
            <br>
            <div class="center bold">PAGUE VIA PIX</div>
            <br>';
        
        if (!empty($store['pix_key'])) {
            $html .= '<div class="center">Chave: ' . htmlspecialchars($store['pix_key']) . '</div>';
        }
        
        $html .= '<br><br>
            <div class="center bold">Obrigado!</div>
            <br><br><br>
        </body>
        </html>';
        
        return $html;
    }

    private function getOrderData($orderId)
    {
        // Busca pedido
        $stmt = $this->pdo->prepare("SELECT * FROM orders WHERE id = ?");
        $stmt->execute([$orderId]);
        $order = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$order) {
            return null;
        }

        // Buscar dados da store (não do user)
        $stmtStore = $this->pdo->prepare("
            SELECT u.id as user_id, u.name, u.email, u.store_slug,
                   s.id as store_id, s.store_name, s.store_phone, s.store_email, 
                   s.store_address, s.store_logo, s.pix_key
            FROM users u
            LEFT JOIN stores s ON u.store_id = s.id
            WHERE u.id = ?
        ");
        $stmtStore->execute([$order['user_id']]);
        $store = $stmtStore->fetch(\PDO::FETCH_ASSOC);

        // Busca itens do pedido
        $stmt = $this->pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
        $stmt->execute([$orderId]);
        $items = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Processa itens com produtos e ingredientes
        $processedItems = [];
        foreach ($items as $item) {
            $stmtProd = $this->pdo->prepare("SELECT name FROM products WHERE id = ?");
            $stmtProd->execute([$item['product_id']]);
            $product = $stmtProd->fetch(\PDO::FETCH_ASSOC);

            $stmtIng = $this->pdo->prepare("SELECT oi.*, i.name FROM order_item_ingredients oi JOIN ingredients i ON oi.ingredient_id = i.id WHERE oi.order_item_id = ?");
            $stmtIng->execute([$item['id']]);
            $ingredients = $stmtIng->fetchAll(\PDO::FETCH_ASSOC);

            $processedItems[] = [
                'quantity' => $item['quantity'],
                'product_name' => $product['name'],
                'unit_price' => $item['unit_price'],
                'ingredients' => $ingredients
            ];
        }

        return [
            'order' => $order,
            'store' => $store,
            'items' => $processedItems
        ];
    }
}