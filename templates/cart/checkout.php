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
            <div class="flex items-center">
                <a href="/<?= $store_slug ?>/carrinho" class="text-purple-600 hover:text-purple-800 mr-4">
                    <i class="fas fa-arrow-left text-xl"></i>
                </a>
                <div>
                    <h1 class="text-xl font-bold text-gray-800">Finalizar Pedido</h1>
                    <p class="text-sm text-gray-600"><?= htmlspecialchars($store['store_name']) ?></p>
                </div>
            </div>
        </div>
    </header>

    <div class="container mx-auto px-4 py-6">
        <?php if ($error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="/<?= $store_slug ?>/checkout" class="space-y-6">
            <!-- Dados do cliente -->
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-user text-purple-600 mr-2"></i>
                    Dados para entrega
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label for="customer_name" class="block text-sm font-medium text-gray-700 mb-1">
                            Nome completo *
                        </label>
                        <input 
                            type="text" 
                            id="customer_name" 
                            name="customer_name" 
                            required 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                            placeholder="Seu nome completo"
                        >
                    </div>

                    <div>
                        <label for="customer_phone" class="block text-sm font-medium text-gray-700 mb-1">
                            WhatsApp *
                        </label>
                        <input 
                            type="tel" 
                            id="customer_phone" 
                            name="customer_phone" 
                            required 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                            placeholder="(11) 99999-9999"
                        >
                    </div>

                    <div class="md:col-span-2">
                        <label for="customer_address" class="block text-sm font-medium text-gray-700 mb-1">
                            Endereço completo *
                        </label>
                        <textarea 
                            id="customer_address" 
                            name="customer_address" 
                            required 
                            rows="3"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                            placeholder="Rua, número, complemento, bairro, cidade"
                        ></textarea>
                    </div>

                    <div class="md:col-span-2">
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">
                            Observações (opcional)
                        </label>
                        <textarea 
                            id="notes" 
                            name="notes" 
                            rows="2"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                            placeholder="Observações especiais sobre o pedido..."
                        ></textarea>
                    </div>
                </div>
            </div>

            <!-- Informações de pagamento -->
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-credit-card text-purple-600 mr-2"></i>
                    Forma de pagamento
                </h2>

                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <i class="fab fa-whatsapp text-green-500 text-2xl mr-3"></i>
                        <div>
                            <h3 class="font-semibold text-gray-800">Pagamento via WhatsApp</h3>
                            <p class="text-sm text-gray-600">
                                Após confirmar o pedido, você será direcionado para o WhatsApp da loja 
                                para acertar a forma de pagamento e entrega.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botões -->
            <div class="flex flex-col sm:flex-row gap-3">
                <a href="/<?= $store_slug ?>/carrinho" 
                   class="flex-1 bg-gray-100 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-200 transition duration-200 text-center">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Voltar ao carrinho
                </a>
                <button 
                    type="submit" 
                    class="flex-1 bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition duration-200 flex items-center justify-center"
                >
                    <i class="fab fa-whatsapp mr-2"></i>
                    Enviar pedido via WhatsApp
                </button>
            </div>
        </form>
    </div>

    <script>
        // Formatação do telefone
        document.getElementById('customer_phone').addEventListener('input', function() {
            let value = this.value.replace(/\D/g, '');
            if (value.length >= 11) {
                value = value.replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
            } else if (value.length >= 7) {
                value = value.replace(/(\d{2})(\d{4})(\d{0,4})/, '($1) $2-$3');
            } else if (value.length >= 3) {
                value = value.replace(/(\d{2})(\d{0,5})/, '($1) $2');
            }
            this.value = value;
        });
    </script>
</body>
</html>
