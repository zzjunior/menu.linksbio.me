<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $pageTitle }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <!-- Header -->
    <header class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div class="flex items-center space-x-4">
                    <a href="/admin" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-arrow-left mr-1"></i> Dashboard
                    </a>
                    <h1 class="text-3xl font-bold text-gray-900">Categorias</h1>
                </div>
                <a href="/admin/categories/new" 
                   class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition-colors">
                    <i class="fas fa-plus mr-1"></i>
                    Nova Categoria
                </a>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        @if (isset($success))
            <div class="bg-green-50 border border-green-200 rounded-md p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle text-green-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">
                            {{ $success }}
                        </p>
                    </div>
                </div>
            </div>
        @endif

        @if (isset($error))
            <div class="bg-red-50 border border-red-200 rounded-md p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle text-red-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-red-800">
                            {{ $error }}
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Lista de Categorias -->
        <div class="bg-white shadow overflow-hidden sm:rounded-md">
            @if (empty($categories))
                <div class="text-center py-12">
                    <i class="fas fa-folder-open text-gray-400 text-6xl mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Nenhuma categoria encontrada</h3>
                    <p class="text-gray-500 mb-6">Comece criando sua primeira categoria de produtos.</p>
                    <a href="/admin/categories/new" 
                       class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition-colors">
                        <i class="fas fa-plus mr-1"></i>
                        Criar Primeira Categoria
                    </a>
                </div>
            @else
                <ul class="divide-y divide-gray-200">
                    @foreach ($categories as $category)
                        <li class="p-6 hover:bg-gray-50 transition-colors">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-4">
                                    <!-- Imagem -->
                                    <div class="flex-shrink-0 h-16 w-16">
                                        @if ($category['image_url'])
                                            <img class="h-16 w-16 rounded-lg object-cover" 
                                                 src="{{ $category['image_url'] }}" 
                                                 alt="{{ $category['name'] }}">
                                        @else
                                            <div class="h-16 w-16 rounded-lg bg-gray-200 flex items-center justify-center">
                                                <i class="fas fa-image text-gray-400 text-xl"></i>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <!-- Informações -->
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center space-x-2">
                                            <h3 class="text-lg font-medium text-gray-900 truncate">
                                                {{ $category['name'] }}
                                            </h3>
                                            
                                            <!-- Badges -->
                                            @if (!$category['is_active'])
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                    Inativa
                                                </span>
                                            @endif
                                            
                                            @if ($category['has_customization'])
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    <i class="fas fa-plus-circle mr-1"></i>
                                                    Personalização
                                                </span>
                                            @endif
                                        </div>
                                        
                                        @if ($category['description'])
                                            <p class="text-sm text-gray-500 truncate mt-1">
                                                {{ $category['description'] }}
                                            </p>
                                        @endif
                                        
                                        <div class="flex items-center space-x-4 text-sm text-gray-500 mt-2">
                                            <span>
                                                <i class="fas fa-sort-numeric-up mr-1"></i>
                                                Ordem: {{ $category['sort_order'] }}
                                            </span>
                                            <span>
                                                <i class="fas fa-calendar mr-1"></i>
                                                Criada: {{ \Carbon\Carbon::parse($category['created_at'])->format('d/m/Y') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Ações -->
                                <div class="flex items-center space-x-2">
                                    <a href="/admin/categories/{{ $category['id'] }}/edit" 
                                       class="text-indigo-600 hover:text-indigo-900 p-2 rounded-md hover:bg-indigo-50 transition-colors"
                                       title="Editar categoria">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    
                                    <button onclick="confirmDelete({{ $category['id'] }}, '{{ addslashes($category['name']) }}')" 
                                            class="text-red-600 hover:text-red-900 p-2 rounded-md hover:bg-red-50 transition-colors"
                                            title="Excluir categoria">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </main>

    <!-- Modal de confirmação de exclusão -->
    <div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="bg-white rounded-lg max-w-md w-full p-6">
                <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full mb-4">
                    <i class="fas fa-exclamation-triangle text-red-600"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 text-center mb-2">Confirmar Exclusão</h3>
                <p class="text-sm text-gray-500 text-center mb-6">
                    Tem certeza que deseja excluir a categoria "<span id="categoryName"></span>"? 
                    Esta ação não pode ser desfeita.
                </p>
                <div class="flex space-x-4">
                    <button onclick="closeDeleteModal()" 
                            class="flex-1 bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Cancelar
                    </button>
                    <form id="deleteForm" method="POST" class="flex-1">
                        @csrf
                        <button type="submit" 
                                class="w-full bg-red-600 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-red-700">
                            Excluir
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function confirmDelete(categoryId, categoryName) {
            document.getElementById('categoryName').textContent = categoryName;
            document.getElementById('deleteForm').action = '/admin/categories/' + categoryId + '/delete';
            document.getElementById('deleteModal').classList.remove('hidden');
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
        }

        // Fechar modal ao clicar fora
        document.getElementById('deleteModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeDeleteModal();
            }
        });
    </script>
</body>
</html>
