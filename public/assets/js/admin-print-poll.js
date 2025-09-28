// --- INÍCIO: Configuração de assinatura digital para QZ Tray ---
// ATENÇÃO: Use seu próprio certificado em produção!
qz.security.setSignaturePromise(function(toSign) {
    return function(resolve, reject) {
        // Exemplo: assinatura demo (NÃO USE EM PRODUÇÃO)
        // Veja https://qz.io/wiki/2.0-signing-messages.html para gerar sua própria assinatura
        resolve(`-----BEGIN PRIVATE KEY-----
MIIEvAIBADANBgkqhkiG9w0BAQEFAASCBKYwggSiAgEAAoIBAQCPoAJBkswre9HH
H03o+bC+WHh3yaSbE2wvbkVXFUv2hWpMPahkES8QChYF+eeGKO2tdYLlMG3+mMMz
7JUccoG6Jj1E8gYweKPQVaNQ7a7OQSgs4v3sfJYpYIwjHD9X1dGmtFszdz9wLqt6
4zxuYkodMQGianMIghaNjdiBVp5aqF1/H7TeKuPwceolBxAplIjjAD7eokSnoxv5
14hx7o8loTnL6+M4JzHvZAFJ5gGDMLp5wEBHQy2w9MityFwrx6pEZ4fOT3uVQLwM
srr1lfotu0cIq8q9RCKGyGtgLHW97Rn9yJjz4hSyzENXnu2gxkUsF0kFjUk3EGti
dLoHue9rAgMBAAECggEAOXjtDjJg8xGUK1sygQTRngmnA0A0Vz3Zb81vf95szFQs
6h9OL+TUcVWn5HJRHFVqbpv0aiQMD/IvQqrsLDsCAGxm3hz/LwxHeQmb1bmW1sAq
BHQ86hIcwKf6y9301wAf6daIHDserpRuWDlwKDe6sp22qrhwGbp8rd5SAceYjGvz
dU5VoVvUCS9P99KmIlOV7wwyEDMqhVdsInFgAbmCNBZaYqmANxg0R1xRR1i98E3/
sJqQkrWhwOwwa0WWPF3Pp1115uV8NC/INB8ajurLiys/UIei3V14iDKcrBd/FAmc
ms1OVuBIRUkHExT+N9k/F9q02qTngiA6hrtgMHlDeQKBgQDKfs4QbvOhk7ToqnLv
+cu2/Qvfxilrnpgon1EhJlxEcKQKn8hix9TedNOJoWkCytprtYDH3LImi1wOpakA
HT/jQ/9pPCPlfY9N3Rcj4iKpQLG0lKWc1LxGCwBmPAapYhrxSFWrHAlANGd3pS3h
1uqGY/2UVidG78BZrr67ub7iTwKBgQC1kxj3deNsDePMFMSfRjCzfoTBX09xBQqB
taAzADxNSbtlTP9ie+8TUcsyev0nPYatRfVoJBDQQuRWWcnkIo+WJIYYTO1JSna1
ye/Yp5wYjXZ8+E4b+eI54vD7OeofnF5AmfD/8IS41VaMxa5isEI70We+VgXdWY9W
ha+ArFymJQKBgDTWo9Sb7wzRaxpvJ7DA4Mxt+UQ6BCLl7lRjRhs9VEQzlEqFaReF
h2FI4M4ABVPKNPZ4FROR3ha6tJnJ1nCGMV6PJr2CCOfgPJ2XvGsLnfnGPNifFRv4
UuyAAGms7hwS0m71bg1JMozDX+BThMZyex34R3oGhRc4hluggnqfvFR3AoGAHGX3
P6zWdu3tNNwCrI6Dy278QGoxuJF7RTIs7g0ZYVUo0/0o7DRJ/hGK4EhQE7URvUP1
z+XkI05y+/ZrFx04q9jmiCJv8kiNLH16373HMifd4knLnaCFe1w9KG20amFAdIYf
JUhu2aG0OawpTBIZKdXkjeZSYH5DZtrdyhgrHWkCgYBPvXTMfC173adQnGQKU/Ie
VjS+vVU8kXj9ayIZ184uW2uQWmFqKWoschz/5irP7fUbirl+cl9rKTU1+Mmj+kiu
L5NwmTy+LxKovBjYZpnAk8AYzMeHyDkRk/GjZ4e+8+ruXClPBeSwkL7SKtAp1JPJ
y8tdItP/jOnuE4M4SF0ctA==
-----END PRIVATE KEY-----`); // Sem assinatura (apenas para testes, QZ Tray deve estar em modo "unsigned")
    };
});

