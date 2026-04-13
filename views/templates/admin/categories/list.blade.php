@extends('layouts.admin')

@section('title', 'Categorias')

@section('topbar-actions')
<a href="/admin/categories/new"
   class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-xl text-sm font-semibold transition flex items-center gap-2 shadow-sm">
    <i class="fas fa-plus text-xs"></i>
    <span class="hidden sm:inline">Nova Categoria</span>
</a>
@endsection

@section('content')

@if (empty($categories))
<div class="text-center py-20">
    <i class="fas fa-folder-open text-slate-300 text-6xl mb-4 block"></i>
    <h3 class="text-lg font-semibold text-slate-800 mb-2">Nenhuma categoria encontrada</h3>
    <p class="text-slate-500 text-sm mb-6">Organize seu cardápio criando categorias.</p>
    <a href="/admin/categories/new" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-xl font-semibold text-sm transition">
        <i class="fas fa-plus mr-2"></i>Criar Categoria
    </a>
</div>
@else

<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
    <ul class="divide-y divide-slate-50">
        @foreach ($categories as $category)
        <li class="p-5 hover:bg-slate-50 transition">
            <div class="flex items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <!-- Image -->
                    <div class="flex-shrink-0 w-16 h-16">
                        @if ($category['image_url'] ?? $category['image'] ?? null)
                            <img class="w-16 h-16 rounded-xl object-cover"
                                 src="{{ $category['image_url'] ?? $category['image'] }}"
                                 alt="{{ $category['name'] }}">
                        @else
                            <div class="w-16 h-16 rounded-xl bg-slate-100 flex items-center justify-center">
                                <i class="fas fa-image text-slate-300 text-xl"></i>
                            </div>
                        @endif
                    </div>
                    <!-- Info -->
                    <div>
                        <div class="flex items-center gap-2 flex-wrap mb-0.5">
                            <h3 class="text-sm font-semibold text-slate-800">{{ $category['name'] }}</h3>
                            @if (!($category['is_active'] ?? 1))
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">Inativa</span>
                            @endif
                            @if ($category['has_customization'] ?? 0)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                                    <i class="fas fa-plus-circle mr-1"></i>Personalização
                                </span>
                            @endif
                        </div>
                        @if ($category['description'] ?? null)
                            <p class="text-xs text-slate-400 truncate max-w-sm">{{ $category['description'] }}</p>
                        @endif
                        <p class="text-xs text-slate-400 mt-1">Ordem: {{ $category['sort_order'] ?? 0 }}</p>
                    </div>
                </div>
                <!-- Actions -->
                <div class="flex items-center gap-2 flex-shrink-0">
                    <a href="/admin/categories/{{ $category['id'] }}/edit"
                       class="text-indigo-600 hover:text-indigo-800 p-2 rounded-lg hover:bg-indigo-50 transition" title="Editar">
                        <i class="fas fa-edit"></i>
                    </a>
                    <button onclick="confirmDelete({{ $category['id'] }}, '{{ addslashes($category['name']) }}')"
                            class="text-red-500 hover:text-red-700 p-2 rounded-lg hover:bg-red-50 transition" title="Excluir">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </li>
        @endforeach
    </ul>
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
            Excluir a categoria "<span id="catName" class="font-semibold text-slate-700"></span>"? Esta ação não pode ser desfeita.
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
    document.getElementById('catName').textContent = name;
    document.getElementById('deleteForm').action = '/admin/categories/' + id + '/delete';
    document.getElementById('deleteModal').classList.remove('hidden');
}
function closeModal() {
    document.getElementById('deleteModal').classList.add('hidden');
}
document.getElementById('deleteModal')?.addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});
</script>
@endsection
