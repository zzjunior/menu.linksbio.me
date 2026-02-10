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
                        primary: '#8B5CF6',    // Roxo azulado
                        secondary: '#A855F7',  // Roxo claro  
                        accent: '#EC4899',     // Rosa
                        success: '#10B981',    // Verde
                        danger: '#EF4444',     // Vermelho
                        dark: '#1E293B',       // Azul escuro
                    },
                    fontFamily: {
                        sans: ['Inter', '-apple-system', 'BlinkMacSystemFont', 'Segoe UI', 'sans-serif']
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.3s ease-in-out',
                        'slide-up': 'slideUp 0.3s ease-out',
                        'bounce-light': 'bounceLight 2s infinite'
                    },
                    boxShadow: {
                        'modern': '0 4px 20px rgba(0, 0, 0, 0.08)',
                        'modern-lg': '0 8px 30px rgba(0, 0, 0, 0.12)',
                        'card': '0 2px 12px rgba(0, 0, 0, 0.06)',
                        'card-hover': '0 8px 25px rgba(0, 0, 0, 0.15)'
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gradient-to-br from-purple-50 to-blue-50 min-h-screen font-sans">
    <!-- Header Moderno -->
<header id="mainHeader" class="bg-white shadow-sm sticky top-0 z-50 transition-all duration-300">
    @if(!empty($store['store_banner']))
        <!-- Banner Background Hero Section -->
        <div class="relative h-32 bg-cover bg-center bg-gray-100 overflow-hidden" 
             style="background-image: linear-gradient(135deg, rgba(255, 107, 53, 0.8), rgba(255, 182, 39, 0.7)), url('{{ $store['store_banner'] }}');">
            <!-- Gradient Overlay -->
            <div class="absolute inset-0 bg-gradient-to-r from-primary/90 to-accent/80"></div>
            
            <!-- Store Info Content -->
            <div class="relative container mx-auto px-4 h-full flex items-center justify-between">
                <!-- Store Info -->
                <div class="flex-1">
                    @if (!empty($store['store_logo']))
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-12 h-12 rounded-full bg-white/20 backdrop-blur-sm border-2 border-white/30 overflow-hidden shadow-lg">
                                <img src="{{ $store['store_logo'] }}" 
                                     alt="Logo {{ $store['store_name'] }}" 
                                     class="w-full h-full object-cover">
                            </div>
                            <div>
                                <h1 class="text-white font-bold text-lg leading-tight">{{ $store['store_name'] }}</h1>
                            </div>
                        </div>
                    @else
                        <h1 class="text-white font-bold text-xl mb-2">{{ $store['store_name'] }}</h1>
                    @endif
                    
                    <!-- Store Details -->
                    <div class="space-y-1">
                        @if (!empty($store['store_address']))
                            <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($store['store_address']) }}" 
                               target="_blank" rel="noopener noreferrer"
                               class="flex items-center gap-2 text-white/90 hover:text-white transition-colors">
                                <i class="fas fa-map-marker-alt text-xs"></i>
                                <span class="text-xs font-medium">{{ $store['store_address'] }}</span>
                            </a>
                        @endif
                        
                        @if (!empty($store['store_phone']))
                            <a href="https://wa.me/55{{ preg_replace('/\D/', '', $store['store_phone']) }}" 
                               target="_blank" rel="noopener noreferrer"
                               class="flex items-center gap-2 text-white/90 hover:text-white transition-colors">
                                <i class="fab fa-whatsapp text-xs"></i>
                                <span class="text-xs font-medium">{{ $store['store_phone'] }}</span>
                            </a>
                        @endif
                    </div>
                </div>
                
                <!-- Cart Button -->
                <div class="ml-4">
                    <a href="/{{ $store_slug }}/carrinho" 
                       class="relative inline-flex items-center justify-center w-12 h-12 bg-white/20 backdrop-blur-sm text-white rounded-full hover:bg-white/30 transition-all duration-200 shadow-lg">
                        <i class="fas fa-shopping-cart text-lg"></i>
                        <span id="cart-count" class="absolute -top-1 -right-1 bg-danger text-white text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center shadow-md">0</span>
                    </a>
                </div>
            </div>
        </div>
    @else
        <!-- Header sem banner -->
        <div class="bg-gradient-to-r from-primary to-secondary text-white">
            <div class="container mx-auto px-4 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        @if (!empty($store['store_logo']))
                            <div class="w-12 h-12 rounded-full overflow-hidden bg-white/20 border-2 border-white/30">
                                <img src="{{ $store['store_logo'] }}" 
                                     alt="Logo {{ $store['store_name'] }}" 
                                     class="w-full h-full object-cover">
                            </div>
                        @endif
                        <div>
                            <h1 class="font-bold text-lg">{{ $store['store_name'] }}</h1>
                            @if (!empty($store['store_address']))
                                <p class="text-white/90 text-sm">{{ $store['store_address'] }}</p>
                            @endif
                            @if (!empty($store['store_phone']))
                                <p class="text-white/80 text-xs">{{ $store['store_phone'] }}</p>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Cart Button -->
                    <a href="/{{ $store_slug }}/carrinho" 
                       class="relative inline-flex items-center justify-center w-10 h-10 bg-white/20 text-white rounded-full hover:bg-white/30 transition-all duration-200">
                        <i class="fas fa-shopping-cart"></i>
                        <span id="cart-count" class="absolute -top-1 -right-1 bg-danger text-white text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center">0</span>
                    </a>
                </div>
            </div>
        </div>
    @endif

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

