<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $pageTitle }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <!-- Header -->
    <header class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div class="flex items-center space-x-4">
                    <a href="/admin" class="text-gray-500 hover:text-gray-700">← Voltar ao Dashboard</a>
                    {{--<h1 class="text-3xl font-bold text-gray-900">{{ $pageTitle }}</h1>--}}
                </div>
            </div>
        </div>
    </header>

    <main class="max-w-4xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <!-- Mensagens -->
        @if (isset($_GET['success']))
            <div class="mb-6 bg-green-50 border border-green-200 rounded-md p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle text-green-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">
                            Configurações atualizadas com sucesso!
                        </p>
                    </div>
                </div>
            </div>
        @endif

        @if (isset($_GET['error']))
            <div class="mb-6 bg-red-50 border border-red-200 rounded-md p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle text-red-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-red-800">
                            Erro: {{ urldecode($_GET['error']) }}
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <form action="/admin/loja/configuracoes" method="POST" enctype="multipart/form-data" class="space-y-8">
            <!-- Informações Básicas -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                        Informações Básicas da Loja
                    </h3>
                    
                    <div class="grid grid-cols-1 gap-6">
                        <!-- Nome da Loja -->
                        <div>
                            <label for="store_name" class="block text-sm font-medium text-gray-700">
                                Nome da Loja *
                            </label>
                            <input type="text" id="store_name" name="store_name" required
                                   value="{{ $settings['store_name'] }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        </div>

                        <!-- Descrição -->
                        <div>
                            <label for="store_description" class="block text-sm font-medium text-gray-700">
                                Descrição da Loja
                            </label>
                            <textarea id="store_description" name="store_description" rows="3"
                                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                      placeholder="Descreva sua loja para os clientes...">{{ $settings['store_description'] }}</textarea>
                        </div>

                        <!-- Endereço -->
                        <div>
                            <label for="store_address" class="block text-sm font-medium text-gray-700">
                                Endereço da Loja
                            </label>
                            <textarea id="store_address" name="store_address" rows="2"
                                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                      placeholder="Rua, número, bairro, cidade...">{{ $settings['store_address'] ?? $settings['address'] ?? '' }}</textarea>
                        </div>

                        <!-- WhatsApp -->
                        <div>
                            <label for="store_phone" class="block text-sm font-medium text-gray-700">
                                Telefone/WhatsApp
                            </label>
                            <input type="tel" id="store_phone" name="store_phone"
                                   value="{{ $settings['store_phone'] ?? $settings['whatsapp'] ?? '' }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   placeholder="(84) 99999-9999">
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="store_email" class="block text-sm font-medium text-gray-700">
                                Email da Loja
                            </label>
                            <input type="email" id="store_email" name="store_email"
                                   value="{{ $settings['store_email'] ?? '' }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   placeholder="contato@minhaloja.com">
                        </div>

                        <!-- Logo da Loja -->
                        <div>
                            <label for="store_logo" class="block text-sm font-medium text-gray-700">
                                Logo da Loja
                            </label>
                            <div class="mt-1 flex items-center space-x-4">
                                @if (!empty($settings['store_logo']) || !empty($settings['logo']))
                                    <img src="{{ $settings['store_logo'] ?? $settings['logo'] ?? '' }}" alt="Logo atual" 
                                         class="h-16 w-16 rounded-lg object-cover border border-gray-300">
                                @else
                                    <div class="h-16 w-16 rounded-lg bg-gray-100 flex items-center justify-center border border-gray-300">
                                        <i class="fas fa-image text-gray-400 text-xl"></i>
                                    </div>
                                @endif
                                <div class="flex-1">
                                    <input type="file" id="store_logo" name="store_logo" accept="image/*"
                                           class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                    <p class="mt-1 text-xs text-gray-500">PNG, JPG, GIF até 2MB</p>
                                </div>
                            </div>
                        </div>

                        <!-- Banner da Loja -->
                        <div>
                            <label for="store_banner" class="block text-sm font-medium text-gray-700">
                                Banner do Cabeçalho
                            </label>
                            <div class="mt-1 space-y-4">
                                @if (!empty($settings['store_banner']))
                                    <img src="{{ $settings['store_banner'] }}" alt="Banner atual" 
                                         class="w-full h-32 rounded-lg object-cover border border-gray-300">
                                @else
                                    <div class="w-full h-32 rounded-lg bg-gray-100 flex items-center justify-center border border-gray-300">
                                        <div class="text-center">
                                            <i class="fas fa-image text-gray-400 text-3xl"></i>
                                            <p class="mt-2 text-sm text-gray-500">Nenhum banner configurado</p>
                                        </div>
                                    </div>
                                @endif
                                <div>
                                    <input type="file" id="store_banner" name="store_banner" accept="image/*"
                                           class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                    <p class="mt-1 text-xs text-gray-500">Banner do cabeçalho do cardápio. Recomendado: 1200x300px, PNG ou JPG até 2MB</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Configurações de Entrega -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                        Configurações de Entrega
                    </h3>
                    
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label for="delivery_fee" class="block text-sm font-medium text-gray-700">
                                Taxa de Entrega (R$)
                            </label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">R$</span>
                                </div>
                                <input type="number" id="delivery_fee" name="delivery_fee" 
                                       step="0.01" min="0"
                                       value="{{ number_format($settings['delivery_fee'], 2, '.', '') }}"
                                       class="pl-10 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                       placeholder="0.00">
                            </div>
                            <p class="mt-1 text-sm text-gray-500">
                                Taxa fixa de entrega. Deixe 0 para entrega gratuita.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Programa de Fidelidade -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                        Programa de Fidelidade
                    </h3>
                    
                    <div class="space-y-6">
                        <!-- Ativar Fidelidade -->
                        <div class="flex items-center">
                            <input type="checkbox" id="loyalty_enabled" name="loyalty_enabled" 
                                   value="1" {{ $settings['loyalty_enabled'] ? 'checked' : '' }}
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="loyalty_enabled" class="ml-2 block text-sm text-gray-900">
                                Ativar programa de fidelidade
                            </label>
                        </div>

                        <div id="loyalty_settings" class="{{ $settings['loyalty_enabled'] ? '' : 'hidden' }}">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Pedidos Necessários -->
                                <div>
                                    <label for="loyalty_orders_required" class="block text-sm font-medium text-gray-700">
                                        Pedidos Necessários para Desconto
                                    </label>
                                    <input type="number" id="loyalty_orders_required" name="loyalty_orders_required" 
                                           min="1" value="{{ $settings['loyalty_orders_required'] }}"
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                    <p class="mt-1 text-sm text-gray-500">
                                        Número de pedidos concluídos para ganhar desconto
                                    </p>
                                </div>

                                <!-- Percentual de Desconto -->
                                <div>
                                    <label for="loyalty_discount_percent" class="block text-sm font-medium text-gray-700">
                                        Desconto de Fidelidade (%)
                                    </label>
                                    <div class="mt-1 relative rounded-md shadow-sm">
                                        <input type="number" id="loyalty_discount_percent" name="loyalty_discount_percent" 
                                               step="0.01" min="0" max="100"
                                               value="{{ number_format($settings['loyalty_discount_percent'], 2, '.', '') }}"
                                               class="block w-full pr-12 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 sm:text-sm">%</span>
                                        </div>
                                    </div>
                                    <p class="mt-1 text-sm text-gray-500">
                                        Percentual de desconto para clientes fiéis
                                    </p>
                                </div>
                            </div>

                            <!-- Explicação do Programa -->
                            <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-info-circle text-blue-400"></i>
                                    </div>
                                    <div class="ml-3">
                                        <h4 class="text-sm font-medium text-blue-800">Como funciona o programa de fidelidade:</h4>
                                        <p class="mt-2 text-sm text-blue-700">
                                            Após <strong id="orders_display">{{ $settings['loyalty_orders_required'] }}</strong> pedidos concluídos, 
                                            o cliente ganha <strong id="discount_display">{{ number_format($settings['loyalty_discount_percent'], 1) }}%</strong> 
                                            de desconto nos próximos pedidos. O desconto é aplicado automaticamente no checkout.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Horários de Funcionamento -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-lg leading-6 font-medium text-gray-900">
                                <i class="fas fa-clock text-purple-600 mr-2"></i>
                                Horários de Funcionamento
                            </h3>
                            <p class="mt-1 text-sm text-gray-500">
                                Configure os horários em que sua loja aceita pedidos
                            </p>
                        </div>
                        <div class="flex items-center">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="hidden" name="is_open" value="0">
                                <input type="checkbox" id="is_open" name="is_open" value="1" 
                                       {{ ($settings['is_open'] ?? 1) ? 'checked' : '' }}
                                       class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600"></div>
                                <span class="ml-3 text-sm font-medium text-gray-900">Loja Aberta</span>
                            </label>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-4 mb-4">
                        <?php
                        $businessHours = isset($settings['business_hours']) 
                            ? (is_string($settings['business_hours']) 
                                ? json_decode($settings['business_hours'], true) 
                                : $settings['business_hours'])
                            : [];
                        
                        $days = [
                            'monday' => 'Segunda-feira',
                            'tuesday' => 'Terça-feira',
                            'wednesday' => 'Quarta-feira',
                            'thursday' => 'Quinta-feira',
                            'friday' => 'Sexta-feira',
                            'saturday' => 'Sábado',
                            'sunday' => 'Domingo'
                        ];
                        ?>
                        
                        @foreach($days as $dayKey => $dayName)
                            <?php
                            $dayData = $businessHours[$dayKey] ?? ['enabled' => true, 'open' => '09:00', 'close' => '18:00'];
                            ?>
                            <div class="flex items-center gap-4 p-3 bg-gray-50 rounded-lg">
                                <div class="w-32">
                                    <label class="flex items-center cursor-pointer">
                                        <!-- Hidden input sempre envia 0 se checkbox não estiver marcado -->
                                        <input type="hidden" name="business_hours[{{ $dayKey }}][enabled]" value="0">
                                        <input type="checkbox" 
                                               name="business_hours[{{ $dayKey }}][enabled]" 
                                               value="1"
                                               {{ $dayData['enabled'] ? 'checked' : '' }}
                                               class="day-enabled w-4 h-4 text-purple-600 bg-gray-100 border-gray-300 rounded focus:ring-purple-500"
                                               onchange="toggleDayInputs(this, '{{ $dayKey }}')">
                                        <span class="ml-2 text-sm font-medium text-gray-900">{{ $dayName }}</span>
                                    </label>
                                </div>
                                
                                <div class="flex items-center gap-2 flex-1">
                                    <div class="flex-1">
                                        <label class="block text-xs text-gray-600 mb-1">Abertura</label>
                                        <input type="time" 
                                               name="business_hours[{{ $dayKey }}][open]" 
                                               value="{{ $dayData['open'] }}"
                                               class="day-input-{{ $dayKey }} w-full px-3 py-2 border border-gray-300 rounded-md text-sm"
                                               {{ !$dayData['enabled'] ? 'disabled' : '' }}>
                                    </div>
                                    
                                    <span class="text-gray-500 mt-5">até</span>
                                    
                                    <div class="flex-1">
                                        <label class="block text-xs text-gray-600 mb-1">Fechamento</label>
                                        <input type="time" 
                                               name="business_hours[{{ $dayKey }}][close]" 
                                               value="{{ $dayData['close'] }}"
                                               class="day-input-{{ $dayKey }} w-full px-3 py-2 border border-gray-300 rounded-md text-sm"
                                               {{ !$dayData['enabled'] ? 'disabled' : '' }}>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Mensagem quando fechado -->
                    <div>
                        <label for="closed_message" class="block text-sm font-medium text-gray-700 mb-2">
                            Mensagem quando fechado
                        </label>
                        <input type="text" 
                               id="closed_message" 
                               name="closed_message"
                               value="{{ $settings['closed_message'] ?? 'No momento estamos fechados. Volte em breve!' }}"
                               placeholder="Ex: No momento estamos fechados. Volte em breve!"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md">
                        <p class="mt-1 text-xs text-gray-500">Esta mensagem será exibida quando a loja estiver fechada</p>
                    </div>
                </div>
            </div>

            <!-- Botão Salvar -->
            <div class="flex justify-end">
                <button type="submit" 
                        class="bg-blue-600 border border-transparent rounded-md shadow-sm py-2 px-4 inline-flex justify-center text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-save mr-2"></i>
                    Salvar Configurações
                </button>
            </div>
        </form>
    </main>

    <script>
        // Toggle das configurações de fidelidade
        const loyaltyToggle = document.getElementById('loyalty_enabled');
        const loyaltySettings = document.getElementById('loyalty_settings');
        const ordersDisplay = document.getElementById('orders_display');
        const discountDisplay = document.getElementById('discount_display');
        
        loyaltyToggle.addEventListener('change', function() {
            if (this.checked) {
                loyaltySettings.classList.remove('hidden');
            } else {
                loyaltySettings.classList.add('hidden');
            }
        });

        // Atualizar preview da fidelidade
        const ordersInput = document.getElementById('loyalty_orders_required');
        const discountInput = document.getElementById('loyalty_discount_percent');
        
        ordersInput.addEventListener('input', function() {
            ordersDisplay.textContent = this.value;
        });
        
        discountInput.addEventListener('input', function() {
            discountDisplay.textContent = parseFloat(this.value).toFixed(1);
        });

        // Formatação do telefone
        const phoneInput = document.getElementById('store_phone');
        phoneInput.addEventListener('input', function() {
            let value = this.value.replace(/\D/g, '');
            if (value.length >= 11) {
                value = value.replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
            } else if (value.length >= 6) {
                value = value.replace(/(\d{2})(\d{4})(\d+)/, '($1) $2-$3');
            } else if (value.length >= 2) {
                value = value.replace(/(\d{2})(\d+)/, '($1) $2');
            }
            this.value = value;
        });

        // Toggle dos horários de funcionamento
        function toggleDayInputs(checkbox, dayKey) {
            const inputs = document.querySelectorAll('.day-input-' + dayKey);
            inputs.forEach(input => {
                input.disabled = !checkbox.checked;
                if (!checkbox.checked) {
                    input.classList.add('bg-gray-100', 'text-gray-400');
                } else {
                    input.classList.remove('bg-gray-100', 'text-gray-400');
                }
            });
        }

        // Inicializar estado dos inputs ao carregar a página
        document.addEventListener('DOMContentLoaded', function() {
            const dayCheckboxes = document.querySelectorAll('.day-enabled');
            dayCheckboxes.forEach(checkbox => {
                const dayKey = checkbox.name.match(/\[(.*?)\]/)[1];
                toggleDayInputs(checkbox, dayKey);
            });
        });
    </script>
</body>
</html>