@extends('layouts.admin')

@section('title', $pageTitle)

@section('topbar-actions')
<a href="/admin/ingredients/new"
   class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-xl text-sm font-semibold transition flex items-center gap-2 shadow-sm">
    <i class="fas fa-plus text-xs"></i>
    <span class="hidden sm:inline">Novo Ingrediente</span>
</a>
@endsection

@section('content')

@if (empty($ingredients))
<div class="text-center py-20">
    <i class="fas fa-apple-alt text-slate-300 text-6xl mb-4 block"></i>
    <h3 class="text-lg font-semibold text-slate-800 mb-2">Nenhum ingrediente encontrado</h3>
    <p class="text-slate-500 text-sm mb-6">Crie ingredientes para personalização dos produtos.</p>
    <a href="/admin/ingredients/new" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-xl font-semibold text-sm transition">
        <i class="fas fa-plus mr-2"></i>Criar Ingrediente
    </a>
</div>
@else

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
    @foreach ($ingredients as $ingredient)
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4 hover:shadow-md transition">
        <div class="flex items-start gap-3">
            @if ($ingredient['image_url'] ?? null)
                <img class="w-14 h-14 rounded-xl object-cover flex-shrink-0"
                     src="{{ $ingredient['image_url'] }}" alt="{{ $ingredient['name'] }}">
            @else
                <div class="w-14 h-14 rounded-xl bg-slate-100 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-leaf text-slate-300 text-xl"></i>
                </div>
            @endif

            <div class="flex-1 min-w-0">
                <div class="flex items-start justify-between gap-2">
                    <h3 class="text-sm font-semibold text-slate-800 truncate">{{ $ingredient['name'] }}</h3>
                    <div class="flex items-center gap-1 flex-shrink-0">
                        <a href="/admin/ingredients/{{ $ingredient['id'] }}/edit"
                           class="text-indigo-500 hover:text-indigo-700 p-1.5 rounded-lg hover:bg-indigo-50 transition">
                            <i class="fas fa-edit text-sm"></i>
                        </a>
                        <button onclick="confirmDelete({{ $ingredient['id'] }}, '{{ addslashes($ingredient['name']) }}')"
                                class="text-red-400 hover:text-red-600 p-1.5 rounded-lg hover:bg-red-50 transition">
                            <i class="fas fa-trash text-sm"></i>
                        </button>
                    </div>
                </div>

                <!-- Price -->
                <div class="mt-1">
                    @if ($ingredient['is_free'] ?? false || floatval($ingredient['additional_price'] ?? 0) == 0)
                        <span class="text-xs font-semibold text-emerald-600"><i class="fas fa-gift mr-1"></i>Grátis</span>
                    @else
                        <span class="text-xs font-semibold text-slate-700">
                            + {{ \App\Helpers\PriceHelper::formatPrice($ingredient['additional_price']) }}
                        </span>
                    @endif
                </div>

                <!-- Badges -->
                <div class="flex flex-wrap gap-1.5 mt-2">
                    @if (!($ingredient['is_active'] ?? 1))
                        <span class="text-xs px-2 py-0.5 rounded-full bg-red-100 text-red-700 font-medium">Inativo</span>
                    @endif
                    <span class="text-xs px-2 py-0.5 rounded-full bg-blue-100 text-blue-700 font-medium">
                        Max: {{ $ingredient['max_quantity'] }}
                    </span>
                    <span class="text-xs px-2 py-0.5 rounded-full bg-amber-100 text-amber-700 font-medium capitalize">
                        {{ $ingredient['type'] }}
                    </span>
                </div>

                @if ($ingredient['description'] ?? null)
                    <p class="text-xs text-slate-400 mt-1.5 truncate">{{ $ingredient['description'] }}</p>
                @endif
            </div>
        </div>
    </div>
    @endforeach
</div>

@endif

<!-- Delete Modal -->
<div id="deleteModal" class="fixed inset-0 bg-black/40 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-sm w-full p-6 shadow-2xl">
        <div class="w-12 h-12 mx-auto bg-red-100 rounded-full flex items-center justify-center mb-4">
            <i class="fas fa-exclamation-triangle text-red-600"></i>
        </div>
        <h3 class="text-base font-bold text-slate-800 text-center mb-2">Confirmar Exclusão</h3>
        <p class="text-sm text-slate-500 text-center mb-6">
            Excluir o ingrediente "<span id="ingredientName" class="font-semibold text-slate-700"></span>"?
        </p>
        <div class="flex gap-3">
            <button onclick="closeModal()" class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-700 py-2.5 px-4 rounded-xl text-sm font-semibold transition">
                Cancelar
            </button>
            <form id="deleteForm" method="POST" class="flex-1">
                @csrf
                <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white py-2.5 px-4 rounded-xl text-sm font-semibold transition">
                    Excluir
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function confirmDelete(id, name) {
    document.getElementById('ingredientName').textContent = name;
    document.getElementById('deleteForm').action = '/admin/ingredients/' + id + '/delete';
    document.getElementById('deleteModal').classList.remove('hidden');
}
function closeModal() { document.getElementById('deleteModal').classList.add('hidden'); }
document.getElementById('deleteModal')?.addEventListener('click', function(e) { if (e.target===this) closeModal(); });
</script>
@endsection