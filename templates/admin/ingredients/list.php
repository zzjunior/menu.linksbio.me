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
        <?php if (isset($success)): ?>
            <div class="bg-green-50 border border-green-200 rounded-md p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle text-green-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">
                            <?= $this->escape($success) ?>
                        </p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <div class="bg-red-50 border border-red-200 rounded-md p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle text-red-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-red-800">
                            <?= $this->escape($error) ?>
                        </p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Lista de Ingredientes -->
        <div class="bg-white shadow overflow-hidden sm:rounded-md">
            <?php if (empty($ingredients)): ?>
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
            <?php else: ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 p-6">
                    <?php foreach ($ingredients as $ingredient): ?>
                        <div class="bg-gray-50 rounded-lg p-4 hover:bg-gray-100 transition-colors border border-gray-200">
                            <div class="flex items-start space-x-3">
                                <!-- Imagem -->
                                <div class="flex-shrink-0">
                                    <?php if ($ingredient['image_url']): ?>
                                        <img class="h-12 w-12 rounded-lg object-cover" 
                                             src="<?= $this->escape($ingredient['image_url']) ?>" 
                                             alt="<?= $this->escape($ingredient['name']) ?>">
                                    <?php else: ?>
                                        <div class="h-12 w-12 rounded-lg bg-gray-300 flex items-center justify-center">
                                            <i class="fas fa-image text-gray-500"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- Informações -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <h3 class="text-sm font-medium text-gray-900 truncate">
                                                <?= $this->escape($ingredient['name']) ?>
                                            </h3>
                                            
                                            <!-- Preço -->
                                            <div class="flex items-center mt-1">
                                                <?php if ($ingredient['is_free'] || $ingredient['price'] == 0): ?>
                                                    <span class="text-sm font-medium text-green-600">
                                                        <i class="fas fa-gift mr-1"></i>
                                                        Grátis
                                                    </span>
                                                <?php else: ?>
                                                    <span class="text-sm font-medium text-gray-900">
                                                        + <?= $this->formatPrice($ingredient['price']) ?>
                                                    </span>
                                                <?php endif; ?>
                                            </div>

                                            <!-- Badges -->
                                            <div class="flex flex-wrap gap-1 mt-2">
                                                <?php if (!$ingredient['is_active']): ?>
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                                        Inativo
                                                    </span>
                                                <?php endif; ?>
                                                
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                    Max: <?= $ingredient['max_quantity'] ?>
                                                </span>
                                            </div>

                                            <?php if ($ingredient['description']): ?>
                                                <p class="text-xs text-gray-500 mt-1 truncate">
                                                    <?= $this->escape($ingredient['description']) ?>
                                                </p>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <!-- Ações -->
                                        <div class="flex items-center space-x-1 ml-2">
                                            <a href="/admin/ingredients/<?= $ingredient['id'] ?>/edit" 
                                               class="text-indigo-600 hover:text-indigo-900 p-1 rounded hover:bg-indigo-50 transition-colors"
                                               title="Editar ingrediente">
                                                <i class="fas fa-edit text-sm"></i>
                                            </a>
                                            
                                            <button onclick="confirmDelete(<?= $ingredient['id'] ?>, '<?= $this->escape($ingredient['name']) ?>')" 
                                                    class="text-red-600 hover:text-red-900 p-1 rounded hover:bg-red-50 transition-colors"
                                                    title="Excluir ingrediente">
                                                <i class="fas fa-trash text-sm"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
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
    <header class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div class="flex items-center space-x-4">
                    <a href="/admin" class="text-gray-500 hover:text-gray-700">← Voltar</a>
                    <h1 class="text-3xl font-bold text-gray-900"><?= $this->escape($pageTitle) ?></h1>
                </div>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <?php if (empty($ingredients)): ?>
            <div class="text-center py-12">
                <div class="text-6xl mb-4">🥝</div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Nenhum ingrediente cadastrado</h3>
                <p class="text-gray-600 mb-4">Os ingredientes padrão serão criados automaticamente</p>
            </div>
        <?php else: ?>
            <div class="bg-white shadow overflow-hidden rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Nome
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tipo
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Preço Adicional
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($ingredients as $ingredient): ?>
                            <tr class="<?= $ingredient['active'] ? '' : 'bg-gray-50 opacity-75' ?>">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        <?= $this->escape($ingredient['name']) ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 capitalize">
                                        <?= $this->escape($ingredient['type']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?php if ($ingredient['additional_price'] > 0): ?>
                                        <?= $this->formatPrice($ingredient['additional_price']) ?>
                                    <?php else: ?>
                                        <span class="text-gray-400">Grátis</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if ($ingredient['active']): ?>
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                            Ativo
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                            Inativo
                                        </span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </main>
</body>
</html>
