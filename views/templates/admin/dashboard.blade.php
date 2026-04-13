@extends('layouts.admin')

@section('title', 'Dashboard')

@section('head')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
@endsection

@section('topbar-actions')
<div class="hidden sm:flex items-center gap-2 text-xs text-slate-500">
    <span class="w-2 h-2 rounded-full bg-emerald-400 inline-block" style="animation:pulse 2s infinite;"></span>
    Kanban ao vivo
</div>
<a href="/admin/pedidos/novo"
   class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-xl text-sm font-semibold transition flex items-center gap-2 shadow-sm">
    <i class="fas fa-plus text-xs"></i>
    <span class="hidden sm:inline">Novo Pedido</span>
</a>
@endsection

@section('content')
<style>
    /* ── STAT CARDS ── */
    .stat-card {
        background:#fff; border-radius:16px; padding:20px 22px;
        box-shadow:0 1px 4px rgba(0,0,0,.06);
        display:flex; align-items:center; gap:16px;
        transition:transform .2s,box-shadow .2s;
    }
    .stat-card:hover { transform:translateY(-2px); box-shadow:0 8px 25px rgba(0,0,0,.08); }
    .stat-icon { width:52px;height:52px;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:22px;flex-shrink:0; }

    /* ── TABS ── */
    .tab-btn {
        padding:9px 18px; border-radius:10px; font-weight:600; font-size:13px;
        color:#64748b; cursor:pointer; border:none; background:transparent;
        display:flex; align-items:center; gap:6px; transition:all .18s;
    }
    .tab-btn.active,.tab-btn:hover { background:#fff; color:#6366f1; box-shadow:0 2px 8px rgba(0,0,0,.07); }

    /* ── KANBAN ── */
    .kanban-col { background:#fff; border-radius:16px; box-shadow:0 1px 4px rgba(0,0,0,.06); display:flex; flex-direction:column; }
    .kanban-col-hdr { padding:12px 14px 10px; border-radius:16px 16px 0 0; display:flex; align-items:center; justify-content:space-between; }
    .kanban-zone { padding:10px; flex:1; min-height:380px; display:flex; flex-direction:column; gap:8px; overflow-y:auto; max-height:60vh; }
    .kanban-zone::-webkit-scrollbar { width:4px; }
    .kanban-zone::-webkit-scrollbar-track { background:transparent; }
    .kanban-zone::-webkit-scrollbar-thumb { background:#cbd5e1; border-radius:4px; }

    .k-card {
        background:#fff; border:1.5px solid #e2e8f0; border-radius:12px;
        padding:11px 12px; cursor:move;
        transition:transform .15s,box-shadow .15s,border-color .15s;
        position:relative; overflow:hidden;
    }
    .k-card:hover { transform:translateY(-1px); box-shadow:0 6px 20px rgba(0,0,0,.1); border-color:#c7d2fe; }
    .k-card .sbar { position:absolute; left:0;top:0;bottom:0;width:3px; }
    .ghost-card { opacity:.35; background:#e0e7ff !important; border:2px dashed #6366f1 !important; }

    /* Order rows */
    .order-row { display:flex;align-items:center;gap:12px;padding:11px 0;border-bottom:1px solid #f1f5f9; }
    .order-row:last-child { border:none; }

    @media (max-width:900px) {
        .kanban-grid { overflow-x:auto; padding-bottom:12px; }
        .kanban-cols { min-width:860px; }
    }
</style>

<!-- ── STATS ── -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="stat-card">
        <div class="stat-icon" style="background:linear-gradient(135deg,#6366f1,#8b5cf6)">
            <i class="fas fa-box-open text-white text-xl"></i>
        </div>
        <div>
            <div class="text-2xl font-bold text-slate-800">{{ $totalProducts }}</div>
            <div class="text-xs text-slate-500 mt-0.5 font-medium">Produtos</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:linear-gradient(135deg,#10b981,#059669)">
            <i class="fas fa-tags text-white text-xl"></i>
        </div>
        <div>
            <div class="text-2xl font-bold text-slate-800">{{ $totalCategories }}</div>
            <div class="text-xs text-slate-500 mt-0.5 font-medium">Categorias</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:linear-gradient(135deg,#f59e0b,#d97706)">
            <i class="fas fa-leaf text-white text-xl"></i>
        </div>
        <div>
            <div class="text-2xl font-bold text-slate-800">{{ $totalIngredients }}</div>
            <div class="text-xs text-slate-500 mt-0.5 font-medium">Ingredientes</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:linear-gradient(135deg,#ef4444,#dc2626)">
            <i class="fas fa-receipt text-white text-xl"></i>
        </div>
        <div>
            <div class="text-2xl font-bold text-slate-800">{{ $totalOrders }}</div>
            <div class="text-xs text-slate-500 mt-0.5 font-medium">Pedidos</div>
        </div>
    </div>
</div>

<!-- ── TABS ── -->
<div class="bg-slate-200/60 rounded-2xl p-1.5 inline-flex gap-1 mb-5">
    <button onclick="switchTab('kanban')" class="tab-btn active" id="tab-kanban">
        <i class="fas fa-th-large"></i> Kanban
    </button>
    <button onclick="switchTab('recent')" class="tab-btn" id="tab-recent">
        <i class="fas fa-list"></i> Recentes
    </button>
</div>

<!-- ══════════ KANBAN ══════════ -->
<div id="view-kanban">

    <!-- Filters -->
    <div class="flex flex-wrap items-center gap-3 mb-4">
        <button onclick="kFilter('all')" class="kfbtn active text-xs font-semibold px-4 py-2 rounded-xl border border-slate-200 bg-white shadow-sm text-indigo-600" data-f="all">
            <i class="fas fa-th mr-1"></i> Todos
        </button>
        <button onclick="kFilter('today')" class="kfbtn text-xs font-semibold px-4 py-2 rounded-xl border border-slate-200 bg-white text-slate-600 hover:text-indigo-600 transition" data-f="today">
            <i class="fas fa-calendar-day mr-1"></i> Hoje
        </button>
        <button onclick="kFilter('high')" class="kfbtn text-xs font-semibold px-4 py-2 rounded-xl border border-slate-200 bg-white text-slate-600 hover:text-indigo-600 transition" data-f="high">
            <i class="fas fa-star mr-1"></i> Alto valor
        </button>
        <div class="relative flex-1 min-w-[160px] max-w-xs">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
            <input id="k-search" type="text" placeholder="Buscar cliente..."
                   class="w-full pl-8 pr-3 py-2 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 shadow-sm">
        </div>
        <button onclick="loadKanban()" class="text-xs font-semibold px-4 py-2 rounded-xl bg-white border border-slate-200 text-slate-600 hover:text-indigo-600 transition shadow-sm">
            <i class="fas fa-sync-alt mr-1"></i> Atualizar
        </button>
    </div>

    <!-- Board -->
    <div class="kanban-grid">
        <div class="kanban-cols grid grid-cols-5 gap-3">

            <div class="kanban-col">
                <div class="kanban-col-hdr" style="background:linear-gradient(135deg,#fef3c7,#fde68a)">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-hourglass-start text-amber-500 text-sm"></i>
                        <span class="text-sm font-bold text-amber-900">Pendente</span>
                    </div>
                    <span class="bg-amber-200 text-amber-800 text-xs font-bold px-2 py-0.5 rounded-full pending-count">0</span>
                </div>
                <div class="kanban-zone" data-status="pending"></div>
            </div>

            <div class="kanban-col">
                <div class="kanban-col-hdr" style="background:linear-gradient(135deg,#ede9fe,#c4b5fd)">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-check-circle text-violet-500 text-sm"></i>
                        <span class="text-sm font-bold text-violet-900">Confirmado</span>
                    </div>
                    <span class="bg-violet-200 text-violet-800 text-xs font-bold px-2 py-0.5 rounded-full confirmed-count">0</span>
                </div>
                <div class="kanban-zone" data-status="confirmed"></div>
            </div>

            <div class="kanban-col">
                <div class="kanban-col-hdr" style="background:linear-gradient(135deg,#dbeafe,#93c5fd)">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-fire text-blue-500 text-sm"></i>
                        <span class="text-sm font-bold text-blue-900">Preparando</span>
                    </div>
                    <span class="bg-blue-200 text-blue-800 text-xs font-bold px-2 py-0.5 rounded-full preparing-count">0</span>
                </div>
                <div class="kanban-zone" data-status="preparing"></div>
            </div>

            <div class="kanban-col">
                <div class="kanban-col-hdr" style="background:linear-gradient(135deg,#dcfce7,#86efac)">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-check-double text-emerald-500 text-sm"></i>
                        <span class="text-sm font-bold text-emerald-900">Pronto</span>
                    </div>
                    <span class="bg-emerald-200 text-emerald-800 text-xs font-bold px-2 py-0.5 rounded-full ready-count">0</span>
                </div>
                <div class="kanban-zone" data-status="ready"></div>
            </div>

            <div class="kanban-col">
                <div class="kanban-col-hdr" style="background:linear-gradient(135deg,#f1f5f9,#e2e8f0)">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-box text-slate-500 text-sm"></i>
                        <span class="text-sm font-bold text-slate-700">Entregue</span>
                    </div>
                    <span class="bg-slate-200 text-slate-700 text-xs font-bold px-2 py-0.5 rounded-full delivered-count">0</span>
                </div>
                <div class="kanban-zone" data-status="delivered"></div>
            </div>

        </div>
    </div>
</div>

<!-- ══════════ RECENTES ══════════ -->
<div id="view-recent" class="hidden">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-slate-800 flex items-center gap-2">
                <i class="fas fa-receipt text-indigo-500"></i> Últimos Pedidos
            </h3>
            <a href="/admin/pedidos" class="text-sm text-indigo-600 hover:text-indigo-700 font-medium flex items-center gap-1">
                Ver todos <i class="fas fa-arrow-right text-xs"></i>
            </a>
        </div>
        <div class="divide-y divide-slate-50 px-6">
            @if (empty($recentOrders))
                <div class="py-12 text-center">
                    <div class="text-5xl mb-3">📋</div>
                    <p class="text-slate-500 text-sm">Nenhum pedido recente.</p>
                </div>
            @else
                @foreach ($recentOrders as $order)
                @php
                    $sc = [
                        'pending'   => ['bg-amber-100','text-amber-700','fas fa-hourglass-start'],
                        'confirmed' => ['bg-violet-100','text-violet-700','fas fa-check-circle'],
                        'preparing' => ['bg-blue-100','text-blue-700','fas fa-fire'],
                        'ready'     => ['bg-emerald-100','text-emerald-700','fas fa-check-double'],
                        'delivered' => ['bg-slate-100','text-slate-600','fas fa-box'],
                        'cancelled' => ['bg-red-100','text-red-600','fas fa-times-circle'],
                    ][$order['status']] ?? ['bg-gray-100','text-gray-600','fas fa-circle'];
                @endphp
                <div class="order-row">
                    <div class="w-8 h-8 {{ $sc[0] }} {{ $sc[1] }} rounded-lg flex items-center justify-center flex-shrink-0 text-xs">
                        <i class="{{ $sc[2] }}"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="font-semibold text-slate-800 text-sm">#{{ $order['id'] }}</span>
                            <span class="font-medium text-slate-700 text-sm truncate">{{ $order['customer_name'] }}</span>
                            <span class="text-xs text-slate-400">({{ $order['customer_phone'] }})</span>
                        </div>
                        <div class="text-xs text-slate-400 mt-0.5">{{ date('d/m/Y H:i', strtotime($order['created_at'])) }}</div>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <div class="font-bold text-slate-800 text-sm">R$ {{ number_format($order['total_amount'], 2, ',', '.') }}</div>
                    </div>
                    <div class="flex items-center gap-1.5 flex-shrink-0">
                        <button class="bg-emerald-50 hover:bg-emerald-100 text-emerald-700 px-2.5 py-1.5 rounded-lg text-xs font-medium transition print-pdf-btn" data-order-id="{{ $order['id'] }}">
                            <i class="fas fa-print"></i>
                        </button>
                        <a href="/admin/pedidos/{{ $order['id'] }}" target="_blank" class="bg-slate-100 hover:bg-slate-200 text-slate-600 px-2.5 py-1.5 rounded-lg text-xs font-medium transition">
                            <i class="fas fa-eye"></i>
                        </a>
                    </div>
                </div>
                @endforeach
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// ── TABS ──
function switchTab(tab) {
    ['kanban','recent'].forEach(t => {
        document.getElementById('view-' + t).classList.toggle('hidden', t !== tab);
        document.getElementById('tab-' + t).classList.toggle('active', t === tab);
    });
}

// ── KANBAN ──
const sbars = { pending:'#f59e0b', confirmed:'#8b5cf6', preparing:'#3b82f6', ready:'#10b981', delivered:'#94a3b8' };
let allOrders = [], curFilter = 'all', kSearch = '';

async function loadKanban() {
    try {
        const r = await fetch('/admin/api/orders');
        const d = await r.json();
        allOrders = d.orders || [];
        doRender(allOrders);
    } catch(e) { console.error(e); }
}

function doRender(orders) {
    const groups = { pending:[], confirmed:[], preparing:[], ready:[], delivered:[] };
    orders.forEach(o => { if (groups[o.status]) groups[o.status].push(o); });
    Object.entries(groups).forEach(([s, list]) => {
        const zone = document.querySelector(`[data-status="${s}"]`);
        if (!zone) return;
        zone.innerHTML = '';
        if (!list.length) {
            zone.innerHTML = '<div style="text-align:center;color:#94a3b8;font-size:11px;padding:24px 0;font-style:italic;">Vazio</div>';
        } else {
            list.forEach(o => zone.appendChild(mkCard(o)));
        }
        const badge = document.querySelector(`.${s}-count`);
        if (badge) badge.textContent = list.length;
    });
}

function mkCard(o) {
    const el = document.createElement('div');
    el.className = 'k-card';
    el.dataset.orderId = o.id;
    const time = new Date(o.created_at).toLocaleTimeString('pt-BR',{hour:'2-digit',minute:'2-digit'});
    const date = new Date(o.created_at).toLocaleDateString('pt-BR',{day:'2-digit',month:'2-digit'});
    const total = parseFloat(o.total_amount).toFixed(2).replace('.',',');
    const bar = sbars[o.status] || '#94a3b8';
    el.innerHTML = `
        <div class="sbar" style="background:${bar}"></div>
        <div style="padding-left:8px;">
            <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:4px;">
                <span style="font-size:11px;font-weight:700;color:#64748b;">#${o.id}</span>
                <span style="font-size:11px;color:#94a3b8;">${date} ${time}</span>
            </div>
            <div style="font-weight:600;color:#1e293b;font-size:13px;margin-bottom:2px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">${esc(o.customer_name)}</div>
            <div style="font-size:11px;color:#64748b;margin-bottom:6px;">${esc(o.customer_phone||'')}</div>
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px;">
                <span style="font-size:11px;color:#94a3b8;">${o.items_count||0} item(s)</span>
                <span style="font-size:13px;font-weight:700;color:#1e293b;">R$ ${total}</span>
            </div>
            ${o.notes ? `<div style="font-size:11px;background:#fffbeb;color:#92400e;border:1px solid #fde68a;border-radius:6px;padding:4px 8px;margin-bottom:6px;">${esc(o.notes)}</div>` : ''}
            <a href="/admin/pedidos/${o.id}" target="_blank" style="display:block;text-align:center;font-size:11px;font-weight:600;background:#f1f5f9;color:#475569;border-radius:7px;padding:5px;text-decoration:none;">
                <i class="fas fa-eye" style="margin-right:4px;"></i>Ver pedido
            </a>
        </div>`;
    return el;
}

function esc(s) {
    return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

function setupDnD() {
    document.querySelectorAll('.kanban-zone').forEach(zone => {
        Sortable.create(zone, {
            group: 'kb',
            animation: 200,
            ghostClass: 'ghost-card',
            onEnd: async evt => {
                const id = evt.item.dataset.orderId;
                const newStatus = evt.to.dataset.status;
                await fetch(`/admin/pedidos/${id}/status`, {
                    method:'POST',
                    headers:{'Content-Type':'application/json'},
                    body: JSON.stringify({status: newStatus})
                });
                const o = allOrders.find(x => x.id == id);
                if (o) o.status = newStatus;
                // update counts only
                const counts = {pending:0,confirmed:0,preparing:0,ready:0,delivered:0};
                allOrders.forEach(x => { if(counts[x.status]!==undefined) counts[x.status]++; });
                Object.entries(counts).forEach(([s,c]) => {
                    const b = document.querySelector(`.${s}-count`);
                    if(b) b.textContent = c;
                });
            }
        });
    });
}

function kFilter(f) {
    curFilter = f;
    document.querySelectorAll('.kfbtn').forEach(b => b.classList.remove('active','text-indigo-600'));
    document.querySelector(`[data-f="${f}"]`)?.classList.add('active','text-indigo-600');
    applyFilters();
}

function applyFilters() {
    let list = [...allOrders];
    if (curFilter === 'today') {
        const t = new Date(); t.setHours(0,0,0,0);
        list = list.filter(o => { const d = new Date(o.created_at); d.setHours(0,0,0,0); return d.getTime()===t.getTime(); });
    } else if (curFilter === 'high') {
        list = list.filter(o => parseFloat(o.total_amount) >= 100);
    }
    const q = kSearch.trim().toLowerCase();
    if (q) list = list.filter(o => (o.customer_name||'').toLowerCase().includes(q) || (o.customer_phone||'').includes(q));
    doRender(list);
}

document.getElementById('k-search')?.addEventListener('input', e => { kSearch = e.target.value; applyFilters(); });

// PDF print buttons (manual)
document.querySelectorAll('.print-pdf-btn').forEach(btn => {
    btn.addEventListener('click', () => window.open('/admin/print-order-pdf/' + btn.dataset.orderId, '_blank'));
});

document.addEventListener('DOMContentLoaded', () => {
    loadKanban();
    setupDnD();
    setInterval(() => { if (!document.getElementById('view-kanban').classList.contains('hidden')) loadKanban(); }, 30000);
});
</script>
@endsection
