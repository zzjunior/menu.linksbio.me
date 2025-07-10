<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <link rel="stylesheet" href="/assets/css/app.css">
    <link rel="icon" type="image/x-icon" href="/assets/favicon.ico">
    <meta name="theme-color" content="#8B5CF6">
    <meta name="description" content="<?= htmlspecialchars($store['store_description'] ?? 'Cardápio digital da sua loja') ?>">
    <link rel="apple-touch-icon" sizes="180x180" href="/assets/apple-touch-icon.png">
    <link rel="manifest" href="/assets/manifest.json">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script>
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
    <!-- Header -->
    <header class="bg-white shadow-lg sticky top-0 z-50">
        <div class="container mx-auto px-4 py-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-xl font-bold text-gray-800"><?= htmlspecialchars($store['store_name']) ?></h1>
                    <?php if ($store['address']): ?>
                        <p class="text-sm text-gray-600">
                            <i class="fas fa-map-marker-alt mr-1"></i>
                            <?= htmlspecialchars($store['address']) ?>
                        </p>
                    <?php endif; ?>
                </div>
                <a href="/<?= $store_slug ?>/carrinho" class="relative bg-primary text-white p-3 rounded-full hover:bg-secondary transition-colors">
                    <i class="fas fa-shopping-cart"></i>
                    <span id="cart-count" class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">0</span>
                </a>
            </div>
        </div>
    </header>

    <!-- Filtros de Categoria -->
    <?php if (!empty($categories)): ?>
        <div class="container mx-auto px-4 py-4">
            <div class="flex gap-2 overflow-x-auto pb-2">
                <a href="/<?= $store_slug ?>" 
                   class="px-4 py-2 rounded-full text-sm font-medium whitespace-nowrap transition-colors
                          <?= empty($currentCategory) ? 'bg-primary text-white' : 'bg-white text-gray-700 hover:bg-gray-100' ?>">
                    Todos
                </a>
                <?php foreach ($categories as $category): ?>
                    <a href="/<?= $store_slug ?>?category=<?= $category['id'] ?>" 
                       class="px-4 py-2 rounded-full text-sm font-medium whitespace-nowrap transition-colors
                              <?= $currentCategory == $category['id'] ? 'bg-primary text-white' : 'bg-white text-gray-700 hover:bg-gray-100' ?>">
                        <?= htmlspecialchars($category['name']) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Lista de Produtos -->
    <main class="max-w-md mx-auto px-4 pb-8">
        <div class="grid gap-4">        <?php foreach ($products as $product): ?>
            <div class="bg-white rounded-2xl shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300 product-card card-hover">
                <?php if ($product['image_url']): ?>
                    <div class="h-40 bg-gradient-to-br from-primary/20 to-accent/20 relative overflow-hidden">
                        <img src="<?= $this->escape($product['image_url']) ?>" 
                             alt="<?= $this->escape($product['name']) ?>"
                             class="w-full h-full object-cover">
                    </div>
                <?php else: ?>
                    <div class="h-40 bg-gradient-to-br from-primary/20 to-accent/20 flex items-center justify-center">
                        <span class="text-4xl">🍇</span>
                    </div>
                <?php endif; ?>
                    
                    <div class="p-4">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="text-lg font-semibold text-gray-900 leading-tight">
                                <?= $this->escape($product['name']) ?>
                                <?php if ($product['size_ml']): ?>
                                    <span class="text-sm text-gray-500">(<?= $product['size_ml'] ?>ml)</span>
                                <?php endif; ?>
                            </h3>
                            <span class="text-primary font-bold text-lg">
                                <?= $this->formatPrice($product['price']) ?>
                            </span>
                        </div>
                        
                        <?php if ($product['description']): ?>
                            <p class="text-gray-600 text-sm mb-3">
                                <?= $this->escape($product['description']) ?>
                            </p>
                        <?php endif; ?>
                        
                        <?php if ($product['category_name'] === 'Açaí'): ?>
                            <button onclick="openAcaiModal(<?= $product['id'] ?>)" 
                                    class="w-full bg-gradient-to-r from-primary to-secondary text-white py-2 px-4 rounded-lg font-medium hover:from-primary/90 hover:to-secondary/90 transition-all duration-200 btn-ripple">
                                🎯 Monte seu Açaí
                            </button>
                        <?php else: ?>
                            <button onclick="app.addToCart({id: <?= $product['id'] ?>, name: '<?= addslashes($product['name']) ?>', price: <?= $product['price'] ?>})"
                                    class="w-full bg-gradient-to-r from-primary to-secondary text-white py-2 px-4 rounded-lg font-medium hover:from-primary/90 hover:to-secondary/90 transition-all duration-200 btn-ripple">
                                🛒 Adicionar ao Pedido
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if (empty($products)): ?>
            <div class="text-center py-12">
                <div class="text-6xl mb-4">🤷‍♀️</div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Nenhum produto encontrado</h3>
                <p class="text-gray-600">Tente selecionar outra categoria</p>
            </div>
        <?php endif; ?>
    </main>

    <!-- Modal de Personalização Estilo iFood -->
    <div id="acaiModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden" onclick="closeAcaiModal()">
        <div class="fixed inset-x-0 bottom-0 bg-white rounded-t-3xl max-h-[90vh] overflow-hidden animate-slide-up shadow-2xl" onclick="event.stopPropagation()">
            
            <!-- Handle para arrastar -->
            <div class="modal-handle bg-gray-300"></div>
            
            <!-- Header com imagem e botões -->
            <div class="relative">
                <!-- Imagem do produto -->
                <div class="h-56 bg-gradient-to-br from-purple-400 to-pink-500 relative overflow-hidden">
                    <img id="modalProductImage" src="" alt="" class="w-full h-full object-cover">
                    
                    <!-- Overlay com gradiente -->
                    <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent">
                        <!-- Botão voltar -->
                        <button onclick="closeAcaiModal()" class="absolute top-4 left-4 w-10 h-10 bg-white/90 backdrop-blur-sm rounded-full flex items-center justify-center shadow-lg hover:bg-white transition-all">
                            <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </button>
                        
                        <!-- Botão favorito -->
                        <button class="absolute top-4 right-14 w-10 h-10 bg-white/90 backdrop-blur-sm rounded-full flex items-center justify-center shadow-lg hover:bg-white transition-all">
                            <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                            </svg>
                        </button>
                        
                        <!-- Botão compartilhar -->
                        <button class="absolute top-4 right-4 w-10 h-10 bg-white/90 backdrop-blur-sm rounded-full flex items-center justify-center shadow-lg hover:bg-white transition-all">
                            <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Conteúdo scrollável -->
            <div class="flex-1 overflow-y-auto custom-scrollbar">
                <!-- Info do produto -->
                <div class="p-6 pb-2">
                    <h2 id="modalTitle" class="text-2xl font-bold text-gray-900 mb-2">Açaí Personalizado</h2>
                    <p id="modalDescription" class="text-base text-gray-600 mb-3 leading-relaxed">Nosso açaí cremoso e delicioso</p>
                    
                    <!-- Tempo e avaliação -->
                    <div class="flex items-center space-x-4 text-sm text-gray-500 mb-4">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-1 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>15-20 min</span>
                        </div>
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-1 text-yellow-500" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path>
                            </svg>
                            <span>4.8 (120+)</span>
                        </div>
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-1 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            <span>Serve 1 pessoa</span>
                        </div>
                    </div>
                </div>

                <!-- Seções de personalização -->
                <div id="modalContent" class="pb-32">
                    <!-- Conteúdo será preenchido via JavaScript -->
                </div>
            </div>

            <!-- Footer fixo com total e botão -->
            <div class="absolute bottom-0 left-0 right-0 bg-white border-t border-gray-100 shadow-lg">
                <!-- Observações -->
                <div class="px-6 py-4 border-b border-gray-100">
                    <div class="flex items-center justify-between">
                        <label class="flex items-center text-sm font-medium text-gray-700">
                            <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-1l-4 4z"></path>
                            </svg>
                            Adicione uma observação
                        </label>
                        <button onclick="toggleComment()" class="text-red-500 text-sm font-medium">
                            Opcional
                        </button>
                    </div>
                    <div id="commentSection" class="hidden mt-3">
                        <textarea 
                            id="productComment"
                            placeholder="Ex: sem açúcar, açaí bem doce, capricha nos ingredientes..."
                            class="w-full px-3 py-3 border border-gray-200 rounded-xl text-sm resize-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all"
                            rows="3"
                            maxlength="140"
                            onInput="updateCommentCounter(this)"></textarea>
                        <div class="text-xs text-gray-500 mt-2 text-right" id="commentCounter">0 / 140</div>
                    </div>
                </div>

                <!-- Contador de quantidade e botão adicionar -->
                <div class="px-6 py-4">
                    <div class="flex items-center justify-between gap-4">
                        <!-- Contador -->
                        <div class="flex items-center border border-gray-200 rounded-xl quantity-counter bg-gray-50">
                            <button onclick="decrementQuantity()" class="w-12 h-12 flex items-center justify-center text-red-500 hover:bg-red-50 rounded-l-xl transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                </svg>
                            </button>
                            <div class="w-12 h-12 flex items-center justify-center font-semibold text-gray-900" id="quantityDisplay">1</div>
                            <button onclick="incrementQuantity()" class="w-12 h-12 flex items-center justify-center text-red-500 hover:bg-red-50 rounded-r-xl transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                            </button>
                        </div>

                        <!-- Botão adicionar -->
                        <button onclick="addToCart()" 
                                class="flex-1 bg-red-500 text-white py-4 px-6 rounded-xl font-semibold flex items-center justify-center space-x-3 add-to-cart-btn btn-ripple shadow-lg"
                                id="addToCartBtn">
                            <span>Adicionar</span>
                            <span id="totalPrice" class="font-bold">R$ 12,90</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="/assets/js/app.js"></script>
    
    <style>
        .animate-slide-up {
            animation: slideUp 0.3s ease-out;
        }
        
        @keyframes slideUp {
            from {
                transform: translateY(100%);
            }
            to {
                transform: translateY(0);
            }
        }
        
        .ingredient-option {
            transition: all 0.2s ease;
        }
        
        .ingredient-option.selected {
            border-color: #ef4444;
            background-color: #fef2f2;
            transform: scale(1.02);
        }
        
        .ingredient-counter {
            transition: all 0.2s ease;
        }
        
        .ingredient-counter.active {
            border-color: #ef4444;
            background-color: #fef2f2;
        }
        
        .ingredient-counter button:hover {
            background-color: #fee2e2;
        }
        
        /* Estilo para textarea */
        .comment-textarea {
            resize: none;
            transition: all 0.2s ease;
        }
        
        .comment-textarea:focus {
            transform: scale(1.02);
        }
        
        /* Animação para botão adicionar */
        .add-to-cart-btn {
            transition: all 0.2s ease;
        }
        
        .add-to-cart-btn:hover {
            transform: scale(1.02);
            box-shadow: 0 8px 25px rgba(239, 68, 68, 0.3);
        }
        
        .add-to-cart-btn:active {
            transform: scale(0.98);
        }
        
        /* Toast animation */
        .toast-enter {
            animation: toastSlideIn 0.3s ease-out;
        }
        
        @keyframes toastSlideIn {
            from {
                transform: translate(-50%, -100px);
                opacity: 0;
            }
            to {
                transform: translate(-50%, 0);
                opacity: 1;
            }
        }
        
        /* Contador com destaque */
        .quantity-counter {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        
        /* Handle do modal */
        .modal-handle {
            width: 40px;
            height: 4px;
            background-color: #D1D5DB;
            border-radius: 2px;
            margin: 8px auto;
        }
        
        /* Scrollbar customizada */
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 2px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #a1a1a1;
        }
        
        /* Efeito ripple nos botões */
        .btn-ripple {
            position: relative;
            overflow: hidden;
        }
        
        .btn-ripple::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }
        
        .btn-ripple:active::after {
            width: 300px;
            height: 300px;
        }
    </style>

    <script>
        let currentProduct = null;
        let selectedIngredients = [];
        let currentQuantity = 1;
        let basePrice = 0;

        // Função para abrir modal
        async function openAcaiModal(productId) {
            try {
                const response = await fetch(`/api/product/${productId}`);
                const product = await response.json();
                
                currentProduct = product;
                basePrice = parseFloat(product.price);
                selectedIngredients = [];
                currentQuantity = 1;
                
                // Atualizar informações do produto
                document.getElementById('modalTitle').textContent = product.name;
                document.getElementById('modalDescription').textContent = product.description || 'Produto delicioso e personalizado';
                document.getElementById('modalBasePrice').textContent = `R$ ${product.price}`;
                document.getElementById('modalProductImage').src = product.image || '/assets/images/default-acai.jpg';
                
                // Gerar conteúdo das seções
                generateModalContent(product);
                
                // Mostrar modal
                document.getElementById('acaiModal').classList.remove('hidden');
                document.body.style.overflow = 'hidden';
                
                updateTotalPrice();
            } catch (error) {
                console.error('Erro ao carregar produto:', error);
            }
        }

        // Função para gerar conteúdo do modal
        function generateModalContent(product) {
            const content = `
                <!-- Seção Cremes (Obrigatório) -->
                <div class="px-4 py-3 border-b border-gray-100">
                    <div class="flex items-center justify-between mb-3">
                        <div>
                            <h3 class="font-semibold text-gray-900">Cremes</h3>
                            <p class="text-sm text-gray-500">Escolha pelo menos 1 opção</p>
                        </div>
                        <div class="flex gap-2">
                            <span class="px-2 py-1 bg-gray-100 text-xs font-medium rounded">0/2</span>
                            <span class="px-2 py-1 bg-red-100 text-red-800 text-xs font-medium rounded">OBRIGATÓRIO</span>
                        </div>
                    </div>
                    
                    <div class="space-y-3">
                        ${generateIngredientOption('acai', 'Açaí', '/assets/images/ingredients/acai.jpg', 0, 'cream', true)}
                        ${generateIngredientOption('cupuacu', 'Creme de Cupuaçu', '/assets/images/ingredients/cupuacu.jpg', 0, 'cream', true)}
                        ${generateIngredientOption('ninho', 'Creme de Ninho', '/assets/images/ingredients/ninho.jpg', 0, 'cream', true)}
                        ${generateIngredientOption('amendoim_cream', 'Creme de Amendoim', '/assets/images/ingredients/amendoim-cream.jpg', 0, 'cream', true)}
                        ${generateIngredientOption('morango', 'Creme de Morango', '/assets/images/ingredients/morango.jpg', 0, 'cream', true)}
                    </div>
                </div>

                <!-- Seção Coberturas (Obrigatório) -->
                <div class="px-4 py-3 border-b border-gray-100">
                    <div class="flex items-center justify-between mb-3">
                        <div>
                            <h3 class="font-semibold text-gray-900">Escolha até 5 coberturas</h3>
                            <p class="text-sm text-gray-500">Escolha pelo menos 1 opção</p>
                        </div>
                        <div class="flex gap-2">
                            <span class="px-2 py-1 bg-gray-100 text-xs font-medium rounded">0/5</span>
                            <span class="px-2 py-1 bg-red-100 text-red-800 text-xs font-medium rounded">OBRIGATÓRIO</span>
                        </div>
                    </div>
                    
                    <div class="space-y-3">
                        ${generateIngredientOption('pacoca', 'Paçoca', '/assets/images/ingredients/pacoca.jpg', 0, 'topping', true)}
                        ${generateIngredientOption('leite_po', 'Leite em Pó', '/assets/images/ingredients/leite-po.jpg', 0, 'topping', true)}
                        ${generateIngredientOption('ovomaltine', 'Ovomaltine', '/assets/images/ingredients/ovomaltine.jpg', 0, 'topping', true)}
                        ${generateIngredientOption('granola', 'Granola', '/assets/images/ingredients/granola.jpg', 0, 'topping', true)}
                        ${generateIngredientOption('amendoim', 'Amendoim Triturado', '/assets/images/ingredients/amendoim.jpg', 0, 'topping', true)}
                        ${generateIngredientOption('chocoball', 'Chocoball', '/assets/images/ingredients/chocoball.jpg', 0, 'topping', true)}
                        ${generateIngredientOption('confetes', 'Confetes', '/assets/images/ingredients/confetes.jpg', 0, 'topping', true)}
                        ${generateIngredientOption('farinha_lacta', 'Farinha Láctea', '/assets/images/ingredients/farinha-lacta.jpg', 0, 'topping', true)}
                        ${generateIngredientOption('banana', 'Banana', '/assets/images/ingredients/banana.jpg', 0, 'topping', true)}
                        ${generateIngredientOption('leite_condensado', 'Leite Condensado', '/assets/images/ingredients/leite-condensado.jpg', 0, 'topping', true)}
                        ${generateIngredientOption('chocolate', 'Cobertura de Chocolate', '/assets/images/ingredients/chocolate.jpg', 0, 'topping', true)}
                    </div>
                </div>

                <!-- Seção Extras (Opcional) -->
                <div class="px-4 py-3">
                    <div class="flex items-center justify-between mb-3">
                        <div>
                            <h3 class="font-semibold text-gray-900">Extras</h3>
                            <p class="text-sm text-gray-500">Escolha até 10 opções</p>
                        </div>
                        <div class="flex gap-2">
                            <span class="text-green-500">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                            </span>
                        </div>
                    </div>
                    
                    <div class="space-y-3">
                        ${generateIngredientOption('leite_po_extra', 'Leite em Pó', '/assets/images/ingredients/leite-po.jpg', 2.90, 'extra', false)}
                        ${generateIngredientOption('pacoca_extra', 'Paçoca', '/assets/images/ingredients/pacoca.jpg', 2.90, 'extra', false)}
                        ${generateIngredientOption('ovomaltine_extra', 'Ovomaltine', '/assets/images/ingredients/ovomaltine.jpg', 2.90, 'extra', false)}
                        ${generateIngredientOption('amendoim_extra', 'Amendoim Triturado', '/assets/images/ingredients/amendoim.jpg', 2.90, 'extra', false)}
                        ${generateIngredientOption('granola_extra', 'Granola', '/assets/images/ingredients/granola.jpg', 2.90, 'extra', false)}
                        ${generateIngredientOption('chocoball_extra', 'Chocoball', '/assets/images/ingredients/chocoball.jpg', 2.90, 'extra', false)}
                        ${generateIngredientOption('confetes_extra', 'Confetes', '/assets/images/ingredients/confetes.jpg', 2.90, 'extra', false)}
                        ${generateIngredientOption('farinha_lacta_extra', 'Farinha Láctea', '/assets/images/ingredients/farinha-lacta.jpg', 2.90, 'extra', false)}
                        ${generateIngredientOption('banana_extra', 'Banana', '/assets/images/ingredients/banana.jpg', 2.90, 'extra', false)}
                        ${generateIngredientOption('leite_condensado_extra', 'Leite Condensado', '/assets/images/ingredients/leite-condensado.jpg', 2.90, 'extra', false)}
                        ${generateIngredientOption('chocolate_extra', 'Cobertura de Chocolate', '/assets/images/ingredients/chocolate.jpg', 2.90, 'extra', false)}
                        ${generateIngredientOption('nutella', 'Nutella', '/assets/images/ingredients/nutella.jpg', 5.50, 'extra', false)}
                    </div>
                </div>
            `;
            
            document.getElementById('modalContent').innerHTML = content;
        }

        // Função para gerar opção de ingrediente
        function generateIngredientOption(id, name, image, price, category, required) {
            const priceText = price > 0 ? `<span class="text-sm font-medium text-green-600">+ R$ ${price.toFixed(2)}</span>` : '';
            
            return `
                <div class="ingredient-option border border-gray-200 rounded-lg p-3" data-id="${id}" data-price="${price}" data-category="${category}" data-required="${required}">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3 flex-1">
                            <div class="w-12 h-12 rounded-lg overflow-hidden bg-gray-100 flex-shrink-0">
                                <img src="${image}" alt="${name}" class="w-full h-full object-cover" onerror="this.src='/assets/images/ingredients/default.jpg'">
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-medium text-gray-900 truncate">${name}</p>
                                ${priceText}
                            </div>
                        </div>
                        
                        <div class="ingredient-counter flex items-center border border-gray-300 rounded-lg ml-3">
                            <button onclick="decrementIngredient('${id}')" class="w-8 h-8 flex items-center justify-center text-red-500 hover:bg-gray-50 rounded-l-lg">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                </svg>
                            </button>
                            <div class="w-8 h-8 flex items-center justify-center text-sm font-medium ingredient-count" data-id="${id}">0</div>
                            <button onclick="incrementIngredient('${id}')" class="w-8 h-8 flex items-center justify-center text-red-500 hover:bg-gray-50 rounded-r-lg">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            `;
        }

        // Funções para incrementar/decrementar ingredientes
        function incrementIngredient(id) {
            const ingredient = selectedIngredients.find(i => i.id === id);
            if (ingredient) {
                ingredient.quantity++;
            } else {
                const option = document.querySelector(`[data-id="${id}"]`);
                selectedIngredients.push({
                    id: id,
                    quantity: 1,
                    price: parseFloat(option.dataset.price),
                    category: option.dataset.category
                });
            }
            
            updateIngredientDisplay(id);
            updateTotalPrice();
        }

        function decrementIngredient(id) {
            const ingredientIndex = selectedIngredients.findIndex(i => i.id === id);
            if (ingredientIndex !== -1) {
                const ingredient = selectedIngredients[ingredientIndex];
                ingredient.quantity--;
                
                if (ingredient.quantity <= 0) {
                    selectedIngredients.splice(ingredientIndex, 1);
                }
            }
            
            updateIngredientDisplay(id);
            updateTotalPrice();
        }

        // Atualizar display do ingrediente
        function updateIngredientDisplay(id) {
            const ingredient = selectedIngredients.find(i => i.id === id);
            const count = ingredient ? ingredient.quantity : 0;
            
            const countElement = document.querySelector(`.ingredient-count[data-id="${id}"]`);
            const optionElement = document.querySelector(`[data-id="${id}"]`);
            
            if (countElement) {
                countElement.textContent = count;
            }
            
            if (optionElement) {
                if (count > 0) {
                    optionElement.classList.add('selected');
                    optionElement.querySelector('.ingredient-counter').classList.add('active');
                } else {
                    optionElement.classList.remove('selected');
                    optionElement.querySelector('.ingredient-counter').classList.remove('active');
                }
            }
        }

        // Funções para quantidade do produto
        function incrementQuantity() {
            currentQuantity++;
            document.getElementById('quantityDisplay').textContent = currentQuantity;
            updateTotalPrice();
        }

        function decrementQuantity() {
            if (currentQuantity > 1) {
                currentQuantity--;
                document.getElementById('quantityDisplay').textContent = currentQuantity;
                updateTotalPrice();
            }
        }

        // Atualizar preço total
        function updateTotalPrice() {
            let ingredientsTotal = selectedIngredients.reduce((total, ingredient) => {
                return total + (ingredient.price * ingredient.quantity);
            }, 0);
            
            let totalPrice = (basePrice + ingredientsTotal) * currentQuantity;
            document.getElementById('totalPrice').textContent = `R$ ${totalPrice.toFixed(2)}`;
        }

        // Fechar modal
        function closeAcaiModal() {
            document.getElementById('acaiModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        // Adicionar ao carrinho
        function addToCart() {
            // Aqui você implementaria a lógica para adicionar ao carrinho
            console.log('Produto:', currentProduct);
            console.log('Ingredientes selecionados:', selectedIngredients);
            console.log('Quantidade:', currentQuantity);
            
            closeAcaiModal();
            
            // Mostrar toast de sucesso
            showToast('Produto adicionado ao carrinho! 🛒');
        }

        // Função para mostrar toast
        function showToast(message) {
            const toast = document.createElement('div');
            toast.className = 'fixed top-4 left-1/2 transform -translate-x-1/2 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg z-50 toast-enter';
            toast.textContent = message;
            
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.style.animation = 'toastSlideIn 0.3s ease-out reverse';
                setTimeout(() => toast.remove(), 300);
            }, 2700);
        }

        // Função para atualizar contador de comentários
        function updateCommentCounter(textarea) {
            const counter = document.getElementById('commentCounter');
            const length = textarea.value.length;
            counter.textContent = `${length} / 140`;
            
            if (length > 120) {
                counter.classList.add('text-yellow-600');
                counter.classList.remove('text-gray-500');
            } else if (length >= 140) {
                counter.classList.add('text-red-600');
                counter.classList.remove('text-yellow-600', 'text-gray-500');
            } else {
                counter.classList.add('text-gray-500');
                counter.classList.remove('text-yellow-600', 'text-red-600');
            }
        }

        // Adicionar swipe para fechar modal (touch)
        let startY = 0;
        let currentY = 0;
        let isModalSwiping = false;

        function handleTouchStart(e) {
            startY = e.touches[0].clientY;
            isModalSwiping = true;
        }

        function handleTouchMove(e) {
            if (!isModalSwiping) return;
            
            currentY = e.touches[0].clientY;
            const diffY = currentY - startY;
            
            if (diffY > 0) {
                const modal = document.querySelector('#acaiModal .animate-slide-up');
                modal.style.transform = `translateY(${diffY}px)`;
            }
        }

        function handleTouchEnd(e) {
            if (!isModalSwiping) return;
            
            const diffY = currentY - startY;
            const modal = document.querySelector('#acaiModal .animate-slide-up');
            
            if (diffY > 100) {
                closeAcaiModal();
            } else {
                modal.style.transform = 'translateY(0)';
            }
            
            isModalSwiping = false;
        }

        // Adicionar eventos de touch ao modal
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.querySelector('#acaiModal .animate-slide-up');
            if (modal) {
                modal.addEventListener('touchstart', handleTouchStart, { passive: false });
                modal.addEventListener('touchmove', handleTouchMove, { passive: false });
                modal.addEventListener('touchend', handleTouchEnd, { passive: false });
            }
        });
    </script>
</body>
</html>
