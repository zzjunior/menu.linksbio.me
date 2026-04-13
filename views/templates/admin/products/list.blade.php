@extends('layouts.admin')

@section('title', $pageTitle)

@section('topbar-actions')
<a href="/admin/products/new"
   class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-xl text-sm font-semibold transition flex items-center gap-2 shadow-sm">
    <i class="fas fa-plus text-xs"></i>
    <span class="hidden sm:inline">Novo Produto</span>
</a>
@endsection

@section('content')

@if (empty($products))
<div class="text-center py-20">
    <div class="text-6xl mb-4">📦</div>
    <h3 class="text-lg font-semibold text-slate-800 mb-2">Nenhum produto cadastrado</h3>
    <p class="text-slate-500 text-sm mb-6">Comece criando seu primeiro produto.</p>
    <a href="/admin/products/new" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-xl font-semibold text-sm transition">
        <i class="fas fa-plus mr-2"></i>Criar Produto
    </a>
</div>
@else

<!-- Desktop table -->
<div class="hidden md:block bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
    <table class="min-w-full">
        <thead class="bg-slate-50 border-b border-slate-100">
            <tr>
                <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Produto</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Categoria</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Preço</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                <th class="px-5 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Ações</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-50">
            @foreach ($products as $product)
            <tr class="hover:bg-slate-50 transition {{ $product['active'] ? '' : 'opacity-60' }}">
                <td class="px-5 py-4">
                    <div class="flex items-center gap-3">
                        @if ($product['image_url'])
                            <img src="{{ $product['image_url'] }}" alt="{{ $product['name'] }}"
                                 class="w-12 h-12 rounded-xl object-cover flex-shrink-0">
                        @else
                            <div class="w-12 h-12 rounded-xl bg-slate-100 flex items-center justify-center flex-shrink-0 text-xl">🍇</div>
                        @endif
                        <div>
                            <div class="text-sm font-semibold text-slate-800">
                                {{ $product['name'] }}
                                @if ($product['size_ml'])<span class="text-slate-400 font-normal">({{ $product['size_ml'] }}ml)</span>@endif
                            </div>
                            @if ($product['description'])
                                <div class="text-xs text-slate-400 truncate max-w-xs">{{ $product['description'] }}</div>
                            @endif
                        </div>
                    </div>
                </td>
                <td class="px-5 py-4 whitespace-nowrap">
                    <span class="inline-flex px-2.5 py-1 text-xs font-semibold rounded-full bg-indigo-50 text-indigo-700">
                        {{ $product['category_name'] ?? 'Sem categoria' }}
                    </span>
                </td>
                <td class="px-5 py-4 whitespace-nowrap text-sm font-bold text-slate-800">
                    R$ {{ number_format($product['price'], 2, ',', '.') }}
                </td>
                <td class="px-5 py-4 whitespace-nowrap">
                    @if ($product['active'])
                        <span class="inline-flex px-2.5 py-1 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-700">Ativo</span>
                    @else
                        <span class="inline-flex px-2.5 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-600">Inativo</span>
                    @endif
                </td>
                <td class="px-5 py-4 whitespace-nowrap text-right">
                    <div class="flex items-center gap-2 justify-end">
                        <a href="/admin/products/{{ $product['id'] }}/edit"
                           class="text-indigo-600 hover:text-indigo-800 text-sm font-medium px-3 py-1.5 rounded-lg hover:bg-indigo-50 transition">
                            <i class="fas fa-edit mr-1"></i>Editar
                        </a>
                        <form method="POST" action="/admin/products/{{ $product['id'] }}/delete" class="inline"
                              onsubmit="return confirm('Remover este produto?')">
                            @csrf
                            <button type="submit" class="text-red-500 hover:text-red-700 text-sm font-medium px-3 py-1.5 rounded-lg hover:bg-red-50 transition">
                                <i class="fas fa-trash mr-1"></i>Remover
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Mobile grid -->
<div class="md:hidden grid grid-cols-1 gap-3">
    @foreach ($products as $product)
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4 flex items-center gap-3 {{ $product['active'] ? '' : 'opacity-60' }}">
        @if ($product['image_url'])
            <img src="{{ $product['image_url'] }}" class="w-14 h-14 rounded-xl object-cover flex-shrink-0">
        @else
            <div class="w-14 h-14 rounded-xl bg-slate-100 flex items-center justify-center flex-shrink-0 text-2xl">🍇</div>
        @endif
        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2">
                <span class="font-semibold text-slate-800 text-sm">{{ $product['name'] }}</span>
                <span class="text-xs {{ $product['active'] ? 'text-emerald-600' : 'text-red-500' }}">
                    {{ $product['active'] ? '● Ativo' : '● Inativo' }}
                </span>
            </div>
            <div class="text-xs text-slate-400 mb-1">{{ $product['category_name'] ?? 'Sem categoria' }}</div>
            <div class="text-sm font-bold text-slate-800">R$ {{ number_format($product['price'], 2, ',', '.') }}</div>
        </div>
        <div class="flex flex-col gap-1.5">
            <a href="/admin/products/{{ $product['id'] }}/edit" class="text-xs bg-indigo-50 text-indigo-600 px-3 py-1.5 rounded-lg font-medium">Editar</a>
            <form method="POST" action="/admin/products/{{ $product['id'] }}/delete" onsubmit="return confirm('Remover?')">
                @csrf
                <button type="submit" class="text-xs bg-red-50 text-red-500 px-3 py-1.5 rounded-lg font-medium w-full">Remover</button>
            </form>
        </div>
    </div>
    @endforeach
</div>

@endif
@endsection
