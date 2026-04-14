@extends('layouts.admin')

@section('title', 'Todos os Pedidos')

@section('topbar-actions')
<a href="/admin/pedidos/novo"
   class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-xl text-sm font-semibold transition flex items-center gap-2 shadow-sm">
    <i class="fas fa-plus text-xs"></i>
    <span class="hidden sm:inline">Novo Pedido</span>
</a>
@endsection

@section('content')

<!-- Filters -->
<div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5 mb-5">
    <form method="GET" action="/admin/pedidos" class="flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-52">
            <label class="block text-xs font-semibold text-slate-600 mb-1">Buscar</label>
            <div class="relative">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text" name="search" value="{{ $search }}" placeholder="Nome, telefone ou ID..."
                       class="w-full pl-8 pr-3 py-2 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
            </div>
        </div>
        <div class="min-w-40">
            <label class="block text-xs font-semibold text-slate-600 mb-1">Status</label>
            <select name="status" class="w-full py-2 px-3 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                <option value="">Todos</option>
                <option value="pending"   {{ $status === 'pending'   ? 'selected' : '' }}>Pendente</option>
                <option value="confirmed" {{ $status === 'confirmed' ? 'selected' : '' }}>Confirmado</option>
                <option value="preparing" {{ $status === 'preparing' ? 'selected' : '' }}>Preparando</option>
                <option value="ready"     {{ $status === 'ready'     ? 'selected' : '' }}>Pronto</option>
                <option value="delivered" {{ $status === 'delivered' ? 'selected' : '' }}>Entregue</option>
                <option value="cancelled" {{ $status === 'cancelled' ? 'selected' : '' }}>Cancelado</option>
            </select>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-xl text-sm font-semibold transition flex items-center gap-2">
                <i class="fas fa-search text-xs"></i> Filtrar
            </button>
            <a href="/admin/pedidos" class="bg-slate-100 hover:bg-slate-200 text-slate-600 px-4 py-2 rounded-xl text-sm font-semibold transition flex items-center gap-2">
                <i class="fas fa-times text-xs"></i> Limpar
            </a>
        </div>
    </form>
</div>

@if (empty($orders))
    <div class="text-center py-20">
        <div class="text-6xl mb-4">📋</div>
        <h3 class="text-lg font-semibold text-slate-800 mb-2">Nenhum pedido encontrado</h3>
        <p class="text-slate-500 text-sm">
            @if (!empty($search) || !empty($status))
                Tente ajustar os filtros.
            @else
                Não há pedidos cadastrados ainda.
            @endif
        </p>
    </div>
@else

<!-- Desktop table -->
<div class="hidden md:block bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
    <table class="min-w-full">
        <thead class="bg-slate-50 border-b border-slate-100">
            <tr>
                <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">#</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Cliente</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Data</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Total</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Itens</th>
                <th class="px-5 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Ações</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-50">
            @foreach ($orders as $order)
            @php
                $sc = [
                    'pending'   => 'bg-amber-100 text-amber-700',
                    'confirmed' => 'bg-violet-100 text-violet-700',
                    'preparing' => 'bg-blue-100 text-blue-700',
                    'ready'     => 'bg-emerald-100 text-emerald-700',
                    'delivered' => 'bg-slate-100 text-slate-600',
                    'cancelled' => 'bg-red-100 text-red-600',
                ][$order['status']] ?? 'bg-gray-100 text-gray-600';
                $sl = [
                    'pending'=>'Pendente','confirmed'=>'Confirmado','preparing'=>'Preparando',
                    'ready'=>'Pronto','delivered'=>'Entregue','cancelled'=>'Cancelado',
                ][$order['status']] ?? ucfirst($order['status']);
            @endphp
            <tr class="hover:bg-slate-50 transition">
                <td class="px-5 py-4 text-sm font-bold text-slate-700">#{{ $order['daily_order_number'] ?? $order['id'] }}</td>
                <td class="px-5 py-4">
                    <div class="text-sm font-semibold text-slate-800">{{ $order['customer_name'] }}</div>
                    <div class="text-xs text-slate-400">{{ $order['customer_phone'] }}</div>
                </td>
                <td class="px-5 py-4 whitespace-nowrap">
                    <div class="text-sm text-slate-700">{{ date('d/m/Y', strtotime($order['created_at'])) }}</div>
                    <div class="text-xs text-slate-400">{{ date('H:i', strtotime($order['created_at'])) }}</div>
                </td>
                <td class="px-5 py-4 whitespace-nowrap">
                    <span class="inline-flex px-2.5 py-1 text-xs font-semibold rounded-full {{ $sc }}">{{ $sl }}</span>
                </td>
                <td class="px-5 py-4 whitespace-nowrap text-sm font-bold text-slate-800">
                    R$ {{ number_format($order['total_amount'], 2, ',', '.') }}
                </td>
                <td class="px-5 py-4 whitespace-nowrap text-sm text-slate-500">
                    {{ $order['items_count'] ?? 0 }} item(s)
                </td>
                <td class="px-5 py-4 whitespace-nowrap text-right">
                    <div class="flex items-center gap-2 justify-end">
                        <form method="POST" action="/admin/pedidos/{{ $order['id'] }}/status" class="inline">
                            <select name="status" onchange="this.form.submit()"
                                    class="text-xs border border-slate-200 rounded-lg px-2 py-1.5 focus:outline-none focus:ring-2 focus:ring-indigo-400 bg-white">
                                <option value="pending"   {{ $order['status']==='pending'   ? 'selected' : '' }}>Pendente</option>
                                <option value="confirmed" {{ $order['status']==='confirmed' ? 'selected' : '' }}>Confirmado</option>
                                <option value="preparing" {{ $order['status']==='preparing' ? 'selected' : '' }}>Preparando</option>
                                <option value="ready"     {{ $order['status']==='ready'     ? 'selected' : '' }}>Pronto</option>
                                <option value="delivered" {{ $order['status']==='delivered' ? 'selected' : '' }}>Entregue</option>
                                <option value="cancelled" {{ $order['status']==='cancelled' ? 'selected' : '' }}>Cancelado</option>
                            </select>
                        </form>
                        <button onclick="window.open('/admin/print-order-pdf/{{ $order['id'] }}','_blank')"
                                class="bg-emerald-50 hover:bg-emerald-100 text-emerald-700 px-2.5 py-1.5 rounded-lg text-xs font-medium transition">
                            <i class="fas fa-print"></i>
                        </button>
                        <a href="/admin/pedidos/{{ $order['id'] }}" target="_blank"
                           class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 rounded-lg text-xs font-semibold transition flex items-center gap-1">
                            <i class="fas fa-eye"></i> Ver
                        </a>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Mobile cards -->
