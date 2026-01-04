-- Adicionar campos de horários de funcionamento na tabela users
-- Tenta adicionar business_hours
SET @query = IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE table_schema = DATABASE() 
     AND table_name = 'users' 
     AND column_name = 'business_hours') = 0,
    'ALTER TABLE users ADD COLUMN business_hours JSON DEFAULT NULL COMMENT "Horários de funcionamento por dia da semana"',
    'SELECT "Column business_hours already exists" AS result'
);
PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Tenta adicionar is_open
SET @query = IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE table_schema = DATABASE() 
     AND table_name = 'users' 
     AND column_name = 'is_open') = 0,
    'ALTER TABLE users ADD COLUMN is_open TINYINT(1) DEFAULT 1 COMMENT "Permite abrir/fechar loja manualmente"',
    'SELECT "Column is_open already exists" AS result'
);
PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Tenta adicionar closed_message
SET @query = IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE table_schema = DATABASE() 
     AND table_name = 'users' 
     AND column_name = 'closed_message') = 0,
    'ALTER TABLE users ADD COLUMN closed_message VARCHAR(255) DEFAULT "No momento estamos fechados. Volte em breve!" COMMENT "Mensagem quando fechado"',
    'SELECT "Column closed_message already exists" AS result'
);
PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Exemplo de estrutura JSON para business_hours:
-- {
--   "monday": {"enabled": true, "open": "09:00", "close": "18:00"},
--   "tuesday": {"enabled": true, "open": "09:00", "close": "18:00"},
--   "wednesday": {"enabled": true, "open": "09:00", "close": "18:00"},
--   "thursday": {"enabled": true, "open": "09:00", "close": "18:00"},
--   "friday": {"enabled": true, "open": "09:00", "close": "18:00"},
--   "saturday": {"enabled": true, "open": "09:00", "close": "14:00"},
--   "sunday": {"enabled": false, "open": null, "close": null}
-- }
