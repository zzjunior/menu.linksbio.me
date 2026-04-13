<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin') — {{ $_SESSION['store_name'] ?? 'Painel' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @yield('head')
    <style>
        /* Box-sizing globally, but scoped font to body (not * — that breaks icon fonts) */
        *, *::before, *::after { box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; }

        /* Preserve Font Awesome's own font stack */
        .fa, .fas, .far, .fab, .fal, .fad, .fa-solid, .fa-regular, .fa-brands,
        [class^="fa-"], [class*=" fa-"] { font-family: inherit !important; }

        /* Re-declare icon font-family so FA still works */
        .fa, .fas, .fa-solid {
            font-family: "Font Awesome 6 Free" !important;
            font-weight: 900;
        }
        .far, .fa-regular {
            font-family: "Font Awesome 6 Free" !important;
            font-weight: 400;
        }
        .fab, .fa-brands {
            font-family: "Font Awesome 6 Brands" !important;
            font-weight: 400;
        }

        :root {
            --sb-w: 256px;
            --sb-col: 68px;
            --sb-bg: #0f172a;
            --sb-hover: #1e293b;
            --accent: #6366f1;
        }

        body {
            background: #f1f5f9;
            margin: 0;
            min-height: 100vh;
            /* Sidebar + content side by side, each filling full height */
            display: flex;
        }

        /* ── SIDEBAR ── */
        #sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;          /* always full viewport height */
            width: var(--sb-w);
            background: var(--sb-bg);
            display: flex;
            flex-direction: column;
            z-index: 40;
            overflow: hidden;       /* outer clips; inner nav scrolls */
            transition: width .25s cubic-bezier(.4,0,.2,1), transform .25s cubic-bezier(.4,0,.2,1);
        }
        #sidebar.collapsed { width: var(--sb-col); }

        /* Scrollable nav area — fills remaining space between header and footer */
        #sidebar-nav {
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
            padding: 8px 10px 12px;
            scrollbar-width: thin;
            scrollbar-color: #334155 transparent;
            /* Ensure it doesn't exceed the sidebar height */
            min-height: 0;
        }
        #sidebar-nav::-webkit-scrollbar { width: 4px; }
        #sidebar-nav::-webkit-scrollbar-track { background: transparent; }
        #sidebar-nav::-webkit-scrollbar-thumb { background: #334155; border-radius: 4px; }

        /* Pinned bottom area inside sidebar */
        #sidebar-footer {
            flex-shrink: 0;
            padding: 10px;
            border-top: 1px solid #1e293b;
        }

        /* ── NAV ITEMS ── */
        .nav-item {
            display: flex;
            align-items: center;
            gap: 11px;
            padding: 9px 10px;
            border-radius: 10px;
            color: #94a3b8;
            text-decoration: none;
            transition: all .18s;
            white-space: nowrap;
            overflow: hidden;
            margin-bottom: 2px;
        }
        .nav-item:hover { background: var(--sb-hover); color: #f1f5f9; }
        .nav-item.active {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: #fff;
            box-shadow: 0 4px 14px rgba(99,102,241,.35);
        }
        .nav-icon {
            width: 36px; height: 36px;
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-size: 14px;
            flex-shrink: 0;
            background: rgba(255,255,255,.06);
            transition: background .18s;
        }
        .nav-item:hover .nav-icon { background: rgba(255,255,255,.1); }
        .nav-item.active .nav-icon { background: rgba(255,255,255,.18); }

        .nav-label {
            font-size: 13px;
            font-weight: 500;
            transition: opacity .2s, width .2s;
            overflow: hidden;
        }

        /* Section titles */
        .sb-section {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: .1em;
            text-transform: uppercase;
            color: #475569;
            padding: 16px 12px 6px;
            transition: opacity .2s;
            white-space: nowrap;
            overflow: hidden;
        }

        /* Collapsed state — hide text */
        #sidebar.collapsed .nav-label,
        #sidebar.collapsed .sb-section,
        #sidebar.collapsed .sb-store-name,
        #sidebar.collapsed .sb-store-sub {
            opacity: 0;
            width: 0;
            pointer-events: none;
        }
        #sidebar.collapsed .nav-item { gap: 0; justify-content: center; padding: 9px 0; }
        #sidebar.collapsed .nav-icon { width: 40px; height: 40px; }

        /* ── MAIN ── */
        #main-wrap {
            margin-left: var(--sb-w);
            flex: 1;                /* grows to fill remaining width */
            min-height: 100vh;      /* always full page height */
            display: flex;
            flex-direction: column;
            transition: margin-left .25s cubic-bezier(.4,0,.2,1);
        }
        #main-wrap.collapsed { margin-left: var(--sb-col); }

        /* ── TOPBAR ── */
        #topbar {
            position: sticky; top: 0; z-index: 30;
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0 24px;
            height: 60px;
            flex-shrink: 0;
        }

        /* Overlay mobile */
        #sb-overlay {
            display: none;
            position: fixed; inset: 0;
            background: rgba(0,0,0,.45);
            z-index: 35;
        }
        #sb-overlay.active { display: block; }

        /* ── RESPONSIVE ── */
        @media (max-width: 1023px) {
            #sidebar {
                transform: translateX(-100%);
                width: var(--sb-w) !important;
            }
            #sidebar.mobile-open { transform: translateX(0); }
            #main-wrap, #main-wrap.collapsed { margin-left: 0 !important; }
            #desktop-collapse-btn { display: none !important; }
        }

        /* ── PRINT STATUS TOAST ── */
        #print-toast {
            position: fixed; bottom: 24px; right: 24px; z-index: 9999;
            background: #0f172a; color: #fff;
            border-radius: 14px;
            padding: 14px 18px;
            display: flex; align-items: center; gap: 12px;
            box-shadow: 0 8px 30px rgba(0,0,0,.25);
            font-size: 13px; font-weight: 500;
            transform: translateY(80px); opacity: 0;
            transition: all .35s cubic-bezier(.4,0,.2,1);
            min-width: 260px;
        }
        #print-toast.show { transform: translateY(0); opacity: 1; }
        #print-toast .pt-icon { font-size: 20px; flex-shrink: 0; }

        /* Pulse dot */
        .pulse-dot {
            width: 8px; height: 8px; border-radius: 50%;
            background: #10b981;
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%,100% { opacity: 1; transform: scale(1); }
            50% { opacity: .5; transform: scale(.8); }
        }
    </style>
