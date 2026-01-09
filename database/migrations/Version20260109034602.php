<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260109034602 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Adiciona campo table_number opcional para pedidos de mesa';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE orders ADD COLUMN table_number VARCHAR(55) NULL AFTER id');
        $this->addSql('CREATE INDEX idx_table_number ON orders(table_number)');
        //$this->addSql("ALTER TABLE orders MODIFY COLUMN order_type ENUM('delivery','pickup','mesa') DEFAULT 'delivery'");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX idx_table_number ON orders');
        $this->addSql('ALTER TABLE orders DROP COLUMN table_number');
    }
}
