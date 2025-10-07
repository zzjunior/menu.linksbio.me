function printWithQZTray(printData) {
    console.log('[QZ] printWithQZTray chamado', printData);
    if (!window.qz) {
        alert('QZ Tray não está disponível. Instale e permita o acesso.');
        return;
    }
    // Usa a impressora padrão (null)
    const config = qz.configs.create(null);
    console.log('[QZ] Config de impressão (impressora padrão):', config);
    
    // CONVERTER TUDO PARA STRINGS SIMPLES
    const data = [];
    if (Array.isArray(printData)) {
        printData.forEach(item => {
            if (typeof item === 'string') {
                data.push(item);
            } else if (item && typeof item === 'object' && item.content) {
                data.push(item.content);
            } else if (item && typeof item === 'object' && item.data) {
                data.push(item.data);
            } else {
                data.push('');
            }
        });
    } else {
        data.push(String(printData));
    }
    
    console.log('[QZ] Dados formatados para impressão (só strings):', data);
    
    qz.print(config, data).then(() => {
        console.log('[QZ] Impressão enviada com sucesso!');
    }).catch(err => {
        alert('Erro ao imprimir: ' + err);
        console.error('[QZ] Erro ao imprimir:', err);
    });
}
