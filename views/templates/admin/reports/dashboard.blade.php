@extends('layouts.admin')

@section('title', 'Relatórios Financeiros')

@section('head')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endsection

@section('topbar-actions')
<input type="date" id="dateFilter" value="{{ $selectedDate }}"
       class="border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400"
       onchange="updateReports()">
<a href="/admin/relatorios/diario"
   class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-xl text-sm font-semibold transition flex items-center gap-2">
    <i class="fas fa-calendar-day text-xs"></i> Resumo do Dia
</a>
@endsection

@section('content')

<!-- Summary Cards -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    @php
    $summaryCards = [
        ['Receita Hoje',    'fas fa-dollar-sign', '#10b981', '#059669', 'R$ ' . number_format($dailyReport['gross_revenue'],2,',','.'),$comparison['revenue_change']],
        ['Pedidos Hoje',    'fas fa-receipt',     '#6366f1', '#4f46e5', $dailyReport['total_orders'], $comparison['orders_change']],
        ['Ticket Médio',    'fas fa-chart-line',  '#f59e0b', '#d97706', 'R$ ' . number_format($dailyReport['avg_order_value'],2,',','.'), $comparison['avg_order_change']],
        ['Lucro Estimado',  'fas fa-coins',       '#8b5cf6', '#7c3aed', 'R$ ' . number_format($dailyReport['estimated_profit'],2,',','.'), null],
    ];
    @endphp
    @foreach ($summaryCards as $card)
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5">
        <div class="flex items-start gap-3">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
                 style="background:linear-gradient(135deg,{{ $card[2] }},{{ $card[3] }})">
                <i class="{{ $card[1] }} text-white text-sm"></i>
            </div>
            <div>
                <div class="text-xs text-slate-500 font-medium mb-0.5">{{ $card[0] }}</div>
                <div class="text-xl font-bold text-slate-800">{{ $card[4] }}</div>
                @if ($card[5] !== null)
                    @if ($card[5] > 0)
                        <div class="text-xs text-emerald-600 font-semibold mt-0.5"><i class="fas fa-arrow-up"></i> +{{ $card[5] }}% vs anterior</div>
                    @elseif ($card[5] < 0)
                        <div class="text-xs text-red-500 font-semibold mt-0.5"><i class="fas fa-arrow-down"></i> {{ $card[5] }}% vs anterior</div>
                    @else
                        <div class="text-xs text-slate-400 mt-0.5">0% vs anterior</div>
                    @endif
                @else
                    <div class="text-xs text-slate-400 mt-0.5">70% da receita bruta</div>
                @endif
            </div>
        </div>
    </div>
    @endforeach
</div>

<!-- Charts -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-6">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
        <h3 class="text-sm font-bold text-slate-800 mb-4">Vendas da Semana</h3>
        <canvas id="weeklyChart" height="200"></canvas>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
        <h3 class="text-sm font-bold text-slate-800 mb-4">Vendas por Hora (Últimos 7 dias)</h3>
        <canvas id="hourlyChart" height="200"></canvas>
    </div>
</div>

<!-- Top Products -->
<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-100">
        <h3 class="text-sm font-bold text-slate-800">Top 5 Produtos (Últimos 7 dias)</h3>
    </div>
    @if (!empty($topProducts))
    <table class="min-w-full">
        <thead class="bg-slate-50 border-b border-slate-100">
            <tr>
                <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Produto</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Quantidade</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Receita</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Pedidos</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-50">
            @foreach ($topProducts as $product)
            <tr class="hover:bg-slate-50 transition">
                <td class="px-5 py-4">
                    <div class="flex items-center gap-3">
                        @if (!empty($product['product_image']))
                            <img class="w-9 h-9 rounded-xl object-cover" src="{{ $product['product_image'] }}" alt="">
                        @endif
                        <span class="text-sm font-semibold text-slate-800">{{ $product['product_name'] }}</span>
                    </div>
                </td>
                <td class="px-5 py-4 text-sm text-slate-700">{{ $product['total_quantity'] }}</td>
                <td class="px-5 py-4 text-sm font-semibold text-slate-800">R$ {{ number_format($product['total_revenue'], 2, ',', '.') }}</td>
                <td class="px-5 py-4 text-sm text-slate-700">{{ $product['orders_count'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div class="py-12 text-center text-slate-400 text-sm">Nenhum produto vendido no período.</div>
    @endif
</div>
@endsection

@section('scripts')
<script>
const weeklyData = @json($weeklyReport);
const weeklyLabels = weeklyData.map(d => new Date(d.date).toLocaleDateString('pt-BR',{weekday:'short',day:'2-digit'}));
const weeklyRevenue = weeklyData.map(d => parseFloat(d.gross_revenue));

new Chart(document.getElementById('weeklyChart').getContext('2d'), {
    type: 'line',
    data: { labels: weeklyLabels, datasets: [{ label: 'Receita (R$)', data: weeklyRevenue,
        borderColor: '#6366f1', backgroundColor: 'rgba(99,102,241,.1)', tension: .3, fill: true }] },
    options: { responsive: true, plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true, ticks: { callback: v => 'R$ ' + v.toFixed(2) } } } }
});

const hourlyData = @json($hourlyData);
const hLabels = Array.from({length:24},(_,i)=>i+'h');
const hRevenue = hLabels.map((_,i) => { const d = hourlyData.find(x => parseInt(x.hour)===i); return d ? parseFloat(d.total_revenue) : 0; });

new Chart(document.getElementById('hourlyChart').getContext('2d'), {
    type: 'bar',
    data: { labels: hLabels, datasets: [{ label: 'Receita (R$)', data: hRevenue,
        backgroundColor: 'rgba(16,185,129,.8)', borderColor: '#10b981', borderWidth: 1 }] },
    options: { responsive: true, plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true, ticks: { callback: v => 'R$'+v } } } }
});

function updateReports() {
    window.location.href = '/admin/relatorios?date=' + document.getElementById('dateFilter').value;
}
</script>
@endsection