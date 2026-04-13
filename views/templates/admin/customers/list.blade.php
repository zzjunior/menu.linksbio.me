@extends('layouts.admin')

@section('title', 'Clientes')

@section('content')

<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
        <div>
            <h2 class="font-bold text-slate-800">Lista de Clientes</h2>
            <p class="text-xs text-slate-400 mt-0.5">Histórico de clientes cadastrados</p>
        </div>
        @if (!empty($clientes))
        <span class="text-sm text-slate-500 font-medium">{{ count($clientes) }} cliente(s)</span>
        @endif
    </div>

    @if (empty($clientes))
        <div class="text-center py-20">
            <i class="fas fa-users text-slate-300 text-6xl mb-4 block"></i>
            <p class="text-slate-400 text-sm">Nenhum cliente encontrado ainda.</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Cliente</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Telefone</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Email</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Endereço</th>
                        <th class="px-5 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">Pedidos</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Gasto</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Cadastro</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach ($clientes as $cliente)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="px-5 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-indigo-100 flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-user text-indigo-500 text-sm"></i>
                                </div>
                                <div class="text-sm font-semibold text-slate-800">{{ $cliente['name'] }}</div>
                            </div>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap">
                            <a href="https://wa.me/55{{ preg_replace('/\D/', '', $cliente['phone']) }}" target="_blank"
                               class="flex items-center gap-1.5 text-sm text-emerald-600 hover:text-emerald-700 font-medium">
                                <i class="fab fa-whatsapp"></i>
                                {{ $cliente['phone'] }}
                            </a>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap text-sm text-slate-500">{{ $cliente['email'] ?? '—' }}</td>
                        <td class="px-5 py-4 text-sm text-slate-500 max-w-xs truncate">{{ $cliente['address'] ?? '—' }}</td>
                        <td class="px-5 py-4 whitespace-nowrap text-center">
                            <span class="inline-flex px-2.5 py-1 text-xs font-semibold rounded-full bg-indigo-100 text-indigo-700">
                                {{ $cliente['total_orders'] ?? $cliente['orders_count'] ?? 0 }}
                            </span>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap text-sm font-semibold text-slate-700">
                            R$ {{ number_format($cliente['total_spent'] ?? 0, 2, ',', '.') }}
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap text-sm text-slate-400">
                            {{ date('d/m/Y', strtotime($cliente['created_at'])) }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
