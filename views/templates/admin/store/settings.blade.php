@extends('layouts.admin')

@section('title', 'Configurações da Loja')

@section('content')

@if (isset($_GET['success']))
    <div class="mb-5 bg-emerald-50 border border-emerald-200 rounded-xl px-4 py-3 flex items-center gap-3 text-emerald-700 text-sm">
        <i class="fas fa-check-circle text-emerald-500"></i> Configurações atualizadas com sucesso!
    </div>
@endif
@if (isset($_GET['error']))
    <div class="mb-5 bg-red-50 border border-red-200 rounded-xl px-4 py-3 flex items-center gap-3 text-red-700 text-sm">
        <i class="fas fa-exclamation-circle text-red-500"></i> Erro: {{ urldecode($_GET['error']) }}
    </div>
@endif

<form action="/admin/loja/configuracoes" method="POST" enctype="multipart/form-data" class="space-y-5 max-w-3xl">

    <!-- Informações Básicas -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
        <h3 class="text-sm font-bold text-slate-800 mb-5 flex items-center gap-2">
            <i class="fas fa-store text-indigo-500"></i> Informações Básicas da Loja
        </h3>
        <div class="grid grid-cols-1 gap-5">
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1.5">Nome da Loja *</label>
                <input type="text" name="store_name" required value="{{ $settings['store_name'] }}"
                       class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1.5">Descrição</label>
                <textarea name="store_description" rows="3" placeholder="Descreva sua loja para os clientes..."
                          class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 resize-none">{{ $settings['store_description'] }}</textarea>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1.5">Endereço</label>
                <textarea name="store_address" rows="2" placeholder="Rua, número, bairro, cidade..."
                          class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 resize-none">{{ $settings['store_address'] ?? $settings['address'] ?? '' }}</textarea>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">WhatsApp</label>
                    <input type="tel" id="store_phone" name="store_phone"
                           value="{{ $settings['store_phone'] ?? $settings['whatsapp'] ?? '' }}"
                           placeholder="(84) 99999-9999"
                           class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Email</label>
                    <input type="email" name="store_email"
                           value="{{ $settings['store_email'] ?? '' }}"
                           placeholder="contato@minhaloja.com"
                           class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
            </div>
            <!-- Logo -->
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1.5">Logo da Loja</label>
                <div class="flex items-center gap-4">
                    @if (!empty($settings['store_logo']) || !empty($settings['logo']))
                        <img src="{{ $settings['store_logo'] ?? $settings['logo'] }}" class="w-16 h-16 rounded-xl object-cover border border-slate-200">
                    @else
                        <div class="w-16 h-16 rounded-xl bg-slate-100 border border-slate-200 flex items-center justify-center">
                            <i class="fas fa-image text-slate-300 text-xl"></i>
                        </div>
                    @endif
                    <div class="flex-1">
                        <input type="file" name="store_logo" accept="image/*"
                               class="text-sm text-slate-500 file:mr-3 file:py-1.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-600 hover:file:bg-indigo-100">
                        <p class="mt-1 text-xs text-slate-400">PNG, JPG até 2MB</p>
                    </div>
                </div>
            </div>
            <!-- Banner -->
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1.5">Banner do Cabeçalho</label>
                @if (!empty($settings['store_banner']))
                    <img src="{{ $settings['store_banner'] }}" class="w-full h-28 rounded-xl object-cover border border-slate-200 mb-3">
                @else
                    <div class="w-full h-28 rounded-xl bg-slate-100 border border-slate-200 flex items-center justify-center mb-3">
                        <div class="text-center"><i class="fas fa-image text-slate-300 text-3xl"></i><p class="text-xs text-slate-400 mt-1">Nenhum banner</p></div>
                    </div>
                @endif
                <input type="file" name="store_banner" accept="image/*"
                       class="text-sm text-slate-500 file:mr-3 file:py-1.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-600 hover:file:bg-indigo-100">
                <p class="mt-1 text-xs text-slate-400">Recomendado: 1200x300px, até 2MB</p>
            </div>
        </div>
    </div>

    <!-- Entrega & PIX -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
        <h3 class="text-sm font-bold text-slate-800 mb-5 flex items-center gap-2">
            <i class="fas fa-motorcycle text-indigo-500"></i> Entrega & Pagamento
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1.5">Taxa de Entrega (R$)</label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm">R$</span>
                    <input type="number" name="delivery_fee" step="0.01" min="0"
                           value="{{ number_format($settings['delivery_fee'], 2, '.', '') }}"
                           class="w-full pl-9 pr-3 py-2 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
                <p class="text-xs text-slate-400 mt-1">0 para entrega gratuita</p>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1.5"><i class="fas fa-qrcode mr-1"></i>Chave PIX</label>
                <input type="text" name="pix_key" value="{{ $settings['pix_key'] ?? '' }}"
                       placeholder="CPF, CNPJ, email ou chave aleatória"
                       class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                <p class="text-xs text-slate-400 mt-1">Exibida nos comprovantes</p>
            </div>
        </div>
    </div>

    <!-- Fidelidade -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
        <h3 class="text-sm font-bold text-slate-800 mb-5 flex items-center gap-2">
            <i class="fas fa-star text-indigo-500"></i> Programa de Fidelidade
        </h3>
        <label class="flex items-center gap-3 cursor-pointer mb-4">
            <input type="checkbox" id="loyalty_enabled" name="loyalty_enabled" value="1"
                   {{ $settings['loyalty_enabled'] ? 'checked' : '' }}
                   class="w-4 h-4 text-indigo-600 rounded border-slate-300 focus:ring-indigo-400">
            <span class="text-sm text-slate-700 font-medium">Ativar programa de fidelidade</span>
        </label>
        <div id="loyalty_settings" class="{{ $settings['loyalty_enabled'] ? '' : 'hidden' }}">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Pedidos para desconto</label>
                    <input type="number" id="loyalty_orders_required" name="loyalty_orders_required"
                           min="1" value="{{ $settings['loyalty_orders_required'] }}"
                           class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Desconto (%)</label>
                    <input type="number" id="loyalty_discount_percent" name="loyalty_discount_percent"
                           step="0.01" min="0" max="100"
                           value="{{ number_format($settings['loyalty_discount_percent'], 2, '.', '') }}"
                           class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
            </div>
            <div class="bg-indigo-50 border border-indigo-100 rounded-xl px-4 py-3 text-xs text-indigo-700">
                <i class="fas fa-info-circle mr-1"></i>
                Após <strong id="orders_display">{{ $settings['loyalty_orders_required'] }}</strong> pedidos concluídos,
                o cliente ganha <strong id="discount_display">{{ number_format($settings['loyalty_discount_percent'], 1) }}%</strong> de desconto.
            </div>
        </div>
    </div>

    <!-- Horários -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                <i class="fas fa-clock text-indigo-500"></i> Horários de Funcionamento
            </h3>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="hidden" name="is_open" value="0">
                <input type="checkbox" id="is_open" name="is_open" value="1"
                       {{ ($settings['is_open'] ?? 1) ? 'checked' : '' }}
                       class="sr-only peer">
                <div class="relative w-11 h-6 bg-slate-200 rounded-full peer peer-checked:bg-indigo-600 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-full"></div>
                <span class="text-sm font-medium text-slate-700">Loja Aberta</span>
            </label>
        </div>

        @php
        $businessHours = isset($settings['business_hours'])
            ? (is_string($settings['business_hours']) ? json_decode($settings['business_hours'], true) : $settings['business_hours'])
            : [];
        $days = [
            'monday'=>'Segunda','tuesday'=>'Terça','wednesday'=>'Quarta',
            'thursday'=>'Quinta','friday'=>'Sexta','saturday'=>'Sábado','sunday'=>'Domingo'
        ];
        @endphp

        <div class="space-y-2">
            @foreach($days as $dayKey => $dayName)
            @php
            $dayData = $businessHours[$dayKey] ?? ['enabled' => true, 'open' => '09:00', 'close' => '18:00'];
            @endphp
            <div class="flex items-center gap-4 p-3 bg-slate-50 rounded-xl">
                <label class="flex items-center gap-2 cursor-pointer w-28 flex-shrink-0">
                    <input type="hidden" name="business_hours[{{ $dayKey }}][enabled]" value="0">
                    <input type="checkbox" name="business_hours[{{ $dayKey }}][enabled]" value="1"
                           {{ $dayData['enabled'] ? 'checked' : '' }}
                           class="day-enabled w-4 h-4 text-indigo-600 rounded border-slate-300"
                           onchange="toggleDayInputs(this, '{{ $dayKey }}')">
                    <span class="text-xs font-semibold text-slate-700">{{ $dayName }}</span>
                </label>
                <div class="flex items-center gap-2 flex-1">
                    <input type="time" name="business_hours[{{ $dayKey }}][open]"
                           value="{{ $dayData['open'] }}"
                           class="day-input-{{ $dayKey }} flex-1 px-2 py-1.5 border border-slate-200 rounded-lg text-xs focus:outline-none focus:ring-1 focus:ring-indigo-400"
                           {{ !$dayData['enabled'] ? 'disabled' : '' }}>
                    <span class="text-slate-400 text-xs">até</span>
                    <input type="time" name="business_hours[{{ $dayKey }}][close]"
                           value="{{ $dayData['close'] }}"
                           class="day-input-{{ $dayKey }} flex-1 px-2 py-1.5 border border-slate-200 rounded-lg text-xs focus:outline-none focus:ring-1 focus:ring-indigo-400"
                           {{ !$dayData['enabled'] ? 'disabled' : '' }}>
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-4">
            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Mensagem quando fechado</label>
            <input type="text" name="closed_message"
                   value="{{ $settings['closed_message'] ?? 'No momento estamos fechados. Volte em breve!' }}"
                   class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
        </div>
    </div>

    <!-- Save -->
    <div class="flex justify-end pb-6">
        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-xl text-sm font-semibold transition flex items-center gap-2 shadow-sm">
            <i class="fas fa-save"></i> Salvar Configurações
        </button>
    </div>
