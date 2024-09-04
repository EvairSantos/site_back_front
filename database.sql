
-- Copiando estrutura do banco de dados para mercadinho
DROP DATABASE IF EXISTS `mercadinho`;
CREATE DATABASE IF NOT EXISTS `mercadinho` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `mercadinho`;

-- Copiando estrutura para tabela mercadinho.banners
DROP TABLE IF EXISTS `banners`;
CREATE TABLE IF NOT EXISTS `banners` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome_banner` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `link` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `logo` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `titulo` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Copiando dados para a tabela mercadinho.banners: ~4 rows (aproximadamente)
INSERT INTO `banners` (`id`, `nome_banner`, `link`, `logo`, `titulo`) VALUES
	(2, 'BANNER_PROMOCAO2', 'https://http2.mlstatic.com/storage/mshops-appearance-api/images/34/b80e7f3c62554b1e89884a1ec3c2b7b71508557134/banner-2023102713131165106.jpg', 'https://i.imgur.com/7zzcguC.jpeg', 'Sddd'),
	(5, 'BANNER_INSPIRADOS4', 'https://marketplace.canva.com/EAFVRPEMDn8/1/0/1600w/canva-banner-promo%C3%A7%C3%A3o-queima-de-estoque-moderno-preto-TcPDOFo6MDw.jpg', 'https://marketplace.canva.com/EAFVRPEMDn8/1/0/1600w/canva-banner-promo%C3%A7%C3%A3o-queima-de-estoque-moderno-preto-TcPDOFo6MDw.jpg', 'Queima de estoque'),
	(6, 'MERCADO_PLAY.1', 'https://img.freepik.com/vetores-premium/ilustracao-com-uma-bola-de-futebol-realista-voando-para-a-rede-de-um-gol-de-futebol-contra-o-pano-de-fundo-das-arquibancadas-do-estadio_444390-18311.jpg', 'http://localhost/public/img/iconeplay.png', 'https://wa.me/5593991135066'),
	(8, 'BANNER_PROMOCAO1', 'https://img.freepik.com/vetores-premium/ilustracao-com-uma-bola-de-futebol-realista-voando-para-a-rede-de-um-gol-de-futebol-contra-o-pano-de-fundo-das-arquibancadas-do-estadio_444390-18311.jpg', 'https://img.freepik.com/vetores-premium/ilustracao-com-uma-bola-de-futebol-realista-voando-para-a-rede-de-um-gol-de-futebol-contra-o-pano-de-fundo-das-arquibancadas-do-estadio_444390-18311.jpg', 'BANNER_PROMOCAO1');

-- Copiando estrutura para tabela mercadinho.carrinhos
DROP TABLE IF EXISTS `carrinhos`;
CREATE TABLE IF NOT EXISTS `carrinhos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `produto_id` int DEFAULT NULL,
  `quantidade` int NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_carrinhos_produto` (`produto_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Copiando dados para a tabela mercadinho.carrinhos: ~0 rows (aproximadamente)

