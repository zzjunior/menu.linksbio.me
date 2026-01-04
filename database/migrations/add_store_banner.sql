-- Adicionar campo store_banner à tabela users
ALTER TABLE `users` ADD COLUMN `store_banner` VARCHAR(255) NULL AFTER `logo`;