</head>
<body>

<!-- Mobile overlay -->
<div id="sb-overlay" onclick="sbClose()"></div>

{{-- ════════════════ SIDEBAR ════════════════ --}}
<aside id="sidebar">

    {{-- Store logo + name --}}
    @php
        /* Resolve logo: prefer settings passed by controller, then session, then nothing */
        $sidebarLogo = $storeSettings['store_logo'] ?? $storeSettings['logo'] ?? $_SESSION['store_logo'] ?? null;
        $sidebarName = $storeSettings['store_name'] ?? $_SESSION['store_name'] ?? 'Minha Loja';
    @endphp
    <div style="padding:16px 14px 12px; border-bottom:1px solid #1e293b; display:flex; align-items:center; gap:10px; flex-shrink:0;">
        @if (!empty($sidebarLogo))
            <img src="{{ $sidebarLogo }}" alt="Logo"
                 style="width:40px;height:40px;border-radius:10px;object-fit:cover;flex-shrink:0;box-shadow:0 0 0 2px #6366f1;">
        @else
            <div style="width:40px;height:40px;border-radius:10px;background:linear-gradient(135deg,#6366f1,#8b5cf6);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <i class="fas fa-store" style="color:#fff;font-size:15px;"></i>
            </div>
        @endif
        <div style="overflow:hidden;">
            <div class="sb-store-name" style="color:#fff;font-weight:700;font-size:13px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:170px;transition:opacity .2s,width .2s;">
                {{ $sidebarName }}
            </div>
            <div class="sb-store-sub" style="color:#64748b;font-size:11px;margin-top:1px;white-space:nowrap;transition:opacity .2s,width .2s;">
                Painel Admin
            </div>
        </div>
    </div>

    {{-- Scrollable nav --}}
    <div id="sidebar-nav">

        <div class="sb-section">Principal</div>
        <a href="/admin" class="nav-item" data-path="/admin" data-exact="1">
            <div class="nav-icon"><i class="fas fa-chart-pie"></i></div>
            <span class="nav-label">Dashboard</span>
        </a>

        <div class="sb-section">Pedidos</div>
        <a href="/admin/pedidos" class="nav-item" data-path="/admin/pedidos" data-exact="1">
            <div class="nav-icon"><i class="fas fa-receipt"></i></div>
            <span class="nav-label">Lista de Pedidos</span>
        </a>
        <a href="/admin/pedidos/novo" class="nav-item" data-path="/admin/pedidos/novo">
            <div class="nav-icon"><i class="fas fa-plus-circle"></i></div>
            <span class="nav-label">Criar Pedido</span>
        </a>

        <div class="sb-section">Cardápio</div>
        <a href="/admin/products" class="nav-item" data-path="/admin/products">
            <div class="nav-icon"><i class="fas fa-box-open"></i></div>
            <span class="nav-label">Produtos</span>
        </a>
        <a href="/admin/categories" class="nav-item" data-path="/admin/categories">
            <div class="nav-icon"><i class="fas fa-tags"></i></div>
            <span class="nav-label">Categorias</span>
        </a>
        <a href="/admin/ingredients" class="nav-item" data-path="/admin/ingredients">
            <div class="nav-icon"><i class="fas fa-leaf"></i></div>
            <span class="nav-label">Ingredientes</span>
        </a>

        <div class="sb-section">Gestão</div>
        <a href="/admin/clientes" class="nav-item" data-path="/admin/clientes">
            <div class="nav-icon"><i class="fas fa-users"></i></div>
            <span class="nav-label">Clientes</span>
        </a>
        <a href="/admin/relatorios" class="nav-item" data-path="/admin/relatorios">
            <div class="nav-icon"><i class="fas fa-chart-bar"></i></div>
            <span class="nav-label">Relatórios</span>
        </a>
        <a href="/admin/loja/configuracoes" class="nav-item" data-path="/admin/loja/configuracoes">
            <div class="nav-icon"><i class="fas fa-cog"></i></div>
            <span class="nav-label">Configurações</span>
        </a>

        @if (!empty($_SESSION['store_slug']))
        <div class="sb-section">Loja</div>
        <a href="/{{ $_SESSION['store_slug'] }}" target="_blank" class="nav-item">
            <div class="nav-icon"><i class="fas fa-external-link-alt" style="color:#34d399;"></i></div>
            <span class="nav-label" style="color:#34d399;">Ver Cardápio</span>
        </a>
        @endif

    </div>{{-- end scrollable nav --}}

    {{-- Footer pinned to bottom --}}
    <div id="sidebar-footer">
        <a href="/admin/logout" class="nav-item" style="margin-bottom:0;">
            <div class="nav-icon"><i class="fas fa-sign-out-alt" style="color:#f87171;"></i></div>
            <span class="nav-label" style="color:#f87171;">Sair</span>
        </a>
    </div>

