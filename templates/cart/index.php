<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b">
        <div class="container mx-auto px-4 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <a href="/<?= $store_slug ?>" class="text-purple-600 hover:text-purple-800 mr-4">
                        <i class="fas fa-arrow-left text-xl"></i>
                    </a>
                    <div>
                        <h1 class="text-xl font-bold text-gray-800">Carrinho</h1>
                        <p class="text-sm text-gray-600"><?= htmlspecialchars($store['store_name']) ?></p>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-lg font-bold text-purple-600">
                        R$ <?= number_format($total, 2, ',', '.') ?>
                    </div>
                    <div class="text-sm text-gray-500">
                        <?= count($cart_items) ?> item(s)
                    </div>
                </div>
            </div>
        </div>
    </header>

    <div class="container mx-auto px-4 py-6">
        <?php if (empty($cart_items)): ?>
            <!-- Carrinho vazio -->
            <div class="text-center py-12">
                <i class="fas fa-shopping-cart text-6xl text-gray-300 mb-4"></i>
                <h2 class="text-2xl font-bold text-gray-800 mb-2">Carrinho vazio</h2>
                <p class="text-gray-600 mb-6">Adicione alguns produtos deliciosos ao seu carrinho</p>
                <a href="/<?= $store_slug ?>" 
                   class="bg-purple-600 text-white px-6 py-3 rounded-lg hover:bg-purple-700 transition duration-200">
                    <i class="fas fa-utensils mr-2"></i>
                    Ver Cardápio
                </a>
            </div>
        <?php else: ?>
            <!-- Itens do carrinho -->
            <div class="space-y-4 mb-6">
                <?php foreach ($cart_items as $item): ?>
                    <div class="bg-white rounded-lg shadow-sm border p-4">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center mb-2">
                                    <?php if ($item['product']['image_url']): ?>
                                        <img src="<?= htmlspecialchars($item['product']['image_url']) ?>" 
                                             alt="<?= htmlspecialchars($item['product']['name']) ?>"
                                             class="w-12 h-12 object-cover rounded-md mr-3">
                                    <?php else: ?>
                                        <div class="w-12 h-12 bg-gray-200 rounded-md flex items-center justify-center mr-3">
                                            <i class="fas fa-image text-gray-400"></i>
                                        </div>
                                    <?php endif; ?>
                                    <div class="flex-1">
                                        <h3 class="font-semibold text-gray-800"><?= htmlspecialchars($item['product']['name']) ?></h3>
                                        <?php if ($item['size']): ?>
                                            <p class="text-sm text-gray-600">Tamanho: <?= htmlspecialchars($item['size']) ?></p>
                                        <?php endif; ?>
                                    </div>
                                    <div class="text-right">
                                        <div class="font-bold text-purple-600">
                                            R$ <?= number_format($item['total'], 2, ',', '.') ?>
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            <?= $item['quantity'] ?>x R$ <?= number_format($item['price'], 2, ',', '.') ?>
                                        </div>
                                    </div>
                                </div>

                                <?php if (!empty($item['ingredients'])): ?>
                                    <div class="mb-2">
                                        <p class="text-sm text-gray-600 mb-1">
                                            <i class="fas fa-plus-circle text-green-500 mr-1"></i>
                                            Adicionais:
                                        </p>
                                        <div class="flex flex-wrap gap-1">
                                            <?php foreach ($item['ingredients'] as $ingredient): ?>
                                                <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">
                                                    <?= htmlspecialchars($ingredient['name']) ?>
                                                    <?php if ($ingredient['quantity'] > 1): ?>
                                                        (<?= $ingredient['quantity'] ?>x)
                                                    <?php endif; ?>
                                                    <?php if ($ingredient['price'] > 0): ?>
                                                        +R$ <?= number_format($ingredient['price'], 2, ',', '.') ?>
                                                    <?php endif; ?>
                                                </span>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <?php if ($item['notes']): ?>
                                    <div class="mb-2">
                                        <p class="text-sm text-gray-600">
                                            <i class="fas fa-sticky-note text-yellow-500 mr-1"></i>
                                            Observações: <?= htmlspecialchars($item['notes']) ?>
                                        </p>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <a href="/<?= $store_slug ?>/carrinho/remover/<?= $item['cart_id'] ?>" 
                               class="ml-4 text-red-500 hover:text-red-700 transition duration-200"
                               onclick="return confirm('Remover este item do carrinho?')">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Resumo e botões -->
            <div class="bg-white rounded-lg shadow-sm border p-4">
                <div class="border-b pb-4 mb-4">
                    <div class="flex justify-between items-center">
                        <span class="text-lg font-semibold text-gray-800">Total</span>
                        <span class="text-2xl font-bold text-purple-600">
                            R$ <?= number_format($total, 2, ',', '.') ?>
                        </span>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-3">
                    <a href="/<?= $store_slug ?>" 
                       class="flex-1 bg-gray-100 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-200 transition duration-200 text-center">
                        <i class="fas fa-plus mr-2"></i>
                        Adicionar mais itens
                    </a>
                    <a href="/<?= $store_slug ?>/checkout" 
                       class="flex-1 bg-purple-600 text-white px-6 py-3 rounded-lg hover:bg-purple-700 transition duration-200 text-center">
                        <i class="fas fa-credit-card mr-2"></i>
                        Finalizar Pedido
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
