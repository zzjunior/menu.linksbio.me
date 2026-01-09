@extends('layouts.customer')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Acompanhar Pedidos</h1>
        <p class="text-gray-600 mt-2">{{ $store['store_name'] }}</p>
    </div>

    @if(empty($orders))
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 text-center">
            <svg class="w-16 h-16 mx-auto text-yellow-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <h3 class="text-lg font-semibold text-gray-800 mb-2">Nenhum pedido encontrado</h3>
            <p class="text-gray-600 mb-4">Não há pedidos para esta loja ainda.</p>
            <a href="/{{ $store_slug }}" class="inline-block bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                Ver Cardápio
            </a>
        </div>
    @else
        <div class="space-y-4">
            @foreach($orders as $order)
                <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="text-lg font-bold text-gray-800">Pedido #{{ $order['id'] }}</h3>
                            <p class="text-sm text-gray-600">{{ date('d/m/Y H:i', strtotime($order['created_at'])) }}</p>
                        </div>
                        <span class="px-3 py-1 rounded-full text-sm font-semibold
                            @if($order['status'] === 'pendente') bg-yellow-100 text-yellow-800
                            @elseif($order['status'] === 'preparando') bg-blue-100 text-blue-800
                            @elseif($order['status'] === 'pronto') bg-green-100 text-green-800
                            @elseif($order['status'] === 'entregue') bg-gray-100 text-gray-800
                            @else bg-red-100 text-red-800
                            @endif">
                            {{ ucfirst($order['status']) }}
                        </span>
                    </div>

                    <div class="border-t border-gray-200 pt-4">
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <p class="text-gray-600">Cliente</p>
                                <p class="font-semibold">{{ $order['customer_name'] }}</p>
                            </div>
                            <div>
                                <p class="text-gray-600">Telefone</p>
                                <p class="font-semibold">{{ $order['customer_phone'] }}</p>
                            </div>
                            <div>
                                <p class="text-gray-600">Tipo</p>
                                <p class="font-semibold">{{ $order['order_type'] === 'delivery' ? 'Delivery' : 'Retirada' }}</p>
                            </div>
                            <div>
                                <p class="text-gray-600">Pagamento</p>
                                <p class="font-semibold">{{ ucfirst($order['payment_method']) }}</p>
                            </div>
                        </div>

                        @if($order['customer_address'])
                        <div class="mt-3">
                            <p class="text-gray-600 text-sm">Endereço</p>
                            <p class="font-semibold text-sm">{{ $order['customer_address'] }}</p>
                        </div>
                        @endif

                        @if($order['notes'])
                        <div class="mt-3">
                            <p class="text-gray-600 text-sm">Observações</p>
                            <p class="text-sm">{{ $order['notes'] }}</p>
                        </div>
                        @endif

                        @if(!empty($order['items']))
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <p class="text-gray-700 font-semibold mb-3">Itens do Pedido:</p>
                            <div class="space-y-3">
                                @foreach($order['items'] as $item)
                                    <div class="bg-gray-50 rounded-lg p-3">
                                        <div class="flex justify-between items-start">
                                            <div class="flex-1">
                                                <p class="font-semibold text-gray-800">
                                                    {{ $item['quantity'] }}x {{ $item['product_name'] }}
                                                    @if($item['size'])
                                                        <span class="text-sm text-gray-600">({{ $item['size'] }})</span>
                                                    @endif
                                                </p>
                                                
                                                @if(!empty($item['ingredients']))
                                                    <div class="mt-1 ml-4 text-sm text-gray-600">
                                                        @foreach($item['ingredients'] as $ingredient)
                                                            <p>
                                                                @if(!empty($ingredient['type']))
                                                                    @if($ingredient['type'] === 'remove')
                                                                        <span class="text-red-600 font-semibold">Remover:</span>
                                                                    @elseif($ingredient['type'] === 'add')
                                                                        <span class="text-green-600 font-semibold">+</span>
                                                                    @else
                                                                        <span class="text-blue-600 font-semibold">{{ $ingredient['type'] }}:</span>
                                                                    @endif
                                                                @else
                                                                    <span class="text-green-600 font-semibold">+</span>
                                                                @endif
                                                                {{ $ingredient['quantity'] }}x {{ $ingredient['ingredient_name'] ?? 'Adicional' }}
                                                            </p>
                                                        @endforeach
                                                    </div>
                                                @endif
                                                
                                                @if($item['notes'])
                                                    <p class="mt-1 text-sm text-gray-600 italic">Obs: {{ $item['notes'] }}</p>
                                                @endif
                                            </div>
                                            <div class="text-right ml-4">
                                                <p class="font-semibold text-gray-800">R$ {{ number_format($item['unit_price'], 2, ',', '.') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <p class="text-lg font-bold text-gray-800">
                                Total: R$ {{ number_format($order['total_amount'], 2, ',', '.') }}
                            </p>
                        </div>

                        <div class="mt-4 flex gap-3">
                            <a href="/querodenovo/{{ $order['id'] }}" 
                               class="flex-1 bg-green-600 text-white text-center px-4 py-2 rounded-lg hover:bg-green-700 transition">
                                <i class="fas fa-redo mr-2"></i>Repetir Pedido
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <div class="mt-6 text-center">
        <a href="/{{ $store_slug }}" class="text-blue-600 hover:text-blue-800 font-semibold">
            <i class="fas fa-arrow-left mr-2"></i>Voltar ao Cardápio
        </a>
    </div>
</div>
@endsection
