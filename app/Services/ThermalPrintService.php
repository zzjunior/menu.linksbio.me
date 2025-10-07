<?php

namespace App\Services;

use TCPDF;

class ThermalPrintService
{
    private $width = 58; // 58mm
    private $dpi = 203; // DPI típico para impressoras térmicas
    
    public function generateThermalPDF($orderData)
    {
        // Criar PDF específico para impressora térmica 58mm
        $pdf = new TCPDF('P', 'mm', array(58, 200), true, 'UTF-8', false);
        
        // Configurações para impressora térmica
        $pdf->SetCreator('Sistema de Pedidos');
        $pdf->SetTitle('Pedido #' . $orderData['order']['id']);
        $pdf->SetMargins(2, 2, 2); // Margens mínimas
        $pdf->SetAutoPageBreak(TRUE, 2);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        
        $pdf->AddPage();
        
        // Fonte para impressora térmica
        $pdf->SetFont('courier', '', 8);
        
        $html = $this->generateThermalHTML($orderData);
        
        $pdf->writeHTML($html, true, false, true, false, '');
        
        return $pdf->Output('pedido_' . $orderData['order']['id'] . '.pdf', 'S');
    }
    
    public function generateQZTrayData($orderData)
    {
        $order = $orderData['order'];
        $store = $orderData['store'];
        $items = $orderData['items'];
        
        // Formato correto para QZ Tray com comandos ESC/POS
        $commands = [
            // Cabeçalho centralizado
            "\x1B\x61\x01", // Centro
            strtoupper($store['store_name']) . "\n",
            "\x1B\x61\x00", // Esquerda
            "\n",
            "Tel: (85) 99999-9999\n",
            "@acaiteria\n",
            "\n",
            str_repeat("=", 32) . "\n",
            "\n",
            
            // Informações do pedido
            "\x1B\x61\x01", // Centro
            "PEDIDO #" . $order['id'] . "\n",
            date('d/m/Y H:i', strtotime($order['created_at'])) . "\n",
            "\x1B\x61\x00", // Esquerda
            "\n",
            
            // Dados do cliente
            "CLIENTE:\n",
            $order['customer_name'] . "\n",
            "Tel: " . $order['customer_phone'] . "\n",
            "\n",
            "ENDERECO:\n",
            $order['customer_address'] . "\n",
            "\n",
            str_repeat("-", 32) . "\n",
            "ITENS:\n",
            "\n"
        ];
        
        // Adicionar itens
        foreach ($items as $item) {
            $commands[] = $item['quantity'] . "x " . $item['product_name'] . "\n";
            $commands[] = "R$ " . number_format($item['unit_price'], 2, ',', '.') . "\n";
            
            if (!empty($item['ingredients'])) {
                foreach ($item['ingredients'] as $ing) {
                    $commands[] = "  +" . $ing['name'] . "\n";
                }
            }
            $commands[] = "\n";
        }
        
        // Total
        $commands[] = str_repeat("-", 32) . "\n";
        $commands[] = "\x1B\x61\x01"; // Centro
        $commands[] = "TOTAL: R$ " . number_format($order['total_amount'], 2, ',', '.') . "\n";
        $commands[] = "\x1B\x61\x00"; // Esquerda
        $commands[] = "\n\n";
        
        // PIX
        $commands[] = str_repeat("=", 32) . "\n";
        $commands[] = "\x1B\x61\x01"; // Centro
        $commands[] = "PAGUE VIA PIX\n";
        $commands[] = "\x1B\x61\x00"; // Esquerda
        $commands[] = "\n";
        $commands[] = "Chave: fortalecai2025@gmail.com\n";
        $commands[] = "\n\n";
        $commands[] = "\x1B\x61\x01"; // Centro
        $commands[] = "Obrigado!\n";
        $commands[] = "\x1B\x61\x00"; // Esquerda
        
        // Comando de corte do papel
        $commands[] = "\n\n\n";
        $commands[] = "\x1D\x56\x42\x00"; // Cortar papel
        
        return implode("", $commands);
    }
    
    private function generateThermalHTML($orderData)
    {
        $order = $orderData['order'];
        $store = $orderData['store'];
        $items = $orderData['items'];
        
        $html = '
        <style>
            body { font-family: "Courier New", monospace; font-size: 10px; margin: 0; padding: 0; }
            .center { text-align: center; }
            .bold { font-weight: bold; }
            .line { border-bottom: 1px solid #000; margin: 3px 0; }
            .item { margin: 5px 0; }
            .price { text-align: right; }
            .total { font-size: 12px; font-weight: bold; text-align: center; }
        </style>
        
        <div class="center bold">' . strtoupper($store['store_name']) . '</div>
        <br>
        <div class="center">Tel: (85) 99999-9999</div>
        <div class="center">@acaiteria</div>
        <br>
        <div class="center">' . str_repeat("=", 32) . '</div>
        <br>
        
        <div class="center bold">PEDIDO #' . $order['id'] . '</div>
        <div class="center">' . date('d/m/Y H:i', strtotime($order['created_at'])) . '</div>
        <br>
        
        <div><strong>CLIENTE:</strong></div>
        <div>' . $order['customer_name'] . '</div>
        <div>Tel: ' . $order['customer_phone'] . '</div>
        <br>
        
        <div><strong>ENDERECO:</strong></div>
        <div>' . $order['customer_address'] . '</div>
        <br>
        
        <div class="line"></div>
        <div class="center bold">ITENS</div>
        <br>';
        
        foreach ($items as $item) {
            $html .= '
            <div class="item">
                <div>' . $item['quantity'] . 'x ' . $item['product_name'] . '</div>
                <div class="price">R$ ' . number_format($item['unit_price'], 2, ',', '.') . '</div>';
            
            if (!empty($item['ingredients'])) {
                foreach ($item['ingredients'] as $ing) {
                    $html .= '<div>  +' . $ing['name'] . '</div>';
                }
            }
            
            $html .= '</div><br>';
        }
        
        $html .= '
        <div class="line"></div>
        <br>
        <div class="total">TOTAL: R$ ' . number_format($order['total_amount'], 2, ',', '.') . '</div>
        <br><br>
        
        <div class="center">' . str_repeat("=", 32) . '</div>
        <br>
        <div class="center bold">PAGUE VIA PIX</div>
        <br>
        <div class="center">Chave: fortalecai2025@gmail.com</div>
        <br><br>
        <div class="center bold">Obrigado!</div>
        <br><br><br>';
        
        return $html;
    }
}