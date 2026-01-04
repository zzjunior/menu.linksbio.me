<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $pageTitle }}</title>
    <script src="https://cdn.tailwindcss.com">
    <a href="/admin/pedidos" 
                       class="block p-6 bg-orange-50 rounded-lg hover:bg-orange-100 transition-colors">
                        <div class="text-center">
                            <div class="text-3xl mb-2">📋</div>
                            <h4 class="text-lg font-medium text-orange-900">Pedidos</h4>
                            <p class="text-sm text-orange-700">Gerenciar pedidos da loja</p>
                        </div>
                    </a>
                    </script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/rsvp/4.8.5/rsvp.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/qz-tray@2.2.5/qz-tray.min.js"></script>
</head>
<body class="bg-gray-100">
    <!-- Header -->
    <header class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <!-- Store Info -->
                <div class="flex items-center space-x-3">
                    @if (!empty($store['logo']))
                        <img src="{{ $store['logo'] }}" alt="Logo da loja" class="h-10 w-10 rounded img-fluid shadow">
                    @endif
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold text-gray-900">{{ $store['store_name'] }}</h1>
                        <p class="text-gray-600 text-sm md:text-base">Painel Administrativo</p>
                    </div>
                </div>
                {{---Logo upload & Actions---}}
                <div class="hidden md:flex items-center space-x-6">
                    {{--<form action="/admin/upload-logo" method="post" enctype="multipart/form-data" class="flex items-center space-x-2">
                        <label for="logo-upload" class="bg-blue-600 text-white px-3 py-2 rounded cursor-pointer hover:bg-blue-700 text-sm flex items-center">
                            <i class="fas fa-upload mr-1"></i>
                            <span>Atualizar Logo</span>
                        </label>
                        <input type="file" id="logo-upload" name="logo" accept="image/*" class="hidden" onchange="this.form.submit()">
                    </form> --}}
                    <a href="/{{ $store['store_slug'] }}" target="_blank" class="text-blue-600 hover:text-blue-800 text-sm flex items-center">
                        <i class="fas fa-external-link-alt mr-1"></i>
                        <span>Ver Cardápio</span>
                    </a>
                    <a href="/admin/loja/configuracoes" class="text-gray-600 hover:text-gray-800 text-sm flex items-center">
                        <i class="fas fa-cog mr-1"></i>
                        <span>Configurações</span>
                    </a>
                    <a href="/admin/logout" class="text-red-600 hover:text-red-800 text-sm flex items-center">
                        <i class="fas fa-sign-out-alt mr-1"></i>
                        <span>Sair</span>
                    </a>
                </div>
                <!-- Botão do menu mobile à direita -->
                <button id="menu-toggle" class="md:hidden text-gray-700 focus:outline-none ml-2">
                    <i class="fas fa-bars text-2xl"></i>
                </button>
            </div>
        </div>
        <!-- Offcanvas Mobile Menu (Direita) -->
        <div id="offcanvas-menu" class="fixed inset-0 z-50 bg-black bg-opacity-40 hidden">
            <div class="fixed right-0 top-0 h-full w-64 bg-white shadow-lg p-6 flex flex-col space-y-6">
                <div class="flex items-center justify-between mb-4">
                    <span class="font-bold text-lg">{{ $store['store_name'] }}</span>
                    <button id="menu-close" class="text-gray-700 focus:outline-none">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <form action="/admin/upload-logo" method="post" enctype="multipart/form-data" class="flex items-center space-x-2">
                    <label for="logo-upload-mobile" class="bg-blue-600 text-white px-3 py-2 rounded cursor-pointer hover:bg-blue-700 text-sm flex items-center">
                        <i class="fas fa-upload mr-1"></i>
                        <span>Atualizar Logo</span>
                    </label>
                    <input type="file" id="logo-upload-mobile" name="logo" accept="image/*" class="hidden" onchange="this.form.submit()">
                </form>
                @if (!empty($store['logo']))
                    <img src="{{ $store['logo'] }}" alt="Logo da loja" class="h-10 w-10 rounded img-fluid shadow">
                @endif
                <a href="/{{ $store['store_slug'] }}" target="_blank" class="text-blue-600 hover:text-blue-800 text-sm flex items-center">
                    <i class="fas fa-external-link-alt mr-1"></i>
                    <span>Ver Cardápio</span>
                </a>
                <a href="/admin/loja/configuracoes" class="text-gray-600 hover:text-gray-800 text-sm flex items-center">
                    <i class="fas fa-cog mr-1"></i>
                    <span>Configurações</span>
                </a>
                <a href="/admin/logout" class="text-red-600 hover:text-red-800 text-sm flex items-center">
                    <i class="fas fa-sign-out-alt mr-1"></i>
                    <span>Sair</span>
                </a>
            </div>
        </div>
        <script>
            const menuToggle = document.getElementById('menu-toggle');
            const offcanvasMenu = document.getElementById('offcanvas-menu');
            const menuClose = document.getElementById('menu-close');
            menuToggle?.addEventListener('click', () => {
                offcanvasMenu.classList.remove('hidden');
            });
            menuClose?.addEventListener('click', () => {
                offcanvasMenu.classList.add('hidden');
            });
            offcanvasMenu?.addEventListener('click', (e) => {
                if (e.target === offcanvasMenu) offcanvasMenu.classList.add('hidden');
            });
        </script>
    </header>

    <main class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <!-- Cards de Estatísticas -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                                <i class="fas fa-boxes text-white"></i>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">
                                    Total de Produtos
                                </dt>
                                <dd class="text-lg font-medium text-gray-900">
                                    {{ $totalProducts }}
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                                <span class="text-white text-sm">🗂️</span>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">
                                    Categorias
                                </dt>
                                <dd class="text-lg font-medium text-gray-900">
                                    {{ $totalCategories }}
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-purple-500 rounded-md flex items-center justify-center">
                                <span class="text-white text-sm">🥝</span>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">
                                    Ingredientes
                                </dt>
                                <dd class="text-lg font-medium text-gray-900">
                                    {{ $totalIngredients }}
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-orange-500 rounded-md flex items-center justify-center">
                                <i class="fas fa-receipt text-white"></i>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">
                                    Total de Pedidos
                                </dt>
                                <dd class="text-lg font-medium text-gray-900">
                                    {{ $totalOrders }}
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Menu de Navegação -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                    Gerenciamento
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <a href="/admin/products" 
                       class="block p-6 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                        <div class="text-center">
                            <div class="text-3xl mb-2">📦</div>
                            <h4 class="text-lg font-medium text-blue-900">Produtos</h4>
                            <p class="text-sm text-blue-700">Gerenciar itens do cardápio</p>
                        </div>
                    </a>

                    <a href="/admin/categories" 
                       class="block p-6 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
                        <div class="text-center">
                            <div class="text-3xl mb-2">🗂️</div>
                            <h4 class="text-lg font-medium text-green-900">Categorias</h4>
                            <p class="text-sm text-green-700">Organizar produtos por tipo</p>
                        </div>
                    </a>

                    <a href="/admin/ingredients" 
                       class="block p-6 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors">
                        <div class="text-center">
                            <div class="text-3xl mb-2">🥝</div>
                            <h4 class="text-lg font-medium text-purple-900">Ingredientes</h4>
                            <p class="text-sm text-purple-700">Opções para personalização</p>
                        </div>
                    </a>

                    <a href="/admin/pedidos" 
                       class="block p-6 bg-orange-50 rounded-lg hover:bg-orange-100 transition-colors">
                        <div class="text-center">
                            <div class="text-3xl mb-2">📋</div>
                            <h4 class="text-lg font-medium text-orange-900">Pedidos</h4>
                            <p class="text-sm text-orange-700">Gerenciar pedidos da loja</p>
                        </div>
                    </a>

                    <a href="/admin/clientes" 
                       class="block p-6 bg-pink-50 rounded-lg hover:bg-pink-100 transition-colors">
                        <div class="text-center">
                            <div class="text-3xl mb-2">👥</div>
                            <h4 class="text-lg font-medium text-pink-900">Clientes</h4>
                            <p class="text-sm text-pink-700">Visualizar clientes cadastrados</p>
                        </div>
                    </a>
                </div>
                
                <!-- Segunda linha de menu -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mt-4">
                    <a href="/admin/pedidos/novo" 
                       class="block p-6 bg-emerald-50 rounded-lg hover:bg-emerald-100 transition-colors">
                        <div class="text-center">
                            <div class="text-3xl mb-2">➕</div>
                            <h4 class="text-lg font-medium text-emerald-900">CRIAR PEDIDO</h4>
                            <p class="text-sm text-emerald-700">Cadastrar pedido manual</p>
                        </div>
                    </a>
                    
                    <a href="/admin/relatorios" 
                       class="block p-6 bg-indigo-50 rounded-lg hover:bg-indigo-100 transition-colors">
                        <div class="text-center">
                            <div class="text-3xl mb-2">$</div>
                            <h4 class="text-lg font-medium text-indigo-900">Ganhos</h4>
                            <p class="text-sm text-indigo-700">Análise das vendas</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>

                <!-- Pedidos Recentes -->
        <div class="bg-white shadow rounded-lg mb-8 mt-5">
            <div class="px-4 py-5 sm:p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg leading-6 font-medium text-gray-900 flex items-center">
                    <i class="fas fa-receipt mr-2 text-blue-600"></i> Últimos Pedidos
                </h3>
                <a href="/admin/pedidos/novo" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded font-semibold text-sm flex items-center gap-2">
                    <i class="fas fa-plus"></i> Adicionar Pedido
                </a>
            </div>
            @if (empty($recentOrders))
                <div class="text-gray-500 text-center py-8">Nenhum pedido recente.</div>
            @else
                <div class="space-y-6">
                @foreach ($recentOrders as $order)
                    <div class="border rounded-lg p-4 flex flex-col md:flex-row md:items-center md:justify-between bg-gray-50">
                    <div class="flex-1">
                        <div class="flex flex-col md:flex-row md:items-center md:space-x-4">
                        <div class="font-semibold text-gray-900 text-base">
                            {{ $order['customer_name'] }}
                            <span class="text-xs text-gray-500 ml-2">({{ $order['customer_phone'] }})</span>
                        </div>
                        <div class="text-xs text-gray-500 mt-1 md:mt-0">{{ $order['customer_address'] }}</div>
                        </div>
                        <div class="flex flex-wrap items-center mt-2 space-x-2">
                        <span class="text-xs text-gray-600">{{ date('d/m/Y H:i', strtotime($order['created_at'])) }}</span>
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">{{ ucfirst($order['status']) }}</span>
                        <span class="text-sm font-bold text-gray-900 ml-2">R$ {{ number_format($order['total_amount'], 2, ',', '.') }}</span>
                        </div>
                        @if (!empty($order['items']))
                        <ul class="mt-2 ml-2 text-sm text-gray-800 space-y-1">
                            @foreach ($order['items'] as $item)
                            <li>
                                <span class="font-medium">{{ $item['product_name'] }}</span>
                                <span class="text-xs text-gray-500">x{{ $item['quantity'] }}</span>
                                @if (!empty($item['ingredients']))
                                <span class="text-xs text-gray-500">- {{ implode(', ', $item['ingredients']) }}</span>
                                @endif
                            </li>
                            @endforeach
                        </ul>
                        @endif
                        @if (!empty($order['notes']))
                        <div class="mt-1 text-xs text-yellow-700 bg-yellow-50 rounded p-2">Obs: {{ $order['notes'] }}</div>
                        @endif
                    </div>
                    <!-- Botões de Impressão e Visualização -->
                    <div class="mt-3 md:mt-0 md:ml-4 flex-shrink-0 flex items-center gap-2">
                        <!-- Botão QZ Tray -->
                        <button type="button" class="bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded flex items-center gap-1 print-btn text-sm" data-order-id="{{ $order['id'] }}">
                        <i class="fas fa-print"></i> QZ Tray
                        </button>
                        <!-- Botão PDF -->
                        <button type="button" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded flex items-center gap-1 print-pdf-btn text-sm" data-order-id="{{ $order['id'] }}">
                        <i class="fas fa-file-pdf"></i> PDF
                        </button>
                        <!-- Botão Ver Pedido -->
                        <a href="/admin/pedidos/{{ $order['id'] }}" target="_blank" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-3 py-2 rounded flex items-center gap-1 text-sm">
                        <i class="fas fa-eye"></i> Ver
                        </a>
                    </div>
                    </div>
                @endforeach
                </div>
                @endif
            </div>
        </div>
    </main>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="assets/js/admin-print-poll.js"></script>
    <script>
        // Configuração de assinatura digital para QZ Tray
        qz.security.setSignaturePromise(function(toSign) {
            return function(resolve, reject) {
                resolve(); // Sem assinatura (modo unsigned)
            };
        });

        qz.security.setCertificatePromise(function(resolve, reject) {
            resolve(null); // Sem certificado (modo unsigned)
        });

        // Conectar ao QZ Tray na inicialização
        window.addEventListener('DOMContentLoaded', () => {
            if (window.qz) {
                qz.websocket.connect().then(() => {
                    console.log('QZ Tray conectado com sucesso');
                }).catch(err => {
                    console.error('Erro ao conectar com QZ Tray:', err);
                });
            }
        });

        // Função para imprimir pedidos via QZ Tray
        document.querySelectorAll('.print-btn').forEach(button => {
            button.addEventListener('click', async () => {
                const orderId = button.getAttribute('data-order-id');
                
                if (!window.qz) {
                    alert('QZ Tray não está disponível. Use a opção PDF.');
                    return;
                }

                try {
                    console.log('Iniciando impressão QZ Tray do pedido:', orderId);
                    
                    // Listar impressoras disponíveis
                    const printers = await qz.printers.find();
                    console.log('Impressoras disponíveis:', printers);
                    
                    if (!printers || printers.length === 0) {
                        alert('Nenhuma impressora encontrada. Verifique se há impressoras instaladas.');
                        return;
                    }
                    
                    // Usar a primeira impressora (geralmente a padrão)
                    const printerName = printers[0];
                    console.log('Usando impressora:', printerName);
                    
                    // Buscar dados de impressão com comandos ESC/POS
                    const resp = await fetch('/admin/print-order/' + orderId);
                    if (!resp.ok) throw new Error('Erro ao buscar dados de impressão');
                    const result = await resp.json();
                    
                    console.log('Dados recebidos:', result);
                    
                    // Configurar impressora específica
                    const config = qz.configs.create(printerName, {
                        colorType: 'blackwhite',
                        encoding: 'UTF-8'
                    });
                    
                    console.log('Configuração criada:', config);
                    
                    // Preparar dados para impressão - usar string com comandos ESC/POS
                    const data = [{
                        type: 'raw',
                        format: 'plain',
                        data: result.printData
                    }];
                    
                    console.log('Enviando para impressão:', data);
                    
                    // Imprimir
                    await qz.print(config, data);
                    
                    console.log('Impressão enviada com sucesso para:', printerName);
                    alert('Pedido enviado para impressão via QZ Tray!');
                    
                } catch (e) {
                    console.error('Erro de impressão QZ Tray:', e);
                    alert('Erro ao imprimir via QZ Tray: ' + (e.message || e) + '\nTente usar a opção PDF.');
                }
            });
        });

        // Função para imprimir pedidos via PDF
        document.querySelectorAll('.print-pdf-btn').forEach(button => {
            button.addEventListener('click', () => {
                const orderId = button.getAttribute('data-order-id');
                console.log('Abrindo PDF de impressão para pedido:', orderId);
                
                // Abrir PDF em nova aba - formatado para impressora térmica
                const pdfUrl = '/admin/print-order-pdf/' + orderId;
                window.open(pdfUrl, '_blank');
            });
        });
    </script>
</body>
</html>
