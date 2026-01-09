<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260109003739 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Adiciona coluna store_id à tabela orders';
    }

    public function up(Schema $schema): void
    {
        // Adicionar coluna store_id à tabela orders
        $this->addSql('ALTER TABLE orders ADD COLUMN store_id INT NULL AFTER user_id');
        
        // Copiar valores de user_id para store_id (considerando que user_id é a loja)
        $this->addSql('UPDATE orders SET store_id = user_id WHERE store_id IS NULL');
        
        // Adicionar foreign key para stores
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT fk_orders_store FOREIGN KEY (store_id) REFERENCES stores(id) ON DELETE CASCADE');
        
        // Criar índice
        $this->addSql('CREATE INDEX idx_orders_store_id ON orders(store_id)');
    }

    public function down(Schema $schema): void
    {
        // Remover índice
        $this->addSql('DROP INDEX idx_orders_store_id ON orders');
        
        // Remover foreign key
        $this->addSql('ALTER TABLE orders DROP FOREIGN KEY fk_orders_store');
        
        // Remover coluna
        $this->addSql('ALTER TABLE orders DROP COLUMN store_id');
    }
}