</aside>
{{-- ════════════════ END SIDEBAR ════════════════ --}}

{{-- ════════════════ MAIN ════════════════ --}}
<div id="main-wrap">

    {{-- TOP BAR --}}
    <header id="topbar">
        {{-- Mobile hamburger --}}
        <button onclick="sbOpen()" class="lg:hidden text-slate-500 hover:text-slate-800 focus:outline-none w-9 h-9 flex items-center justify-center rounded-lg hover:bg-slate-100">
            <i class="fas fa-bars"></i>
        </button>
        {{-- Desktop collapse --}}
        <button id="desktop-collapse-btn" onclick="sbToggle()" class="hidden lg:flex text-slate-400 hover:text-slate-700 w-9 h-9 items-center justify-center rounded-lg hover:bg-slate-100 focus:outline-none transition">
            <i class="fas fa-bars text-sm"></i>
        </button>

        {{-- Page title --}}
        <div class="flex-1 min-w-0">
            <h1 class="text-base font-bold text-slate-800 truncate">@yield('title', 'Dashboard')</h1>
        </div>

        {{-- Right slot --}}
        <div class="flex items-center gap-3">
            {{-- Print status --}}
            <div id="print-status-indicator" class="hidden sm:flex items-center gap-2 text-xs text-slate-500 cursor-pointer" onclick="togglePrintPanel()" title="Status da impressão">
                <span class="pulse-dot" id="print-pulse" style="background:#94a3b8;"></span>
                <span id="print-status-text">Impressão desligada</span>
            </div>
            @yield('topbar-actions')
        </div>
    </header>

    {{-- PAGE CONTENT --}}
    <main class="flex-1 p-4 sm:p-6 max-w-screen-2xl mx-auto w-full">
        @if(isset($_SESSION['success']))
            <div class="mb-4 bg-emerald-50 border border-emerald-200 rounded-xl px-4 py-3 flex items-center gap-3 text-emerald-700 text-sm">
                <i class="fas fa-check-circle text-emerald-500"></i>
                {{ $_SESSION['success'] }}
            </div>
            @php unset($_SESSION['success']); @endphp
        @endif
        @if(isset($_SESSION['error']))
            <div class="mb-4 bg-red-50 border border-red-200 rounded-xl px-4 py-3 flex items-center gap-3 text-red-700 text-sm">
                <i class="fas fa-exclamation-circle text-red-500"></i>
                {{ $_SESSION['error'] }}
            </div>
            @php unset($_SESSION['error']); @endphp
        @endif

        @yield('content')
    </main>

