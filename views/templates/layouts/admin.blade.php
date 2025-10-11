<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin') - {{ $_SESSION['store_name'] ?? 'Painel Admin' }}</title>
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
<body class="bg-gray-100">
    <!-- Overlay para mobile -->
    <div id="sidebarOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-30 lg:hidden hidden"></div>
    
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div id="sidebar" class="fixed lg:static inset-y-0 left-0 z-40 w-64 bg-white shadow-lg transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out">
            <div class="flex items-center justify-between h-16 border-b border-gray-200 px-4">
                <h1 class="text-xl font-bold text-gray-800">Painel Admin</h1>
                <!-- Botão fechar mobile -->
                <button id="closeSidebar" class="lg:hidden text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <nav class="mt-6">
                <div class="px-4 space-y-2">
                    <a href="/admin" class="flex items-center px-4 py-2 text-gray-700 rounded-lg hover:bg-gray-100 transition-colors">
                        <i class="fas fa-chart-bar mr-3"></i>
                        Dashboard
                    </a>
                    
                    <a href="/admin/products" class="flex items-center px-4 py-2 text-gray-700 rounded-lg hover:bg-gray-100 transition-colors">
                        <i class="fas fa-box mr-3"></i>
                        Produtos
                    </a>
                    
                    <a href="/admin/categories" class="flex items-center px-4 py-2 text-gray-700 rounded-lg hover:bg-gray-100 transition-colors">
                        <i class="fas fa-tags mr-3"></i>
                        Categorias
                    </a>
                    
                    <a href="/admin/ingredients" class="flex items-center px-4 py-2 text-gray-700 rounded-lg hover:bg-gray-100 transition-colors">
                        <i class="fas fa-seedling mr-3"></i>
                        Ingredientes
                    </a>
                    
                    <a href="/admin/pedidos" class="flex items-center px-4 py-2 text-gray-700 rounded-lg hover:bg-gray-100 transition-colors">
                        <i class="fas fa-shopping-cart mr-3"></i>
                        Pedidos
                    </a>
                    
                    <a href="/admin/relatorios" class="flex items-center px-4 py-2 text-gray-700 rounded-lg hover:bg-gray-100 transition-colors">
                        <i class="fas fa-chart-line mr-3"></i>
                        Relatórios
                    </a>
                    
                    <a href="/admin/loja/configuracoes" class="flex items-center px-4 py-2 text-gray-700 rounded-lg hover:bg-gray-100 transition-colors">
                        <i class="fas fa-cog mr-3"></i>
                        Configurações
                    </a>
                </div>
                
                <div class="px-4 mt-8 pt-4 border-t border-gray-200">
                    <div class="flex items-center px-4 py-2 text-gray-700">
                        <i class="fas fa-user mr-3"></i>
                        <span class="text-sm">{{ $_SESSION['user_name'] ?? 'Usuário' }}</span>
                    </div>
                    
                    <a href="/admin/logout" class="flex items-center px-4 py-2 text-red-600 rounded-lg hover:bg-red-50 transition-colors">
                        <i class="fas fa-sign-out-alt mr-3"></i>
                        Sair
                    </a>
                </div>
            </nav>
        </div>
        
        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden lg:ml-0">
            <!-- Header -->
            <header class="bg-white shadow-sm border-b border-gray-200">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between items-center py-4">
                        <div class="flex items-center">
                            <!-- Botão hambúrguer -->
                            <button id="toggleSidebar" class="lg:hidden mr-4 text-gray-500 hover:text-gray-700 focus:outline-none">
                                <i class="fas fa-bars text-xl"></i>
                            </button>
                            <h2 class="text-xl font-semibold text-gray-900">@yield('title', 'Admin')</h2>
                        </div>
                        
                        <div class="flex items-center space-x-4">
                            @if (!empty($_SESSION['store_slug']))
                                <a href="/{{ $_SESSION['store_slug'] }}" target="_blank" class="text-gray-600 hover:text-gray-900 transition-colors">
                                    <i class="fas fa-external-link-alt mr-1"></i>
                                    Ver Loja
                                </a>
                            @endif
                            
                            <div class="text-sm text-gray-500">
                                {{ date('d/m/Y H:i') }}
                            </div>
                        </div>
                    </div>
                </div>
            </header>
            
            <!-- Page Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <script>
        // Controle do sidebar offcanvas
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const toggleBtn = document.getElementById('toggleSidebar');
        const closeBtn = document.getElementById('closeSidebar');

        // Função para abrir sidebar
        function openSidebar() {
            sidebar.classList.remove('-translate-x-full');
            overlay.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }

        // Função para fechar sidebar
        function closeSidebar() {
            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }

        // Event listeners
        toggleBtn.addEventListener('click', openSidebar);
        closeBtn.addEventListener('click', closeSidebar);
        overlay.addEventListener('click', closeSidebar);

        // Fechar sidebar ao clicar em links (mobile)
        const sidebarLinks = sidebar.querySelectorAll('a');
        sidebarLinks.forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth < 1024) { // lg breakpoint
                    closeSidebar();
                }
            });
        });

        // Gerenciar estado do sidebar ao redimensionar
        window.addEventListener('resize', () => {
            if (window.innerWidth >= 1024) {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            } else {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }
        });

        // Ativar link atual
        const currentPath = window.location.pathname;
        const navLinks = document.querySelectorAll('nav a');
        
        navLinks.forEach(link => {
            const href = link.getAttribute('href');
            
            // Remove classes ativas de todos os links
            link.classList.remove('bg-primary', 'text-white');
            link.classList.add('text-gray-700');
            
            // Adiciona classe ativa ao link atual
            if (href === currentPath || (href !== '/admin' && currentPath.startsWith(href))) {
                link.classList.remove('text-gray-700', 'hover:bg-gray-100');
                link.classList.add('bg-primary', 'text-white');
            }
        });
    </script>
</body>
</html>
