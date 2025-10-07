@extends('layouts.admin')

@section('title', 'Novo Pedido de Mesa')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Novo Pedido de Mesa</h1>
    <a href="/admin/pedidos" class="text-gray-600 hover:text-gray-900">
        <i class="fas fa-arrow-left mr-2"></i>
        Voltar para Pedidos
    </a>
</div>

@if (isset($_SESSION['error']))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        {{ $_SESSION['error'] }}
    </div>
    @php unset($_SESSION['error']); @endphp
@endif

<form method="POST" action="/admin/pedidos/novo" id="orderForm">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Informações do Pedido -->
        <div class="lg:col-span-2">
            <div class="bg-white shadow rounded-lg p-6 mb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Informações do Pedido</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="table_number" class="block text-sm font-medium text-gray-700 mb-1">Mesa *</label>
                        <input type="text" 
                               id="table_number" 
                               name="table_number" 
                               required
                               class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="Número da mesa">
                    </div>
                    
                    <div>
                        <label for="customer_name" class="block text-sm font-medium text-gray-700 mb-1">Nome do Cliente *</label>
                        <input type="text" 
                               id="customer_name" 
                               name="customer_name" 
                               required
                               class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="Nome do cliente">
                    </div>
                    
                    <div>
                        <label for="customer_phone" class="block text-sm font-medium text-gray-700 mb-1">Telefone</label>
                        <input type="tel" 
                               id="customer_phone" 
                               name="customer_phone" 
                               class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="(11) 99999-9999">
                    </div>
                    
                    <div>
                        <label for="customer_address" class="block text-sm font-medium text-gray-700 mb-1">Endereço</label>
                        <input type="text" 
                               id="customer_address" 
                               name="customer_address" 
                               class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="Endereço do cliente">
                    </div>
                </div>
                
                <div class="mt-4">
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Observações</label>
                    <textarea id="notes" 
                              name="notes" 
                              rows="3"
                              class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                              placeholder="Observações sobre o pedido"></textarea>
                </div>
            </div>
            
            <!-- Produtos -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Selecionar Produtos</h3>
                
                <!-- Filtro por categoria -->
                <div class="mb-4">
                    <select id="categoryFilter" class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Todas as categorias</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category['id'] }}">{{ $category['name'] }}</option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Lista de produtos -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4" id="productsList">
                    @foreach ($products as $product)
                        <div class="border border-gray-200 rounded-lg p-4 product-item" data-category="{{ $product['category_id'] ?? '' }}">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="font-medium text-gray-900">{{ $product['name'] }}</h4>
                                <span class="text-green-600 font-semibold">R$ {{ number_format($product['price'], 2, ',', '.') }}</span>
                            </div>
                            
                            @if ($product['description'])
                                <p class="text-sm text-gray-600 mb-3">{{ $product['description'] }}</p>
                            @endif
                            
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-2">
                                    <button type="button" onclick="changeQuantity({{ $product['id'] }}, -1)" class="bg-gray-200 text-gray-700 px-2 py-1 rounded">-</button>
                                    <span id="qty-{{ $product['id'] }}" class="w-8 text-center">0</span>
                                    <button type="button" onclick="changeQuantity({{ $product['id'] }}, 1)" class="bg-blue-600 text-white px-2 py-1 rounded">+</button>
                                </div>
                                
                                @if ($product['max_ingredients'] > 0)
                                    <button type="button" onclick="openIngredientsModal({{ $product['id'] }})" class="text-blue-600 hover:text-blue-800 text-sm">
                                        <i class="fas fa-cog mr-1"></i>
                                        Personalizar
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        
        <!-- Resumo do Pedido -->
        <div class="lg:col-span-1">
            <div class="bg-white shadow rounded-lg p-6 sticky top-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Resumo do Pedido</h3>
                
                <div id="orderSummary">
                    <p class="text-gray-500 text-center py-4">Nenhum item adicionado</p>
                </div>
                
                <div class="border-t pt-4 mt-4">
                    <div class="flex justify-between items-center mb-4">
                        <span class="text-lg font-semibold text-gray-900">Total:</span>
                        <span id="orderTotal" class="text-xl font-bold text-green-600">R$ 0,00</span>
                    </div>
                    
                    <button type="submit" class="w-full bg-green-600 text-white py-3 px-4 rounded-md hover:bg-green-700 transition-colors font-medium">
                        <i class="fas fa-check mr-2"></i>
                        Criar Pedido
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <input type="hidden" name="items" id="itemsData">
</form>

<script>
let orderItems = {};
let products = @json($products);

function changeQuantity(productId, delta) {
    if (!orderItems[productId]) {
        orderItems[productId] = {
            product_id: productId,
            quantity: 0,
            ingredients: {},
            notes: ''
        };
    }
    
    orderItems[productId].quantity = Math.max(0, orderItems[productId].quantity + delta);
    
    if (orderItems[productId].quantity === 0) {
        delete orderItems[productId];
    }
    
    updateDisplay();
}

function updateDisplay() {
    // Atualizar quantidades na tela
    products.forEach(product => {
        const qty = orderItems[product.id] ? orderItems[product.id].quantity : 0;
        document.getElementById('qty-' + product.id).textContent = qty;
    });
    
    // Atualizar resumo
    updateOrderSummary();
    
    // Atualizar campo hidden
    document.getElementById('itemsData').value = JSON.stringify(Object.values(orderItems));
}

function updateOrderSummary() {
    const summaryEl = document.getElementById('orderSummary');
    const totalEl = document.getElementById('orderTotal');
    
    if (Object.keys(orderItems).length === 0) {
        summaryEl.innerHTML = '<p class="text-gray-500 text-center py-4">Nenhum item adicionado</p>';
        totalEl.textContent = 'R$ 0,00';
        return;
    }
    
    let html = '';
    let total = 0;
    
    Object.values(orderItems).forEach(item => {
        const product = products.find(p => p.id == item.product_id);
        if (product) {
            const itemTotal = product.price * item.quantity;
            total += itemTotal;
            
            html += `
                <div class="flex justify-between items-center py-2 border-b">
                    <div>
                        <div class="font-medium">${product.name}</div>
                        <div class="text-sm text-gray-500">Qtd: ${item.quantity}</div>
                    </div>
                    <div class="text-green-600 font-semibold">
                        R$ ${itemTotal.toFixed(2).replace('.', ',')}
                    </div>
                </div>
            `;
        }
    });
    
    summaryEl.innerHTML = html;
    totalEl.textContent = 'R$ ' + total.toFixed(2).replace('.', ',');
}

// Filtro de categoria
document.getElementById('categoryFilter').addEventListener('change', function() {
    const categoryId = this.value;
    const items = document.querySelectorAll('.product-item');
    
    items.forEach(item => {
        if (!categoryId || item.dataset.category === categoryId) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
});

// Validação do formulário
document.getElementById('orderForm').addEventListener('submit', function(e) {
    if (Object.keys(orderItems).length === 0) {
        e.preventDefault();
        alert('Adicione pelo menos um item ao pedido!');
    }
});
</script>
@endsection