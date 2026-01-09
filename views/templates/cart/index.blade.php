<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b">
        <div class="container mx-auto px-4 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <a href="/{{ $store_slug }}" class="text-purple-600 hover:text-purple-800 mr-4">
                        <i class="fas fa-arrow-left text-xl"></i>
                    </a>
                    <div>
                        <h1 class="text-xl font-bold text-gray-800">Carrinho</h1>
                        <p class="text-sm text-gray-600">{{ $store['store_name'] }}</p>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <div class="container mx-auto px-4 py-6">
        <div id="cart-items"></div>
        <!-- Resumo e botões -->
        <div id="cart-summary" class="hidden bg-white rounded-lg shadow-sm border p-4"></div>
    </div>
<script>
// Dados dos ingredientes e produtos passados do PHP
const ingredientsData = @json($ingredients ?? []);
const productsData = @json($products ?? []);
const sessionCart = @json($session_cart ?? []);

console.log('Products Data:', productsData); // Debug
console.log('Ingredients Data:', ingredientsData); // Debug
console.log('Session Cart:', sessionCart); // Debug

// Sincronizar carrinho da sessão PHP com localStorage
if (sessionCart && sessionCart.length > 0) {
    localStorage.setItem('cart_{{ $store['store_id'] }}', JSON.stringify(sessionCart));
    console.log('Carrinho sincronizado da sessão para localStorage');
}

// Função para buscar dados do ingrediente por ID
function getIngredientData(ingredientId) {
    return ingredientsData.find(ingredient => ingredient.id == ingredientId) || null;
}

// Função para buscar dados do produto por ID
function getProductData(productId) {
    const product = productsData.find(product => product.id == productId);
    console.log('Searching for product:', productId, 'Found:', product); // Debug
    return product || null;
}

