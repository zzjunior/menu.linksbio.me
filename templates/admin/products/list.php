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
                    <a href="/admin" class="text-gray-500 hover:text-gray-700">← Voltar</a>
                    <h1 class="text-3xl font-bold text-gray-900"><?= $this->escape($pageTitle) ?></h1>
                </div>
                <a href="/admin/products/new" 
                   class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                    + Novo Produto
                </a>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <?php if (empty($products)): ?>
            <div class="text-center py-12">
                <div class="text-6xl mb-4">📦</div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Nenhum produto cadastrado</h3>
                <p class="text-gray-600 mb-4">Comece criando seu primeiro produto</p>
                <a href="/admin/products/new" 
                   class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700">
                    Criar Produto
                </a>
            </div>
        <?php else: ?>
            <div class="bg-white shadow overflow-hidden rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Produto
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Categoria
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Preço
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Ações
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($products as $product): ?>
                            <tr class="<?= $product['active'] ? '' : 'bg-gray-50 opacity-75' ?>">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-12 w-12">
                                            <?php if ($product['image_url']): ?>
                                                <img class="h-12 w-12 rounded-lg object-cover" 
                                                     src="<?= $this->escape($product['image_url']) ?>" 
                                                     alt="<?= $this->escape($product['name']) ?>">
                                            <?php else: ?>
                                                <div class="h-12 w-12 bg-gray-200 rounded-lg flex items-center justify-center">
                                                    <span class="text-gray-400 text-xl">🍇</span>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                <?= $this->escape($product['name']) ?>
                                                <?php if ($product['size_ml']): ?>
                                                    <span class="text-gray-500">(<?= $product['size_ml'] ?>ml)</span>
                                                <?php endif; ?>
                                            </div>
                                            <?php if ($product['description']): ?>
                                                <div class="text-sm text-gray-500 max-w-xs truncate">
                                                    <?= $this->escape($product['description']) ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                        <?= $this->escape($product['category_name'] ?? 'Sem categoria') ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?= $this->formatPrice($product['price']) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if ($product['active']): ?>
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                            Ativo
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                            Inativo
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="/admin/products/<?= $product['id'] ?>/edit" 
                                           class="text-blue-600 hover:text-blue-900">
                                            Editar
                                        </a>
                                        <form method="POST" action="/admin/products/<?= $product['id'] ?>/delete" 
                                              class="inline" 
                                              onsubmit="return confirm('Tem certeza que deseja remover este produto?')">
                                            <button type="submit" class="text-red-600 hover:text-red-900">
                                                Remover
                                            </button>
                                        </form>
                                    </div>
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
