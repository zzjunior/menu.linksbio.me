<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->escape($pageTitle) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <!-- Header -->
    <header class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div class="flex items-center space-x-4">
                    <a href="/admin/products" class="text-gray-500 hover:text-gray-700">← Voltar</a>
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
                  action="<?= isset($product) ? '/admin/products/' . $product['id'] : '/admin/products' ?>" 
                  enctype="multipart/form-data" 
                  class="p-6">
                <div class="grid grid-cols-1 gap-6">
                    <!-- Nome -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">
                            Nome do Produto *
                        </label>
                        <input type="text" 
                               id="name" 
                               name="name" 
                               required
                               value="<?= $this->escape($formData['name'] ?? $product['name'] ?? '') ?>"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <!-- Descrição -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700">
                            Descrição
                        </label>
                        <textarea id="description" 
                                  name="description" 
                                  rows="3"
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"><?= $this->escape($formData['description'] ?? $product['description'] ?? '') ?></textarea>
                    </div>

                    <!-- Preço e Categoria -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="price" class="block text-sm font-medium text-gray-700">
                                Preço (R$) *
                            </label>
                            <input type="number" 
                                   id="price" 
                                   name="price" 
                                   step="0.01" 
                                   min="0" 
                                   required
                                   value="<?= $this->escape($formData['price'] ?? $product['price'] ?? '') ?>"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label for="category_id" class="block text-sm font-medium text-gray-700">
                                Categoria *
                            </label>
                            <select id="category_id" 
                                    name="category_id" 
                                    required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Selecione uma categoria</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= $category['id'] ?>" 
                                            <?= ($formData['category_id'] ?? $product['category_id'] ?? '') == $category['id'] ? 'selected' : '' ?>>
                                        <?= $this->escape($category['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Upload de Imagem -->
                    <div>
                        <label for="image" class="block text-sm font-medium text-gray-700">
                            Imagem do Produto
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
                                    PNG, JPG, WEBP até 2MB
                                </p>
                            </div>
                        </div>
                        
                        <!-- Preview da imagem -->
                        <div id="imagePreview" class="mt-4 hidden">
                            <img id="previewImg" src="" alt="Preview" class="max-w-xs max-h-48 rounded-lg shadow-md">
                        </div>
                        
                        <!-- Imagem atual (se existir) -->
                        <?php if (isset($product['image_url']) && $product['image_url']): ?>
                            <div class="mt-4">
                                <p class="text-sm text-gray-700 mb-2">Imagem atual:</p>
                                <img src="<?= $this->escape($product['image_url']) ?>" 
                                     alt="Imagem atual" 
                                     class="max-w-xs max-h-48 rounded-lg shadow-md">
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- URL da Imagem (alternativa) -->
                    <div>
                        <label for="image_url" class="block text-sm font-medium text-gray-700">
                            URL da Imagem (alternativa ao upload)
                        </label>
                        <input type="url" 
                               id="image_url" 
                               name="image_url" 
                               value="<?= $this->escape($formData['image_url'] ?? $product['image_url'] ?? '') ?>"
                               placeholder="https://exemplo.com/imagem.jpg"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <p class="mt-1 text-sm text-gray-500">
                            Se você fizer upload de arquivo, a URL será ignorada
                        </p>
                    </div>

                    <!-- Configurações de Açaí -->
                    <div class="border-t pt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Configurações Específicas de Açaí</h3>
                        <p class="text-sm text-gray-600 mb-4">
                            Preencha apenas se este produto for um açaí que pode ser personalizado
                        </p>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label for="size_ml" class="block text-sm font-medium text-gray-700">
                                    Tamanho (ml)
                                </label>
                                <input type="number" 
                                       id="size_ml" 
                                       name="size_ml" 
                                       min="0"
                                       value="<?= $this->escape($formData['size_ml'] ?? $product['size_ml'] ?? '') ?>"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            <div>
                                <label for="max_ingredients" class="block text-sm font-medium text-gray-700">
                                    Máx. Ingredientes
                                </label>
                                <input type="number" 
                                       id="max_ingredients" 
                                       name="max_ingredients" 
                                       min="0"
                                       value="<?= $this->escape($formData['max_ingredients'] ?? $product['max_ingredients'] ?? '') ?>"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <p class="mt-1 text-xs text-gray-500">
                                    Deixe vazio para ilimitado
                                </p>
                            </div>

                            <div>
                                <label for="size_order" class="block text-sm font-medium text-gray-700">
                                    Ordem de Tamanho
                                </label>
                                <input type="number" 
                                       id="size_order" 
                                       name="size_order" 
                                       min="0"
                                       value="<?= $this->escape($formData['size_order'] ?? $product['size_order'] ?? '0') ?>"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <p class="mt-1 text-xs text-gray-500">
                                    Para ordenação (ex: 1=P, 2=M, 3=G)
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Status -->
                    <div>
                        <div class="flex items-center">
                            <input id="active" 
                                   name="active" 
                                   type="checkbox" 
                                   value="1"
                                   <?= ($formData['active'] ?? $product['active'] ?? '1') ? 'checked' : '' ?>
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="active" class="ml-2 block text-sm text-gray-900">
                                Produto ativo
                            </label>
                        </div>
                        <p class="mt-1 text-sm text-gray-500">
                            Produtos inativos não aparecem no cardápio público
                        </p>
                    </div>
                </div>

                <!-- Botões -->
                <div class="mt-6 flex justify-end space-x-3">
                    <a href="/admin/products" 
                       class="bg-white py-2 px-4 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Cancelar
                    </a>
                    <button type="submit" 
                            class="bg-blue-600 py-2 px-4 border border-transparent rounded-md text-sm font-medium text-white hover:bg-blue-700">
                        <?= isset($product) ? 'Atualizar' : 'Criar' ?> Produto
                    </button>
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

        // Limpar URL quando arquivo é selecionado
        document.getElementById('image').addEventListener('change', function() {
            if (this.files.length > 0) {
                document.getElementById('image_url').value = '';
            }
        });

        // Limpar arquivo quando URL é preenchida
        document.getElementById('image_url').addEventListener('input', function() {
            if (this.value.trim() !== '') {
                document.getElementById('image').value = '';
                document.getElementById('imagePreview').classList.add('hidden');
            }
        });
    </script>
</body>
</html>
