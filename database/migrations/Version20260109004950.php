<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260109004950 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Adiciona coluna slug à tabela users (alias para store_slug)';
    }

    public function up(Schema $schema): void
    {
        // Adicionar coluna slug como cópia de store_slug
        $this->addSql('ALTER TABLE users ADD COLUMN slug VARCHAR(255) NULL AFTER store_slug');
        
        // Copiar valores de store_slug para slug
        $this->addSql('UPDATE users SET slug = store_slug WHERE slug IS NULL');
        
        // Criar índice UNIQUE
        $this->addSql('CREATE UNIQUE INDEX idx_users_slug ON users(slug)');
    }

    public function down(Schema $schema): void
    {
        // Remover índice
        $this->addSql('DROP INDEX idx_users_slug ON users');
        
        // Remover coluna
        $this->addSql('ALTER TABLE users DROP COLUMN slug');
    }
}
