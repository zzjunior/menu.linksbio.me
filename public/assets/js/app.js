/**
 * Açaíteria Digital - JavaScript Principal
 * Funcionalidades do cardápio e personalização de açaí
 */

// Função global para adicionar produto sem personalização direto ao carrinho
function addToCart(productId, productName, price) {
    const cart = JSON.parse(localStorage.getItem('cart_' + window.storeId) || '[]');
    
    const cartItem = {
        cart_id: 'cart_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9),
        product_id: productId,
        name: productName,
        price: price,
        quantity: 1,
        size: '',
        notes: '',
        ingredients: {}
    };
    
    cart.push(cartItem);
    localStorage.setItem('cart_' + window.storeId, JSON.stringify(cart));
    
    // Atualizar contador do carrinho
    updateCartCount();
    
    // Mostrar toast de sucesso
    showToast('Produto adicionado ao carrinho!', 'success');
}

// Função para atualizar contador do carrinho
function updateCartCount() {
    const cart = JSON.parse(localStorage.getItem('cart_' + window.storeId) || '[]');
    const cartCount = document.getElementById('cart-count');
    if (cartCount) {
        cartCount.textContent = cart.length;
        cartCount.style.display = cart.length > 0 ? 'flex' : 'none';
    }
}

// Função para mostrar toast
function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg z-50 transition-all duration-300 ${
        type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : 'bg-blue-500'
    } text-white`;
    toast.textContent = message;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.style.opacity = '0';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

class AcaiteriaApp {
    constructor() {
        this.currentProduct = null;
        this.selectedIngredients = {}; // { [ingredientId]: { id, price, qty } }
        this.cart = [];
        this.init();
    }

    init() {
        // Inicializa event listeners
        this.setupEventListeners();
        
        // Carrega carrinho do localStorage
        this.loadCart();
        
        // Inicializa animações
        this.initAnimations();
    }

    setupEventListeners() {
        // Event listener para fechar modal ao clicar fora
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('modal-backdrop')) {
                this.closeModal(e.target.dataset.modal);
            }
        });

        // Event listener para tecla ESC
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                this.closeAllModals();
            }
        });

        // Event listeners para filtros de categoria
        const categoryFilters = document.querySelectorAll('.category-filter');
        categoryFilters.forEach(filter => {
            filter.addEventListener('click', (e) => {
                e.preventDefault();
                this.filterByCategory(filter.dataset.category);
            });
        });
    }

    initAnimations() {
        // Animação fade-in para cards de produtos
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('fade-in-up');
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);

        document.querySelectorAll('.product-card').forEach(card => {
            observer.observe(card);
        });
    }

    async openAcaiModal(productId) {
        try {
            this.showLoading('Carregando produto...');
            
            const response = await fetch(`/api/product/${productId}`);
            if (!response.ok) {
                throw new Error('Produto não encontrado');
            }
            
            const product = await response.json();
            this.currentProduct = product;
            this.selectedIngredients = {}; // Resetar seleção
            
            this.renderAcaiModal(product);
            this.showModal('acaiModal');
            
        } catch (error) {
            this.showToast('Erro ao carregar produto', 'error');
            console.error(error);
        } finally {
            this.hideLoading();
        }
    }

    renderAcaiModal(product) {
        const modal = document.getElementById('acaiModal');
        const title = modal.querySelector('#modalTitle');
        const content = modal.querySelector('#modalContent');
        const totalPrice = modal.querySelector('#totalPrice');

        title.textContent = product.name;
        totalPrice.textContent = this.formatPrice(product.price);

        let html = `
            <div class="ifood-modal-header">
                <img src="${product.image || '/assets/img/default-acai.jpg'}" alt="${product.name}" class="ifood-modal-img"/>
                <div>
                    <h2 class="ifood-modal-title">${product.name}</h2>
                    <div class="ifood-modal-price">${this.formatPrice(product.price)}</div>
                </div>
            </div>
            <div class="ifood-modal-desc">${product.description}</div>
            <div class="ifood-modal-ingredients">
        `;

        if (product.ingredients) {
            for (const [type, ingredients] of Object.entries(product.ingredients)) {
                html += `
                    <div class="ifood-modal-group">
                        <h4 class="ifood-modal-group-title">${this.getTypeIcon(type)} ${type}</h4>
                        <div>
                `;
                ingredients.forEach(ingredient => {
                    const extraPrice = ingredient.additional_price > 0 ? 
                        ` <span class="text-green-600">(+${this.formatPrice(ingredient.additional_price)})</span>` : '';
                    const qty = this.selectedIngredients[ingredient.id]?.qty || 0;
                    html += `
                        <div class="ifood-ingredient-row">
                            <span>${ingredient.name}${extraPrice}</span>
                            <div class="ifood-stepper">
                                <button type="button" onclick="app.decrementIngredient(${ingredient.id}, ${ingredient.additional_price})" class="ifood-stepper-btn">-</button>
                                <span class="ifood-stepper-qty">${qty}</span>
                                <button type="button" onclick="app.incrementIngredient(${ingredient.id}, ${ingredient.additional_price}, '${type}')" class="ifood-stepper-btn">+</button>
                            </div>
                        </div>
                    `;
                });
                html += `</div></div>`;
            }
        }
        html += `</div>`;

        // Botão fixo na base
        html += `
            <div class="ifood-modal-footer">
                <button class="ifood-add-btn" onclick="app.addToCart()">
                    Adicionar <span id="ifoodTotalPrice">${this.formatPrice(product.price)}</span>
                </button>
            </div>
        `;

        content.innerHTML = html;
        this.updateTotalPrice(); // Atualiza preço inicial
    }

    incrementIngredient(ingredientId, additionalPrice, type) {
        // Limite por tipo, se houver
        if (this.currentProduct.max_ingredients) {
            const totalQty = Object.values(this.selectedIngredients).reduce((sum, ing) => sum + ing.qty, 0);
            if (totalQty >= this.currentProduct.max_ingredients) {
                this.showToast(`Máximo de ${this.currentProduct.max_ingredients} ingredientes permitidos`, 'error');
                return;
            }
        }
        if (!this.selectedIngredients[ingredientId]) {
            this.selectedIngredients[ingredientId] = { id: ingredientId, price: additionalPrice, qty: 1 };
        } else {
            this.selectedIngredients[ingredientId].qty += 1;
        }
        this.renderAcaiModal(this.currentProduct); // Re-renderiza para atualizar UI
    }

    decrementIngredient(ingredientId, additionalPrice) {
        if (this.selectedIngredients[ingredientId]) {
            this.selectedIngredients[ingredientId].qty -= 1;
            if (this.selectedIngredients[ingredientId].qty <= 0) {
                delete this.selectedIngredients[ingredientId];
            }
            this.renderAcaiModal(this.currentProduct);
        }
    }

    async updateTotalPrice() {
        try {
            const ingredientsArr = [];
            Object.values(this.selectedIngredients).forEach(ing => {
                for (let i = 0; i < ing.qty; i++) ingredientsArr.push(ing.id);
            });
            const response = await fetch('/api/calculate-price', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    product_id: this.currentProduct.id,
                    ingredients: ingredientsArr
                })
            });
            
            const result = await response.json();
            document.getElementById('totalPrice').textContent = result.formatted_price;
            const ifoodTotal = document.getElementById('ifoodTotalPrice');
            if (ifoodTotal) ifoodTotal.textContent = result.formatted_price;
            
        } catch (error) {
            console.error('Erro ao calcular preço:', error);
        }
    }

    addToCart() {
        const product = this.currentProduct;
        if (!product) return;
        const selected = Object.values(this.selectedIngredients).filter(ing => ing.qty > 0);
        const cartItem = {
            id: product.id,
            name: product.name,
            price: this.calculateFinalPrice(product),
            ingredients: selected,
            timestamp: Date.now()
        };
        this.cart.push(cartItem);
        this.saveCart();
        this.updateCartUI();
        this.showToast('Produto adicionado ao carrinho!', 'success');
        this.closeModal('acaiModal');
    }

    calculateFinalPrice(product) {
        let total = parseFloat(product.price);
        Object.values(this.selectedIngredients).forEach(ingredient => {
            total += parseFloat(ingredient.price) * ingredient.qty;
        });
        return total;
    }

    removeFromCart(index) {
        this.cart.splice(index, 1);
        this.saveCart();
        this.updateCartUI();
        this.showToast('Produto removido do carrinho', 'success');
    }

    clearCart() {
        this.cart = [];
        this.saveCart();
        this.updateCartUI();
        this.showToast('Carrinho limpo!', 'success');
    }

    saveCart() {
        localStorage.setItem('acaiteria_cart', JSON.stringify(this.cart));
    }

    loadCart() {
        const saved = localStorage.getItem('acaiteria_cart');
        this.cart = saved ? JSON.parse(saved) : [];
        this.updateCartUI();
    }

    updateCartUI() {
        const cartCount = document.getElementById('cartCount');
        const cartTotal = document.getElementById('cartTotal');
        
        if (cartCount) {
            cartCount.textContent = this.cart.length;
            cartCount.style.display = this.cart.length > 0 ? 'block' : 'none';
        }
        
        if (cartTotal) {
            const total = this.cart.reduce((sum, item) => sum + item.price, 0);
            cartTotal.textContent = this.formatPrice(total);
        }
    }

    filterByCategory(categoryId) {
        const url = categoryId ? `/?category=${categoryId}` : '/';
        window.location.href = url;
    }

    showModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
    }

    closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('hidden');
            document.body.style.overflow = '';
        }
    }

    closeAllModals() {
        document.querySelectorAll('.modal').forEach(modal => {
            modal.classList.add('hidden');
        });
        document.body.style.overflow = '';
    }

    showLoading(message = 'Carregando...') {
        // Implementar loading spinner se necessário
        console.log(message);
    }

    hideLoading() {
        // Esconder loading spinner
    }

    showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `toast ${type}`;
        toast.innerHTML = `
            <div class="flex items-center">
                <span class="mr-2">${type === 'success' ? '✅' : '❌'}</span>
                <span>${message}</span>
            </div>
        `;
        
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.classList.add('show');
        }, 100);
        
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => {
                document.body.removeChild(toast);
            }, 300);
        }, 3000);
    }

    formatPrice(price) {
        return 'R$ ' + parseFloat(price).toFixed(2).replace('.', ',');
    }

    getTypeIcon(type) {
        const icons = {
            'frutas': '🍓',
            'complementos': '🥜',
            'caldas': '🍯',
            'granolas': '🌾',
            'outros': '✨'
        };
        return icons[type] || '📋';
    }
}

// Inicializa a aplicação quando o DOM estiver carregado
document.addEventListener('DOMContentLoaded', () => {
    window.app = new AcaiteriaApp();
});

// Funções globais para compatibilidade com templates
function openAcaiModal(productId) {
    window.app.openAcaiModal(productId);
}

function closeAcaiModal() {
    window.app.closeModal('acaiModal');
}

function toggleIngredient(ingredientId, additionalPrice) {
    window.app.toggleIngredient(ingredientId, additionalPrice);
}
