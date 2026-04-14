<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Adiciona daily_order_number na tabela orders
 */
final class Version20260414000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Adiciona daily_order_number para exibir no frontend numeracao diaria';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE orders ADD COLUMN daily_order_number INT NULL AFTER id');
        $this->addSql('CREATE INDEX idx_orders_daily_number ON orders(store_id, daily_order_number)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX idx_orders_daily_number ON orders');
        $this->addSql('ALTER TABLE orders DROP COLUMN daily_order_number');
    }
}
