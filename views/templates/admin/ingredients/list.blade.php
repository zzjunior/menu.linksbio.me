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
                    <h1 class="text-3xl font-bold text-gray-900">Ingredientes</h1>
                </div>
                <a href="/admin/ingredients/new" 
                   class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition-colors">
                    <i class="fas fa-plus mr-1"></i>
                    Novo Ingrediente
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

        <!-- Lista de Ingredientes -->
        <div class="bg-white shadow overflow-hidden sm:rounded-md">
            @if (empty($ingredients))
                <div class="text-center py-12">
                    <i class="fas fa-apple-alt text-gray-400 text-6xl mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Nenhum ingrediente encontrado</h3>
                    <p class="text-gray-500 mb-6">Comece criando ingredientes para personalização dos produtos.</p>
                    <a href="/admin/ingredients/new" 
                       class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition-colors">
                        <i class="fas fa-plus mr-1"></i>
                        Criar Primeiro Ingrediente
                    </a>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 p-6">
                    @foreach ($ingredients as $ingredient)
                        <div class="bg-gray-50 rounded-lg p-4 hover:bg-gray-100 transition-colors border border-gray-200">
                            <div class="flex items-start space-x-3">
                                <!-- Imagem -->
                                <div class="flex-shrink-0">
                                    @if ($ingredient['image_url'])
                                        <img class="h-12 w-12 rounded-lg object-cover" 
                                             src="{{ $ingredient['image_url'] }}" 
                                             alt="{{ $ingredient['name'] }}">
                                    @else
                                        <div class="h-12 w-12 rounded-lg bg-gray-300 flex items-center justify-center">
                                            <i class="fas fa-image text-gray-500"></i>
                                        </div>
                                    @endif
                                </div>
                                
                                <!-- Informações -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <h3 class="text-sm font-medium text-gray-900 truncate">
                                                {{ $ingredient['name'] }}
                                            </h3>
                                            
                                            <!-- Preço -->
                                            <div class="flex items-center mt-1">
                                                @if ($ingredient['is_free'] || $ingredient['price'] == 0)
                                                    <span class="text-sm font-medium text-green-600">
                                                        <i class="fas fa-gift mr-1"></i>
                                                        Grátis
                                                    </span>
                                                @else
                                                    <span class="text-sm font-medium text-gray-900">
                                                        + {{ \App\Helpers\PriceHelper::formatPrice($ingredient['price']) }}
                                                    </span>
                                                @endif
                                            </div>

                                            <!-- Badges -->
                                            <div class="flex flex-wrap gap-1 mt-2">
                                                @if (!$ingredient['is_active'])
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                                        Inativo
                                                    </span>
                                                @endif
                                                
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                    Max: {{ $ingredient['max_quantity'] }}
                                                </span>
                                            </div>
                                            {{-- Tipo --}}
                                            <div class="flex flex-wrap gap-1 mt-2">
                                                <span class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-800 capitalize">
                                                    Tipo: {{ $ingredient['type'] }}
                                                </span>
                                            </div>

                                            @if ($ingredient['description'])
                                                <p class="text-xs text-gray-500 mt-1 truncate">
                                                    {{ $ingredient['description'] }}
                                                </p>
                                            @endif
                                        </div>
                                        
                                        <!-- Ações -->
                                        <div class="flex items-center space-x-1 ml-2">
                                            <a href="/admin/ingredients/{{ $ingredient['id'] }}/edit" 
                                               class="text-indigo-600 hover:text-indigo-900 p-1 rounded hover:bg-indigo-50 transition-colors"
                                               title="Editar ingrediente">
                                                <i class="fas fa-edit text-sm"></i>
                                            </a>
                                            
                                            <button onclick="confirmDelete({{ $ingredient['id'] }}, '{{ addslashes($ingredient['name']) }}')" 
                                                    class="text-red-600 hover:text-red-900 p-1 rounded hover:bg-red-50 transition-colors"
                                                    title="Excluir ingrediente">
                                                <i class="fas fa-trash text-sm"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
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
                    Tem certeza que deseja excluir o ingrediente "<span id="ingredientName"></span>"? 
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
        function confirmDelete(ingredientId, ingredientName) {
            document.getElementById('ingredientName').textContent = ingredientName;
            document.getElementById('deleteForm').action = '/admin/ingredients/' + ingredientId + '/delete';
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