</div>
{{-- ════════════════ END MAIN ════════════════ --}}

{{-- PRINT PANEL --}}
<div id="print-panel" style="display:none; position:fixed; bottom:80px; right:24px; z-index:9998; width:320px; background:#fff; border-radius:16px; box-shadow:0 8px 40px rgba(0,0,0,.15); border:1px solid #e2e8f0; overflow:hidden;">
    <div style="background:linear-gradient(135deg,#6366f1,#8b5cf6); padding:14px 16px; display:flex; align-items:center; justify-content:space-between;">
        <div style="color:#fff; font-weight:700; font-size:14px; display:flex; align-items:center; gap:8px;">
            <i class="fas fa-print"></i> Impressão Automática
        </div>
        <button onclick="togglePrintPanel()" style="color:rgba(255,255,255,.7); background:none; border:none; cursor:pointer; font-size:16px;">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <div style="padding:16px;">
        <p style="font-size:12px;color:#64748b;margin:0 0 12px;">Receba e imprima novos pedidos automaticamente na impressora térmica conectada ao computador.</p>

        <label style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
            <span style="font-size:13px;font-weight:600;color:#1e293b;">Ativar polling</span>
            <div onclick="toggleAutoPrint()" id="auto-print-toggle"
                 style="width:44px;height:24px;border-radius:12px;background:#e2e8f0;cursor:pointer;position:relative;transition:background .2s;">
                <div id="toggle-thumb" style="width:20px;height:20px;border-radius:50%;background:#fff;position:absolute;top:2px;left:2px;transition:left .2s;box-shadow:0 1px 4px rgba(0,0,0,.2);"></div>
            </div>
        </label>

        <div style="margin-bottom:12px;">
            <label style="font-size:12px;font-weight:600;color:#475569;display:block;margin-bottom:4px;">Impressora</label>
            <select id="printer-select" style="width:100%;font-size:12px;padding:7px 10px;border:1px solid #e2e8f0;border-radius:8px;background:#f8fafc;color:#1e293b;outline:none;">
                <option value="browser">Impressora padrão (navegador)</option>
                <option value="thermal-58">Térmica 58mm</option>
                <option value="thermal-80">Térmica 80mm</option>
            </select>
        </div>

        <div style="margin-bottom:12px;">
            <label style="font-size:12px;font-weight:600;color:#475569;display:block;margin-bottom:4px;">Intervalo de verificação</label>
            <select id="poll-interval-select" style="width:100%;font-size:12px;padding:7px 10px;border:1px solid #e2e8f0;border-radius:8px;background:#f8fafc;color:#1e293b;outline:none;">
                <option value="10000">10 segundos</option>
                <option value="20000" selected>20 segundos</option>
                <option value="30000">30 segundos</option>
                <option value="60000">1 minuto</option>
            </select>
        </div>

        <div id="print-log" style="background:#f8fafc;border-radius:8px;padding:8px;font-size:11px;color:#64748b;max-height:80px;overflow-y:auto;font-family:monospace;border:1px solid #e2e8f0;">
            <div>Sistema de impressão pronto.</div>
        </div>
    </div>