qz.security.setCertificatePromise(function(resolve, reject) {
    // Certificado demo embutido (NÃO USE EM PRODUÇÃO)
    resolve(`-----BEGIN CERTIFICATE-----
MIIECzCCAvOgAwIBAgIGAZlWYngQMA0GCSqGSIb3DQEBCwUAMIGiMQswCQYDVQQG
EwJVUzELMAkGA1UECAwCTlkxEjAQBgNVBAcMCUNhbmFzdG90YTEbMBkGA1UECgwS
UVogSW5kdXN0cmllcywgTExDMRswGQYDVQQLDBJRWiBJbmR1c3RyaWVzLCBMTEMx
HDAaBgkqhkiG9w0BCQEWDXN1cHBvcnRAcXouaW8xGjAYBgNVBAMMEVFaIFRyYXkg
RGVtbyBDZXJ0MB4XDTI1MDkxNjA2MzUxN1oXDTQ1MDkxNjA2MzUxN1owgaIxCzAJ
BgNVBAYTAlVTMQswCQYDVQQIDAJOWTESMBAGA1UEBwwJQ2FuYXN0b3RhMRswGQYD
VQQKDBJRWiBJbmR1c3RyaWVzLCBMTEMxGzAZBgNVBAsMElFaIEluZHVzdHJpZXMs
IExMQzEcMBoGCSqGSIb3DQEJARYNc3VwcG9ydEBxei5pbzEaMBgGA1UEAwwRUVog
VHJheSBEZW1vIENlcnQwggEiMA0GCSqGSIb3DQEBAQUAA4IBDwAwggEKAoIBAQCP
oAJBkswre9HHH03o+bC+WHh3yaSbE2wvbkVXFUv2hWpMPahkES8QChYF+eeGKO2t
dYLlMG3+mMMz7JUccoG6Jj1E8gYweKPQVaNQ7a7OQSgs4v3sfJYpYIwjHD9X1dGm
tFszdz9wLqt64zxuYkodMQGianMIghaNjdiBVp5aqF1/H7TeKuPwceolBxAplIjj
AD7eokSnoxv514hx7o8loTnL6+M4JzHvZAFJ5gGDMLp5wEBHQy2w9MityFwrx6pE
Z4fOT3uVQLwMsrr1lfotu0cIq8q9RCKGyGtgLHW97Rn9yJjz4hSyzENXnu2gxkUs
F0kFjUk3EGtidLoHue9rAgMBAAGjRTBDMBIGA1UdEwEB/wQIMAYBAf8CAQEwDgYD
VR0PAQH/BAQDAgEGMB0GA1UdDgQWBBQBJ5fxvcxv12lFe3clavgkHwIKyDANBgkq
hkiG9w0BAQsFAAOCAQEAaINMjQljQZoInFvMp0uAR/xgRVnM4jrsfjLp+HiphP1n
q5qxWjniFbKXpXJby8i+I4COVWd6dh7o90qF9ErqrK/yrhFLXcKO0QO4iVYjZ2p9
3dTrcNpqWpzAYcMzHSM7YkGC3TNBRn5xVKSpdAFOqvzYVHf/JRIIn7J1urqYSHSJ
OGyyLEzUhC01hOOGO2O4QK7LbaMT6xJ3Wm3f66+/LjdpNOPjpniFZ+s+hPJBD7Nb
03Azc2dIG/CFim+kt4La8+u+3Fe/sDZNjsrdsc5KHw/7wRSs04TIYPWMMDUVmcrU
Ys0Zl1jrVmqi7B8nNWzH+lOeYlNCF3+CyWNuJHF4cA==
-----END CERTIFICATE-----`); // Sem certificado (apenas para testes, QZ Tray deve estar em modo "unsigned")
});
// --- FIM: Configuração de assinatura digital ---
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
    console.log('[QZ] printWithQZTray chamado', printData);
    if (!window.qz) {
        alert('QZ Tray não está disponível. Instale e permita o acesso.');
        return;
    }
    const printerName = "POS-58"; // Nome exato da impressora
    // Lista todas as impressoras disponíveis
    qz.printers.find().then(allPrinters => {
        console.log('[QZ] Impressoras disponíveis:', allPrinters);
        // Busca a impressora pelo nome exato
        qz.printers.find(printerName).then(found => {
            console.log('[QZ] Valor retornado por qz.printers.find(printerName):', found);
            if (!found || typeof found !== 'string') {
                alert("Impressora 'POS-58' não encontrada. Veja o console para os nomes disponíveis.");
                return;
            }
            // Força o nome da impressora no objeto de configuração
            const config = qz.configs.create(found);
            console.log('[QZ] Config de impressão:', config);
            const data = Array.isArray(printData)
                ? printData.map(line => line.content || '')
                : Object.values(printData).map(line => line.content || '');
            qz.print(config, data).then(() => {
                console.log('[QZ] Impressão enviada com sucesso!');
            }).catch(err => {
                alert('Erro ao imprimir: ' + err);
                console.error('[QZ] Erro ao imprimir:', err);
            });
        }).catch(err => {
            console.error('[QZ] Erro ao buscar impressora por nome:', err);
        });
    }).catch(err => {
        console.error('[QZ] Erro ao listar impressoras:', err);
    });
}

// Loop de polling
async function pollForNewOrders() {
    let lastPrinted = localStorage.getItem(LAST_ORDER_KEY) || 0;
    setInterval(async () => {
        console.log('[QZ] Polling... último impresso:', lastPrinted);
        const lastOrderId = await fetchLastOrderId();
        console.log('[QZ] Último pedido do backend:', lastOrderId);
        if (lastOrderId && lastOrderId > lastPrinted) {
            try {
                const printData = await fetchPrintData(lastOrderId);
                console.log('[QZ] printData recebido do backend:', printData);
                printWithQZTray(printData);
                localStorage.setItem(LAST_ORDER_KEY, lastOrderId);
            } catch (e) {
                console.error('[QZ] Erro ao imprimir pedido:', e);
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