</form>
@endsection

@section('scripts')
<script>
document.getElementById('loyalty_enabled').addEventListener('change', function() {
    document.getElementById('loyalty_settings').classList.toggle('hidden', !this.checked);
});
document.getElementById('loyalty_orders_required')?.addEventListener('input', function() {
    document.getElementById('orders_display').textContent = this.value;
});
document.getElementById('loyalty_discount_percent')?.addEventListener('input', function() {
    document.getElementById('discount_display').textContent = parseFloat(this.value).toFixed(1);
});
const phoneInput = document.getElementById('store_phone');
phoneInput?.addEventListener('input', function() {
    let v = this.value.replace(/\D/g,'');
    if (v.length >= 11) v = v.replace(/(\d{2})(\d{5})(\d{4})/,'($1) $2-$3');
    else if (v.length >= 6) v = v.replace(/(\d{2})(\d{4})(\d+)/,'($1) $2-$3');
    else if (v.length >= 2) v = v.replace(/(\d{2})(\d+)/,'($1) $2');
    this.value = v;
});
function toggleDayInputs(cb, dayKey) {
    document.querySelectorAll('.day-input-' + dayKey).forEach(inp => {
        inp.disabled = !cb.checked;
        inp.classList.toggle('bg-slate-100', !cb.checked);
        inp.classList.toggle('text-slate-400', !cb.checked);
    });
}
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.day-enabled').forEach(cb => {
        const dayKey = cb.name.match(/\[(.*?)\]/)[1];
        toggleDayInputs(cb, dayKey);
    });
});
</script>
@endsection