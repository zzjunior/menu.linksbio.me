<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260109045353 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Adiciona campos de verificação de email (email_verified_at e email_verification_token)';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE users ADD COLUMN email_verified_at DATETIME NULL AFTER email');
        $this->addSql('ALTER TABLE users ADD COLUMN email_verification_token VARCHAR(64) NULL AFTER email_verified_at');
        $this->addSql('CREATE INDEX idx_email_verification_token ON users(email_verification_token)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX idx_email_verification_token ON users');
        $this->addSql('ALTER TABLE users DROP COLUMN email_verification_token');
        $this->addSql('ALTER TABLE users DROP COLUMN email_verified_at');
    }
}
