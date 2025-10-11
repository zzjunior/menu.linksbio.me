<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- { Ativando print do pedido na impressora } -->
<script>
    // Limpa o carrinho do localStorage ao exibir a tela de sucesso
    document.addEventListener('DOMContentLoaded', function() {
        try {
            localStorage.removeItem('cart_{{ $store['id'] }}');
            if (typeof updateCartCount === 'function') updateCartCount();
        } catch (e) {}
    });
//window.onload = function() {
   // window.location.href = "my.bluetoothprint.scheme://https://menu.linksbio.me/imprimir-pedido/{{ $order['id'] }}";
//}; // ignora o antigo modelo de impressão
</script>
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="container mx-auto px-4 py-6">
        <div class="max-w-lg mx-auto">
            <!-- Sucesso -->
            <div class="bg-white rounded-lg shadow-lg p-6 text-center mb-6">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-check text-2xl text-green-600"></i>
                </div>
                
                <h1 class="text-2xl font-bold text-gray-800 mb-2">Pedido Enviado!</h1>
                <p class="text-gray-600 mb-4">
                    Seu pedido foi enviado com sucesso. Clique no botão abaixo para 
                    continuar no WhatsApp e finalizar com a loja.
                </p>
                
                <div class="bg-gray-50 rounded-lg p-4 mb-6">
                    <div class="text-sm text-gray-600 mb-2">
                        <strong>Pedido #{{ $order['id'] }}</strong>
                    </div>
                    <div class="text-sm text-gray-600 mb-2">
                        <strong>Cliente:</strong> {{ $order['customer_name'] }}
                    </div>
                    <div class="text-sm text-gray-600 mb-2">
                        <strong>Total:</strong> R$ {{ number_format($order['total_amount'], 2, ',', '.') }}
                    </div>
                    <div class="text-sm text-gray-600">
                        <strong>Data:</strong> {{ \Carbon\Carbon::parse($order['created_at'])->format('d/m/Y H:i') }}
                    </div>
                </div>

                <a 
                    href="{{ $whatsapp_url }}" 
                    target="_blank"
                    class="inline-block bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition duration-200 mb-4"
                >
                    <i class="fab fa-whatsapp mr-2"></i>
                    Continuar no WhatsApp
                </a>

                <div class="text-center">
                    <a href="/{{ $store_slug }}" class="text-purple-600 hover:text-purple-800">
                        <i class="fas fa-arrow-left mr-1"></i>
                        Voltar ao cardápio
                    </a>
                </div>
            </div>

            <!-- Resumo do pedido -->
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-receipt text-purple-600 mr-2"></i>
                    Resumo do pedido
                </h2>

                <div class="space-y-3">
                    @foreach ($order['items'] as $item)
                        <div class="flex justify-between items-start py-2 border-b border-gray-100 last:border-b-0">
                            <div class="flex-1">
                                <div class="font-medium text-gray-800">
                                    {{ $item['quantity'] }}x {{ $item['product_name'] }}
                                </div>
                                
                                @if (!empty($item['size']))
                                    <div class="text-sm text-gray-600">
                                        Tamanho: {{ $item['size'] }}
                                    </div>
                                @endif

                                @if (!empty($item['ingredients']))
                                    <div class="text-sm text-gray-600">
                                        Adicionais: 
                                        @php
                                            $ingredientNames = collect($item['ingredients'])->map(function($ing) {
                                                $name = $ing['name'];
                                                if ($ing['quantity'] > 1) {
                                                    $name .= " ({$ing['quantity']}x)";
                                                }
                                                return $name;
                                            })->toArray();
                                        @endphp
                                        {{ implode(', ', $ingredientNames) }}
                                    </div>
                                @endif

                                @if (!empty($item['notes']))
                                    <div class="text-sm text-gray-600">
                                        Obs: {{ $item['notes'] }}
                                    </div>
                                @endif
                            </div>
                            
                            <div class="text-right ml-4">
                                <div class="font-medium text-gray-800">
                                    R$ {{ number_format($item['unit_price'] * $item['quantity'], 2, ',', '.') }}
                                </div>
                                @if (!empty($item['ingredients']))
                                    @php
                                        $additionalTotal = 0;
                                        foreach ($item['ingredients'] as $ingredient) {
                                            $additionalTotal += $ingredient['price'] * $ingredient['quantity'] * $item['quantity'];
                                        }
                                    @endphp
                                    @if ($additionalTotal > 0)
                                        <div class="text-sm text-green-600">
                                            +R$ {{ number_format($additionalTotal, 2, ',', '.') }}
                                        </div>
                                    @endif
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="border-t pt-4 mt-4">
                    <div class="flex justify-between items-center">
                        <span class="text-lg font-semibold text-gray-800">Total</span>
                        <span class="text-xl font-bold text-purple-600">
                            R$ {{ number_format($order['total_amount'], 2, ',', '.') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Auto-abrir WhatsApp em 3 segundos
        setTimeout(function() {
            if (confirm('Deseja abrir o WhatsApp automaticamente?')) {
                window.open('{{ $whatsapp_url }}', '_blank');
            }
        }, 3000);
    </script>
</body>
</html>
