<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Pedidos - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <!-- Header -->
    <header class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div class="flex items-center space-x-4">
                    <a href="/admin" class="text-gray-500 hover:text-gray-700">← Voltar</a>
                    <h1 class="text-3xl font-bold text-gray-900">Todos os Pedidos</h1>
                </div>
                <div class="text-sm text-gray-600">
                    Total: {{ $totalOrders }} pedidos
                </div>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <!-- Filtros -->
        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <form method="GET" action="/admin/pedidos" class="flex flex-wrap gap-4 items-end">
                <div class="flex-1 min-w-64">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">
                        Buscar por cliente, telefone ou ID
                    </label>
                    <input type="text" 
                           id="search" 
                           name="search" 
                           value="{{ $search }}"
                           placeholder="Digite para buscar..."
                           class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div class="min-w-40">
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select id="status" 
                            name="status" 
                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Todos os status</option>
                        <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pendente</option>
                        <option value="confirmed" {{ $status === 'confirmed' ? 'selected' : '' }}>Confirmado</option>
                        <option value="preparing" {{ $status === 'preparing' ? 'selected' : '' }}>Preparando</option>
                        <option value="ready" {{ $status === 'ready' ? 'selected' : '' }}>Pronto</option>
                        <option value="delivered" {{ $status === 'delivered' ? 'selected' : '' }}>Entregue</option>
                        <option value="cancelled" {{ $status === 'cancelled' ? 'selected' : '' }}>Cancelado</option>
                    </select>
                </div>
                
                <div class="flex gap-2">
                    <button type="submit" 
                            class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 flex items-center gap-2">
                        <i class="fas fa-search"></i> Filtrar
                    </button>
                    <a href="/admin/pedidos" 
                       class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 flex items-center gap-2">
                        <i class="fas fa-times"></i> Limpar
                    </a>
                </div>
            </form>
        </div>

        @if (empty($orders))
            <div class="text-center py-12">
                <div class="text-6xl mb-4">📋</div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Nenhum pedido encontrado</h3>
                <p class="text-gray-600">
                    @if (!empty($search) || !empty($status))
                        Tente ajustar os filtros para encontrar pedidos.
                    @else
                        Não há pedidos cadastrados ainda.
                    @endif
                </p>
            </div>
        @else
            <!-- Tabela Desktop -->
            <div class="hidden md:block bg-white shadow rounded-lg overflow-hidden">
                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Pedido
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Cliente
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Data/Hora
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Total
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Itens
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Ações
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($orders as $order)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">#{{ $order['id'] }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $order['customer_name'] }}</div>
                                    <div class="text-sm text-gray-500">{{ $order['customer_phone'] }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ date('d/m/Y', strtotime($order['created_at'])) }}</div>
                                    <div class="text-sm text-gray-500">{{ date('H:i', strtotime($order['created_at'])) }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $statusColors = [
                                            'pending' => 'bg-yellow-100 text-yellow-800',
                                            'confirmed' => 'bg-indigo-100 text-indigo-800',
                                            'preparing' => 'bg-blue-100 text-blue-800',
                                            'ready' => 'bg-green-100 text-green-800',
                                            'delivered' => 'bg-gray-100 text-gray-800',
                                            'cancelled' => 'bg-red-100 text-red-800'
                                        ];
                                        $statusClass = $statusColors[$order['status']] ?? 'bg-gray-100 text-gray-800';
                                    @endphp
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statusClass }}">
                                        {{ ucfirst($order['status']) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        R$ {{ number_format($order['total_amount'], 2, ',', '.') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-500">
                                        {{ $order['items_count'] ?? 0 }} item(s)
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex gap-2 justify-end">
                                        <!-- Dropdown de Status -->
                                        <form method="POST" action="/admin/pedidos/{{ $order['id'] }}/status" class="inline">
                                            <select name="status" onchange="this.form.submit()" 
                                                    class="text-xs border border-gray-300 rounded px-2 py-1 focus:outline-none focus:ring-1 focus:ring-blue-500">
                                                <option value="pending" {{ $order['status'] === 'pending' ? 'selected' : '' }}>Pendente</option>
                                                <option value="confirmed" {{ $order['status'] === 'confirmed' ? 'selected' : '' }}>Confirmado</option>
                                                <option value="preparing" {{ $order['status'] === 'preparing' ? 'selected' : '' }}>Preparando</option>
                                                <option value="ready" {{ $order['status'] === 'ready' ? 'selected' : '' }}>Pronto</option>
                                                <option value="delivered" {{ $order['status'] === 'delivered' ? 'selected' : '' }}>Entregue</option>
                                                <option value="cancelled" {{ $order['status'] === 'cancelled' ? 'selected' : '' }}>Cancelado</option>
                                            </select>
                                        </form>
                                        
                                        <a href="/admin/pedidos/{{ $order['id'] }}" 
                                           target="_blank"
                                           class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700 inline-flex items-center gap-1">
                                            <i class="fas fa-eye"></i> Ver
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Cards Mobile -->
            <div class="md:hidden space-y-4">
                @foreach ($orders as $order)
                    <div class="bg-white shadow rounded-lg p-4">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <div class="text-lg font-medium text-gray-900">Pedido #{{ $order['id'] }}</div>
                                <div class="text-sm text-gray-500">{{ date('d/m/Y H:i', strtotime($order['created_at'])) }}</div>
                            </div>
                            @php
                                $statusColors = [
                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                    'confirmed' => 'bg-indigo-100 text-indigo-800',
                                    'preparing' => 'bg-blue-100 text-blue-800',
                                    'ready' => 'bg-green-100 text-green-800',
                                    'delivered' => 'bg-gray-100 text-gray-800',
                                    'cancelled' => 'bg-red-100 text-red-800'
                                ];
                                $statusClass = $statusColors[$order['status']] ?? 'bg-gray-100 text-gray-800';
                            @endphp
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statusClass }}">
                                {{ ucfirst($order['status']) }}
                            </span>
                        </div>
                        
                        <div class="space-y-2 mb-4">
                            <div>
                                <span class="text-sm font-medium text-gray-700">Cliente:</span>
                                <span class="text-sm text-gray-900">{{ $order['customer_name'] }}</span>
                            </div>
                            <div>
                                <span class="text-sm font-medium text-gray-700">Telefone:</span>
                                <span class="text-sm text-gray-900">{{ $order['customer_phone'] }}</span>
                            </div>
                            <div>
                                <span class="text-sm font-medium text-gray-700">Total:</span>
                                <span class="text-sm font-medium text-gray-900">
                                    R$ {{ number_format($order['total_amount'], 2, ',', '.') }}
                                </span>
                            </div>
                            <div>
                                <span class="text-sm font-medium text-gray-700">Itens:</span>
                                <span class="text-sm text-gray-900">{{ $order['items_count'] ?? 0 }} item(s)</span>
                            </div>
                        </div>
                        
                        <div class="flex justify-between items-end">
                            <!-- Dropdown de Status Mobile -->
                            <form method="POST" action="/admin/pedidos/{{ $order['id'] }}/status" class="inline">
                                <select name="status" onchange="this.form.submit()" 
                                        class="text-sm border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="pending" {{ $order['status'] === 'pending' ? 'selected' : '' }}>Pendente</option>
                                    <option value="confirmed" {{ $order['status'] === 'confirmed' ? 'selected' : '' }}>Confirmado</option>
                                    <option value="preparing" {{ $order['status'] === 'preparing' ? 'selected' : '' }}>Preparando</option>
                                    <option value="ready" {{ $order['status'] === 'ready' ? 'selected' : '' }}>Pronto</option>
                                    <option value="delivered" {{ $order['status'] === 'delivered' ? 'selected' : '' }}>Entregue</option>
                                    <option value="cancelled" {{ $order['status'] === 'cancelled' ? 'selected' : '' }}>Cancelado</option>
                                </select>
                            </form>
                            
                            <a href="/admin/pedidos/{{ $order['id'] }}" 
                               target="_blank"
                               class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 flex items-center gap-2">
                                <i class="fas fa-eye"></i> Ver Detalhes
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Paginação -->
            @if ($totalPages > 1)
                <div class="bg-white shadow rounded-lg p-4 mt-6">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-700">
                            Página {{ $currentPage }} de {{ $totalPages }}
                            ({{ $totalOrders }} pedidos no total)
                        </div>
                        
                        <div class="flex space-x-2">
                            @if ($currentPage > 1)
                                <a href="?page={{ $currentPage - 1 }}&search={{ urlencode($search) }}&status={{ urlencode($status) }}" 
                                   class="bg-gray-300 text-gray-700 px-3 py-2 rounded hover:bg-gray-400">
                                    ← Anterior
                                </a>
                            @endif
                            
                            @for ($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++)
                                <a href="?page={{ $i }}&search={{ urlencode($search) }}&status={{ urlencode($status) }}" 
                                   class="px-3 py-2 rounded {{ $i == $currentPage ? 'bg-blue-600 text-white' : 'bg-gray-300 text-gray-700 hover:bg-gray-400' }}">
                                    {{ $i }}
                                </a>
                            @endfor
                            
                            @if ($currentPage < $totalPages)
                                <a href="?page={{ $currentPage + 1 }}&search={{ urlencode($search) }}&status={{ urlencode($status) }}" 
                                   class="bg-gray-300 text-gray-700 px-3 py-2 rounded hover:bg-gray-400">
                                    Próxima →
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        @endif
    </main>
</body>
</html>