-- Copiando estrutura para tabela mercadinho.categorias
DROP TABLE IF EXISTS `categorias`;
CREATE TABLE IF NOT EXISTS `categorias` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `descricao` text COLLATE utf8mb4_general_ci,
  `data_criacao` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `imagem_link` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nome` (`nome`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Copiando dados para a tabela mercadinho.categorias: ~10 rows (aproximadamente)
INSERT INTO `categorias` (`id`, `nome`, `descricao`, `data_criacao`, `imagem_link`) VALUES
	(13, 'Produtos de Limpeza', 'Detergentes, desinfetantes e produtos para limpeza geral.', '2024-08-22 06:21:25', 'http://http2.mlstatic.com/D_666039-MLB76845150150_062024-O.jpg'),
	(14, 'Higiene Pessoal', 'Sabonetes, shampoos e produtos de cuidados pessoais.', '2024-08-22 06:21:25', 'http://http2.mlstatic.com/D_839393-MLB75933325225_042024-O.jpg'),
	(15, 'Mercearia', 'Enlatados, produtos secos e snacks.', '2024-08-22 06:21:25', 'http://http2.mlstatic.com/D_821874-MLU78449018041_082024-O.jpg'),
	(16, 'Bebidas Alcoólicas', 'Cervejas, vinhos e destilados.', '2024-08-22 06:21:25', 'http://http2.mlstatic.com/D_798382-MLB72712618130_112023-O.jpg'),
	(17, 'Produtos para Bebês', 'Fraldas, alimentos e cuidados para bebês.', '2024-08-22 06:21:25', 'http://http2.mlstatic.com/D_710951-MLB70211509042_062023-O.jpg'),
	(19, 'Congelados', 'Comidas prontas e produtos congelados.', '2024-08-22 06:21:25', 'http://http2.mlstatic.com/D_613585-MLB44759591696_012021-O.jpg'),
	(20, 'Padaria', 'Pães frescos, bolos e tortas.', '2024-08-22 06:21:25', 'http://http2.mlstatic.com/D_777416-MLB74861613976_032024-O.jpg'),
	(21, 'Alimentos', 'Alimentos TESTES', '2024-08-29 16:01:32', 'http://http2.mlstatic.com/D_793396-MLB76567106530_062024-O.jpg'),
	(22, 'Cervejas', 'Cervejas', '2024-08-30 05:36:00', 'https://http2.mlstatic.com/storage/mshops-appearance-api/images/34/b80e7f3c62554b1e89884a1ec3c2b7b71508557134/banner-2023102713131165106.jpg'),
	(23, 'Cuidados com o Cabelo', 'Cuidados com o Cabelo', '2024-08-30 05:38:46', 'http://http2.mlstatic.com/D_815943-MLU77375460100_072024-O.jpg');

-- Copiando estrutura para tabela mercadinho.cliques
DROP TABLE IF EXISTS `cliques`;
CREATE TABLE IF NOT EXISTS `cliques` (
  `id` int NOT NULL AUTO_INCREMENT,
  `produto_id` int DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_cliques_produto` (`produto_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Copiando dados para a tabela mercadinho.cliques: ~0 rows (aproximadamente)

-- Copiando estrutura para tabela mercadinho.compras
DROP TABLE IF EXISTS `compras`;
CREATE TABLE IF NOT EXISTS `compras` (
  `id` int NOT NULL AUTO_INCREMENT,
  `produto_id` int DEFAULT NULL,
  `quantidade` int NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_compras_produto` (`produto_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Copiando dados para a tabela mercadinho.compras: ~0 rows (aproximadamente)

-- Copiando estrutura para tabela mercadinho.estoque
DROP TABLE IF EXISTS `estoque`;
CREATE TABLE IF NOT EXISTS `estoque` (
  `id` int NOT NULL AUTO_INCREMENT,
  `produto_id` int DEFAULT NULL,
  `quantidade` int NOT NULL,
  `data_entrada` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `produto_id` (`produto_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Copiando dados para a tabela mercadinho.estoque: ~0 rows (aproximadamente)

-- Copiando estrutura para tabela mercadinho.itens_pedido
DROP TABLE IF EXISTS `itens_pedido`;
CREATE TABLE IF NOT EXISTS `itens_pedido` (
  `id` int NOT NULL AUTO_INCREMENT,
  `pedido_id` int DEFAULT NULL,
  `produto_id` int DEFAULT NULL,
  `quantidade` int NOT NULL,
  `preco` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `pedido_id` (`pedido_id`),
  KEY `produto_id` (`produto_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Copiando dados para a tabela mercadinho.itens_pedido: ~0 rows (aproximadamente)

-- Copiando estrutura para tabela mercadinho.orders
DROP TABLE IF EXISTS `orders`;
CREATE TABLE IF NOT EXISTS `orders` (
  `id` int NOT NULL AUTO_INCREMENT,
  `order_id` varchar(255) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_id` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Copiando dados para a tabela mercadinho.orders: ~0 rows (aproximadamente)

-- Copiando estrutura para tabela mercadinho.order_items
DROP TABLE IF EXISTS `order_items`;
CREATE TABLE IF NOT EXISTS `order_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `order_id` varchar(255) NOT NULL,
  `product_id` int NOT NULL,
  `quantity` int NOT NULL,
  `price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Copiando dados para a tabela mercadinho.order_items: ~0 rows (aproximadamente)

-- Copiando estrutura para tabela mercadinho.pedidos
DROP TABLE IF EXISTS `pedidos`;
CREATE TABLE IF NOT EXISTS `pedidos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `usuario_id` int DEFAULT NULL,
  `data_pedido` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('pendente','em processamento','concluído','cancelado') COLLATE utf8mb4_general_ci DEFAULT 'pendente',
  `total` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `usuario_id` (`usuario_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Copiando dados para a tabela mercadinho.pedidos: ~0 rows (aproximadamente)

-- Copiando estrutura para tabela mercadinho.produtos
DROP TABLE IF EXISTS `produtos`;
CREATE TABLE IF NOT EXISTS `produtos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `descricao` text COLLATE utf8mb4_general_ci,
  `preco` decimal(10,2) NOT NULL,
  `quantidade_estoque` int NOT NULL,
  `categoria_id` int DEFAULT NULL,
  `data_criacao` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `imagem` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `categoria_id` (`categoria_id`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Copiando dados para a tabela mercadinho.produtos: ~27 rows (aproximadamente)
INSERT INTO `produtos` (`id`, `nome`, `descricao`, `preco`, `quantidade_estoque`, `categoria_id`, `data_criacao`, `imagem`) VALUES
	(4, 'CERVEJA SKOL', 'Este é um teste de cerveja skol', 0.10, 1, 16, '2024-08-21 19:49:57', 'https://http2.mlstatic.com/D_NQ_NP_2X_866730-MLB68822864479_032023-F.webp'),
	(5, 'Feijão Carioca Cores Kicaldo Em Pacote Sem Glúten 1 Kg', 'O que você precisa saber sobre este produto    Unidades por kit: 1    Tipo de embalagem: Pacote    Peso líquido: 1 kg    Sem glúten.    Embalagem de 1 kg.', 6.89, 2, 12, '2024-08-22 00:13:19', 'http://http2.mlstatic.com/D_881816-MLU74537678531_022024-O.jpg'),
	(7, 'Óleo De Soja Liza Pet 900ml', 'Teste', 7.15, 20, 15, '2024-08-22 00:21:42', 'https://th.bing.com/th/id/OIP.uZRfj2i4B1td04nWcsjovQHaHa?rs=1&pid=ImgDetMain'),
	(8, 'Detergente Neutro Ypê 500ml', 'Teste', 3.69, 30, 13, '2024-08-22 00:23:20', 'https://th.bing.com/th?id=OIP.E5diTv9DMURnGbfdXwZRWQHaHa&w=250&h=250&c=8&rs=1&qlt=90&o=6&dpr=1.3&pid=3.1&rm=2'),
	(9, 'Sabonete Liquido Figo Ambarado 200ml - L\'envie', 'Sabonete íntimo feminino Vichy', 129.00, 0, 14, '2024-08-22 00:26:43', 'https://http2.mlstatic.com/D_NQ_NP_686136-MLU75131608899_032024-O.webp'),
	(11, 'Leite Condensado Moça 395g', 'Leite Condensado Moça 395g', 6.20, 40, 15, '2024-08-22 00:32:51', 'https://http2.mlstatic.com/D_NQ_NP_2X_667383-MLU69999836766_062023-F.webp'),
	(12, 'Pão de Forma Pullman 680g', 'Pão de Forma Pullman 680g', 17.80, 20, 20, '2024-08-22 00:35:12', 'https://http2.mlstatic.com/D_NQ_NP_2X_717193-MLU74220039915_012024-F.webp'),
	(13, 'Cerveja Skol Pilsen Com 15 Unidades De 269ml', 'https://http2.mlstatic.com/D_NQ_NP_2X_937194-MLB75983310079_042024-F.webp', 45.00, 10, 16, '2024-08-29 02:23:01', 'https://http2.mlstatic.com/D_NQ_NP_2X_937194-MLB75983310079_042024-F.webp'),
	(14, 'Cerveja Heineken Premium Garrafa 6 Long Neck 330ml', 'Cerveja Heineken Premium Garrafa 6 Long Neck 330ml', 45.00, 10, 16, '2024-08-29 02:24:14', 'https://http2.mlstatic.com/D_NQ_NP_2X_627965-MLA74957014119_032024-F.webp'),
	(15, 'Leite Pó Integral Ninho Forti+ Lata 380g Nestle Infantil ', 'Leite Pó Integral Ninho Forti+ Lata 380g Nestle Infantil ', 20.00, 5, 17, '2024-08-29 02:27:28', 'https://http2.mlstatic.com/D_NQ_NP_2X_751847-MLU78424497971_082024-F.webp'),
	(16, 'Amaciante Concentrado Downy Brisa De Verão 1l', 'Amaciante Concentrado Downy Brisa De Verão 1l', 18.70, 2, 13, '2024-08-29 02:34:47', 'https://http2.mlstatic.com/D_NQ_NP_2X_919365-MLA74781980159_022024-F.webp'),
	(17, 'Brilhante lava roupas líquido limpeza total 3 litros ', 'Brilhante lava roupas líquido limpeza total 3 litros ', 27.50, 3, 13, '2024-08-29 02:36:06', 'https://http2.mlstatic.com/D_NQ_NP_2X_673393-MLU72748111401_112023-F.webp'),
	(18, 'TESTE', 'TESTE', 10.00, 10, 17, '2024-08-29 03:37:20', 'https://http2.mlstatic.com/D_NQ_NP_2X_866730-MLB68822864479_032023-F.webp'),
	(19, 'OLEO VINAGRE', 'Óleo De Gergelim Torrado Kenko Pote 100ml', 10.00, 10, 1, '2024-08-29 04:08:28', 'http://http2.mlstatic.com/D_826262-MLB47482524514_092021-I.jpg'),
	(20, 'SABÃO HOMO', 'Sabão Líquido Omo Lavagem Perfeita 5l', 12.00, 2, 13, '2024-08-29 04:24:42', 'https://http2.mlstatic.com/D_678628-MLU70866090545_082023-I.jpg'),
	(21, 'Óleo De Soja Orgânico Coopernatural 500ml', 'Óleo De Soja Orgânico Coopernatural 500ml', 12.55, 1, 12, '2024-08-29 05:17:12', 'https://http2.mlstatic.com/D_719870-MLB51134301646_082022-O.jpg'),
	(22, 'Sabão Liquido Omo Pro Lavanderia Profissional 7 L', 'Sabão Liquido Omo Pro Lavanderia Profissional 7 L', 72.41, 1, 14, '2024-08-29 05:40:17', 'https://http2.mlstatic.com/D_934028-MLU69497205231_052023-O.jpg'),
	(23, 'Kit 15 Mudas De Banana, Sendo 5 Nanica 5 Maçã E 5 Pratas', 'Kit 15 Mudas De Banana, Sendo 5 Nanica 5 Maçã E 5 Pratas', 178.90, 2, 12, '2024-08-29 05:44:27', 'https://http2.mlstatic.com/D_936383-MLB77607213322_072024-O.jpg'),
	(24, 'Junta Tampa De Valvula Fiesta 1.0 1.6 Zetec Rocam Sabo 75022', 'Junta Tampa De Valvula Fiesta 1.0 1.6 Zetec Rocam Sabo 75022', 41.90, 1, 15, '2024-08-29 05:52:45', 'http://http2.mlstatic.com/D_715839-MLB53828449696_022023-O.jpg'),
	(25, 'Amendoim Dori Tipo Japonês Pct 500g', 'Amendoim Dori Tipo Japonês Pct 500g', 35.90, 1, 15, '2024-08-29 12:12:11', 'http://http2.mlstatic.com/D_834486-MLB48822308144_012022-O.jpg'),
	(26, 'Chocolate Ao Leite M&m\'s 148g', 'Chocolate Ao Leite M&m\'s 148g', 9.89, 2, 15, '2024-08-29 19:50:02', 'http://http2.mlstatic.com/D_933329-MLU74227164767_012024-O.jpg'),
	(27, 'Kit 27un Achocolatado Pirakids Piracanjuba 200ml', 'Kit 27un Achocolatado Pirakids Piracanjuba 200ml', 49.88, 2, 15, '2024-08-29 19:57:48', 'http://http2.mlstatic.com/D_987528-MLU76906458782_062024-O.jpg'),
	(28, 'Creme Dental Menta Original Colgate Tripla Ação Caixa 180g Preço Especial', 'Creme Dental Menta Original Colgate Tripla Ação Caixa 180g Preço Especial', 6.85, 1, 15, '2024-08-29 20:00:04', 'http://http2.mlstatic.com/D_964389-MLU75402447920_042024-O.jpg'),
	(29, 'Kit 2 Escovas De Dente Infantil Tandy Colgate Extra Macia', 'Kit 2 Escovas De Dente Infantil Tandy Colgate Extra Macia', 24.90, 2, 15, '2024-08-29 20:01:34', 'http://http2.mlstatic.com/D_740378-MLB78440931225_082024-O.jpg'),
	(30, 'Neugebauer Chocolate 40% Cacau Ao Leite Barra 80g', 'Neugebauer Chocolate 40% Cacau Ao Leite Barra 80g', 8.00, 5, 15, '2024-08-29 20:06:54', 'http://http2.mlstatic.com/D_915120-MLB78455045820_082024-O.jpg'),
	(31, 'Salgadinho De Trigo Bacon Qualitá Pacote 100g', 'Salgadinho De Trigo Bacon Qualitá Pacote 100g', 5.99, 1, 15, '2024-08-29 23:18:34', 'http://http2.mlstatic.com/D_764656-MLU72700308311_112023-O.jpg'),
	(32, 'Biscoito Tortuguita De Morango Sem Sal 130 G', 'Biscoito Tortuguita De Morango Sem Sal 130 G', 12.94, 2, 15, '2024-08-29 23:20:24', 'http://http2.mlstatic.com/D_903306-MLU74225005545_012024-O.jpg'),
	(33, 'Bala Butter Toffees  Caramelo Intense 53% Cacau 90g', 'Bala Butter Toffees  Caramelo Intense 53% Cacau 90g', 8.00, 1, 15, '2024-08-29 23:22:51', 'http://http2.mlstatic.com/D_832236-MLB73978226388_012024-O.jpg'),
	(34, 'Chocolate Lacta Caixa Bis Xtra Black 24un X 45g', 'Chocolate Lacta Caixa Bis Xtra Black 24un X 45g', 2.99, 1, 15, '2024-08-29 23:24:03', 'http://http2.mlstatic.com/D_810453-MLU75006631716_032024-O.jpg'),
	(35, 'Chocolate Bibs Sticks Ao Leite Caixa C/16un De 32g ', 'Chocolate Bibs Sticks Ao Leite Caixa C/16un De 32g ', 24.98, 12, 15, '2024-08-29 23:25:41', 'http://http2.mlstatic.com/D_823661-MLU74431376168_022024-O.jpg');

-- Copiando estrutura para tabela mercadinho.promocoes
DROP TABLE IF EXISTS `promocoes`;
CREATE TABLE IF NOT EXISTS `promocoes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `titulo` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `descricao` text COLLATE utf8mb4_general_ci,
  `imagem` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `produto_id` int NOT NULL,
  `data_inicio` date NOT NULL,
  `data_fim` date NOT NULL,
  PRIMARY KEY (`id`),
  KEY `produto_id` (`produto_id`),
  CONSTRAINT `promocoes_ibfk_1` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Copiando dados para a tabela mercadinho.promocoes: ~5 rows (aproximadamente)
INSERT INTO `promocoes` (`id`, `titulo`, `descricao`, `imagem`, `produto_id`, `data_inicio`, `data_fim`) VALUES
	(1, 'Super desconto no amaciante ', 'Super desconto no amaciante ', 'https://http2.mlstatic.com/D_NQ_NP_2X_919365-MLA74781980159_022024-F.webp', 16, '2024-08-22', '2024-08-31'),
	(2, 'Promoção de Feijão', '\r\nFeijão Carioca Cores Kicaldo Em Pacote Sem Glúten 1 Kg', 'http://http2.mlstatic.com/D_634018-MLU77963946262_082024-O.jpg', 5, '2024-08-22', '2024-08-31'),
	(3, 'Óleo em Oferta', 'Óleo De Soja Liza com desconto', 'https://th.bing.com/th/id/OIP.uZRfj2i4B1td04nWcsjovQHaHa?rs=1&pid=ImgDetMain', 7, '2024-08-22', '2024-08-31'),
	(5, 'teste', 'teste', 'https://http2.mlstatic.com/D_NQ_NP_2X_937194-MLB75983310079_042024-F.webp', 13, '2024-08-29', '2024-09-02'),
	(6, 'Eva Thaisa', 'Santos de Andrade', 'http://http2.mlstatic.com/D_740378-MLB78440931225_082024-O.jpg', 29, '2024-08-29', '2024-09-03'),
	(7, 'Lava Roupas Líquido', 'Lava Roupas Líquido Limpeza Total 3 Litros Brilhante', 'http://http2.mlstatic.com/D_898322-MLB71782820531_092023-O.jpg', 17, '2024-08-30', '2024-08-31');

-- Copiando estrutura para tabela mercadinho.usuarios
DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `senha` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `endereco` text COLLATE utf8mb4_general_ci,
  `telefone` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `data_criacao` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Copiando dados para a tabela mercadinho.usuarios: ~0 rows (aproximadamente)

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
