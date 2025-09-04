<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <link rel="stylesheet" href="/assets/css/custom.css">
    <link rel="stylesheet" href="/assets/css/ifood-modal.css">
    <link rel="icon" type="image/x-icon" href="/assets/favicon.ico">
    <meta name="theme-color" content="#8B5CF6">
    <meta name="description" content="<?= htmlspecialchars($store['store_description'] ?? 'Cardápio digital da sua loja') ?>">
    <link rel="apple-touch-icon" sizes="180x180" href="/assets/apple-touch-icon.png">
    <link rel="manifest" href="/assets/manifest.json">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
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
                <div class="flex items-center space-x-4">
                    <?php if (!empty($store['logo'])): ?>
                        <img src="<?= htmlspecialchars($store['logo']) ?>" alt="Logo da loja"
                             class="w-16 h-16 rounded-full object-cover border-2 border-primary flex-shrink-0">
                    <?php endif; ?>
                    <div class="flex-1 min-w-0">
                        <h1 class="text-lg sm:text-xl font-bold text-gray-800 truncate"><?= htmlspecialchars($store['store_name']) ?></h1>
                        <?php if (!empty($store['address'])): ?>
                            <p class="text-xs sm:text-sm text-gray-600 truncate">
                                <i class="fas fa-map-marker-alt mr-1"></i>
                                <?= htmlspecialchars($store['address']) ?>
                            </p>
                        <?php endif; ?>
                        <?php if (!empty($store['whatsapp'])): ?>
                            <p class="text-xs sm:text-sm text-gray-600 truncate">
                                <i class="fab fa-whatsapp mr-1"></i>
                                <?= htmlspecialchars($store['whatsapp']) ?>
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
                <a href="/<?= $store_slug ?>/carrinho" class="relative bg-primary text-white p-2 rounded-full hover:bg-secondary transition-colors flex-shrink-0">
                    <i class="fas fa-shopping-cart p-2" style="font-size: 0.7rem;"></i>
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

    <!-- Produtos -->
    <main class="container mx-auto px-4 pb-6">
        <?php if (empty($products)): ?>
            <div class="text-center py-12">
                <div class="text-6xl mb-4">🤷‍♀️</div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Nenhum produto encontrado</h3>
                <p class="text-gray-600">Adicione produtos ao seu cardápio no painel admin</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <?php foreach ($products as $product): ?>
                    <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow duration-300 overflow-hidden">
                        <?php if ($product['image_url']): ?>
                            <div class="h-40 bg-gradient-to-br from-primary/20 to-accent/20 relative overflow-hidden">
                                <img src="<?= htmlspecialchars($product['image_url']) ?>" 
                                     alt="<?= htmlspecialchars($product['name']) ?>"
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
                                    <?= htmlspecialchars($product['name']) ?>
                                    <?php if ($product['size_ml']): ?>
                                        <span class="text-sm text-gray-500">(<?= $product['size_ml'] ?>ml)</span>
                                    <?php endif; ?>
                                </h3>
                                <span class="text-primary font-bold text-lg">
                                    R$ <?= number_format($product['price'], 2, ',', '.') ?>
                                </span>
                            </div>
                            
                            <?php if ($product['description']): ?>
                                <p class="text-gray-600 text-sm mb-3">
                                    <?= htmlspecialchars($product['description']) ?>
                                </p>
                            <?php endif; ?>
                            
                            <?php if ($product['max_ingredients'] > 0): ?>
                                <button onclick="openCustomizeModal(<?= $product['id'] ?>)" 
                                        class="w-full bg-gradient-to-r from-primary to-secondary text-white py-2 px-4 rounded-lg font-medium hover:from-primary/90 hover:to-secondary/90 transition-all duration-200">
                                    🎯 Monte o seu
                                </button>
                            <?php else: ?>
                                <button onclick="addToCart(<?= $product['id'] ?>, '<?= addslashes($product['name']) ?>', <?= $product['price'] ?>)"
                                        class="w-full bg-gradient-to-r from-primary to-secondary text-white py-2 px-4 rounded-lg font-medium hover:from-primary/90 hover:to-secondary/90 transition-all duration-200">
                                    🛒 Adicionar ao Pedido
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>

    <!-- Modal de Personalização -->
    <div id="customizeModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
        <div class="flex items-end justify-center min-h-screen p-4">
            <div class="bg-white rounded-t-3xl w-full max-w-md max-h-[80vh] overflow-hidden">
                <div class="p-4 border-b">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-semibold" id="modalTitle">Monte o seu Produto</h3>
                        <button onclick="closeCustomizeModal()" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                </div>
                
                <form id="customizeForm" class="overflow-y-auto max-h-96">
                    <div class="p-4" id="modalContent">
                        <!-- Conteúdo será preenchido via JavaScript -->
                    </div>
                </form>
                
                <div class="p-4 border-t bg-gray-50">
                    <div class="flex justify-between items-center mb-3">
                        <span class="font-medium">Total:</span>
                        <span class="text-xl font-bold text-primary" id="totalPrice">R$ 0,00</span>
                    </div>
                    <button onclick="addCustomizedToCart()" 
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

        // Atualizar contador do carrinho
        function updateCartCount() {
            const cart = JSON.parse(localStorage.getItem('cart_<?= $store['id'] ?>') || '[]');
            document.getElementById('cart-count').textContent = cart.length;
        }

        // Adicionar produto simples ao carrinho
        function addToCart(productId, productName, price) {
            const cart = JSON.parse(localStorage.getItem('cart_<?= $store['id'] ?>') || '[]');
            cart.push({
                cart_id: Date.now().toString(),
                product_id: productId,
                name: productName,
                price: price,
                quantity: 1,
                ingredients: {},
                notes: '',
                size: ''
            });
            localStorage.setItem('cart_<?= $store['id'] ?>', JSON.stringify(cart));
            updateCartCount();
            showToast('Produto adicionado ao carrinho!');
        }

        // Abrir modal de personalização
        async function openCustomizeModal(productId) {
            try {
                const response = await fetch(`/<?= $store_slug ?>/api/product/${productId}`);
                const product = await response.json();

                if (product.error) {
                    showToast('Erro ao carregar produto', 'error');
                    return;
                }

                currentProduct = product;
                currentTotal = parseFloat(product.price);

                document.getElementById('modalTitle').textContent = `Monte seu ${product.name}`;
                document.getElementById('totalPrice').textContent = `R$ ${currentTotal.toFixed(2).replace('.', ',')}`;

                let content = `
                    <input type="hidden" id="productId" value="${product.id}">
                    <input type="hidden" id="productName" value="${product.name}">
                    <input type="hidden" id="basePrice" value="${product.price}">
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Quantidade</label>
                        <select id="quantity" class="w-full border border-gray-300 rounded-md px-3 py-2" onchange="updateTotal()">
                            <option value="1">1 unidade</option>
                            <option value="2">2 unidades</option>
                            <option value="3">3 unidades</option>
                        </select>
                    </div>
                `;

                // Adicionar ingredientes agrupados por tipo
                if (product.ingredients) {
                    for (const [type, ingredients] of Object.entries(product.ingredients)) {
                        content += `
                            <div class="mb-4">
                                <h4 class="text-md font-semibold text-gray-800 mb-2">${type}</h4>
                                <div class="space-y-2">
                        `;
                        ingredients.forEach(ingredient => {
                            content += `
                                <div class="flex items-center justify-between">
                                    <label class="flex items-center">
                                        <input type="checkbox" 
                                            class="ingredient-checkbox mr-2" 
                                            data-id="${ingredient.id}" 
                                            data-price="${ingredient.additional_price}"
                                            onchange="updateTotal()">
                                        ${ingredient.name}
                                    </label>
                                    ${ingredient.additional_price > 0 ? 
                                        `<span class="text-green-600 text-sm">+R$ ${parseFloat(ingredient.additional_price).toFixed(2).replace('.', ',')}</span>` : 
                                        '<span class="text-gray-500 text-sm">Grátis</span>'
                                    }
                                </div>
                            `;
                        });
                        content += '</div></div>';
                    }
                }

                content += `
                    <div class="mb-4">
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Observações</label>
                        <textarea id="notes" 
                            class="w-full border border-gray-300 rounded-md px-3 py-2" 
                            rows="3" 
                            placeholder="Alguma observação especial?"></textarea>
                    </div>
                `;

                document.getElementById('modalContent').innerHTML = content;

                // Tornar modal fullscreen e responsivo
                const modal = document.getElementById('customizeModal');
                modal.classList.remove('hidden');
                modal.classList.add('flex', 'items-center', 'justify-center');
                modal.style.padding = '0';

                // Preenche toda tela no mobile e desktop
                const modalOuter = modal.querySelector('div');
                modalOuter.className = 'flex items-center justify-center min-h-screen w-full p-0';

                const modalBox = modalOuter.querySelector('div');
                modalBox.className = 'bg-white w-full h-full rounded-none shadow-lg max-w-none max-h-none overflow-auto flex flex-col';

                // Ajusta bordas para mobile e desktop
                if (window.innerWidth < 640) {
                    modalBox.classList.add('rounded-none');
                    modalBox.style.borderRadius = '0';
                } else {
                    modalBox.classList.add('rounded-2xl');
                    modalBox.style.borderRadius = '1.5rem';
                }

                // Garante que modal ocupa toda tela no mobile
                modalBox.style.width = '100vw';
                modalBox.style.height = '100vh';
                modalBox.style.maxWidth = '100vw';
                modalBox.style.maxHeight = '100vh';
                modalBox.style.boxSizing = 'border-box';
                modalBox.style.display = 'flex';
                modalBox.style.flexDirection = 'column';
            } catch (error) {
                console.error('Erro:', error);
                showToast('Erro ao carregar produto', 'error');
            }
        }

        // Fechar modal
        function closeCustomizeModal() {
            document.getElementById('customizeModal').classList.add('hidden');
            currentProduct = null;
        }

        // Atualizar total
        function updateTotal() {
            if (!currentProduct) return;

            const quantity = parseInt(document.getElementById('quantity').value);
            const basePrice = parseFloat(document.getElementById('basePrice').value);
            let total = basePrice * quantity;

            // Adicionar preço dos ingredientes
            document.querySelectorAll('.ingredient-checkbox:checked').forEach(checkbox => {
                const price = parseFloat(checkbox.dataset.price);
                total += price * quantity;
            });

            currentTotal = total;
            document.getElementById('totalPrice').textContent = `R$ ${total.toFixed(2).replace('.', ',')}`;
        }

        // Add carrinho
        function addCustomizedToCart() {
            if (!currentProduct) return;

            const quantity = parseInt(document.getElementById('quantity').value);
            const notes = document.getElementById('notes').value;
            const ingredients = {};

            document.querySelectorAll('.ingredient-checkbox:checked').forEach(checkbox => {
                ingredients[checkbox.dataset.id] = 1;
            });

            const cart = JSON.parse(localStorage.getItem('cart_<?= $store['id'] ?>') || '[]');
            cart.push({
                cart_id: Date.now().toString(),
                product_id: currentProduct.id,
                name: currentProduct.name,
                price: parseFloat(currentProduct.price),
                quantity: quantity,
                ingredients: ingredients,
                notes: notes,
                size: ''
            });

            localStorage.setItem('cart_<?= $store['id'] ?>', JSON.stringify(cart));
            updateCartCount();
            closeCustomizeModal();
            showToast('Produto adicionado ao carrinho!');
        }

        // Mostrar toast de feedback
        function showToast(message, type = 'success') {
            const toast = document.createElement('div');
            toast.className = `fixed top-4 right-4 z-50 px-4 py-2 rounded-lg text-white ${type === 'error' ? 'bg-red-500' : 'bg-green-500'}`;
            toast.textContent = message;
            document.body.appendChild(toast);

            setTimeout(() => {
                toast.remove();
            }, 3000);
        }

        // Inicializar contador do carrinho
        document.addEventListener('DOMContentLoaded', function() {
            updateCartCount();
        });
    </script>
</body>
</html>

