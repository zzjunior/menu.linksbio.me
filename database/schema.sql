-- Açaíteria Digital - Estrutura do Banco


CREATE TABLE `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `price` decimal(10,2) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `image_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `size_ml` int(11) DEFAULT NULL COMMENT 'Tamanho em ml para açaís',
  `max_ingredients` int(11) DEFAULT NULL COMMENT 'Máximo de ingredientes permitidos',
  `size_order` int(11) DEFAULT '0' COMMENT 'Ordem de tamanho (1=P, 2=M, 3=G)',
  `active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_category` (`category_id`),
  KEY `idx_active` (`active`),
  KEY `idx_products_category_active` (`category_id`,`active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `ingredients` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('frutas','complementos','caldas','granolas','outros') COLLATE utf8mb4_unicode_ci NOT NULL,
  `additional_price` decimal(10,2) DEFAULT '0.00',
  `active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_type` (`type`),
  KEY `idx_active` (`active`),
  KEY `idx_ingredients_type_active` (`type`,`active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



-- Categorias
INSERT INTO `categories` (`id`, `name`, `description`) VALUES
(1, 'Açaí', 'Açaí natural com opções de personalização'),
(2, 'Vitaminas', 'Vitaminas e smoothies naturais'),
(3, 'Lanches', 'Lanches naturais e saudáveis'),
(4, 'Bebidas', 'Sucos naturais e outras bebidas'),
(5, 'Sobremesas', 'Sobremesas saudáveis e naturais');

-- Ingredientes
INSERT INTO `ingredients` (`name`, `type`, `additional_price`) VALUES
-- Frutas
('Banana', 'frutas', 0.00),
('Morango', 'frutas', 1.50),
('Kiwi', 'frutas', 2.00),
('Manga', 'frutas', 1.00),
('Abacaxi', 'frutas', 1.00),
('Maçã', 'frutas', 0.50),
('Uva', 'frutas', 1.50),
('Mamão', 'frutas', 0.50),

-- Complementos
('Granola', 'complementos', 1.00),
('Aveia', 'complementos', 0.50),
('Castanha do Pará', 'complementos', 2.00),
('Amendoim', 'complementos', 1.00),
('Coco Ralado', 'complementos', 1.00),
('Chia', 'complementos', 1.50),
('Linhaça', 'complementos', 1.00),

-- Caldas
('Mel', 'caldas', 0.50),
('Leite Condensado', 'caldas', 1.50),
('Nutella', 'caldas', 3.00),
('Chocolate', 'caldas', 2.00),
('Caramelo', 'caldas', 2.00),

-- Granolas
('Granola Tradicional', 'granolas', 1.00),
('Granola de Chocolate', 'granolas', 1.50),
('Granola sem Açúcar', 'granolas', 1.50);

-- Produtos
INSERT INTO `products` (`name`, `description`, `price`, `category_id`, `size_ml`, `max_ingredients`, `size_order`) VALUES
-- Açaís
('Açaí Pequeno (300ml)', 'Açaí natural cremoso servido no copo', 12.00, 1, 300, 3, 1),
('Açaí Médio (500ml)', 'Açaí natural cremoso servido no copo', 18.00, 1, 500, 5, 2),
('Açaí Grande (700ml)', 'Açaí natural cremoso servido no copo', 25.00, 1, 700, 7, 3),
('Açaí Família (1L)', 'Açaí natural cremoso ideal para compartilhar', 35.00, 1, 1000, 10, 4),

-- Vitaminas
('Vitamina de Banana', 'Vitamina cremosa com banana e leite', 8.00, 2, NULL, NULL, 0),
('Vitamina de Morango', 'Vitamina refrescante com morango e leite', 10.00, 2, NULL, NULL, 0),
('Vitamina de Abacate', 'Vitamina nutritiva com abacate e mel', 12.00, 2, NULL, NULL, 0),

-- Lanches
('Sanduíche Natural', 'Pão integral com peito de peru, queijo branco e salada', 15.00, 3, NULL, NULL, 0),
('Wrap de Frango', 'Wrap integral com frango grelhado e vegetais', 18.00, 3, NULL, NULL, 0),

-- Bebidas
('Suco de Laranja (300ml)', 'Suco natural de laranja', 6.00, 4, NULL, NULL, 0),
('Suco de Limão (300ml)', 'Suco natural de limão', 5.50, 4, NULL, NULL, 0),
('Água de Coco (300ml)', 'Água de coco natural gelada', 4.50, 4, NULL, NULL, 0);

-- Adiciona as chaves estrangeiras após inserção dos dados
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

-- Finaliza transação
COMMIT;
