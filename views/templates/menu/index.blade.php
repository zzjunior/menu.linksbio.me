<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $pageTitle }}</title>
    <link rel="stylesheet" href="/assets/css/custom.css">
    <link rel="stylesheet" href="/assets/css/ifood-modal.css">
    <link rel="icon" type="image/x-icon" href="{{ $store['store_logo'] ?? '/assets/favicon.ico' }}">
    <meta name="theme-color" content="#8B5CF6">
    <meta name="description" content="{{ $store['store_description'] ?? 'Cardápio digital da sua loja' }}">
    <link rel="apple-touch-icon" sizes="180x180" href="/assets/apple-touch-icon.png">
    <link rel="manifest" href="/assets/manifest.json">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script>
// Atualiza o número de itens no carrinho no ícone
function updateCartCount() {
    try {
        const cart = JSON.parse(localStorage.getItem('cart_{{ $store['id'] }}') || '[]');
        document.getElementById('cart-count').textContent = cart.length;
    } catch (e) {
        document.getElementById('cart-count').textContent = 0;
    }
}

// Atualiza o carrinho ao carregar a página
document.addEventListener('DOMContentLoaded', function() {
    updateCartCount();
});
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#8B5CF6',
                        secondary: '#A855F7',
                        accent: '#EC4899'
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gradient-to-br from-purple-50 to-pink-50 min-h-screen">
    <!-- Header com Banner como Background -->
<header id="mainHeader" class="bg-white shadow-lg sticky top-0 z-50 transition-transform duration-300 relative @if(!empty($store['store_banner'])) bg-cover bg-center @endif" 
        @if(!empty($store['store_banner'])) style="background-image: url('{{ $store['store_banner'] }}');" @endif>
    <!-- Overlay opaco -->
    @if(!empty($store['store_banner']))
        <div class="absolute inset-0 bg-black bg-opacity-70"></div>
    @endif
    
    <div class="container mx-auto px-4 py-4 relative z-10">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                @if (!empty($store['store_logo']))
                    <img src="{{ $store['store_logo'] }}" alt="Logo da loja"
                         class="w-16 h-16 rounded-full object-cover border-2 border-white shadow-lg flex-shrink-0">
                @endif
                <div class="flex-1 min-w-0">
                    <h1 class="text-lg sm:text-xl font-bold text-white truncate" style="text-shadow: 2px 2px 4px rgba(0,0,0,0.8);">{{ $store['store_name'] }}</h1>
                    @if (!empty($store['store_address']))
                        <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($store['store_address']) }}" target="_blank" rel="noopener noreferrer" class="text-xs sm:text-sm text-white truncate flex items-center hover:underline" style="text-shadow: 1px 1px 3px rgba(0,0,0,0.8);">
                            <i class="fas fa-map-marker-alt mr-1"></i>
                            {{ $store['store_address'] }}
                        </a>
                    @endif
                    @if (!empty($store['store_phone']))
                        <a href="https://wa.me/55{{ preg_replace('/\D/', '', $store['store_phone']) }}" target="_blank" rel="noopener noreferrer" class="text-xs sm:text-sm text-white truncate flex items-center hover:underline" style="text-shadow: 1px 1px 3px rgba(0,0,0,0.8);">
                            <i class="fab fa-whatsapp mr-1"></i>
                            {{ $store['store_phone'] }}
                        </a>
                    @endif
                </div>
            </div>
            <a href="/{{ $store_slug }}/carrinho" class="relative bg-white bg-opacity-90 backdrop-blur-sm hover:bg-opacity-100 text-gray-900 p-2 rounded-full transition-all shadow-lg flex-shrink-0">
                <i class="fas fa-shopping-cart p-2" style="font-size: 0.7rem;"></i>
                <span id="cart-count" class="absolute -top-2 -right-2 bg-primary text-white text-xs rounded-full w-5 h-5 flex items-center justify-center font-bold shadow-md">0</span>
            </a>
        </div>
    </div>
</header>

