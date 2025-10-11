<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b">
        <div class="container mx-auto px-4 py-4">
            <div class="flex items-center">
                <a href="/{{ $store_slug }}/carrinho" class="text-purple-600 hover:text-purple-800 mr-4">
                    <i class="fas fa-arrow-left text-xl"></i>
                </a>
                <div>
                    <h1 class="text-xl font-bold text-gray-800">Finalizar Pedido</h1>
                    <p class="text-sm text-gray-600">{{ $store['store_name'] }}</p>
                </div>
            </div>
        </div>
    </header>

    <div class="container mx-auto px-4 py-6">
        @if ($error)
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                <i class="fas fa-exclamation-circle mr-2"></i>
                {{ $error }}
            </div>
        @endif

        <form method="POST" action="/{{ $store_slug }}/checkout" class="space-y-6">
            @csrf
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

            <!-- Tipo de pedido -->
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-truck text-purple-600 mr-2"></i>
                    Tipo de pedido
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="flex items-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                            <input 
                                type="radio" 
                                name="order_type" 
                                value="delivery" 
                                class="mr-3 text-purple-600 focus:ring-purple-500" 
                                checked
                            >
                            <div>
                                <i class="fas fa-motorcycle text-purple-600 mr-2"></i>
                                <span class="font-medium">Delivery</span>
                                <p class="text-sm text-gray-600 mt-1">Entrega no endereço informado</p>
                            </div>
                        </label>
                    </div>

                    <div>
                        <label class="flex items-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                            <input 
                                type="radio" 
                                name="order_type" 
                                value="pickup" 
                                class="mr-3 text-purple-600 focus:ring-purple-500"
                            >
                            <div>
                                <i class="fas fa-store text-purple-600 mr-2"></i>
                                <span class="font-medium">Retirada no Balcão</span>
                                <p class="text-sm text-gray-600 mt-1">Retirar na loja</p>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Taxa de entrega -->
                <div id="delivery-fee" class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-700">Taxa de entrega:</span>
                        <span class="text-sm font-bold text-blue-600">
                            @if(isset($store_settings['delivery_fee']) && floatval($store_settings['delivery_fee']) > 0)
                                R$ {{ number_format(floatval($store_settings['delivery_fee']), 2, ',', '.') }}
                            @else
                                A consultar
                            @endif
                        </span>
                    </div>
                    <p class="text-xs text-gray-600 mt-1">A confirmar com a loja via WhatsApp</p>
                </div>
            </div>

            <!-- Informações de pagamento -->
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-credit-card text-purple-600 mr-2"></i>
                    Forma de pagamento
                </h2>

                <div class="grid grid-cols-1 gap-3">
                    <label class="flex items-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                        <input 
                            type="radio" 
                            name="payment_method" 
                            value="pix" 
                            class="mr-3 text-purple-600 focus:ring-purple-500" 
                            checked
                        >
                        <div class="flex-1">
                            <i class="fas fa-qrcode text-purple-600 mr-2"></i>
                            <span class="font-medium">PIX</span>
                            <p class="text-sm text-gray-600 mt-1">Chave PIX será enviada após confirmação</p>
                        </div>
                    </label>

                    <label class="flex items-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                        <input 
                            type="radio" 
                            name="payment_method" 
                            value="money" 
                            class="mr-3 text-purple-600 focus:ring-purple-500"
                        >
                        <div class="flex-1">
                            <i class="fas fa-money-bill text-purple-600 mr-2"></i>
                            <span class="font-medium">Dinheiro</span>
                            <p class="text-sm text-gray-600 mt-1">Pagamento na entrega/retirada</p>
                        </div>
                    </label>

                    <label class="flex items-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                        <input 
                            type="radio" 
                            name="payment_method" 
                            value="card" 
                            class="mr-3 text-purple-600 focus:ring-purple-500"
                        >
                        <div class="flex-1">
                            <i class="fas fa-credit-card text-purple-600 mr-2"></i>
                            <span class="font-medium">Cartão</span>
                            <p class="text-sm text-gray-600 mt-1">Débito ou crédito na entrega/retirada</p>
                        </div>
                    </label>
                </div>

                <!-- Troco -->
                <div id="change-field" class="mt-4 hidden">
                    <label for="change_for" class="block text-sm font-medium text-gray-700 mb-1">
                        Troco para:
                    </label>
                    <input 
                        type="number" 
                        id="change_for" 
                        name="change_for" 
                        step="0.01"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                        placeholder="R$ 0,00"
                    >
                </div>

                <div class="mt-4 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <i class="fab fa-whatsapp text-green-500 text-2xl mr-3"></i>
                        <div>
                            <h3 class="font-semibold text-gray-800">Confirmação via WhatsApp</h3>
                            <p class="text-sm text-gray-600">
                                Após confirmar o pedido, você será direcionado para o WhatsApp da loja 
                                para finalizar os detalhes de pagamento e entrega.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Resumo do pedido -->
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-receipt text-purple-600 mr-2"></i>
                    Resumo do pedido
                </h2>

                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Subtotal dos itens:</span>
                        <span class="font-medium">R$ {{ number_format($total, 2, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between" id="delivery-fee-summary" style="display: block;">
                        <span class="text-gray-600">Taxa de entrega:</span>
                        <span class="font-medium" id="delivery-fee-value">
                            @if(isset($store_settings['delivery_fee']) && floatval($store_settings['delivery_fee']) > 0)
                                R$ {{ number_format(floatval($store_settings['delivery_fee']), 2, ',', '.') }}
                            @else
                                R$ 0,00
                            @endif
                        </span>
                    </div>
                    <div class="border-t pt-2 flex justify-between text-base font-bold">
                        <span class="text-gray-800">Total:</span>
                        <span class="text-purple-600" id="total-with-delivery">
                            @php
                                $deliveryFee = floatval($store_settings['delivery_fee'] ?? 0.00);
                                $totalWithDelivery = $total + $deliveryFee;
                            @endphp
                            R$ {{ number_format($totalWithDelivery, 2, ',', '.') }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Botões -->
            <div class="flex flex-col sm:flex-row gap-3">
                <a href="/{{ $store_slug }}/carrinho" 
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
        // Controla exibição da taxa de entrega
        document.querySelectorAll('input[name="order_type"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const deliveryFee = document.getElementById('delivery-fee');
                const addressField = document.getElementById('customer_address').closest('.md\\:col-span-2');
                
                if (this.value === 'delivery') {
                    deliveryFee.style.display = 'block';
                    addressField.style.display = 'block';
                    document.getElementById('customer_address').required = true;
                } else {
                    deliveryFee.style.display = 'none';
                    addressField.style.display = 'none';
                    document.getElementById('customer_address').required = false;
                }
            });
        });

        // Controla exibição do campo de troco
        document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const changeField = document.getElementById('change-field');
                if (this.value === 'money') {
                    changeField.classList.remove('hidden');
                } else {
                    changeField.classList.add('hidden');
                    document.getElementById('change_for').value = '';
                }
            });
        });

        // Busca dados do cliente no backend ao digitar telefone
        async function autofillCustomerDataFromBackend() {
            const phone = document.getElementById('customer_phone').value.replace(/\D/g, '');
            if (phone.length >= 8) {
                try {
                    const resp = await fetch(`/api/last-order-by-phone?phone=${encodeURIComponent(phone)}`);
                    if (!resp.ok) return;
                    const obj = await resp.json();
                    if (obj.customer_name) document.getElementById('customer_name').value = obj.customer_name;
                    if (obj.customer_address) document.getElementById('customer_address').value = obj.customer_address;
                    if (obj.notes) document.getElementById('notes').value = obj.notes;
                } catch (e) {}
            }
        }

        // Valores para cálculo
        const subtotal = {{ $total }};
        const deliveryFee = {{ floatval($store_settings['delivery_fee'] ?? 0.00) }};

        // Função para atualizar o total
        function updateTotal() {
            const orderType = document.querySelector('input[name="order_type"]:checked')?.value || 'delivery';
            const currentDeliveryFee = orderType === 'delivery' ? deliveryFee : 0;
            const total = subtotal + currentDeliveryFee;

            // Atualizar exibição da taxa de entrega no resumo
            const deliveryFeeSummary = document.getElementById('delivery-fee-summary');
            const deliveryFeeValue = document.getElementById('delivery-fee-value');
            const totalWithDelivery = document.getElementById('total-with-delivery');

            if (deliveryFeeSummary && deliveryFeeValue && totalWithDelivery) {
                deliveryFeeSummary.style.display = 'flex';
                deliveryFeeValue.textContent = 'R$ ' + currentDeliveryFee.toFixed(2).replace('.', ',');
                totalWithDelivery.textContent = 'R$ ' + total.toFixed(2).replace('.', ',');
            }
        }

        // Controla exibição da taxa de entrega
        document.querySelectorAll('input[name="order_type"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const deliveryFeeDiv = document.getElementById('delivery-fee');
                const addressField = document.getElementById('customer_address').closest('.md\\:col-span-2');
                
                if (this.value === 'delivery') {
                    if (deliveryFeeDiv) deliveryFeeDiv.style.display = 'block';
                    if (addressField) addressField.style.display = 'block';
                    document.getElementById('customer_address').required = true;
                } else {
                    if (deliveryFeeDiv) deliveryFeeDiv.style.display = 'none';
                    if (addressField) addressField.style.display = 'none';
                    document.getElementById('customer_address').required = false;
                }

                // Atualizar total
                updateTotal();
            });
        });

        // Controla exibição do campo de troco
        document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const changeField = document.getElementById('change-field');
                if (this.value === 'money') {
                    changeField.classList.remove('hidden');
                } else {
                    changeField.classList.add('hidden');
                    document.getElementById('change_for').value = '';
                }
            });
        });

        // Inicializar na carga da página
        document.addEventListener('DOMContentLoaded', function() {
            updateTotal();
        });

        document.getElementById('customer_phone').addEventListener('blur', autofillCustomerDataFromBackend);
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
