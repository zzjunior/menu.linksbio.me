-- Adicionar campos de horários de funcionamento na tabela stores
-- Tenta adicionar business_hours
SET @query = IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE table_schema = DATABASE() 
     AND table_name = 'stores' 
     AND column_name = 'business_hours') = 0,
    'ALTER TABLE stores ADD COLUMN business_hours JSON DEFAULT NULL COMMENT "Horários de funcionamento por dia da semana"',
    'SELECT "Column business_hours already exists in stores" AS result'
);
PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Tenta adicionar is_open
SET @query = IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE table_schema = DATABASE() 
     AND table_name = 'stores' 
     AND column_name = 'is_open') = 0,
    'ALTER TABLE stores ADD COLUMN is_open TINYINT(1) DEFAULT 1 COMMENT "Permite abrir/fechar loja manualmente"',
    'SELECT "Column is_open already exists in stores" AS result'
);
PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Tenta adicionar closed_message
SET @query = IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE table_schema = DATABASE() 
     AND table_name = 'stores' 
     AND column_name = 'closed_message') = 0,
    'ALTER TABLE stores ADD COLUMN closed_message VARCHAR(255) DEFAULT "No momento estamos fechados. Volte em breve!" COMMENT "Mensagem quando fechado"',
    'SELECT "Column closed_message already exists in stores" AS result'
);
PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
