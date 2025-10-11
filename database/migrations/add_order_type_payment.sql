-- Adicionar colunas para tipo de pedido e forma de pagamento na tabela orders

ALTER TABLE `orders` 
ADD COLUMN `order_type` enum('delivery','pickup') DEFAULT 'delivery' AFTER `notes`,
ADD COLUMN `payment_method` enum('pix','money','card') DEFAULT 'pix' AFTER `order_type`,
ADD COLUMN `change_for` decimal(10,2) NULL DEFAULT NULL AFTER `payment_method`;