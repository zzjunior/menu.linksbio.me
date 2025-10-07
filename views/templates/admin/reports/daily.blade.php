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
                    <a href="/admin/relatorios" class="text-gray-500 hover:text-gray-700">← Voltar aos Relatórios</a>
                </div>
                <div class="flex items-center space-x-4">
                    <input type="date" id="dateFilter" value="{{ $selectedDate }}" 
                           class="border border-gray-300 rounded-md px-3 py-2 text-sm"
                           onchange="updateReport()">
                </div>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <!-- Resumo do Dia -->
        <div class="bg-white shadow rounded-lg p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-900 mb-6">Resumo do Dia</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="text-center">
                    <div class="text-3xl font-bold text-green-600">{{ $report['total_orders'] }}</div>
                    <div class="text-sm text-gray-500">Total de Pedidos</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-blue-600">
                        R$ {{ number_format($report['gross_revenue'], 2, ',', '.') }}
                    </div>
                    <div class="text-sm text-gray-500">Receita Bruta</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-purple-600">
                        R$ {{ number_format($report['estimated_profit'], 2, ',', '.') }}
                    </div>
                    <div class="text-sm text-gray-500">Lucro Estimado</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-yellow-600">{{ $report['unique_customers'] }}</div>
                    <div class="text-sm text-gray-500">Clientes Únicos</div>
                </div>
            </div>
        </div>

        <!-- Status dos Pedidos -->
        <div class="bg-white shadow rounded-lg p-6 mb-8">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Breakdown por Status</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-green-50 p-4 rounded-lg">
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-green-500 rounded-full mr-2"></div>
                        <span class="text-sm font-medium text-green-800">Concluídos</span>
                    </div>
                    <div class="mt-2 text-xl font-bold text-green-600">
                        R$ {{ number_format($report['completed_revenue'], 2, ',', '.') }}
                    </div>
                </div>
                <div class="bg-yellow-50 p-4 rounded-lg">
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-yellow-500 rounded-full mr-2"></div>
                        <span class="text-sm font-medium text-yellow-800">Pendentes</span>
                    </div>
                    <div class="mt-2 text-xl font-bold text-yellow-600">
                        R$ {{ number_format($report['pending_revenue'], 2, ',', '.') }}
                    </div>
                </div>
                <div class="bg-red-50 p-4 rounded-lg">
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-red-500 rounded-full mr-2"></div>
                        <span class="text-sm font-medium text-red-800">Cancelados</span>
                    </div>
                    <div class="mt-2 text-xl font-bold text-red-600">
                        R$ {{ number_format($report['cancelled_revenue'], 2, ',', '.') }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Comparação com Dia Anterior -->
        @if (isset($comparison))
        <div class="bg-white shadow rounded-lg p-6 mb-8">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Comparação com Dia Anterior</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="text-center">
                    <div class="text-sm text-gray-500">Receita</div>
                    <div class="flex items-center justify-center mt-1">
                        @if ($comparison['revenue_change'] > 0)
                            <i class="fas fa-arrow-up text-green-500 mr-1"></i>
                            <span class="text-green-600 font-medium">+{{ $comparison['revenue_change'] }}%</span>
                        @elseif ($comparison['revenue_change'] < 0)
                            <i class="fas fa-arrow-down text-red-500 mr-1"></i>
                            <span class="text-red-600 font-medium">{{ $comparison['revenue_change'] }}%</span>
                        @else
                            <i class="fas fa-minus text-gray-500 mr-1"></i>
                            <span class="text-gray-600 font-medium">0%</span>
                        @endif
                    </div>
                </div>
                <div class="text-center">
                    <div class="text-sm text-gray-500">Pedidos</div>
                    <div class="flex items-center justify-center mt-1">
                        @if ($comparison['orders_change'] > 0)
                            <i class="fas fa-arrow-up text-green-500 mr-1"></i>
                            <span class="text-green-600 font-medium">+{{ $comparison['orders_change'] }}%</span>
                        @elseif ($comparison['orders_change'] < 0)
                            <i class="fas fa-arrow-down text-red-500 mr-1"></i>
                            <span class="text-red-600 font-medium">{{ $comparison['orders_change'] }}%</span>
                        @else
                            <i class="fas fa-minus text-gray-500 mr-1"></i>
                            <span class="text-gray-600 font-medium">0%</span>
                        @endif
                    </div>
                </div>
                <div class="text-center">
                    <div class="text-sm text-gray-500">Ticket Médio</div>
                    <div class="flex items-center justify-center mt-1">
                        @if ($comparison['avg_order_change'] > 0)
                            <i class="fas fa-arrow-up text-green-500 mr-1"></i>
                            <span class="text-green-600 font-medium">+{{ $comparison['avg_order_change'] }}%</span>
                        @elseif ($comparison['avg_order_change'] < 0)
                            <i class="fas fa-arrow-down text-red-500 mr-1"></i>
                            <span class="text-red-600 font-medium">{{ $comparison['avg_order_change'] }}%</span>
                        @else
                            <i class="fas fa-minus text-gray-500 mr-1"></i>
                            <span class="text-gray-600 font-medium">0%</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Gráficos -->
        <div class="grid grid-cols-1 lg:grid-cols-1 gap-6 mb-8">
            <!-- Vendas por Hora do Dia -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Vendas por Hora</h3>
                @if (!empty($hourlyData))
                    <canvas id="hourlyChart" width="400" height="200"></canvas>
                @else
                    <p class="text-gray-500 text-center py-8">Nenhuma venda registrada neste dia.</p>
                @endif
            </div>
        </div>

        <!-- Top Produtos do Dia -->
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Produtos Vendidos</h3>
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
                                    Preço Médio
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
                                        R$ {{ number_format($product['avg_price'], 2, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-gray-500 text-center py-8">Nenhum produto vendido neste dia.</p>
            @endif
        </div>
    </main>

    <script>
        @if (!empty($hourlyData))
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
        @endif

        function updateReport() {
            const selectedDate = document.getElementById('dateFilter').value;
            window.location.href = `/admin/relatorios/diario?date=${selectedDate}`;
        }
    </script>
</body>
</html>