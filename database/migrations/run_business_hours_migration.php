<?php
// Script para adicionar colunas de horários na tabela stores

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../config/bootstrap.php';

use Doctrine\DBAL\DriverManager;

try {
    $config = require __DIR__ . '/../../config/settings.php';
    
    $connectionParams = [
        'dbname' => $config['db']['database'],
        'user' => $config['db']['username'],
        'password' => $config['db']['password'],
        'host' => $config['db']['host'],
        'driver' => 'pdo_mysql',
        'charset' => 'utf8mb4'
    ];
    
    $connection = DriverManager::getConnection($connectionParams);

    echo "Conectado ao banco de dados\n";
    echo "Adicionando colunas na tabela stores...\n\n";

    // Verificar e adicionar business_hours
    $checkBusinessHours = $connection->fetchOne(
        "SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
         WHERE table_schema = DATABASE() 
         AND table_name = 'stores' 
         AND column_name = 'business_hours'"
    );

    if ($checkBusinessHours == 0) {
        $connection->executeStatement(
            "ALTER TABLE stores ADD COLUMN business_hours JSON DEFAULT NULL COMMENT 'Horários de funcionamento por dia da semana'"
        );
        echo "✓ Coluna 'business_hours' adicionada\n";
    } else {
        echo "- Coluna 'business_hours' já existe\n";
    }

    // Verificar e adicionar is_open
    $checkIsOpen = $connection->fetchOne(
        "SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
         WHERE table_schema = DATABASE() 
         AND table_name = 'stores' 
         AND column_name = 'is_open'"
    );

    if ($checkIsOpen == 0) {
        $connection->executeStatement(
            "ALTER TABLE stores ADD COLUMN is_open TINYINT(1) DEFAULT 1 COMMENT 'Permite abrir/fechar loja manualmente'"
        );
        echo "✓ Coluna 'is_open' adicionada\n";
    } else {
        echo "- Coluna 'is_open' já existe\n";
    }

    // Verificar e adicionar closed_message
    $checkClosedMessage = $connection->fetchOne(
        "SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
         WHERE table_schema = DATABASE() 
         AND table_name = 'stores' 
         AND column_name = 'closed_message'"
    );

    if ($checkClosedMessage == 0) {
        $connection->executeStatement(
            "ALTER TABLE stores ADD COLUMN closed_message VARCHAR(255) DEFAULT 'No momento estamos fechados. Volte em breve!' COMMENT 'Mensagem quando fechado'"
        );
        echo "✓ Coluna 'closed_message' adicionada\n";
    } else {
        echo "- Coluna 'closed_message' já existe\n";
    }

    echo "\n✅ Migração concluída com sucesso!\n";

} catch (\Exception $e) {
    echo "\n❌ Erro: " . $e->getMessage() . "\n";
    exit(1);
}
