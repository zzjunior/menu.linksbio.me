<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260109004134 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajusta constraint UNIQUE de phone para permitir mesmo telefone em lojas diferentes';
    }

    public function up(Schema $schema): void
    {
        // Remover constraint UNIQUE do phone
        $this->addSql('ALTER TABLE customers DROP INDEX phone');
        
        // Adicionar constraint UNIQUE composta (phone, store_id)
        $this->addSql('ALTER TABLE customers ADD UNIQUE KEY unique_phone_store (phone, store_id)');
    }

    public function down(Schema $schema): void
    {
        // Remover constraint UNIQUE composta
        $this->addSql('ALTER TABLE customers DROP INDEX unique_phone_store');
        
        // Restaurar constraint UNIQUE simples no phone
        $this->addSql('ALTER TABLE customers ADD UNIQUE KEY phone (phone)');
    }
}
