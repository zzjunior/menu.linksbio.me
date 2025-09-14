<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $pageTitle }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <!-- Header -->
    <header class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ $store['store_name'] }}</h1>
                    <p class="text-gray-600">Painel Administrativo</p>
                </div>
            <!---logo upload--->
            <div class="flex items-center space-x-4 m-2 mb-3">
                <form action="/admin/upload-logo" method="post" enctype="multipart/form-data" class="flex items-center space-x-2">
                    <label for="logo-upload" class="bg-blue-600 text-white px-4 py-2 rounded cursor-pointer hover:bg-blue-700">
                        <i class="fas fa-upload mr-1"></i>
                        Adicionar Logo à loja
            </label>
            <input type="file" id="logo-upload" name="logo" accept="image/*" class="hidden" onchange="this.form.submit()">
            </form>
            @if (!empty($store['logo']))
            <img src="{{ $store['logo'] }}" alt="Logo da loja" class="h-10 rounded shadow">
            @endif
        </div>
        
                <div class="flex items-center space-x-4">
                    <a href="/{{ $store['store_slug'] }}" 
                       target="_blank"
                       class="text-blue-600 hover:text-blue-800">
                        <i class="fas fa-external-link-alt mr-1"></i>
                        Ver Cardápio
                    </a>
                    <a href="/admin/logout" class="text-red-600 hover:text-red-800">
                        <i class="fas fa-sign-out-alt mr-1"></i>
                        Sair
                    </a>
                </div>
            </div>
        </div>
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
        </div>

        <!-- Menu de Navegação -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                    Gerenciamento
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
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
                </div>
            </div>
        </div>

                <!-- Pedidos Recentes -->
        <div class="bg-white shadow rounded-lg mb-8 mt-5">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-receipt mr-2 text-blue-600"></i> Últimos Pedidos
                </h3>
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
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </main>
</body>
</html>
