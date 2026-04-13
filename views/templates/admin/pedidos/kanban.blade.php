<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acompanhamento de Pedidos - Kanban</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
</head>
<body class="bg-gradient-to-br from-gray-50 to-gray-100">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div class="flex items-center space-x-4">
                    <a href="/admin" class="text-gray-500 hover:text-gray-700 transition">
                        <i class="fas fa-arrow-left text-xl"></i>
                    </a>
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Acompanhamento de Pedidos</h1>
                        <p class="text-sm text-gray-600 mt-1">Gerencie pedidos com drag e drop</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <div class="hidden sm:flex items-center gap-2 text-sm text-gray-600">
                        <i class="fas fa-info-circle text-blue-500"></i>
                        <span>Arraste pedidos entre colunas</span>
                    </div>
                    <a href="/admin/pedidos" class="text-blue-600 hover:text-blue-800 transition font-medium flex items-center gap-2">
                        <i class="fas fa-list"></i> Vista em Lista
                    </a>
                </div>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <!-- Filtros Rápidos -->
        <div class="mb-6 flex flex-wrap gap-3">
            <button class="filter-btn active px-4 py-2 rounded-lg bg-white text-blue-600 border-2 border-blue-600 font-medium text-sm hover:shadow-md transition" data-filter="all">
                <i class="fas fa-th mr-2"></i> Todos
            </button>
            <button class="filter-btn px-4 py-2 rounded-lg bg-white text-gray-700 border border-gray-300 font-medium text-sm hover:shadow-md transition" data-filter="today">
                <i class="fas fa-calendar-day mr-2"></i> Hoje
            </button>
            <button class="filter-btn px-4 py-2 rounded-lg bg-white text-gray-700 border border-gray-300 font-medium text-sm hover:shadow-md transition" data-filter="high-value">
                <i class="fas fa-star mr-2"></i> Valor Alto
            </button>
            <div class="flex-1 max-w-xs">
                <input type="text" id="search-orders" placeholder="Buscar por cliente..." 
                       class="w-full px-4 py-2 rounded-lg border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
        </div>

        <!-- Kanban Board -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 auto-rows-max">
            <!-- Pendente -->
            <div class="bg-white rounded-lg shadow-sm border-l-4 border-yellow-400 overflow-hidden flex flex-col h-fit">
                <div class="bg-yellow-50 px-4 py-3 border-b border-yellow-200">
                    <div class="flex items-center justify-between">
                        <h3 class="font-semibold text-yellow-900 flex items-center gap-2">
                            <i class="fas fa-hourglass-start text-yellow-500"></i> Pendente
                        </h3>
                        <span class="bg-yellow-200 text-yellow-800 text-xs font-bold px-2 py-1 rounded-full pending-count">0</span>
                    </div>
                </div>
                <div class="kanban-column flex-1 overflow-y-auto p-4 space-y-3" data-status="pending" style="min-height: 600px;">
                    <!-- Pedidos serão inseridos aqui -->
                </div>
            </div>

            <!-- Confirmado -->
            <div class="bg-white rounded-lg shadow-sm border-l-4 border-indigo-400 overflow-hidden flex flex-col h-fit">
                <div class="bg-indigo-50 px-4 py-3 border-b border-indigo-200">
                    <div class="flex items-center justify-between">
                        <h3 class="font-semibold text-indigo-900 flex items-center gap-2">
                            <i class="fas fa-check-circle text-indigo-500"></i> Confirmado
                        </h3>
                        <span class="bg-indigo-200 text-indigo-800 text-xs font-bold px-2 py-1 rounded-full confirmed-count">0</span>
                    </div>
                </div>
                <div class="kanban-column flex-1 overflow-y-auto p-4 space-y-3" data-status="confirmed" style="min-height: 600px;">
                    <!-- Pedidos serão inseridos aqui -->
                </div>
            </div>

            <!-- Preparando -->
            <div class="bg-white rounded-lg shadow-sm border-l-4 border-blue-400 overflow-hidden flex flex-col h-fit">
                <div class="bg-blue-50 px-4 py-3 border-b border-blue-200">
                    <div class="flex items-center justify-between">
                        <h3 class="font-semibold text-blue-900 flex items-center gap-2">
                            <i class="fas fa-fire text-blue-500"></i> Preparando
                        </h3>
                        <span class="bg-blue-200 text-blue-800 text-xs font-bold px-2 py-1 rounded-full preparing-count">0</span>
                    </div>
                </div>
                <div class="kanban-column flex-1 overflow-y-auto p-4 space-y-3" data-status="preparing" style="min-height: 600px;">
                    <!-- Pedidos serão inseridos aqui -->
                </div>
            </div>

            <!-- Pronto -->
            <div class="bg-white rounded-lg shadow-sm border-l-4 border-green-400 overflow-hidden flex flex-col h-fit">
                <div class="bg-green-50 px-4 py-3 border-b border-green-200">
                    <div class="flex items-center justify-between">
                        <h3 class="font-semibold text-green-900 flex items-center gap-2">
                            <i class="fas fa-check text-green-500"></i> Pronto
                        </h3>
                        <span class="bg-green-200 text-green-800 text-xs font-bold px-2 py-1 rounded-full ready-count">0</span>
                    </div>
                </div>
                <div class="kanban-column flex-1 overflow-y-auto p-4 space-y-3" data-status="ready" style="min-height: 600px;">
                    <!-- Pedidos serão inseridos aqui -->
                </div>
            </div>

            <!-- Entregue -->
            <div class="bg-white rounded-lg shadow-sm border-l-4 border-gray-400 overflow-hidden flex flex-col h-fit">
                <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                            <i class="fas fa-box text-gray-500"></i> Entregue
                        </h3>
                        <span class="bg-gray-200 text-gray-800 text-xs font-bold px-2 py-1 rounded-full delivered-count">0</span>
                    </div>
                </div>
                <div class="kanban-column flex-1 overflow-y-auto p-4 space-y-3" data-status="delivered" style="min-height: 600px;">
                    <!-- Pedidos serão inseridos aqui -->
                </div>
            </div>
        </div>
    </main>

    <script>
        // Configuração de cores por status
        const statusColors = {
            pending: { bg: 'bg-yellow-50', border: 'border-yellow-200', text: 'text-yellow-700', badge: 'bg-yellow-100' },
            confirmed: { bg: 'bg-indigo-50', border: 'border-indigo-200', text: 'text-indigo-700', badge: 'bg-indigo-100' },
            preparing: { bg: 'bg-blue-50', border: 'border-blue-200', text: 'text-blue-700', badge: 'bg-blue-100' },
            ready: { bg: 'bg-green-50', border: 'border-green-200', text: 'text-green-700', badge: 'bg-green-100' },
            delivered: { bg: 'bg-gray-50', border: 'border-gray-200', text: 'text-gray-700', badge: 'bg-gray-100' }
        };

        const statusLabels = {
            pending: 'Pendente',
            confirmed: 'Confirmado',
            preparing: 'Preparando',
            ready: 'Pronto',
            delivered: 'Entregue'
        };

        let allOrders = [];

        // Inicializar kanban ao carregar a página
        async function initKanban() {
            try {
                const response = await fetch('/api/orders/all');
                const data = await response.json();
                allOrders = data.orders || [];
                renderOrders(allOrders);
                setupDragAndDrop();
            } catch (error) {
                console.error('Erro ao carregar pedidos:', error);
            }
        }

        function renderOrders(orders) {
            // Limpar todas as colunas
            document.querySelectorAll('.kanban-column').forEach(col => {
                col.innerHTML = '';
            });

            // Agrupar pedidos por status
            const ordersByStatus = {
                pending: [],
                confirmed: [],
                preparing: [],
                ready: [],
                delivered: []
            };

            orders.forEach(order => {
                if (ordersByStatus[order.status]) {
                    ordersByStatus[order.status].push(order);
                }
            });

            // Renderizar pedidos em cada coluna
            Object.entries(ordersByStatus).forEach(([status, orders]) => {
                const column = document.querySelector(`[data-status="${status}"]`);
                const count = document.querySelector(`.${status}-count`);
                count.textContent = orders.length;

                orders.forEach(order => {
                    const card = createOrderCard(order);
                    column.appendChild(card);
                });
            });
        }

        function createOrderCard(order) {
            const card = document.createElement('div');
            card.className = `order-card bg-white rounded-lg border-2 ${statusColors[order.status].border} p-3 cursor-move hover:shadow-md transition`;
            card.draggable = true;
            card.dataset.orderId = order.id;
            
            const time = new Date(order.created_at).toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });
            const date = new Date(order.created_at).toLocaleDateString('pt-BR', { month: '2-digit', day: '2-digit' });
            
            card.innerHTML = `
                <div class="flex justify-between items-start mb-2">
                    <div class="font-bold text-gray-900 text-sm">#${order.id}</div>
                    <span class="text-xs text-gray-500">${time}</span>
                </div>
                <div class="text-sm font-medium text-gray-800 mb-1">${order.customer_name}</div>
                <div class="text-xs text-gray-600 mb-2">${order.customer_phone}</div>
                <div class="flex justify-between items-center text-xs">
                    <span class="text-gray-500">${order.items_count || 0} item(s)</span>
                    <span class="font-bold text-gray-900">R$ ${parseFloat(order.total_amount).toFixed(2).replace('.', ',')}</span>
                </div>
                ${order.notes ? `<div class="mt-2 text-xs bg-yellow-50 text-yellow-700 p-2 rounded border border-yellow-200"><i class="fas fa-sticky-note mr-1"></i>${order.notes}</div>` : ''}
            `;

            // Adicionar eventos de clique
            card.addEventListener('click', (e) => {
                if (e.button === 1 || e.metaKey || e.ctrlKey) return; // Middle click ou Ctrl
                e.preventDefault();
                openOrderDetail(order.id);
            });

            card.addEventListener('contextmenu', (e) => {
                e.preventDefault();
                openOrderMenu(e, order);
            });

            return card;
        }

        function openOrderDetail(orderId) {
            window.open(`/admin/pedidos/${orderId}`, '_blank');
        }

        function openOrderMenu(event, order) {
            // Você pode adicionar um menu de contexto aqui se desejar
            console.log('Menu de contexto para pedido:', order.id);
        }

        // Configurar drag and drop com SortableJS
        function setupDragAndDrop() {
            const columns = document.querySelectorAll('.kanban-column');
            columns.forEach(column => {
                Sortable.create(column, {
                    group: 'orders',
                    animation: 150,
                    ghostClass: 'opacity-50 bg-blue-50',
                    onEnd: async (evt) => {
                        const orderId = evt.item.dataset.orderId;
                        const newStatus = evt.to.dataset.status;
                        
                        // Atualizar status no servidor
                        await updateOrderStatus(orderId, newStatus);
                    }
                });
            });
        }

        async function updateOrderStatus(orderId, newStatus) {
            try {
                const response = await fetch(`/admin/pedidos/${orderId}/status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ status: newStatus })
                });

                if (!response.ok) {
                    alert('Erro ao atualizar status do pedido');
                    location.reload();
                }

                // Atualizar o array allOrders
                const order = allOrders.find(o => o.id === parseInt(orderId));
                if (order) {
                    order.status = newStatus;
                }
            } catch (error) {
                console.error('Erro:', error);
                alert('Erro ao atualizar status');
                location.reload();
            }
        }

        // Filtros
        function setupFilters() {
            const filterBtns = document.querySelectorAll('.filter-btn');
            const searchInput = document.getElementById('search-orders');

            filterBtns.forEach(btn => {
                btn.addEventListener('click', () => {
                    filterBtns.forEach(b => {
                        b.classList.remove('active', 'bg-blue-600', 'border-blue-600', 'text-white');
                        b.classList.add('bg-white', 'border-gray-300', 'text-gray-700');
                    });
                    
                    btn.classList.add('active', 'bg-blue-600', 'border-blue-600', 'text-white');
                    btn.classList.remove('bg-white', 'border-gray-300', 'text-gray-700');

                    const filter = btn.dataset.filter;
                    applyFilters(filter, searchInput.value);
                });
            });

            searchInput.addEventListener('input', (e) => {
                const activeFilter = document.querySelector('.filter-btn.active').dataset.filter;
                applyFilters(activeFilter, e.target.value);
            });
        }

        function applyFilters(filter, search) {
            let filtered = allOrders;

            // Aplicar filtro de data/valor
            if (filter === 'today') {
                const today = new Date();
                today.setHours(0, 0, 0, 0);
                filtered = filtered.filter(order => {
                    const orderDate = new Date(order.created_at);
                    orderDate.setHours(0, 0, 0, 0);
                    return orderDate.getTime() === today.getTime();
                });
            } else if (filter === 'high-value') {
                filtered = filtered.filter(order => parseFloat(order.total_amount) >= 100);
            }

            // Aplicar filtro de busca
            if (search.trim()) {
                filtered = filtered.filter(order =>
                    order.customer_name.toLowerCase().includes(search.toLowerCase()) ||
                    order.customer_phone.includes(search)
                );
            }

            renderOrders(filtered);
        }

        // Inicializar ao carregar
        document.addEventListener('DOMContentLoaded', () => {
            initKanban();
            setupFilters();

            // Atualizar a cada 30 segundos
            setInterval(initKanban, 30000);
        });
    </script>
</body>
</html>
