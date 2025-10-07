<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedido #{{ $order['id'] }} - Detalhes</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        @media print {
            .no-print { display: none !important; }
            body { font-size: 12px; }
        }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Header -->
    <header class="bg-white shadow no-print">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-4">
                    <button onclick="window.close()" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times"></i> Fechar
                    </button>
                    <h1 class="text-2xl font-bold text-gray-900">Pedido #{{ $order['id'] }}</h1>
                </div>
                <div class="flex items-center space-x-2">
                    <button onclick="window.print()" 
                            class="bg-green-600 text-white px-3 py-2 rounded hover:bg-green-700 flex items-center gap-2">
                        <i class="fas fa-print"></i> Imprimir
                    </button>
                    <a href="/admin/pedidos" 
                       class="bg-blue-600 text-white px-3 py-2 rounded hover:bg-blue-700 flex items-center gap-2">
                        <i class="fas fa-list"></i> Voltar à Lista
                    </a>
                </div>
            </div>
        </div>
    </header>

    <main class="max-w-4xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <!-- Status do Pedido -->
        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4 gap-4">
                <h2 class="text-xl font-semibold text-gray-900">Status do Pedido</h2>
                <div class="flex flex-col md:flex-row items-start md:items-center gap-4">
                    @php
                        $statusColors = [
                            'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                            'confirmed' => 'bg-indigo-100 text-indigo-800 border-indigo-200',
                            'preparing' => 'bg-blue-100 text-blue-800 border-blue-200',
                            'ready' => 'bg-green-100 text-green-800 border-green-200',
                            'delivered' => 'bg-gray-100 text-gray-800 border-gray-200',
                            'cancelled' => 'bg-red-100 text-red-800 border-red-200'
                        ];
                        $statusLabels = [
                            'pending' => 'Pendente',
                            'confirmed' => 'Confirmado',
                            'preparing' => 'Preparando',
                            'ready' => 'Pronto',
                            'delivered' => 'Entregue',
                            'cancelled' => 'Cancelado'
                        ];
                        $statusClass = $statusColors[$order['status']] ?? 'bg-gray-100 text-gray-800 border-gray-200';
                        $statusLabel = $statusLabels[$order['status']] ?? ucfirst($order['status']);
                    @endphp
                    <span class="inline-flex px-3 py-2 text-sm font-semibold rounded-full border {{ $statusClass }}">
                        {{ $statusLabel }}
                    </span>
                    
                    <!-- Dropdown para alterar status -->
                    <div class="no-print">
                        <form method="POST" action="/admin/pedidos/{{ $order['id'] }}/status" class="flex items-center gap-2">
                            <label for="status" class="text-sm font-medium text-gray-700">Alterar para:</label>
                            <select name="status" id="status" onchange="if(confirm('Tem certeza que deseja alterar o status do pedido?')) { this.form.submit(); }" 
                                    class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                                <option value="">Selecione...</option>
                                <option value="pending" {{ $order['status'] === 'pending' ? 'selected disabled' : '' }}>Pendente</option>
                                <option value="confirmed" {{ $order['status'] === 'confirmed' ? 'selected disabled' : '' }}>Confirmado</option>
                                <option value="preparing" {{ $order['status'] === 'preparing' ? 'selected disabled' : '' }}>Preparando</option>
                                <option value="ready" {{ $order['status'] === 'ready' ? 'selected disabled' : '' }}>Pronto</option>
                                <option value="delivered" {{ $order['status'] === 'delivered' ? 'selected disabled' : '' }}>Entregue</option>
                                <option value="cancelled" {{ $order['status'] === 'cancelled' ? 'selected disabled' : '' }}>Cancelado</option>
                            </select>
                        </form>
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                <div>
                    <span class="font-medium text-gray-700">Data do Pedido:</span>
                    <div class="text-gray-900">{{ date('d/m/Y H:i', strtotime($order['created_at'])) }}</div>
                </div>
                <div>
                    <span class="font-medium text-gray-700">Total:</span>
                    <div class="text-lg font-bold text-green-600">R$ {{ number_format($order['total_amount'], 2, ',', '.') }}</div>
                </div>
                <div>
                    <span class="font-medium text-gray-700">Quantidade de Itens:</span>
                    <div class="text-gray-900">{{ count($order['items']) }} item(s)</div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Informações do Cliente -->
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <i class="fas fa-user text-blue-600"></i>
                    Informações do Cliente
                </h2>
                <div class="space-y-3">
                    <div>
                        <span class="font-medium text-gray-700">Nome:</span>
                        <div class="text-gray-900">{{ $order['customer_name'] }}</div>
                    </div>
                    <div>
                        <span class="font-medium text-gray-700">Telefone:</span>
                        <div class="text-gray-900">
                            <a href="tel:{{ $order['customer_phone'] }}" 
                               class="text-blue-600 hover:text-blue-800">
                                {{ $order['customer_phone'] }}
                            </a>
                        </div>
                    </div>
                    @if ($order['customer_address'])
                        <div>
                            <span class="font-medium text-gray-700">Endereço:</span>
                            <div class="text-gray-900">{{ $order['customer_address'] }}</div>
                        </div>
                    @endif
                    @if ($order['notes'])
                        <div>
                            <span class="font-medium text-gray-700">Observações:</span>
                            <div class="text-gray-900 bg-yellow-50 p-3 rounded border border-yellow-200">
                                {{ $order['notes'] }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Ações Rápidas -->
            <div class="bg-white shadow rounded-lg p-6 no-print">
                <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <i class="fas fa-tools text-green-600"></i>
                    Ações
                </h2>
                <div class="space-y-3">
                    <a href="https://wa.me/55{{ preg_replace('/\D/', '', $order['customer_phone']) }}" 
                       target="_blank"
                       class="w-full bg-green-600 text-white px-4 py-3 rounded hover:bg-green-700 flex items-center justify-center gap-2">
                        <i class="fab fa-whatsapp"></i> Chamar no WhatsApp
                    </a>
                    <a href="tel:{{ $order['customer_phone'] }}" 
                       class="w-full bg-blue-600 text-white px-4 py-3 rounded hover:bg-blue-700 flex items-center justify-center gap-2">
                        <i class="fas fa-phone"></i> Ligar para Cliente
                    </a>
                    <div class="flex gap-2">
                        <button onclick="printOrder({{ $order['id'] }})" 
                                class="flex-1 bg-purple-600 text-white px-4 py-3 rounded hover:bg-purple-700 flex items-center justify-center gap-2">
                            <i class="fas fa-print"></i> QZ Tray
                        </button>
                        <a href="/admin/print-order-pdf/{{ $order['id'] }}" 
                           target="_blank"
                           class="flex-1 bg-indigo-600 text-white px-4 py-3 rounded hover:bg-indigo-700 flex items-center justify-center gap-2">
                            <i class="fas fa-file-pdf"></i> PDF
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Itens do Pedido -->
        <div class="bg-white shadow rounded-lg p-6 mt-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <i class="fas fa-shopping-cart text-orange-600"></i>
                Itens do Pedido
            </h2>
            <div class="space-y-4">
                @php $itemIndex = 1; @endphp
                @foreach ($order['items'] as $item)
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2">
                                    <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2 py-1 rounded">
                                        {{ $itemIndex }}
                                    </span>
                                    <h3 class="text-lg font-medium text-gray-900">{{ $item['product_name'] }}</h3>
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm mb-3">
                                    <div>
                                        <span class="font-medium text-gray-700">Quantidade:</span>
                                        <span class="text-gray-900">{{ $item['quantity'] }}x</span>
                                    </div>
                                    <div>
                                        <span class="font-medium text-gray-700">Preço Unitário:</span>
                                        <span class="text-gray-900">R$ {{ number_format($item['unit_price'], 2, ',', '.') }}</span>
                                    </div>
                                    <div>
                                        <span class="font-medium text-gray-700">Subtotal:</span>
                                        <span class="text-green-600 font-semibold">
                                            R$ {{ number_format($item['quantity'] * $item['unit_price'], 2, ',', '.') }}
                                        </span>
                                    </div>
                                </div>

                                @if (!empty($item['ingredients']))
                                    <div class="bg-gray-50 rounded p-3">
                                        <span class="font-medium text-gray-700 text-sm">Adicionais:</span>
                                        <div class="mt-1">
                                            @foreach ($item['ingredients'] as $ingredient)
                                                <div class="flex justify-between items-center text-sm">
                                                    <span class="text-gray-900">
                                                        • {{ $ingredient['ingredient_name'] }}
                                                        @if ($ingredient['quantity'] > 1)
                                                            ({{ $ingredient['quantity'] }}x)
                                                        @endif
                                                    </span>
                                                    @if ($ingredient['price'] > 0)
                                                        <span class="text-green-600">
                                                            +R$ {{ number_format($ingredient['price'], 2, ',', '.') }}
                                                        </span>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                @if (!empty($item['notes']))
                                    <div class="bg-yellow-50 border border-yellow-200 rounded p-3 mt-3">
                                        <span class="font-medium text-gray-700 text-sm">Observações do item:</span>
                                        <div class="text-sm text-gray-900 mt-1">{{ $item['notes'] }}</div>
                                    </div>
                                @endif
                            </div>
                            
                            @if (!empty($item['product_image']))
                                <div class="flex-shrink-0 ml-4">
                                    <img src="{{ $item['product_image'] }}" 
                                         alt="{{ $item['product_name'] }}"
                                         class="w-16 h-16 object-cover rounded-lg">
                                </div>
                            @endif
                        </div>
                    </div>
                    @php $itemIndex++; @endphp
                @endforeach
            </div>

            <!-- Resumo do Total -->
            <div class="border-t border-gray-200 mt-6 pt-6">
                <div class="flex justify-end">
                    <div class="text-right">
                        <div class="text-lg font-semibold text-gray-900">
                            Total do Pedido: 
                            <span class="text-2xl text-green-600">
                                R$ {{ number_format($order['total_amount'], 2, ',', '.') }}
                            </span>
                        </div>
                        <div class="text-sm text-gray-600 mt-1">
                            {{ count($order['items']) }} item(s) • 
                            Pedido realizado em {{ date('d/m/Y \à\s H:i', strtotime($order['created_at'])) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- QZ Tray Scripts -->
    <script src="assets/js/admin-print-poll.js"></script>
    <script>
        // Configuração QZ Tray
        if (window.qz) {
            qz.security.setSignaturePromise(function(toSign) {
                return function(resolve, reject) {
                    resolve();
                };
            });

            qz.security.setCertificatePromise(function(resolve, reject) {
                resolve(null);
            });

            // Conectar ao QZ Tray
            qz.websocket.connect().then(() => {
                console.log('QZ Tray conectado');
            }).catch(err => {
                console.error('Erro ao conectar QZ Tray:', err);
            });
        }

        // Função de impressão
        async function printOrder(orderId) {
            if (!window.qz) {
                alert('QZ Tray não disponível. Use a opção PDF.');
                return;
            }

            try {
                const printers = await qz.printers.find();
                if (!printers || printers.length === 0) {
                    alert('Nenhuma impressora encontrada.');
                    return;
                }

                const printerName = printers[0];
                const resp = await fetch('/admin/print-order/' + orderId);
                const result = await resp.json();

                const config = qz.configs.create(printerName, {
                    colorType: 'blackwhite',
                    encoding: 'UTF-8'
                });

                const data = [{
                    type: 'raw',
                    format: 'plain',
                    data: result.printData
                }];

                await qz.print(config, data);
                alert('Pedido enviado para impressão via QZ Tray!');
            } catch (e) {
                console.error('Erro:', e);
                alert('Erro ao imprimir: ' + e.message);
            }
        }
    </script>
</body>
</html>