<!-- Status da Loja (Aberto/Fechado) -->
<div id="storeStatusBanner" class="sticky top-16 z-40 transition-all duration-300">
    @if(!$storeStatus['is_open'])
        <!-- Loja Fechada - Banner vermelho -->
        <div class="bg-red-600 text-white py-3 px-4 shadow-md">
            <div class="container mx-auto flex items-center justify-center gap-3">
                <i class="fas fa-store-slash text-xl"></i>
                <div class="text-center">
                    <p class="font-bold text-sm">🔒 Loja Fechada</p>
                    <p class="text-xs opacity-90">{{ $storeStatus['message'] }}</p>
                    @if($storeStatus['next_opening'])
                        <p class="text-xs mt-1 opacity-80">{{ $storeStatus['next_opening'] }}</p>
                    @endif
                </div>
            </div>
        </div>
    @else
        <!-- Loja Aberta - Banner verde discreto -->
        <div class="bg-green-600 text-white py-2 px-4">
            <div class="container mx-auto flex items-center justify-center gap-2">
                <i class="fas fa-store text-sm"></i>
                <p class="text-xs font-medium">✅ {{ $storeStatus['message'] }}</p>
            </div>
        </div>
    @endif
</div>

<!-- Filtros de Categoria -->
@if (!empty($categories))
    <div class="bg-white shadow sticky top-0 z-40 transition-all duration-300 mb-5">
        <div class="container mx-auto px-4 py-3">
            <div class="flex gap-2 overflow-x-auto pb-2 scrollbar-thin scrollbar-thumb-primary scrollbar-track-gray-200 snap-x snap-mandatory">
                <a href="/{{ $store_slug }}" 
                   class="px-4 py-2 rounded-full text-sm font-medium whitespace-nowrap transition-colors snap-start
                          {{ empty($currentCategory) ? 'bg-primary text-white' : 'bg-white text-gray-700 hover:bg-gray-100' }}">
                    Todos
                </a>
                @foreach ($categories as $category)
                    <a href="/{{ $store_slug }}?category={{ $category['id'] }}" 
                       class="px-4 py-2 rounded-full text-sm font-medium whitespace-nowrap transition-colors snap-start
                              {{ $currentCategory == $category['id'] ? 'bg-primary text-white' : 'bg-white text-gray-700 hover:bg-gray-100' }}">
                        {{ $category['name'] }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>
@endif

<script>
    // Header hide/show ao rolar
    let lastScroll = 0;
    const header = document.getElementById('mainHeader');
    window.addEventListener('scroll', function() {
        const currentScroll = window.pageYOffset;
        if (currentScroll > lastScroll && currentScroll > 60) {
            // Rolando para baixo
            header.style.transform = 'translateY(-100%)';
        } else {
            // Rolando para cima
            header.style.transform = 'translateY(0)';
        }
        lastScroll = currentScroll;
    });
</script>

    <!-- Produtos -->
    <main class="container mx-auto px-4 pb-6">
        @if (empty($products))
            <div class="text-center py-12">
                <div class="text-6xl mb-4">🤷‍♀️</div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Nenhum produto encontrado</h3>
                <p class="text-gray-600">Adicione produtos ao seu cardápio no painel admin</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($products as $product)
                    <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow duration-300 overflow-hidden {{ !$storeStatus['is_open'] ? 'opacity-60' : '' }}">
                        @if ($product['image_url'])
                            <div class="h-40 bg-gradient-to-br from-primary/20 to-accent/20 relative overflow-hidden">
                                <img src="{{ $product['image_url'] }}" 
                                     alt="{{ $product['name'] }}"
                                     class="w-full h-full object-cover {{ !$storeStatus['is_open'] ? 'grayscale' : '' }}">
                            </div>
                        @else
                            <div class="h-40 bg-gradient-to-br from-primary/20 to-accent/20 flex items-center justify-center">
                                <span class="text-4xl">🍇</span>
                            </div>
                        @endif
                            
                        <div class="p-4">
                            <div class="flex justify-between items-start mb-2">
                                <h3 class="text-lg font-semibold text-gray-900 leading-tight">
                                    {{ $product['name'] }}
                                    @if ($product['size_ml'])
                                        <span class="text-sm text-gray-500">({{ $product['size_ml'] }}ml)</span>
                                    @endif
                                </h3>
                                <span class="text-primary font-bold text-lg">
                                    R$ {{ number_format($product['price'], 2, ',', '.') }}
                                </span>
                            </div>
                            
                            @if ($product['description'])
                                <p class="text-gray-600 text-sm mb-3">
                                    {{ $product['description'] }}
                                </p>
                            @endif
                            
                            @if (!$storeStatus['is_open'])
                                <button disabled
                                        class="w-full bg-gray-400 text-white py-2 px-4 rounded-lg font-medium cursor-not-allowed">
                                    <i class="fas fa-lock"></i> Loja Fechada
                                </button>
                            @else
                                <button onclick="openCustomizeModal({{ $product['id'] }})" 
                                        class="w-full bg-gradient-to-r from-primary to-secondary text-white py-2 px-4 rounded-lg font-medium hover:from-primary/90 hover:to-secondary/90 transition-all duration-200">
                                    @if ($product['max_ingredients'] > 0)
                                        🎯 Monte o seu
                                    @else
                                        <i class="fas fa-plus"></i> Adicionar
                                    @endif
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </main>

<!-- Modal de Personalização -->
<div id="customizeModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden w-screen h-screen">
    <div class="flex items-center justify-center w-full h-full p-0">
        <div class="bg-white w-full h-full rounded-none shadow-lg flex flex-col">
            <!-- Header -->
            <div class="p-4 border-b flex-shrink-0">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-semibold" id="modalTitle">Monte o seu Produto</h3>
                    <button onclick="closeCustomizeModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>
            <!-- Formulário central com rolagem -->
            <form id="customizeForm" class="flex-1 overflow-y-auto">
                <div class="p-4" id="modalContent">
                    <!-- Conteúdo será preenchido via JavaScript -->
                </div>
            </form>
            <!-- Rodapé fixo -->
            <div class="p-4 border-t bg-gray-50 flex-shrink-0">
                <div class="flex justify-between items-center mb-3">
                    <span class="font-medium">Total:</span>
                    <span class="text-xl font-bold text-primary" id="totalPrice">R$ 0,00</span>
                </div>
                <button type="button" onclick="addCustomizedToCart()" 
                class="w-full bg-gradient-to-r from-primary to-secondary text-white py-3 px-4 rounded-lg font-medium hover:from-primary/90 hover:to-secondary/90 transition-all duration-200">
                Finalizar <i class="fa-solid fa-arrow-right"></i>
            </button>
            </div>
        </div>
    </div>
</div>
<script>
let currentProduct = null;
let currentTotal = 0;
let selectedIngredients = []; // Agora é um array de objetos, um para cada unidade
let productQty = 1;
const maxIngredientsRules = @json($maxIngredientsRules ?? []);

async function openCustomizeModal(productId) {
    try {
        const response = await fetch(`/{{ $store_slug }}/api/product/${productId}`);
        const product = await response.json();
        const productRules = maxIngredientsRules[product.id] || {};

        if (product.error) {
            showToast('Erro ao carregar produto', 'error');
            return;
        }

        currentProduct = product;
        currentTotal = parseFloat(product.price);
        productQty = 1;
        selectedIngredients = [createEmptySelection(product)]; // Inicializa para 1 unidade

        renderModalContent();
        showModal();
        updateTotal();
    } catch (error) {
        console.error('Erro:', error);
        showToast('Erro ao carregar produto', 'error');
    }
}

function createEmptySelection(product) {
    // Cria um objeto para armazenar as escolhas de ingredientes por unidade
    const selection = {};
    if (product.ingredients) {
        Object.keys(product.ingredients).forEach(type => {
            product.ingredients[type].forEach(ingredient => {
                selection[ingredient.id] = { qty: 0, type: type };
            });
        });
    }
    return selection;
}

function renderModalContent() {
    const product = currentProduct;
    const productRules = maxIngredientsRules[product.id] || {};
    let content = `
        <input type="hidden" id="productId" value="${product.id}">
        <input type="hidden" id="productName" value="${product.name}">
        <input type="hidden" id="basePrice" value="${product.price}">
        
        <!-- Imagem e Descrição do Produto -->
        ${product.image_url ? `
        <div class="mb-4 -mx-4 -mt-4">
            <img src="${product.image_url}" alt="${product.name}" class="w-full h-48 object-cover">
        </div>
        ` : ''}
        
        <div class="mb-4">
            <h2 class="text-xl font-bold text-gray-900 mb-2">${product.name}</h2>
            ${product.description ? `<p class="text-gray-600 text-sm mb-2">${product.description}</p>` : ''}
            <p class="text-primary font-bold text-lg">R$ ${parseFloat(product.price).toFixed(2).replace('.', ',')}</p>
        </div>
        
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Quantidade</label>
            ${renderStepper('qtyValue', productQty, 1, 10, 'changeProductQty(-1)', 'changeProductQty(1)')}
        </div>
    `;

    // Só mostra seção de personalização se houver ingredientes
    const hasIngredients = product.ingredients && Object.keys(product.ingredients).some(type => 
        product.ingredients[type] && product.ingredients[type].length > 0
    );

    if (hasIngredients) {
        for (let i = 0; i < productQty; i++) {
            content += `<div class="mb-4 p-3 border border-primary/20 rounded-lg bg-gradient-to-br from-purple-50/50 to-pink-50/50">
                <div class="font-semibold text-primary mb-3 flex items-center gap-2">
                    <i class="fas fa-utensils"></i>
                    Personalize seu pedido ${productQty > 1 ? `(nº ${i + 1})` : ''}
                </div>`;
            
            Object.keys(product.ingredients).forEach(type => {
                const maxPorTipo = productRules[type] || null;
                if (product.ingredients[type] && product.ingredients[type].length > 0) {
                    content += `
                        <div class="mb-3">
                            <h4 class="text-md font-semibold text-gray-800 mb-2">
                                ${type.charAt(0).toUpperCase() + type.slice(1)}
                                ${maxPorTipo ? `<span class="text-xs text-gray-500">(Máx: ${maxPorTipo})</span>` : ''}
                            </h4>
                            <div class="grid grid-cols-3 sm:grid-cols-4 gap-2">`;
                    product.ingredients[type].forEach(ingredient => {
                        const img = ingredient.image_url || '/assets/images/ingredients/default.jpg';
                        const price = ingredient.additional_price > 0 ? `<span class="text-green-600 text-xs mt-1">+R$ ${parseFloat(ingredient.additional_price).toFixed(2).replace('.', ',')}</span>` : '';
                        content += `
                            <div class="flex flex-col items-center bg-white rounded-lg p-2 min-w-[90px] shadow-sm">
                                <img src="${img}" alt="${ingredient.name}" class="w-12 h-12 object-cover rounded mb-1">
                                <span class="text-xs text-center mb-1 font-medium">${ingredient.name}</span>
                                ${renderStepper(
                                    `ingredient-qty-${ingredient.id}-unit${i}`,
                                    selectedIngredients[i][ingredient.id]?.qty || 0,
                                    0,
                                    10,
                                    `changeIngredientQty(${i},'${ingredient.id}', -1, '${type}')`,
                                    `changeIngredientQty(${i},'${ingredient.id}', 1, '${type}')`
                                )}
                                ${price}
                            </div>
                        `;
                    });
                    content += `</div></div>`;
                }
            });
            content += `</div>`;
        }
    }

    content += `
        <div class="mb-4">
            <label for="notes" class="block text-sm font-semibold text-gray-800 mb-2 flex items-center gap-2">
                <i class="fas fa-comment-dots text-primary"></i>
                Alguma observação?
            </label>
            <textarea id="notes" 
                class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-primary focus:border-transparent transition-all" 
                rows="3" 
                placeholder="Ex: sem cebola, ponto da carne, retirar ingrediente..."></textarea>
        </div>
    `;

    document.getElementById('modalContent').innerHTML = content;
    document.getElementById('qtyValue').textContent = productQty;
}

function renderStepper(id, value, min, max, onMinus, onPlus) {
    return `
        <div class="flex items-center gap-2">
            <button type="button" class="ifood-stepper-btn" onclick="${onMinus}">-</button>
            <span id="${id}" class="ifood-stepper-qty">${value}</span>
            <button type="button" class="ifood-stepper-btn" onclick="${onPlus}">+</button>
        </div>
    `;
}

function changeProductQty(delta) {
    const newQty = Math.max(1, productQty + delta);
    if (newQty === productQty) return;
    productQty = newQty;
    // Ajusta o array de seleções
    while (selectedIngredients.length < productQty) {
        selectedIngredients.push(createEmptySelection(currentProduct));
    }
    while (selectedIngredients.length > productQty) {
        selectedIngredients.pop();
    }
    renderModalContent();
    updateTotal();
}

function changeIngredientQty(unitIndex, id, delta, type) {
    const selection = selectedIngredients[unitIndex];
    if (!selection[id]) selection[id] = { qty: 0, type: type };
    let newQty = selection[id].qty + delta;
    if (newQty < 0) newQty = 0;

    // Limite por tipo para esta unidade
    const productRules = maxIngredientsRules[currentProduct.id] || {};
    const maxPorTipo = productRules[type] || null;
    if (maxPorTipo) {
        let totalGrupo = 0;
        Object.keys(selection).forEach(iid => {
            if (selection[iid].type === type) {
                totalGrupo += selection[iid].qty;
            }
        });
        if (delta > 0 && totalGrupo >= maxPorTipo) {
            showToast(`Máximo de ${maxPorTipo} para ${type} na unidade ${unitIndex + 1}`, 'warning');
            return;
        }
    }

    selection[id].qty = newQty;
    document.getElementById(`ingredient-qty-${id}-unit${unitIndex}`).textContent = newQty;
    updateTotal();
}

function updateTotal() {
    if (!currentProduct) return;
    let total = parseFloat(currentProduct.price) * productQty;
    for (let i = 0; i < productQty; i++) {
        Object.keys(selectedIngredients[i]).forEach(id => {
            const ing = selectedIngredients[i][id];
            if (ing.qty > 0) {
                const ingredient = findIngredientById(id);
                if (ingredient && ingredient.additional_price > 0) {
                    total += parseFloat(ingredient.additional_price) * ing.qty;
                }
            }
        });
    }
    currentTotal = total;
    document.getElementById('totalPrice').textContent = `R$ ${total.toFixed(2).replace('.', ',')}`;
}

function findIngredientById(id) {
    if (!currentProduct || !currentProduct.ingredients) return null;
    for (const type of Object.keys(currentProduct.ingredients)) {
        if (currentProduct.ingredients[type]) {
            const found = currentProduct.ingredients[type].find(ing => ing.id == id);
            if (found) return found;
        }
    }
    return null;
}

function showModal() {
    const modal = document.getElementById('customizeModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex', 'items-center', 'justify-center');
    modal.style.padding = '0';
    // Força o modal a ocupar 100% da tela
    modal.style.width = '100vw';
    modal.style.height = '100vh';
    const modalOuter = modal.querySelector('div');
    modalOuter.className = 'flex items-center justify-center w-full h-full p-0';
    const modalBox = modalOuter.querySelector('div');
    modalBox.className = 'bg-white w-full h-full rounded-none shadow-lg overflow-auto flex flex-col';
    modalBox.style.width = '100vw';
    modalBox.style.height = '100vh';
    modalBox.style.maxWidth = '100vw';
    modalBox.style.maxHeight = '100vh';
    modalBox.style.borderRadius = '0';
    modalBox.style.boxSizing = 'border-box';
    modalBox.style.display = 'flex';
    modalBox.style.flexDirection = 'column';
}

function closeCustomizeModal() {
    document.getElementById('customizeModal').classList.add('hidden');
    currentProduct = null;
    selectedIngredients = [];
    productQty = 1;
}

function addCustomizedToCart() {
    if (!currentProduct) return;
    const notes = document.getElementById('notes').value;
    const cart = JSON.parse(localStorage.getItem('cart_{{ $store['id'] }}') || '[]');
    for (let i = 0; i < productQty; i++) {
        const ingredients = {};
        Object.keys(selectedIngredients[i]).forEach(id => {
            if (selectedIngredients[i][id].qty > 0) {
                ingredients[id] = selectedIngredients[i][id].qty;
            }
        });
        cart.push({
            cart_id: Date.now().toString() + '-' + i,
            product_id: currentProduct.id,
            name: currentProduct.name + ' #' + (i + 1),
            price: parseFloat(currentProduct.price),
            quantity: 1,
            ingredients: ingredients,
            notes: notes,
            size: ''
        });
    }
    localStorage.setItem('cart_{{ $store['id'] }}', JSON.stringify(cart));
    updateCartCount();
    // Fecha o modal e redireciona após garantir atualização
    closeCustomizeModal();
    setTimeout(function() {
        window.location.href = '/{{ $store_slug }}/carrinho';
    }, 100);
}
</script>

<!-- Rodapé com Horários -->
@if(!empty($formattedHours))
<footer class="bg-white border-t mt-8 py-6">
    <div class="container mx-auto px-4">
        <button onclick="document.getElementById('hoursModal').classList.remove('hidden')" 
                class="text-primary hover:text-secondary flex items-center gap-2 mx-auto">
            <i class="far fa-clock"></i>
            Ver horários de funcionamento
        </button>
    </div>
</footer>

<!-- Modal de Horários -->
<div id="hoursModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-lg max-w-md w-full shadow-xl">
        <div class="p-4 border-b flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-900">
                <i class="far fa-clock text-primary mr-2"></i>
                Horários de Funcionamento
            </h3>
            <button onclick="document.getElementById('hoursModal').classList.add('hidden')" 
                    class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div class="p-4">
            <div class="space-y-2">
                @foreach($formattedHours as $dayHours)
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-sm font-medium text-gray-700">{{ $dayHours['day'] }}</span>
                        <span class="text-sm {{ $dayHours['enabled'] ? 'text-green-600 font-medium' : 'text-red-500' }}">
                            {{ $dayHours['hours'] }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="p-4 bg-gray-50 rounded-b-lg">
            <p class="text-xs text-gray-600 text-center">
                <i class="fas fa-info-circle mr-1"></i>
                Os horários podem variar em feriados
            </p>
        </div>
    </div>
</div>
@endif

</body>
</html>