function renderCart() {
    const cart = JSON.parse(localStorage.getItem('cart_{{ $store['store_id'] }}') || '[]');
    const cartItemsDiv = document.getElementById('cart-items');
    const cartSummaryDiv = document.getElementById('cart-summary');
    cartItemsDiv.innerHTML = '';
    let total = 0;

    if (cart.length === 0) {
        cartItemsDiv.innerHTML = `
            <div class="text-center py-12">
                <i class="fas fa-shopping-cart text-6xl text-gray-300 mb-4"></i>
                <h2 class="text-2xl font-bold text-gray-800 mb-2">Carrinho vazio</h2>
                <p class="text-gray-600 mb-6">Adicione alguns produtos deliciosos ao seu carrinho</p>
                <a href="/{{ $store_slug }}" 
                   class="bg-purple-600 text-white px-6 py-3 rounded-lg hover:bg-purple-700 transition duration-200">
                    <i class="fas fa-utensils mr-2"></i>
                    Ver Cardápio
                </a>
            </div>
        `;
        cartSummaryDiv.classList.add('hidden');
        return;
    }

    cart.forEach(item => {
        console.log('Processing cart item:', item); // Debug
        
        let itemTotal = item.price * item.quantity;
        let ingredientsHtml = '';
        let ingredientsTotal = 0;
        
        // Busca dados do produto para pegar category_name
        const productData = getProductData(item.product_id);
        console.log('Product data found:', productData); // Debug
        
        const displayName = productData ? 
            (productData.category_name ? productData.category_name + ' ' + productData.name : productData.name) : 
            (item.name || 'Produto');
            
        console.log('Display name:', displayName); // Debug
        
        if (item.ingredients && typeof item.ingredients === 'object') {
            for (const [id, qty] of Object.entries(item.ingredients)) {
                const ingredientData = getIngredientData(id);
                if (ingredientData) {
                    const ingredientPrice = parseFloat(ingredientData.additional_price || 0);
                    const ingredientSubtotal = ingredientPrice * qty * item.quantity;
                    ingredientsTotal += ingredientSubtotal;
                    
                    ingredientsHtml += `<div class="flex justify-between items-center text-xs text-gray-600 mt-1">
                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full">
                            • ${ingredientData.name} ${qty > 1 ? '(' + qty + 'x)' : ''}
                        </span>
                        ${ingredientPrice > 0 ? `<span class="text-green-600 font-medium">+R$ ${ingredientSubtotal.toFixed(2).replace('.', ',')}</span>` : ''}
                    </div>`;
                } else {
                    ingredientsHtml += `<div class="text-xs text-gray-500 mt-1">
                        <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded-full">
                            • Ingrediente #${id} ${qty > 1 ? '(' + qty + 'x)' : ''}
                        </span>
                    </div>`;
                }
            }
        }
        
        itemTotal += ingredientsTotal;
        total += itemTotal;

        cartItemsDiv.innerHTML += `
            <div class="bg-white rounded-lg shadow-sm border p-4 mb-4">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="font-bold text-gray-800 text-lg">
                            ${displayName}
                        </div>
                        <div class="text-sm text-gray-600 mb-2">
                            Quantidade: ${item.quantity}x • Preço unitário: R$ ${item.price.toFixed(2).replace('.', ',')}
                        </div>
                        ${ingredientsHtml ? `<div class="space-y-1">${ingredientsHtml}</div>` : ''}
                        ${item.notes ? `<div class="mt-2 text-xs text-gray-600 bg-yellow-50 p-2 rounded border-l-2 border-yellow-400">
                            <strong>Observação:</strong> ${item.notes}
                        </div>` : ''}
                    </div>
                    <div class="flex flex-col items-end gap-2 ml-4">
                        <span class="text-lg font-semibold text-purple-600">R$ ${itemTotal.toFixed(2).replace('.', ',')}</span>
                        <button onclick="removeCartItem('${item.cart_id}')" class="text-red-500 hover:text-red-700 p-2">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
    });

    cartSummaryDiv.innerHTML = `
        <div class="border-b pb-4 mb-4">
            <div class="flex justify-between items-center">
                <span class="text-lg font-semibold text-gray-800">Total</span>
                <span class="text-2xl font-bold text-purple-600">
                    R$ ${total.toFixed(2).replace('.', ',')}
                </span>
            </div>
        </div>
        <div class="flex flex-col sm:flex-row gap-3">
            <a href="/{{ $store_slug }}" 
               class="flex-1 bg-gray-100 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-200 transition duration-200 text-center">
                <i class="fas fa-plus mr-2"></i>
                Adicionar mais itens
            </a>
            <a href="#" 
               class="flex-1 bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition duration-200 text-center" id="checkout-btn">
                <i class="fas fa-credit-card mr-2"></i>
                Finalizar Pedido
            </a>
        </div>
    `;
    cartSummaryDiv.classList.remove('hidden');
}

function removeCartItem(cartId) {
    let cart = JSON.parse(localStorage.getItem('cart_{{ $store['id'] }}') || '[]');
    cart = cart.filter(item => item.cart_id !== cartId);
    localStorage.setItem('cart_{{ $store['id'] }}', JSON.stringify(cart));
    renderCart();
    if (typeof updateCartCount === 'function') updateCartCount();
}

document.addEventListener('DOMContentLoaded', function() {
    renderCart();
    if (typeof updateCartCount === 'function') updateCartCount();

    // Checkout sincroniza carrinho antes de ir para o checkout
    const checkoutBtn = document.getElementById('checkout-btn');
    if (checkoutBtn) {
        checkoutBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const storeId = "{{ $store['id'] }}";
            const storeSlug = "{{ $store_slug }}";
            const cart = JSON.parse(localStorage.getItem('cart_' + storeId) || '[]');
            fetch('/' + storeSlug + '/carrinho/sync', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ cart })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    window.location.href = '/' + storeSlug + '/checkout';
                } else {
                    alert('Erro ao salvar o carrinho. Tente novamente.');
                }
            });
        });
    }
});
</script>
</body>
</html>
