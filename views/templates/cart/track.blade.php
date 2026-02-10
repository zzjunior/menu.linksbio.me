@extends('layouts.customer')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header da Loja com Banner -->
    @if(!empty($store['store_banner']))
        <!-- Banner Background Hero Section -->
        <div class="relative h-36 bg-cover bg-center bg-gray-100 overflow-hidden rounded-2xl mb-8" 
             style="background-image: linear-gradient(135deg, rgba(139, 92, 246, 0.8), rgba(236, 72, 153, 0.7)), url('{{ $store['store_banner'] }}');">
            <!-- Gradient Overlay -->
            <div class="absolute inset-0 bg-gradient-to-r from-primary/90 to-accent/80"></div>
            
            <!-- Store Info Content -->
            <div class="relative h-full flex items-center justify-center px-8 py-8">
                <div class="text-center text-white">
                    @if(!empty($store['store_logo']))
                        <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-white/20 backdrop-blur-sm border-2 border-white/30 overflow-hidden shadow-lg">
                            <img src="{{ $store['store_logo'] }}" 
                                 alt="Logo {{ $store['store_name'] }}" 
                                 class="w-full h-full object-cover">
                        </div>
                    @endif
                    <h1 class="text-2xl font-bold mb-3">{{ $store['store_name'] }}</h1>
                    <div class="flex justify-center items-center space-x-4 text-sm text-white/90">
                        <span><i class="fas fa-receipt mr-1"></i>Acompanhar Pedidos</span>
                        @if(!empty($store['whatsapp']))
                            <span><i class="fab fa-whatsapp mr-1"></i>{{ $store['whatsapp'] }}</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- Header sem banner -->
        <div class="text-center mb-8 bg-gradient-to-r from-primary to-secondary text-white rounded-2xl p-10">
            @if(!empty($store['store_logo']))
                <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-white/20 border-2 border-white/30 overflow-hidden shadow-lg">
                    <img src="{{ $store['store_logo'] }}" 
                         alt="Logo {{ $store['store_name'] }}" 
                         class="w-full h-full object-cover">
                </div>
            @else
                <div class="w-16 h-16 mx-auto bg-white/20 rounded-full flex items-center justify-center mb-4 shadow-lg">
                    <i class="fas fa-utensils text-white text-2xl"></i>
                </div>
            @endif
            <h1 class="text-2xl font-bold mb-3">{{ $store['store_name'] }}</h1>
            <div class="flex justify-center items-center space-x-4 text-sm text-white/90">
                <span><i class="fas fa-receipt mr-1"></i>Acompanhar Pedidos</span>
                @if(!empty($store['whatsapp']))
                    <span><i class="fab fa-whatsapp mr-1"></i>{{ $store['whatsapp'] }}</span>
                @endif
            </div>
        </div>
    @endif

    @if(isset($show_phone_form) && $show_phone_form)
        <!-- Formulário para inserir telefone -->
        <div class="bg-white rounded-xl shadow-xl p-8 border border-gray-100">
            <div class="text-center mb-8">
                <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-800 mb-2">Acesse seus pedidos</h3>
                <p class="text-gray-600 mb-2">Informe seu telefone para visualizar o histórico de pedidos</p>
                <div class="flex justify-center items-center space-x-2 text-sm text-gray-500">
                    <i class="fas fa-shield-alt"></i>
                    <span>Suas informações estão seguras</span>
                </div>
            </div>

            @if($errors = $_GET['error'] ?? null)
                @if($errors === 'phone_required')
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded mb-4">
                        Por favor, digite seu número de telefone.
                    </div>
                @elseif($errors === 'phone_invalid')
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded mb-4">
                        Por favor, digite um número de telefone válido.
                    </div>
                @elseif($errors === 'no_orders_found')
                    <div class="bg-yellow-50 border border-yellow-200 text-yellow-700 px-4 py-3 rounded mb-4">
                        Não encontramos pedidos com este número de telefone nesta loja.
                        <br><small>Tente digitar o telefone no formato que você usou no pedido (com ou sem caracteres especiais).</small>
                    </div>
                @endif
            @endif

            <form action="/order/{{ $store_slug }}/validate-phone" method="POST" class="space-y-6">
                <div>
                    <label for="customer_phone" class="block text-sm font-semibold text-gray-700 mb-3">
                        <i class="fas fa-mobile-alt mr-2 text-blue-600"></i>
                        Número do Telefone
                    </label>
                    <div class="relative">
                        <input 
                            type="tel" 
                            id="customer_phone" 
                            name="customer_phone" 
                            class="w-full px-4 py-4 pl-12 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition text-lg"
                            placeholder="(85) 99999-9999"
                            required
                            autocomplete="tel"
                        >
                        <div class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400">
                            <i class="fas fa-phone text-lg"></i>
                        </div>
                    </div>
                    <p class="text-sm text-gray-500 mt-2 flex items-center">
                        <i class="fas fa-info-circle mr-1"></i>
                        Use o mesmo telefone que você cadastrou nos pedidos
                    </p>
                </div>

                <div class="flex flex-col sm:flex-row gap-3">
                    <button 
                        type="submit" 
                        class="flex-1 bg-gradient-to-r from-blue-600 to-purple-600 text-white px-8 py-4 rounded-xl hover:from-blue-700 hover:to-purple-700 transition font-semibold text-lg shadow-lg"
                    >
                        <i class="fas fa-search mr-2"></i>Acessar meus pedidos
                    </button>
                    <a 
                        href="/{{ $store_slug }}" 
                        class="flex-1 bg-gray-100 text-gray-700 text-center px-8 py-4 rounded-xl hover:bg-gray-200 transition font-semibold text-lg border-2 border-gray-200"
                    >
                        <i class="fas fa-store mr-2"></i>Ver Cardápio
                    </a>
                </div>
            </form>
        </div>
    @else
        <!-- Informações do Cliente Logado -->
        @if(isset($customer_data) && $customer_data)
            <div class="bg-gradient-to-r from-blue-50 to-purple-50 border-l-4 border-blue-500 rounded-lg p-6 mb-6 shadow-sm">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <div class="flex items-center mb-3">
                            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mr-4">
                                <i class="fas fa-user text-blue-600 text-lg"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800">{{ $customer_data['name'] }}</h3>
                                <p class="text-sm text-gray-600">Cliente da {{ $store['store_name'] }}</p>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            <div>
                                <p class="text-gray-600 mb-1"><i class="fas fa-phone mr-2"></i>Telefone</p>
                                <p class="font-semibold text-gray-800">{{ $customer_data['phone'] }}</p>
                            </div>
                            @if($customer_data['address'])
                            <div>
                                <p class="text-gray-600 mb-1"><i class="fas fa-map-marker-alt mr-2"></i>Endereço</p>
                                <p class="font-semibold text-gray-800">{{ $customer_data['address'] }}</p>
                            </div>
                            @endif
                            <div>
                                <p class="text-gray-600 mb-1"><i class="fas fa-shopping-bag mr-2"></i>Total de Pedidos</p>
                                <p class="font-semibold text-gray-800">{{ $customer_data['total_orders'] }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="ml-4">
                        <a href="/order/{{ $store_slug }}/logout" 
                           class="inline-flex items-center px-4 py-2 bg-white border border-blue-300 rounded-lg text-blue-700 hover:bg-blue-50 transition font-medium text-sm shadow-sm">
                            <i class="fas fa-sign-out-alt mr-2"></i>
                            Sair
                        </a>
                    </div>
                </div>
            </div>
        @endif

        <!-- Lista de pedidos -->
        @if(empty($orders))
            <div class="bg-gradient-to-br from-yellow-50 to-orange-50 border-l-4 border-yellow-400 rounded-xl p-8 text-center shadow-sm">
                <div class="w-20 h-20 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-3">Primeiro pedido?</h3>
                <p class="text-gray-600 mb-6 max-w-md mx-auto">Você ainda não fez nenhum pedido nesta loja com este telefone. Que tal conhecer nosso delicioso cardápio?</p>
                <div class="space-y-3">
                    <a href="/{{ $store_slug }}" class="inline-block bg-gradient-to-r from-blue-600 to-purple-600 text-white px-8 py-3 rounded-xl hover:from-blue-700 hover:to-purple-700 transition font-semibold shadow-lg">
                        <i class="fas fa-utensils mr-2"></i>Ver Cardápio
                    </a>
                    <p class="text-sm text-gray-500">
                        <i class="fas fa-gift mr-1"></i>
                        Ofertas especiais para novos clientes!
                    </p>
                </div>
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
                            <a href="/querodenovo/{{ $store_slug }}/{{ $order['id'] }}" 
                               class="flex-1 bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600 text-white text-center px-4 py-2 rounded-lg transition font-semibold">
                                <i class="fas fa-redo mr-2"></i>Quero de Novo
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        @endif

        <div class="mt-8 text-center">
            <div class="bg-gray-50 rounded-xl p-6 border border-gray-200">
                <div class="flex justify-center items-center space-x-6">
                    <a href="/{{ $store_slug }}" class="text-blue-600 hover:text-blue-800 font-semibold transition flex items-center">
                        <i class="fas fa-arrow-left mr-2"></i>Voltar ao Cardápio
                    </a>
                    @if(!empty($store['whatsapp']))
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $store['whatsapp']) }}" 
                           target="_blank"
                           class="text-green-600 hover:text-green-800 font-semibold transition flex items-center">
                            <i class="fab fa-whatsapp mr-2"></i>Falar no WhatsApp
                        </a>
                    @endif
                </div>
                <p class="text-sm text-gray-500 mt-3">
                    <i class="fas fa-heart text-red-500 mr-1"></i>
                    Obrigado por escolher {{ $store['store_name'] }}
                </p>
            </div>
        </div>
    @endif
</div>
@endsection