</div>

{{-- PRINT TOAST --}}
<div id="print-toast">
    <span class="pt-icon">🖨️</span>
    <div>
        <div style="font-weight:700;" id="pt-title">Novo Pedido!</div>
        <div style="opacity:.7;font-size:12px;" id="pt-sub">Imprimindo pedido #...</div>
    </div>
</div>

{{-- Hidden iframe for auto-print --}}
<iframe id="print-iframe" style="display:none;" onload="iframePrintLoaded(this)"></iframe>

<script>
// ══════════════════════════════════════════
//  SIDEBAR
// ══════════════════════════════════════════
let sbCollapsed = JSON.parse(localStorage.getItem('sb_collapsed') || 'false');

function sbApplyState() {
    const sb = document.getElementById('sidebar');
    const mw = document.getElementById('main-wrap');
    sb.classList.toggle('collapsed', sbCollapsed);
    mw.classList.toggle('collapsed', sbCollapsed);
}

function sbToggle() {
    sbCollapsed = !sbCollapsed;
    localStorage.setItem('sb_collapsed', sbCollapsed);
    sbApplyState();
}

function sbOpen() {
    document.getElementById('sidebar').classList.add('mobile-open');
    document.getElementById('sb-overlay').classList.add('active');
}

function sbClose() {
    document.getElementById('sidebar').classList.remove('mobile-open');
    document.getElementById('sb-overlay').classList.remove('active');
}

// Active nav link
(function() {
    const path = window.location.pathname;
    document.querySelectorAll('.nav-item[data-path]').forEach(link => {
        const lp = link.dataset.path;
        const exact = link.dataset.exact === '1';
        const isActive = exact ? path === lp : path.startsWith(lp);
        if (isActive) link.classList.add('active');
    });
    // Close sidebar on link click (mobile)
    document.querySelectorAll('#sidebar .nav-item').forEach(a => {
        a.addEventListener('click', () => {
            if (window.innerWidth < 1024) sbClose();
        });
    });
})();

// Apply sidebar state on load
sbApplyState();

// ══════════════════════════════════════════
//  AUTO-PRINT SYSTEM
// ══════════════════════════════════════════
let autoPrintEnabled = JSON.parse(localStorage.getItem('auto_print') || 'false');
let lastPrintedOrderId = parseInt(localStorage.getItem('last_printed_order') || '0');
let pollTimer = null;
let iframeQueue = [];
let iframeBusy = false;

function togglePrintPanel() {
    const panel = document.getElementById('print-panel');
    panel.style.display = panel.style.display === 'none' ? 'block' : 'none';
}

function updatePrintToggleUI() {
    const toggle = document.getElementById('auto-print-toggle');
    const thumb = document.getElementById('toggle-thumb');
    const pulse = document.getElementById('print-pulse');
    const text = document.getElementById('print-status-text');
    const indicator = document.getElementById('print-status-indicator');
    indicator.classList.remove('hidden');
    if (autoPrintEnabled) {
        toggle.style.background = '#6366f1';
        thumb.style.left = '22px';
        pulse.style.background = '#10b981';
        text.textContent = 'Impressão ativa';
    } else {
        toggle.style.background = '#e2e8f0';
        thumb.style.left = '2px';
        pulse.style.background = '#94a3b8';
        text.textContent = 'Impressão desligada';
    }
}

