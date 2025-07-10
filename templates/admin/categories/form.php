<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->escape($pageTitle) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <!-- Header -->
    <header class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div class="flex items-center space-x-4">
                    <a href="/admin/categories" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-arrow-left mr-1"></i> Voltar
                    </a>
                    <h1 class="text-3xl font-bold text-gray-900"><?= $this->escape($pageTitle) ?></h1>
                </div>
            </div>
        </div>
    </header>

    <main class="max-w-3xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <?php if (isset($error)): ?>
            <div class="bg-red-50 border border-red-200 rounded-md p-4 mb-6">
                <div class="flex">
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">
                            Erro
                        </h3>
                        <div class="mt-2 text-sm text-red-700">
                            <?= $this->escape($error) ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="bg-white shadow rounded-lg">
            <form method="POST" 
                  action="<?= isset($category) ? '/admin/categories/' . $category['id'] : '/admin/categories' ?>" 
                  enctype="multipart/form-data" 
                  class="p-6">
                <div class="grid grid-cols-1 gap-6">
                    
                    <!-- Nome -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">
                            Nome da Categoria *
                        </label>
                        <input type="text" 
                               id="name" 
                               name="name" 
                               required
                               value="<?= $this->escape($formData['name'] ?? $category['name'] ?? '') ?>"
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
                                  placeholder="Descrição opcional da categoria"
                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"><?= $this->escape($formData['description'] ?? $category['description'] ?? '') ?></textarea>
                    </div>

                    <!-- Upload de Imagem -->
                    <div>
                        <label for="image" class="block text-sm font-medium text-gray-700">
                            Imagem da Categoria
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
                        <?php if (isset($category['image_url']) && $category['image_url']): ?>
                            <div class="mt-4">
                                <p class="text-sm text-gray-700 mb-2">Imagem atual:</p>
                                <img src="<?= $this->escape($category['image_url']) ?>" 
                                     alt="Imagem atual" 
                                     class="w-24 h-24 rounded-lg shadow-md object-cover">
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- URL de Imagem (alternativa) -->
                    <div>
                        <label for="image_url" class="block text-sm font-medium text-gray-700">
                            URL da Imagem (alternativa ao upload)
                        </label>
                        <input type="url" 
                               id="image_url" 
                               name="image_url" 
                               value="<?= $this->escape($formData['image_url'] ?? $category['image_url'] ?? '') ?>"
                               placeholder="https://exemplo.com/imagem.jpg"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <p class="mt-1 text-sm text-gray-500">
                            Se você fizer upload de arquivo, este campo será preenchido automaticamente
                        </p>
                    </div>

                    <!-- Tem Personalização -->
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <div class="flex items-start">
                            <input type="checkbox" 
                                   id="has_customization" 
                                   name="has_customization" 
                                   value="1"
                                   <?= (isset($formData['has_customization']) ? $formData['has_customization'] : ($category['has_customization'] ?? false)) ? 'checked' : '' ?>
                                   class="mt-1 h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            <div class="ml-3">
                                <label for="has_customization" class="text-sm font-medium text-gray-700">
                                    <i class="fas fa-plus-circle text-blue-500 mr-1"></i>
                                    Esta categoria permite personalização/adicionais
                                </label>
                                <p class="text-sm text-gray-500">
                                    Marque esta opção se os produtos desta categoria podem ser personalizados com ingredientes adicionais (como açaí com frutas, coberturas, etc.)
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Ordem -->
                    <div>
                        <label for="sort_order" class="block text-sm font-medium text-gray-700">
                            Ordem de Exibição
                        </label>
                        <input type="number" 
                               id="sort_order" 
                               name="sort_order" 
                               value="<?= $this->escape($formData['sort_order'] ?? $category['sort_order'] ?? 0) ?>"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <p class="mt-1 text-sm text-gray-500">
                            Número menor aparece primeiro na lista
                        </p>
                    </div>

                    <!-- Ativo -->
                    <div class="flex items-center">
                        <input type="checkbox" 
                               id="is_active" 
                               name="is_active" 
                               value="1"
                               <?= (isset($formData['is_active']) ? $formData['is_active'] : ($category['is_active'] ?? true)) ? 'checked' : '' ?>
                               class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                        <label for="is_active" class="ml-2 text-sm font-medium text-gray-700">
                            Categoria ativa
                        </label>
                    </div>
                </div>

                <!-- Botões -->
                <div class="pt-6 border-t border-gray-200 mt-6">
                    <div class="flex justify-end space-x-3">
                        <a href="/admin/categories" 
                           class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Cancelar
                        </a>
                        <button type="submit" 
                                class="bg-indigo-600 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <i class="fas fa-save mr-1"></i>
                            <?= isset($category) ? 'Atualizar' : 'Criar' ?> Categoria
                        </button>
                    </div>
                </div>
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

        // Quando arquivo é selecionado, o backend vai preencher o image_url automaticamente
        document.getElementById('image').addEventListener('change', function() {
            // Não limpar o campo image_url, deixar o backend preencher
        });

        // Limpar arquivo quando URL é preenchida manualmente
        document.getElementById('image_url').addEventListener('input', function() {
            if (this.value.trim() !== '' && !this.value.startsWith('/uploads/')) {
                document.getElementById('image').value = '';
                document.getElementById('imagePreview').classList.add('hidden');
            }
        });
    </script>
</body>
</html>
        });
    </script>
</body>
</html>
