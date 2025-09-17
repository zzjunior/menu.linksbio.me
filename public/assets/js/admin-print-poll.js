// admin-print-poll.js
// Script para polling de novos pedidos e impressão automática via QZ Tray
// Requer QZ Tray instalado no PC do admin: https://qz.io/

// CONFIGURAÇÕES
const POLL_INTERVAL = 5000; // ms (5 segundos)
const PRINT_ENDPOINT = '/admin/print-order/'; // endpoint que retorna o JSON de impressão (ajuste se necessário)
const LAST_ORDER_KEY = 'acaiteria_last_printed_order';

// Função para buscar o último pedido do backend
async function fetchLastOrderId() {
    try {
        const resp = await fetch('/api/last-order-id'); // Crie esse endpoint para retornar o último ID
        if (!resp.ok) return null;
        const data = await resp.json();
        return data.last_order_id;
    } catch (e) {
        console.error('Erro ao buscar último pedido:', e);
        return null;
    }
}

// Função para buscar o JSON de impressão de um pedido
async function fetchPrintData(orderId) {
    const resp = await fetch(PRINT_ENDPOINT + orderId);
    if (!resp.ok) throw new Error('Erro ao buscar dados de impressão');
    return await resp.json();
}

// Função para imprimir via QZ Tray
function printWithQZTray(printData) {
    if (!window.qz) {
        alert('QZ Tray não está disponível. Instale e permita o acesso.');
        return;
    }
    // Exemplo: imprime texto simples (ajuste conforme seu layout)
    const config = qz.configs.create(null); // null = impressora padrão
    const data = Array.isArray(printData)
    ? printData.map(line => line.content || '')
    : Object.values(printData).map(line => line.content || '');
    qz.print(config, data).catch(err => {
        alert('Erro ao imprimir: ' + err);
    });
}

// Loop de polling
async function pollForNewOrders() {
    let lastPrinted = localStorage.getItem(LAST_ORDER_KEY) || 0;
    setInterval(async () => {
        const lastOrderId = await fetchLastOrderId();
        if (lastOrderId && lastOrderId > lastPrinted) {
            try {
                const printData = await fetchPrintData(lastOrderId);
                printWithQZTray(printData);
                localStorage.setItem(LAST_ORDER_KEY, lastOrderId);
            } catch (e) {
                console.error('Erro ao imprimir pedido:', e);
            }
        }
    }, POLL_INTERVAL);
}

// Inicialização
window.addEventListener('DOMContentLoaded', () => {
    if (window.qz) {
        qz.websocket.connect().then(() => {
            pollForNewOrders();
        });
    } else {
        alert('QZ Tray não detectado. Instale e permita o acesso para impressão automática.');
    }
});
