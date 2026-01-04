<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260104122545 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Adiciona coluna pix_key na tabela stores';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE stores ADD COLUMN pix_key VARCHAR(255) NULL AFTER closed_message');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE stores DROP COLUMN pix_key');
    }
}