<div class="md:hidden space-y-3">
    @foreach ($orders as $order)
    @php
        $sc = ['pending'=>'bg-amber-100 text-amber-700','confirmed'=>'bg-violet-100 text-violet-700',
               'preparing'=>'bg-blue-100 text-blue-700','ready'=>'bg-emerald-100 text-emerald-700',
               'delivered'=>'bg-slate-100 text-slate-600','cancelled'=>'bg-red-100 text-red-600'][$order['status']] ?? 'bg-gray-100 text-gray-600';
        $sl = ['pending'=>'Pendente','confirmed'=>'Confirmado','preparing'=>'Preparando',
               'ready'=>'Pronto','delivered'=>'Entregue','cancelled'=>'Cancelado'][$order['status']] ?? ucfirst($order['status']);
    @endphp
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4">
        <div class="flex justify-between items-start mb-3">
            <div>
                <span class="font-bold text-slate-800">#{{ $order['daily_order_number'] ?? $order['id'] }}</span>
                <span class="text-xs text-slate-400 ml-2">{{ date('d/m/Y H:i', strtotime($order['created_at'])) }}</span>
            </div>
            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $sc }}">{{ $sl }}</span>
        </div>
        <div class="text-sm font-semibold text-slate-800 mb-0.5">{{ $order['customer_name'] }}</div>
        <div class="text-xs text-slate-400 mb-3">{{ $order['customer_phone'] }}</div>
        <div class="flex justify-between items-center">
            <span class="text-sm font-bold text-slate-800">R$ {{ number_format($order['total_amount'], 2, ',', '.') }}</span>
            <div class="flex gap-2">
                <form method="POST" action="/admin/pedidos/{{ $order['id'] }}/status">
                    <select name="status" onchange="this.form.submit()" class="text-xs border border-slate-200 rounded-lg px-2 py-1.5">
                        <option value="pending"   {{ $order['status']==='pending'   ? 'selected' : '' }}>Pendente</option>
                        <option value="confirmed" {{ $order['status']==='confirmed' ? 'selected' : '' }}>Confirmado</option>
                        <option value="preparing" {{ $order['status']==='preparing' ? 'selected' : '' }}>Preparando</option>
                        <option value="ready"     {{ $order['status']==='ready'     ? 'selected' : '' }}>Pronto</option>
                        <option value="delivered" {{ $order['status']==='delivered' ? 'selected' : '' }}>Entregue</option>
                        <option value="cancelled" {{ $order['status']==='cancelled' ? 'selected' : '' }}>Cancelado</option>
                    </select>
                </form>
                <a href="/admin/pedidos/{{ $order['id'] }}" target="_blank"
                   class="bg-indigo-600 text-white px-3 py-1.5 rounded-lg text-xs font-semibold">
                    <i class="fas fa-eye"></i>
                </a>
            </div>
        </div>
    </div>
    @endforeach
</div>

<!-- Pagination -->
@if ($totalPages > 1)
<div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4 mt-4">
    <div class="flex items-center justify-between">
        <div class="text-sm text-slate-500">
            Página {{ $currentPage }} de {{ $totalPages }} ({{ $totalOrders }} pedidos)
        </div>
        <div class="flex gap-2">
            @if ($currentPage > 1)
                <a href="?page={{ $currentPage-1 }}&search={{ urlencode($search) }}&status={{ urlencode($status) }}"
                   class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-sm font-medium transition">← Anterior</a>
            @endif
            @for ($i = max(1, $currentPage-2); $i <= min($totalPages, $currentPage+2); $i++)
                <a href="?page={{ $i }}&search={{ urlencode($search) }}&status={{ urlencode($status) }}"
                   class="px-3 py-2 rounded-xl text-sm font-medium transition {{ $i == $currentPage ? 'bg-indigo-600 text-white' : 'bg-slate-100 hover:bg-slate-200 text-slate-600' }}">
                    {{ $i }}
                </a>
            @endfor
            @if ($currentPage < $totalPages)
                <a href="?page={{ $currentPage+1 }}&search={{ urlencode($search) }}&status={{ urlencode($status) }}"
                   class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-sm font-medium transition">Próxima →</a>
            @endif
        </div>
    </div>
</div>
@endif

@endif
@endsection
