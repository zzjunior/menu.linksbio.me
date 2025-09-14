<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $pageTitle }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <!-- Header -->
    <header class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div class="flex items-center space-x-4">
                    <a href="/admin" class="text-gray-500 hover:text-gray-700">← Voltar</a>
                    <h1 class="text-3xl font-bold text-gray-900">{{ $pageTitle }}</h1>
                </div>
                <a href="/admin/products/new" 
                   class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                    + Novo Produto
                </a>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        @if (empty($products))
            <div class="text-center py-12">
                <div class="text-6xl mb-4">📦</div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Nenhum produto cadastrado</h3>
                <p class="text-gray-600 mb-4">Comece criando seu primeiro produto</p>
                <a href="/admin/products/new" 
                   class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700">
                    Criar Produto
                </a>
            </div>
        @else
            <!-- Grid responsivo para mobile -->
            <div class="md:hidden grid grid-cols-1 gap-4">
                @foreach ($products as $product)
                    <div class="bg-white shadow rounded-lg p-4 flex items-center {{ $product['active'] ? '' : 'bg-gray-50 opacity-75' }}">
                        <div class="flex-shrink-0 h-16 w-16">
                            @if ($product['image_url'])
                                <img class="h-16 w-16 rounded-lg object-cover" src="{{ $product['image_url'] }}" alt="{{ $product['name'] }}">
                            @else
                                <div class="h-16 w-16 bg-gray-200 rounded-lg flex items-center justify-center">
                                    <span class="text-gray-400 text-2xl">🍇</span>
                                </div>
                            @endif
                        </div>
                        <div class="ml-4 flex-1">
                            <div class="flex justify-between items-center">
                                <div class="text-base font-semibold text-gray-900">
                                    {{ $product['name'] }}
                                    @if ($product['size_ml'])
                                        <span class="text-gray-500 text-sm">({{ $product['size_ml'] }}ml)</span>
                                    @endif
                                </div>
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ $product['category_name'] ?? 'Sem categoria' }}
                                </span>
                            </div>
                            @if ($product['description'])
                                <div class="text-xs text-gray-500 mt-1 truncate">{{ $product['description'] }}</div>
                            @endif
                            <div class="flex items-center justify-between mt-2">
                                <div class="text-sm font-bold text-gray-900">R$ {{ number_format($product['price'], 2, ',', '.') }}</div>
                                <div>
                                    @if ($product['active'])
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Ativo</span>
                                    @else
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Inativo</span>
                                    @endif
                                </div>
                                <div class="flex space-x-2 ml-2">
                                    <a href="/admin/products/{{ $product['id'] }}/edit" class="text-blue-600 hover:text-blue-900 text-xs">Editar</a>
                                    <form method="POST" action="/admin/products/{{ $product['id'] }}/delete" class="inline" onsubmit="return confirm('Tem certeza que deseja remover este produto?')">
                                        @csrf
                                        <button type="submit" class="text-red-600 hover:text-red-900 text-xs">Remover</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <!-- Tabela para desktop -->
            <div class="hidden md:block bg-white shadow overflow-hidden rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produto</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Categoria</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Preço</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($products as $product)
                            <tr class="{{ $product['active'] ? '' : 'bg-gray-50 opacity-75' }}">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-12 w-12">
                                            @if ($product['image_url'])
                                                <img class="h-12 w-12 rounded-lg object-cover" src="{{ $product['image_url'] }}" alt="{{ $product['name'] }}">
                                            @else
                                                <div class="h-12 w-12 bg-gray-200 rounded-lg flex items-center justify-center">
                                                    <span class="text-gray-400 text-xl">🍇</span>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $product['name'] }}
                                                @if ($product['size_ml'])
                                                    <span class="text-gray-500">({{ $product['size_ml'] }}ml)</span>
                                                @endif
                                            </div>
                                            @if ($product['description'])
                                                <div class="text-sm text-gray-500 max-w-xs truncate">{{ $product['description'] }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">{{ $product['category_name'] ?? 'Sem categoria' }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">R$ {{ number_format($product['price'], 2, ',', '.') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($product['active'])
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Ativo</span>
                                    @else
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Inativo</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="/admin/products/{{ $product['id'] }}/edit" class="text-blue-600 hover:text-blue-900">Editar</a>
                                        <form method="POST" action="/admin/products/{{ $product['id'] }}/delete" class="inline" onsubmit="return confirm('Tem certeza que deseja remover este produto?')">
                                            @csrf
                                            <button type="submit" class="text-red-600 hover:text-red-900">Remover</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </main>
</body>
</html>
