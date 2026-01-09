<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260109000730 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Atualiza tabela sessions com colunas necessárias para autenticação segura';
    }

    public function up(Schema $schema): void
    {
        // Verificar e adicionar colunas na tabela sessions
        $this->addSql("
            ALTER TABLE sessions 
            ADD COLUMN IF NOT EXISTS session_id VARCHAR(255) NOT NULL,
            ADD COLUMN IF NOT EXISTS user_id INT NOT NULL,
            ADD COLUMN IF NOT EXISTS ip_address VARCHAR(45) NOT NULL,
            ADD COLUMN IF NOT EXISTS user_agent VARCHAR(255) NOT NULL,
            ADD COLUMN IF NOT EXISTS expires_at DATETIME NOT NULL,
            ADD COLUMN IF NOT EXISTS created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            ADD COLUMN IF NOT EXISTS last_activity DATETIME NULL,
            ADD UNIQUE INDEX idx_session_id (session_id),
            ADD INDEX idx_user_id (user_id),
            ADD INDEX idx_expires_at (expires_at)
        ");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("
            ALTER TABLE sessions 
            DROP COLUMN IF EXISTS session_id,
            DROP COLUMN IF EXISTS user_id,
            DROP COLUMN IF EXISTS ip_address,
            DROP COLUMN IF EXISTS user_agent,
            DROP COLUMN IF EXISTS expires_at,
            DROP COLUMN IF EXISTS created_at,
            DROP COLUMN IF EXISTS last_activity,
            DROP INDEX IF EXISTS idx_session_id,
            DROP INDEX IF EXISTS idx_user_id,
            DROP INDEX IF EXISTS idx_expires_at
        ");
    }
}
