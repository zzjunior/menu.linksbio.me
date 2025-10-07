<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $pageTitle }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-100">
    <!-- Header -->
    <header class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div class="flex items-center space-x-4">
                    <a href="/admin" class="text-gray-500 hover:text-gray-700">← Voltar ao Dashboard</a>
                </div>
                <div class="flex items-center space-x-4">
                    <input type="date" id="dateFilter" value="{{ $selectedDate }}" 
                           class="border border-gray-300 rounded-md px-3 py-2 text-sm"
                           onchange="updateReports()">
                    <a href="/admin/relatorios/diario" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                        <i class="fas fa-calendar-day mr-1"></i> Resumo Dia
                    </a>
                </div>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <!-- Cards de Resumo -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Receita do Dia -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                                <i class="fas fa-dollar-sign text-white text-sm"></i>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Receita Hoje</dt>
                                <dd class="text-lg font-medium text-gray-900">
                                    R$ {{ number_format($dailyReport['gross_revenue'], 2, ',', '.') }}
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-5 py-3">
                    <div class="text-sm">
                        @if ($comparison['revenue_change'] > 0)
                            <span class="text-green-600 font-medium">
                                <i class="fas fa-arrow-up"></i> +{{ $comparison['revenue_change'] }}%
                            </span>
                        @elseif ($comparison['revenue_change'] < 0)
                            <span class="text-red-600 font-medium">
                                <i class="fas fa-arrow-down"></i> {{ $comparison['revenue_change'] }}%
                            </span>
                        @else
                            <span class="text-gray-600 font-medium">
                                <i class="fas fa-minus"></i> 0%
                            </span>
                        @endif
                        <span class="text-gray-600"> vs período anterior</span>
                    </div>
                </div>
            </div>

            <!-- Pedidos do Dia -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                                <i class="fas fa-shopping-cart text-white text-sm"></i>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Pedidos Hoje</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $dailyReport['total_orders'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-5 py-3">
                    <div class="text-sm">
                        @if ($comparison['orders_change'] > 0)
                            <span class="text-green-600 font-medium">
                                <i class="fas fa-arrow-up"></i> +{{ $comparison['orders_change'] }}%
                            </span>
                        @elseif ($comparison['orders_change'] < 0)
                            <span class="text-red-600 font-medium">
                                <i class="fas fa-arrow-down"></i> {{ $comparison['orders_change'] }}%
                            </span>
                        @else
                            <span class="text-gray-600 font-medium">
                                <i class="fas fa-minus"></i> 0%
                            </span>
                        @endif
                        <span class="text-gray-600"> vs período anterior</span>
                    </div>
                </div>
            </div>

            <!-- Ticket Médio -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-yellow-500 rounded-md flex items-center justify-center">
                                <i class="fas fa-chart-line text-white text-sm"></i>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Ticket Médio</dt>
                                <dd class="text-lg font-medium text-gray-900">
                                    R$ {{ number_format($dailyReport['avg_order_value'], 2, ',', '.') }}
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-5 py-3">
                    <div class="text-sm">
                        @if ($comparison['avg_order_change'] > 0)
                            <span class="text-green-600 font-medium">
                                <i class="fas fa-arrow-up"></i> +{{ $comparison['avg_order_change'] }}%
                            </span>
                        @elseif ($comparison['avg_order_change'] < 0)
                            <span class="text-red-600 font-medium">
                                <i class="fas fa-arrow-down"></i> {{ $comparison['avg_order_change'] }}%
                            </span>
                        @else
                            <span class="text-gray-600 font-medium">
                                <i class="fas fa-minus"></i> 0%
                            </span>
                        @endif
                        <span class="text-gray-600"> vs período anterior</span>
                    </div>
                </div>
            </div>

            <!-- Lucro Estimado -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-purple-500 rounded-md flex items-center justify-center">
                                <i class="fas fa-coins text-white text-sm"></i>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Lucro Estimado</dt>
                                <dd class="text-lg font-medium text-gray-900">
                                    R$ {{ number_format($dailyReport['estimated_profit'], 2, ',', '.') }}
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-5 py-3">
                    <div class="text-sm">
                        <span class="text-gray-600">70% da receita bruta</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráficos -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Vendas da Semana -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Vendas da Semana</h3>
                <canvas id="weeklyChart" width="400" height="200"></canvas>
            </div>

            <!-- Vendas por Hora -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Vendas por Hora (Últimos 7 dias)</h3>
                <canvas id="hourlyChart" width="400" height="200"></canvas>
            </div>
        </div>

        <!-- Top Produtos -->
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Top 5 Produtos (Últimos 7 dias)</h3>
            @if (!empty($topProducts))
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Produto
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Quantidade
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Receita
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Pedidos
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($topProducts as $product)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            @if (!empty($product['product_image']))
                                                <img class="h-10 w-10 rounded-full object-cover mr-3" 
                                                     src="{{ $product['product_image'] }}" 
                                                     alt="{{ $product['product_name'] }}">
                                            @endif
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $product['product_name'] }}
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $product['total_quantity'] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        R$ {{ number_format($product['total_revenue'], 2, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $product['orders_count'] }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-gray-500 text-center py-8">Nenhum produto vendido no período.</p>
            @endif
        </div>
    </main>

    <script>
        // Dados da semana
        const weeklyData = @json($weeklyReport);
        const weeklyLabels = weeklyData.map(item => {
            const date = new Date(item.date);
            return date.toLocaleDateString('pt-BR', { weekday: 'short', day: '2-digit' });
        });
        const weeklyRevenue = weeklyData.map(item => parseFloat(item.gross_revenue));

        // Gráfico da semana
        const weeklyCtx = document.getElementById('weeklyChart').getContext('2d');
        new Chart(weeklyCtx, {
            type: 'line',
            data: {
                labels: weeklyLabels,
                datasets: [{
                    label: 'Receita (R$)',
                    data: weeklyRevenue,
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'R$ ' + value.toFixed(2);
                            }
                        }
                    }
                }
            }
        });

        // Dados por hora
        const hourlyData = @json($hourlyData);
        const hourlyLabels = [];
        const hourlyRevenue = [];
        
        // Preencher todas as horas (0-23)
        for (let i = 0; i < 24; i++) {
            hourlyLabels.push(i + 'h');
            const hourData = hourlyData.find(item => parseInt(item.hour) === i);
            hourlyRevenue.push(hourData ? parseFloat(hourData.total_revenue) : 0);
        }

        // Gráfico por hora
        const hourlyCtx = document.getElementById('hourlyChart').getContext('2d');
        new Chart(hourlyCtx, {
            type: 'bar',
            data: {
                labels: hourlyLabels,
                datasets: [{
                    label: 'Receita (R$)',
                    data: hourlyRevenue,
                    backgroundColor: 'rgba(34, 197, 94, 0.8)',
                    borderColor: 'rgb(34, 197, 94)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'R$ ' + value.toFixed(2);
                            }
                        }
                    }
                }
            }
        });

        function updateReports() {
            const selectedDate = document.getElementById('dateFilter').value;
            window.location.href = `/admin/relatorios?date=${selectedDate}`;
        }
    </script>
</body>
</html>