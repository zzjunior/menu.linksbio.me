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
    <!-- Sidebar -->
    <div class="flex h-screen">
        <div class="w-64 bg-white shadow-lg">
            <div class="flex items-center justify-center h-16 border-b border-gray-200">
                <h1 class="text-xl font-bold text-gray-800">Painel Admin</h1>
            </div>
            
            <nav class="mt-6">
                <div class="px-4 space-y-2">
                    <a href="/admin" class="flex items-center px-4 py-2 text-gray-700 rounded-lg hover:bg-gray-100">
                        <i class="fas fa-chart-bar mr-3"></i>
                        Dashboard
                    </a>
                    
                    <a href="/admin/products" class="flex items-center px-4 py-2 text-gray-700 rounded-lg hover:bg-gray-100">
                        <i class="fas fa-box mr-3"></i>
                        Produtos
                    </a>
                    
                    <a href="/admin/categories" class="flex items-center px-4 py-2 text-gray-700 rounded-lg hover:bg-gray-100">
                        <i class="fas fa-tags mr-3"></i>
                        Categorias
                    </a>
                    
                    <a href="/admin/ingredients" class="flex items-center px-4 py-2 text-gray-700 rounded-lg hover:bg-gray-100">
                        <i class="fas fa-seedling mr-3"></i>
                        Ingredientes
                    </a>
                    
                    <a href="/admin/pedidos" class="flex items-center px-4 py-2 text-gray-700 rounded-lg hover:bg-gray-100 bg-primary text-white">
                        <i class="fas fa-shopping-cart mr-3"></i>
                        Pedidos
                    </a>
                    
                    <a href="/admin/loja/configuracoes" class="flex items-center px-4 py-2 text-gray-700 rounded-lg hover:bg-gray-100">
                        <i class="fas fa-cog mr-3"></i>
                        Configurações
                    </a>
                </div>
                
                <div class="px-4 mt-8 pt-4 border-t border-gray-200">
                    <div class="flex items-center px-4 py-2 text-gray-700">
                        <i class="fas fa-user mr-3"></i>
                        <span class="text-sm">{{ $_SESSION['user_name'] ?? 'Usuário' }}</span>
                    </div>
                    
                    <a href="/admin/logout" class="flex items-center px-4 py-2 text-red-600 rounded-lg hover:bg-red-50">
                        <i class="fas fa-sign-out-alt mr-3"></i>
                        Sair
                    </a>
                </div>
            </nav>
        </div>
        
        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Header -->
            <header class="bg-white shadow-sm border-b border-gray-200">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between items-center py-4">
                        <div class="flex items-center">
                            <h2 class="text-xl font-semibold text-gray-900">@yield('title', 'Admin')</h2>
                        </div>
                        
                        <div class="flex items-center space-x-4">
                            @if (!empty($_SESSION['store_slug']))
                                <a href="/{{ $_SESSION['store_slug'] }}" target="_blank" class="text-gray-600 hover:text-gray-900">
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
</body>
</html>
