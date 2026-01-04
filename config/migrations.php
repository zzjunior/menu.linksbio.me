<?php

/**
 * Configuração do Doctrine Migrations
 * Documentação: https://www.doctrine-project.org/projects/migrations.html
 */

return [
    'table_storage' => [
        'table_name' => 'migrations',
        'version_column_name' => 'migration',
        'version_column_length' => 255,
        'executed_at_column_name' => 'executed_at',
    ],

    'migrations_paths' => [
        'App\Database\Migrations' => __DIR__ . '/../database/migrations',
    ],

    'all_or_nothing' => true,
    'check_database_platform' => false,
    'organize_migrations' => 'none', // 'BY_YEAR', 'BY_YEAR_AND_MONTH', 'none'
    'connection' => null, // Será definido via CLI
    'em' => null,
];
