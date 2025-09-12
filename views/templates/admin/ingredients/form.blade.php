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
                    <a href="/admin/ingredients" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-arrow-left mr-1"></i> Voltar
                    </a>
                    <h1 class="text-3xl font-bold text-gray-900">{{ $pageTitle }}</h1>
                </div>
            </div>
        </div>
    </header>

    <main class="max-w-3xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        @if (isset($error))
            <div class="bg-red-50 border border-red-200 rounded-md p-4 mb-6">
                <div class="flex">
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">
                            Erro
                        </h3>
                        <div class="mt-2 text-sm text-red-700">
                            {{ $error }}
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="bg-white shadow rounded-lg">
            <form method="POST" 
                  action="{{ isset($ingredient) ? '/admin/ingredients/' . $ingredient['id'] : '/admin/ingredients' }}" 
                  enctype="multipart/form-data" 
                  class="p-6">
                @csrf
                <div class="grid grid-cols-1 gap-6">
                    
                    <!-- Nome -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">
                            Nome do Ingrediente *
                        </label>
                        <input type="text" 
                               id="name" 
                               name="name" 
                               required
                               value="{{ $formData['name'] ?? $ingredient['name'] ?? '' }}"
                               placeholder="Ex: Morango, Granola, Leite Condensado"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <!-- Descrição -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700">
                            Descrição
                        </label>
                        <textarea id="description" 
                                  name="description" 
                                  rows="2"
                                  placeholder="Descrição opcional do ingrediente"
                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ $formData['description'] ?? $ingredient['description'] ?? '' }}</textarea>
                    </div>

                    <!-- Tipo do Ingrediente -->
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700">
                            Tipo do Ingrediente *
                        </label>
                        <input type="text"
                               id="type"
                               name="type"
                               required
                               value="{{ $formData['type'] ?? $ingredient['type'] ?? '' }}"
                               placeholder="Ex: frutas, caldas, granolas, complementos, outros"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <p class="mt-1 text-sm text-gray-500">
                            Digite o tipo do ingrediente (frutas, caldas, granolas, complementos, etc.)
                        </p>
                    </div>

                    <!-- Upload de Imagem -->
                    <div>
                        <label for="image" class="block text-sm font-medium text-gray-700">
                            Imagem do Ingrediente
                        </label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                            <div class="space-y-1 text-center">
                                <div class="flex text-sm text-gray-600">
                                    <label for="image" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500">
                                        <span>Fazer upload de arquivo</span>
                                        <input id="image" name="image" type="file" accept="image/*" class="sr-only" onchange="previewImage(this)">
                                    </label>
                                    <p class="pl-1">ou arraste e solte</p>
                                </div>
                                <p class="text-xs text-gray-500">
                                    PNG, JPG, WEBP até 2MB (recomendado: quadrada 200x200px)
                                </p>
                            </div>
                        </div>
                        
                        <!-- Preview da imagem -->
                        <div id="imagePreview" class="mt-4 hidden">
                            <img id="previewImg" src="" alt="Preview" class="max-w-xs max-h-48 rounded-lg shadow-md">
                        </div>
                        
                        <!-- Imagem atual (se existir) -->
                        @if (isset($ingredient['image_url']) && $ingredient['image_url'])
                            <div class="mt-4">
                                <p class="text-sm text-gray-700 mb-2">Imagem atual:</p>
                                <img src="{{ $ingredient['image_url'] }}" 
                                     alt="Imagem atual" 
                                     class="w-24 h-24 rounded-lg shadow-md object-cover">
                            </div>
                        @endif
                    </div>

                    <!-- URL de Imagem (alternativa) -->
                    <div>
                        <label for="image_url" class="block text-sm font-medium text-gray-700">
                            URL da Imagem (alternativa ao upload)
                        </label>
                        <input type="url" 
                               id="image_url" 
                               name="image_url" 
                               value="{{ $formData['image_url'] ?? $ingredient['image_url'] ?? '' }}"
                               placeholder="https://exemplo.com/imagem.jpg"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <p class="mt-1 text-sm text-gray-500">
                            Se você fizer upload de arquivo, a URL será ignorada
                        </p>
                    </div>

                    <!-- Configurações de Preço -->
                    <div class="bg-yellow-50 p-4 rounded-lg">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">
                            <i class="fas fa-dollar-sign text-yellow-500 mr-2"></i>
                            Configurações de Preço
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Preço -->
                            <div>
                                <label for="price" class="block text-sm font-medium text-gray-700">
                                    Preço Adicional (R$)
                                </label>
                                <input type="number" 
                                       id="price" 
                                       name="price" 
                                       step="0.01"
                                       min="0"
                                       value="{{ $formData['price'] ?? $ingredient['price'] ?? '0.00' }}"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <p class="mt-1 text-sm text-gray-500">
                                    Valor que será adicionado ao preço base
                                </p>
                            </div>

                            <!-- É Gratuito -->
                            <div class="flex items-center mt-6">
                                <input type="checkbox" 
                                       id="is_free" 
                                       name="is_free" 
                                       value="1"
                                       @if ((isset($formData['is_free']) ? $formData['is_free'] : ($ingredient['is_free'] ?? false))) checked @endif
                                       class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                       onchange="togglePrice()">
                                <label for="is_free" class="ml-2 text-sm font-medium text-gray-700">
                                    <i class="fas fa-gift text-green-500 mr-1"></i>
                                    Ingrediente gratuito
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Configurações de Disponibilidade -->
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">
                            <i class="fas fa-cog text-blue-500 mr-2"></i>
                            Configurações de Disponibilidade
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Quantidade Máxima -->
                            <div>
                                <label for="max_quantity" class="block text-sm font-medium text-gray-700">
                                    Quantidade Máxima por Pedido
                                </label>
                                <input type="number" 
                                       id="max_quantity" 
                                       name="max_quantity" 
                                       min="1"
                                       value="{{ $formData['max_quantity'] ?? $ingredient['max_quantity'] ?? '5' }}"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <p class="mt-1 text-sm text-gray-500">
                                    Máximo que um cliente pode adicionar
                                </p>
                            </div>

                            <!-- Ordem -->
                            <div>
                                <label for="sort_order" class="block text-sm font-medium text-gray-700">
                                    Ordem de Exibição
                                </label>
                                <input type="number" 
                                       id="sort_order" 
                                       name="sort_order" 
                                       value="{{ $formData['sort_order'] ?? $ingredient['sort_order'] ?? 0 }}"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <p class="mt-1 text-sm text-gray-500">
                                    Número menor aparece primeiro
                                </p>
                            </div>
                        </div>

                        <!-- Ativo -->
                        <div class="mt-4">
                            <div class="flex items-center">
                                <input type="checkbox" 
                                       id="is_active" 
                                       name="is_active" 
                                       value="1"
                                       @if ((isset($formData['is_active']) ? $formData['is_active'] : ($ingredient['is_active'] ?? true))) checked @endif
                                       class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                <label for="is_active" class="ml-2 text-sm font-medium text-gray-700">
                                    <i class="fas fa-eye text-green-500 mr-1"></i>
                                    Ingrediente disponível
                                </label>
                            </div>
                            <p class="ml-6 text-sm text-gray-500">
                                Desmarque para ocultar temporariamente este ingrediente
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Botões -->
                <div class="pt-6 border-t border-gray-200 mt-6">
                    <div class="flex justify-end space-x-3">
                        <a href="/admin/ingredients" 
                           class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Cancelar
                        </a>
                        <button type="submit" 
                                class="bg-indigo-600 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <i class="fas fa-save mr-1"></i>
                            {{ isset($ingredient) ? 'Atualizar' : 'Criar' }} Ingrediente
                        </button>
                    </div>
                </div>

                <!-- Campos hidden para garantir envio -->
                <input type="hidden" name="type" value="{{ $formData['type'] ?? $ingredient['type'] ?? '' }}">
                <input type="hidden" name="image_url" value="{{ $formData['image_url'] ?? $ingredient['image_url'] ?? '' }}">
            </form>
        </div>
    </main>

    <script>
        function previewImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    document.getElementById('previewImg').src = e.target.result;
                    document.getElementById('imagePreview').classList.remove('hidden');
                };
                
                reader.readAsDataURL(input.files[0]);
            }
        }

        function togglePrice() {
            const isFree = document.getElementById('is_free').checked;
            const priceField = document.getElementById('price');
            
            if (isFree) {
                priceField.value = '0.00';
                priceField.disabled = true;
                priceField.classList.add('bg-gray-100');
            } else {
                priceField.disabled = false;
                priceField.classList.remove('bg-gray-100');
            }
        }

        // Sincronizar campo visível com hidden
        document.getElementById('type').addEventListener('input', function() {
            document.querySelector('input[type="hidden"][name="type"]').value = this.value;
        });

        // Limpar URL quando arquivo é selecionado
        document.getElementById('image').addEventListener('change', function() {
            if (this.files.length > 0) {
                document.getElementById('image_url').value = '';
                document.querySelector('input[type="hidden"][name="image_url"]').value = '';
            }
        });

        // Sincronizar campo visível com hidden para image_url
        document.getElementById('image_url').addEventListener('input', function() {
            document.querySelector('input[type="hidden"][name="image_url"]').value = this.value;
            if (this.value.trim() !== '') {
                document.getElementById('image').value = '';
                document.getElementById('imagePreview').classList.add('hidden');
            }
        });

        // Inicializar estado do preço
        document.addEventListener('DOMContentLoaded', function() {
            togglePrice();
        });
    </script>
</body>
</html>