<!-- Filtros de Categoria - Design Moderno -->
@if (!empty($categories))
    <div class="bg-white shadow-sm sticky top-20 z-40 transition-all duration-300 border-b border-gray-100">
        <div class="container mx-auto px-4 py-4">
            <div class="flex gap-3 overflow-x-auto pb-2 scrollbar-hide snap-x snap-mandatory">
                <a href="/{{ $store_slug }}" 
                   class="px-5 py-2.5 rounded-full text-sm font-semibold whitespace-nowrap transition-all snap-start flex-shrink-0
                          {{ empty($currentCategory) ? 'bg-primary text-white shadow-lg' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    <i class="fas fa-th-large text-xs mr-2"></i>
                    Todos
                </a>
                @foreach ($categories as $category)
                    <a href="/{{ $store_slug }}?category={{ $category['id'] }}" 
                       class="px-5 py-2.5 rounded-full text-sm font-semibold whitespace-nowrap transition-all snap-start flex-shrink-0
                              {{ $currentCategory == $category['id'] ? 'bg-primary text-white shadow-lg' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        {{ $category['name'] }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>
@endif

<script>
    // Header responsivo - esconde/mostra ao rolar
    let lastScroll = 0;
    const header = document.getElementById('mainHeader');
    
    if (header) {
        window.addEventListener('scroll', function() {
            const currentScroll = window.pageYOffset;
            
            if (currentScroll > lastScroll && currentScroll > 100) {
                // Rolando para baixo - esconde header
                header.style.transform = 'translateY(-100%)';
            } else {
                // Rolando para cima - mostra header  
                header.style.transform = 'translateY(0)';
            }
            lastScroll = currentScroll;
        });
    }
</script>

    <!-- Produtos - Layout Moderno iFood Style -->
    <main class="container mx-auto px-4 pb-6 pt-4">
        @if (empty($products))
            <div class="text-center py-16">
                <div class="w-24 h-24 mx-auto mb-6 bg-gray-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-utensils text-3xl text-gray-400"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Nenhum produto encontrado</h3>
                <p class="text-gray-600 max-w-sm mx-auto">Nosso cardápio está sendo preparado. Volte em breve para conferir nossas delícias!</p>
            </div>
        @else
            <!-- Grid de Produtos Modernizado -->
            <div class="space-y-3 md:space-y-4">
                @foreach ($products as $product)
                    <!-- Card Produto - Estilo iFood/Brendi -->
                    <div class="bg-white rounded-2xl shadow-card hover:shadow-card-hover transition-all duration-300 overflow-hidden card-modern {{ !$storeStatus['is_open'] ? 'opacity-60' : '' }}">
                        <div class="flex {{ $product['image_url'] ? 'min-h-[120px] md:h-32' : 'h-auto' }}">
                            <!-- Informações do Produto -->
                            <div class="flex-1 p-4 md:p-5 flex flex-col justify-between">
                                <div class="space-y-2">
                                    <div class="flex items-start justify-between">
                                        <h3 class="text-base md:text-lg font-bold text-gray-900 leading-tight line-clamp-2 pr-2">
                                            {{ $product['name'] }}
                                            @if ($product['size_ml'])
                                                <span class="text-sm text-gray-500 font-normal">({{ $product['size_ml'] }}ml)</span>
                                            @endif
                                        </h3>
                                    </div>
                                    
                                    @if ($product['description'])
                                        <p class="text-gray-600 text-sm leading-relaxed line-clamp-2 md:line-clamp-3">
                                            {{ $product['description'] }}
                                        </p>
                                    @endif
                                </div>
                                
                                <!-- Preço e Botão -->
                                <div class="flex items-end justify-between mt-3 gap-3">
                                    <div class="flex-1">
                                        <span class="text-primary font-bold text-lg md:text-xl">
                                            R$ {{ number_format($product['price'], 2, ',', '.') }}
                                        </span>
                                    </div>
                                    
                                    @if (!$storeStatus['is_open'])
                                        <button disabled
                                                class="px-3 py-2 md:px-4 bg-gray-300 text-gray-500 rounded-lg font-medium text-xs md:text-sm cursor-not-allowed flex items-center gap-2 flex-shrink-0">
                                            <i class="fas fa-lock text-xs"></i>
                                            <span class="hidden sm:inline">Indisponível</span>
                                        </button>
                                    @else
                                        @php
                                            // Verifica se a categoria do produto tem personalização
                                            $hasCustomization = false;
                                            if (isset($product['category_id']) && $product['category_id']) {
                                                $productCategory = collect($categories ?? [])->firstWhere('id', $product['category_id']);
                                                $hasCustomization = $productCategory && isset($productCategory['has_customization']) && $productCategory['has_customization'] == 1;
                                            } else {
                                                // Fallback para produtos sem categoria - verifica max_ingredients
                                                $hasCustomization = isset($product['max_ingredients']) && $product['max_ingredients'] > 0;
                                            }
                                        @endphp
                                        
                                        <button onclick="openCustomizeModal({{ $product['id'] }})" 
                                                class="px-4 py-2 md:px-6 bg-gradient-to-r from-primary to-secondary text-white rounded-lg font-medium text-xs md:text-sm hover:from-primary/90 hover:to-secondary/90 transition-all duration-200 flex items-center gap-2 shadow-md hover:shadow-lg btn-modern flex-shrink-0">
                                            @if ($hasCustomization)
                                                <i class="fas fa-cog text-xs"></i>
                                                <span class="hidden sm:inline">Montar</span>
                                                <span class="sm:hidden">+</span>
                                            @else
                                                <i class="fas fa-plus text-xs"></i>
                                                <span class="hidden sm:inline">Adicionar</span>
                                                <span class="sm:hidden">+</span>
                                            @endif
                                        </button>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Imagem do Produto -->
                            @if ($product['image_url'])
                                <div class="w-24 md:w-28 h-[120px] md:h-32 bg-gray-100 flex-shrink-0 relative overflow-hidden">
                                    <img src="{{ $product['image_url'] }}" 
                                         alt="{{ $product['name'] }}"
                                         class="w-full h-full object-cover {{ !$storeStatus['is_open'] ? 'grayscale' : '' }}">
                                    
                                    <!-- Badge de desconto ou novo (se necessário) -->
                                    {{-- @if (isset($product['discount']) && $product['discount'] > 0)
                                        <div class="absolute top-2 left-2 bg-danger text-white text-xs font-bold px-2 py-1 rounded-full">
                                            {{ $product['discount'] }}% OFF
                                        </div>
                                    @endif --}}
                                </div>
                            @else
                                <div class="w-24 md:w-28 h-[120px] md:h-32 bg-gradient-to-br from-primary/20 to-accent/20 flex-shrink-0 flex items-center justify-center">
                                    <span class="text-2xl md:text-3xl opacity-60">🍽️</span>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </main>

<!-- Modal de Personalização - Design Moderno -->
<div id="customizeModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden">
    <div class="flex items-end justify-center w-full h-full p-0 md:items-center md:p-4">
        <div class="bg-white w-full h-full md:max-w-lg md:max-h-[90vh] md:rounded-2xl shadow-2xl flex flex-col overflow-hidden">
            <!-- Header Moderno -->
            <div class="p-4 md:p-6 border-b border-gray-100 flex-shrink-0 bg-gradient-to-r from-primary/5 to-secondary/5">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-lg md:text-xl font-bold text-gray-900" id="modalTitle">Monte seu pedido</h3>
                        <p class="text-sm text-gray-600 mt-1">Personalize como quiser</p>
                    </div>
                    <button onclick="closeCustomizeModal()" 
                            class="w-10 h-10 bg-gray-100 hover:bg-gray-200 rounded-full flex items-center justify-center transition-colors z-10">
                        <i class="fas fa-times text-gray-600"></i>
                    </button>
                </div>
            </div>
            
            <!-- Conteúdo com Scroll -->
            <form id="customizeForm" class="flex-1 overflow-y-auto min-h-0">
                <div class="p-4 md:p-6 space-y-4 md:space-y-6" id="modalContent">
                    <!-- Conteúdo será preenchido via JavaScript -->
                </div>
            </form>
            
            <!-- Footer Fixo -->
            <div class="p-4 md:p-6 border-t border-gray-100 bg-white flex-shrink-0 z-10">
                <div class="flex justify-between items-center mb-3 md:mb-4">
                    <span class="text-base md:text-lg font-semibold text-gray-900">Total:</span>
                    <span class="text-xl md:text-2xl font-bold text-primary" id="totalPrice">R$ 0,00</span>
                </div>
                <button type="button" onclick="addCustomizedToCart()" 
                        class="w-full bg-gradient-to-r from-primary to-secondary text-white py-3 md:py-4 px-4 md:px-6 rounded-xl md:rounded-2xl font-semibold text-base md:text-lg hover:from-primary/90 hover:to-secondary/90 transition-all duration-200 shadow-lg hover:shadow-xl flex items-center justify-center gap-2 md:gap-3">
                    <i class="fas fa-shopping-cart"></i>
                    Adicionar ao carrinho
                    <i class="fas fa-arrow-right"></i>
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
        // Limpa dados anteriores primeiro
        currentProduct = null;
        selectedIngredients = [];
        productQty = 1;
        
        console.log('Opening modal for product ID:', productId); // Debug log
        
        const response = await fetch(`/{{ $store_slug }}/api/product/${productId}`);
        const product = await response.json();
        
        console.log('Product data received:', product); // Debug log
        
        if (product.error) {
            showToast('Erro ao carregar produto', 'error');
            return;
        }

        // Verifica se a categoria tem personalização
        let hasCustomization = false;
        if (product.category_id) {
            // Busca a categoria nas categorias disponíveis
            const categories = @json($categories ?? []);
            const productCategory = categories.find(cat => cat.id == product.category_id);
            hasCustomization = productCategory && productCategory.has_customization == 1;
            console.log('Category has customization:', hasCustomization, productCategory); // Debug log
        } else {
            // Fallback para produtos sem categoria
            hasCustomization = product.max_ingredients > 0;
        }

        // Garante que estamos usando o produto correto
        currentProduct = product;
        currentTotal = parseFloat(product.price);
        productQty = 1;
        selectedIngredients = [createEmptySelection(product)]; // Inicializa para 1 unidade

        // Atualiza o título do modal baseado na personalização
        const modalTitle = hasCustomization ? `Monte seu ${product.name}` : `Adicionar ${product.name}`;
        document.getElementById('modalTitle').textContent = modalTitle;
        
        renderModalContent(hasCustomization);
        showModal();
        updateTotal();
    } catch (error) {
        console.error('Erro ao abrir modal:', error);
        showToast('Erro ao carregar produto', 'error');
    }
}

function createEmptySelection(product) {
    // Cria um objeto para armazenar as escolhas de ingredientes por unidade
    const selection = {};
    
    // Só processa ingredientes se o produto realmente tiver ingredientes
    if (product && product.ingredients && typeof product.ingredients === 'object') {
        Object.keys(product.ingredients).forEach(type => {
            if (Array.isArray(product.ingredients[type])) {
                product.ingredients[type].forEach(ingredient => {
                    if (ingredient && ingredient.id) {
                        selection[ingredient.id] = { qty: 0, type: type };
                    }
                });
            }
        });
    }
    
    console.log('Created selection for product:', product?.name, selection); // Debug log
    return selection;
}

function renderModalContent(hasCustomization = true) {
    const product = currentProduct;
    
    if (!product) {
        console.error('No current product to render');
        return;
    }
    
    console.log('Rendering modal for product:', product.name, 'ID:', product.id, 'Has customization:', hasCustomization); // Debug log
    
    const productRules = maxIngredientsRules[product.id] || {};
    let content = `
        <input type="hidden" id="productId" value="${product.id}">
        <input type="hidden" id="productName" value="${product.name}">
        <input type="hidden" id="basePrice" value="${product.price}">
        
        <!-- Produto Info Card -->
        <div class="bg-gradient-to-r from-primary/5 to-secondary/5 rounded-2xl p-6 mb-6">
            ${product.image_url ? `
            <div class="mb-4">
                <img src="${product.image_url}" alt="${product.name}" class="w-full h-48 object-cover rounded-xl">
            </div>
            ` : ''}
            
            <div class="space-y-3">
                <h2 class="text-2xl font-bold text-gray-900">${product.name}</h2>
                ${product.description ? `<p class="text-gray-600 leading-relaxed">${product.description}</p>` : ''}
                <div class="flex items-center gap-2">
                    <span class="text-3xl font-bold text-primary">R$ ${parseFloat(product.price).toFixed(2).replace('.', ',')}</span>
                    <span class="text-sm text-gray-500 bg-gray-100 px-2 py-1 rounded-full">por unidade</span>
                </div>
            </div>
        </div>
        
        <!-- Quantidade -->
        <div class="bg-white border border-gray-200 rounded-2xl p-6 mb-6">
            <label class="block text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <i class="fas fa-sort-numeric-up text-primary"></i>
                Quantidade
            </label>
            <div class="flex items-center justify-center">
                ${renderModernStepper('qtyValue', productQty, 1, 10, 'changeProductQty(-1)', 'changeProductQty(1)')}
            </div>
        </div>
    `;

    // Só mostra seção de personalização se hasCustomization for true E houver ingredientes
    if (hasCustomization) {
        const hasIngredients = product.ingredients && 
                              typeof product.ingredients === 'object' && 
                              Object.keys(product.ingredients).some(type => 
                                  Array.isArray(product.ingredients[type]) && product.ingredients[type].length > 0
                              );

        console.log('Product has ingredients:', hasIngredients, product.ingredients); // Debug log

        if (hasIngredients) {
            for (let i = 0; i < productQty; i++) {
                content += `<div class="bg-white border border-gray-200 rounded-2xl p-6 mb-6">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 bg-primary/10 rounded-full flex items-center justify-center">
                            <i class="fas fa-utensils text-primary"></i>
                        </div>
                        <div>
                            <h4 class="text-lg font-semibold text-gray-900">
                                Personalize seu pedido ${productQty > 1 ? `#${i + 1}` : ''}
                            </h4>
                            <p class="text-sm text-gray-600">Escolha os ingredientes que desejar</p>
                        </div>
                    </div>`;
                
                Object.keys(product.ingredients).forEach(type => {
                    const maxPorTipo = productRules[type] || null;
                    if (Array.isArray(product.ingredients[type]) && product.ingredients[type].length > 0) {
                        content += `
                            <div class="mb-6 last:mb-0">
                                <h5 class="text-base font-semibold text-gray-800 mb-4 flex items-center gap-2">
                                    <span class="w-2 h-2 bg-primary rounded-full"></span>
                                    ${type.charAt(0).toUpperCase() + type.slice(1)}
                                    ${maxPorTipo ? `<span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded-full ml-2">Máx: ${maxPorTipo}</span>` : ''}
                                </h5>
                                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">`;
                        product.ingredients[type].forEach(ingredient => {
                            if (ingredient && ingredient.id) {
                                const img = ingredient.image_url || '/assets/images/ingredients/default.jpg';
                                const price = ingredient.additional_price > 0 ? `<span class="text-green-600 text-xs font-semibold mt-1">+R$ ${parseFloat(ingredient.additional_price).toFixed(2).replace('.', ',')}</span>` : '';
                                content += `
                                    <div class="bg-gray-50 hover:bg-gray-100 rounded-2xl p-4 transition-colors">
                                        <div class="text-center mb-3">
                                            <div class="w-16 h-16 mx-auto mb-2 bg-white rounded-xl overflow-hidden shadow-sm">
                                                <img src="${img}" alt="${ingredient.name}" class="w-full h-full object-cover">
                                            </div>
                                            <span class="text-sm font-medium text-gray-900 block">${ingredient.name}</span>
                                            ${price}
                                        </div>
                                        <div class="flex justify-center">
                                            ${renderModernStepper(
                                                `ingredient-qty-${ingredient.id}-unit${i}`,
                                                selectedIngredients[i] && selectedIngredients[i][ingredient.id] ? selectedIngredients[i][ingredient.id].qty : 0,
                                                0,
                                                10,
                                                `changeIngredientQty(${i},'${ingredient.id}', -1, '${type}')`,
                                                `changeIngredientQty(${i},'${ingredient.id}', 1, '${type}')`
                                            )}
                                        </div>
                                    </div>
                                `;
                            }
                        });
                        content += `</div></div>`;
                    }
                });
                content += `</div>`;
            }
        }
    }

    content += `
        <!-- Observações -->
        <div class="bg-white border border-gray-200 rounded-2xl p-6">
            <label for="notes" class="block text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <i class="fas fa-comment-dots text-primary"></i>
                Alguma observação?
            </label>
            <textarea id="notes" 
                class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary focus:border-transparent transition-all resize-none" 
                rows="3" 
                placeholder="Ex: sem cebola, ponto da carne, retirar ingrediente..."></textarea>
        </div>
    `;

    document.getElementById('modalContent').innerHTML = content;
    if (document.getElementById('qtyValue')) {
        document.getElementById('qtyValue').textContent = productQty;
    }
    
    console.log('Modal content rendered successfully'); // Debug log
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

function renderModernStepper(id, value, min, max, onMinus, onPlus) {
    const canDecrease = value > min;
    const canIncrease = value < max;
    
    return `
        <div class="flex items-center gap-3">
            <button type="button" 
                    class="w-10 h-10 ${canDecrease ? 'bg-gray-100 hover:bg-gray-200 text-gray-700' : 'bg-gray-50 text-gray-300 cursor-not-allowed'} rounded-xl flex items-center justify-center transition-all duration-200 font-bold text-lg" 
                    onclick="${canDecrease ? onMinus : ''}" ${!canDecrease ? 'disabled' : ''}>
                -
            </button>
            <span id="${id}" class="w-12 text-center font-bold text-lg text-gray-900">${value}</span>
            <button type="button" 
                    class="w-10 h-10 ${canIncrease ? 'bg-primary hover:bg-primary/90 text-white' : 'bg-gray-300 text-gray-500 cursor-not-allowed'} rounded-xl flex items-center justify-center transition-all duration-200 font-bold text-lg" 
                    onclick="${canIncrease ? onPlus : ''}" ${!canIncrease ? 'disabled' : ''}>
                +
            </button>
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
    if (maxPorTipo && delta > 0) {
        let totalGrupo = 0;
        Object.keys(selection).forEach(iid => {
            if (selection[iid].type === type && iid != id) {
                totalGrupo += selection[iid].qty;
            }
        });
        if (totalGrupo + newQty > maxPorTipo) {
            showToast(`Máximo de ${maxPorTipo} para ${type} na unidade ${unitIndex + 1}`, 'warning');
            return;
        }
    }

    selection[id].qty = newQty;
    
    // Atualiza o display do stepper
    const stepperElement = document.getElementById(`ingredient-qty-${id}-unit${unitIndex}`);
    if (stepperElement) {
        stepperElement.textContent = newQty;
    }
    
    // Re-renderiza o stepper para atualizar botões enabled/disabled
    const stepperContainer = stepperElement?.parentElement?.parentElement;
    if (stepperContainer) {
        const newStepperHTML = renderModernStepper(
            `ingredient-qty-${id}-unit${unitIndex}`,
            newQty,
            0,
            10,
            `changeIngredientQty(${unitIndex},'${id}', -1, '${type}')`,
            `changeIngredientQty(${unitIndex},'${id}', 1, '${type}')`
        );
        stepperContainer.innerHTML = newStepperHTML;
    }
    
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
    modal.classList.add('flex');
    
    // Previne scroll do body quando modal está aberto
    document.body.style.overflow = 'hidden';
    
    // Adiciona animação suave
    setTimeout(() => {
        const modalContent = modal.querySelector('.bg-white');
        modalContent.style.transform = 'translateY(0)';
        modalContent.style.opacity = '1';
    }, 10);
}

function closeCustomizeModal() {
    const modal = document.getElementById('customizeModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    
    // Restaura scroll do body
    document.body.style.overflow = 'auto';
    
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