function toggleAutoPrint() {
    autoPrintEnabled = !autoPrintEnabled;
    localStorage.setItem('auto_print', autoPrintEnabled);
    updatePrintToggleUI();
    if (autoPrintEnabled) {
        printLog('✅ Polling ativado.');
        startPollLoop();
    } else {
        printLog('⏹ Polling desativado.');
        stopPollLoop();
    }
}

function printLog(msg) {
    const log = document.getElementById('print-log');
    if (!log) return;
    const now = new Date().toLocaleTimeString('pt-BR');
    const div = document.createElement('div');
    div.textContent = `[${now}] ${msg}`;
    log.appendChild(div);
    log.scrollTop = log.scrollHeight;
}

function getInterval() {
    const sel = document.getElementById('poll-interval-select');
    return parseInt(sel ? sel.value : 20000);
}

function startPollLoop() {
    stopPollLoop();
    pollTimer = setInterval(checkNewOrders, getInterval());
    checkNewOrders(); // immediate first check
}

function stopPollLoop() {
    if (pollTimer) { clearInterval(pollTimer); pollTimer = null; }
}

async function checkNewOrders() {
    try {
        const resp = await fetch('/admin/api/orders');
        const data = await resp.json();
        const orders = data.orders || [];
        // Find new pending orders since last printed
        const newOrders = orders.filter(o => {
            return o.status === 'pending' && parseInt(o.id) > lastPrintedOrderId;
        });
        if (newOrders.length > 0) {
            printLog(`🆕 ${newOrders.length} novo(s) pedido(s) encontrado(s).`);
            newOrders.sort((a, b) => a.id - b.id);
            newOrders.forEach(o => iframeQueue.push(o));
            processQueue();
            // Update last ID
            const maxId = Math.max(...newOrders.map(o => o.id));
            lastPrintedOrderId = maxId;
            localStorage.setItem('last_printed_order', maxId);
        }
    } catch(e) {
        printLog(`❌ Erro ao verificar pedidos: ${e.message}`);
    }
}

function processQueue() {
    if (iframeBusy || iframeQueue.length === 0) return;
    iframeBusy = true;
    const order = iframeQueue.shift();
    showPrintToast(order);
    printLog(`🖨 Imprimindo pedido #${order.id}...`);
    const iframe = document.getElementById('print-iframe');
    iframe.src = `/admin/print-order-pdf/${order.id}`;
}

function iframePrintLoaded(iframe) {
    if (!iframe.src || iframe.src === 'about:blank') return;
    try {
        // The PDF page auto-calls window.print() on load
        // Wait a bit then free the queue
        setTimeout(() => {
            iframeBusy = false;
            processQueue();
        }, 4000);
    } catch(e) {
        iframeBusy = false;
        processQueue();
    }
}

function showPrintToast(order) {
    const toast = document.getElementById('print-toast');
    document.getElementById('pt-title').textContent = `Novo Pedido #${order.id}`;
    document.getElementById('pt-sub').textContent = `${order.customer_name} — R$ ${parseFloat(order.total_amount).toFixed(2).replace('.',',')}`;
    toast.classList.add('show');
    setTimeout(() => toast.classList.remove('show'), 5000);
}

// Init on load
document.addEventListener('DOMContentLoaded', () => {
    updatePrintToggleUI();
    if (autoPrintEnabled) startPollLoop();

    // Restart polling if interval changes
    const intSel = document.getElementById('poll-interval-select');
    if (intSel) {
        intSel.addEventListener('change', () => {
            if (autoPrintEnabled) { stopPollLoop(); startPollLoop(); }
        });
    }
});
</script>

@yield('scripts')
</body>
</html>
