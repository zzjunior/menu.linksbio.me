@extends('layouts.customer')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-purple-50 to-pink-50">
	<div class="max-w-4xl mx-auto">
		<!-- Header da Loja com Banner -->
		@if(!empty($store['store_banner']))
			<!-- Banner Background Hero Section -->
			<div class="relative h-48 bg-cover bg-center bg-gray-100 overflow-hidden rounded-2xl mb-8" 
				 style="background-image: linear-gradient(135deg, rgba(139, 92, 246, 0.8), rgba(236, 72, 153, 0.7)), url('{{ $store['store_banner'] }}');">
				<!-- Gradient Overlay -->
				<div class="absolute inset-0 bg-gradient-to-r from-primary/90 to-accent/80"></div>
				
				<!-- Store Info Content -->
				<div class="absolute inset-0 flex items-center justify-center">
					<div class="text-center text-white p-6">
						@if(!empty($store['store_logo']))
							<div class="w-20 h-20 mx-auto mb-4 rounded-full bg-white/20 backdrop-blur-sm border-2 border-white/30 overflow-hidden shadow-lg">
								<img src="{{ $store['store_logo'] }}" 
									 alt="Logo {{ $store['store_name'] }}" 
									 class="w-full h-full object-cover">
							</div>
						@endif
						<h1 class="text-3xl font-bold mb-2">Repetir Pedido</h1>
						<p class="text-lg opacity-90">{{ $store['store_name'] }}</p>
					</div>
				</div>
			</div>
		@else
			<!-- Header sem banner -->
			<div class="relative h-48 bg-gradient-to-r from-purple-600 to-pink-600 rounded-2xl mb-8">
				<div class="absolute inset-0 flex items-center justify-center">
					<div class="text-center text-white p-6">
						@if(!empty($store['store_logo']))
							<div class="w-20 h-20 mx-auto mb-4 rounded-full bg-white/20 backdrop-blur-sm border-2 border-white/30 overflow-hidden shadow-lg">
								<img src="{{ $store['store_logo'] }}" 
									 alt="Logo {{ $store['store_name'] }}" 
									 class="w-full h-full object-cover">
							</div>
						@endif
						<h1 class="text-3xl font-bold mb-2">Repetir Pedido</h1>
						<p class="text-lg opacity-90">{{ $store['store_name'] }}</p>
					</div>
				</div>
			</div>
		@endif

		<div class="p-6">
		<!-- Card com informações do pedido -->
		<div class="bg-white rounded-2xl shadow-lg border border-gray-100 mb-6 overflow-hidden">
			<div class="bg-gradient-to-r from-purple-500 to-pink-500 text-white p-6">
				<div class="flex justify-between items-start">
					<div>
						<h2 class="text-xl font-semibold mb-2">Pedido #{{ $order['id'] }}</h2>
						<p class="text-purple-100">{{ date('d/m/Y \à\s H:i', strtotime($order['created_at'])) }}</p>
					</div>
					<div class="text-right">
						<div class="text-2xl font-bold">R$ {{ number_format($order['total_amount'], 2, ',', '.') }}</div>
						<span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-white bg-opacity-20 text-white mt-2">
							{{ ucfirst($order['status']) }}
						</span>
					</div>
				</div>
			</div>

			<div class="p-6">
				<h3 class="text-lg font-semibold text-gray-800 mb-4">Itens do Pedido</h3>
				<div class="space-y-4">
					@foreach($order['items'] as $item)
						<div class="flex items-start space-x-4 p-4 bg-gray-50 rounded-xl">
							<div class="flex-shrink-0">
								<div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
									<span class="text-purple-600 font-bold">{{ $item['quantity'] }}x</span>
								</div>
							</div>
							<div class="flex-1">
								<h4 class="font-semibold text-gray-800">{{ $item['product_name'] }}</h4>
								@if(!empty($item['size']))
									<p class="text-sm text-gray-600">Tamanho: {{ $item['size'] }}</p>
								@endif
								@if(!empty($item['notes']))
									<p class="text-sm text-gray-600">Observações: {{ $item['notes'] }}</p>
								@endif
								@if(!empty($item['ingredients']))
									<div class="mt-2">
										<p class="text-sm text-gray-600 font-medium">Ingredientes:</p>
										<div class="flex flex-wrap gap-1 mt-1">
											@foreach($item['ingredients'] as $ingredient)
												<span class="inline-flex items-center px-2 py-1 rounded-lg bg-purple-100 text-purple-700 text-xs font-medium">
													{{ $ingredient['ingredient_name'] }} ({{ $ingredient['quantity'] }})
												</span>
											@endforeach
										</div>
									</div>
								@endif
							</div>
							<div class="text-right">
							<div class="font-semibold text-gray-800">R$ {{ number_format($item['quantity'] * $item['unit_price'], 2, ',', '.') }}</div>
								<div class="text-sm text-gray-500">R$ {{ number_format($item['unit_price'], 2, ',', '.') }} cada</div>
							</div>
						</div>
					@endforeach
				</div>
			</div>
		</div>

		<!-- Informações do cliente -->
		<div class="bg-white rounded-2xl shadow-lg border border-gray-100 mb-6">
			<div class="p-6">
				<h3 class="text-lg font-semibold text-gray-800 mb-4">Informações de Entrega</h3>
				<div class="space-y-3">
					<div class="flex items-center">
						<div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
							<svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
							</svg>
						</div>
						<div>
							<p class="font-medium text-gray-800">{{ $order['customer_name'] }}</p>
							<p class="text-sm text-gray-600">{{ $order['customer_phone'] }}</p>
						</div>
					</div>
					@if(!empty($order['customer_address']) || !empty($order['delivery_address']))
						<div class="flex items-start">
							<div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
								<svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
								</svg>
							</div>
							<div>
								<p class="font-medium text-gray-800">Endereço de Entrega</p>
								<p class="text-sm text-gray-600">{{ $order['delivery_address'] ?? $order['customer_address'] ?? 'Não informado' }}</p>
							</div>
						</div>
					@endif
				</div>
			</div>
		</div>

		<!-- Ações -->
		<div class="bg-white rounded-2xl shadow-lg border border-gray-100">
			<div class="p-6">
				<div class="text-center mb-6">
					<div class="text-4xl mb-4">🛒</div>
					<h3 class="text-xl font-semibold text-gray-800 mb-2">Deseja repetir este pedido?</h3>
					<p class="text-gray-600">Os itens serão adicionados ao seu carrinho exatamente como no pedido original.</p>
				</div>
				
				<div class="flex space-x-4">
					<a href="/order/{{ $store_slug }}" 
						class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 text-center py-4 px-6 rounded-xl font-semibold transition-colors duration-200">
						Voltar aos Pedidos
					</a>
					<form method="POST" action="/querodenovo/{{ $store_slug }}/{{ $order['id'] }}/confirmar" class="flex-1">
						<button type="submit" 
							class="w-full bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600 text-white text-center py-4 px-6 rounded-xl font-semibold transition-all duration-200 transform hover:scale-[1.02]">
							Sim, Repetir Pedido
						</button>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection

@section('scripts')
<script>
// Adicionar animações suaves nos elementos
document.addEventListener('DOMContentLoaded', function() {
	// Fade in nos cards
	const cards = document.querySelectorAll('.bg-white');
	cards.forEach((card, index) => {
		card.style.opacity = '0';
		card.style.transform = 'translateY(20px)';
		setTimeout(() => {
			card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
			card.style.opacity = '1';
			card.style.transform = 'translateY(0)';
		}, index * 100);
	});
});
</script>
@endsection