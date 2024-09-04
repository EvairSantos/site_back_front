-- Criação da tabela `carrinhos`
CREATE TABLE `carrinhos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `produto_id` int(11) DEFAULT NULL,
  `quantidade` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_carrinhos_produto` (`produto_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Criação da tabela `categorias`
CREATE TABLE `categorias` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `descricao` text DEFAULT NULL,
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp(),
  `imagem_link` VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nome` (`nome`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Criação da tabela `cliques`
CREATE TABLE `cliques` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `produto_id` int(11) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_cliques_produto` (`produto_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Criação da tabela `compras`
CREATE TABLE `compras` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `produto_id` int(11) DEFAULT NULL,
  `quantidade` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_compras_produto` (`produto_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Criação da tabela `estoque`
CREATE TABLE `estoque` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `produto_id` int(11) DEFAULT NULL,
  `quantidade` int(11) NOT NULL,
  `data_entrada` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `produto_id` (`produto_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Criação da tabela `itens_pedido`
CREATE TABLE `itens_pedido` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pedido_id` int(11) DEFAULT NULL,
  `produto_id` int(11) DEFAULT NULL,
  `quantidade` int(11) NOT NULL,
  `preco` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `pedido_id` (`pedido_id`),
  KEY `produto_id` (`produto_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Criação da tabela `pedidos`
CREATE TABLE `pedidos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) DEFAULT NULL,
  `data_pedido` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pendente','em processamento','concluído','cancelado') DEFAULT 'pendente',
  `total` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `usuario_id` (`usuario_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Criação da tabela `produtos`
CREATE TABLE `produtos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `descricao` text DEFAULT NULL,
  `preco` decimal(10,2) NOT NULL,
  `quantidade_estoque` int(11) NOT NULL,
  `categoria_id` int(11) DEFAULT NULL,
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp(),
  `imagem` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `categoria_id` (`categoria_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Criação da tabela `usuarios`
CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `endereco` text DEFAULT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Criação da tabela `promocoes`
CREATE TABLE `promocoes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titulo` varchar(255) NOT NULL,
  `descricao` text DEFAULT NULL,
  `imagem` varchar(255) NOT NULL,
  `produto_id` int(11) NOT NULL,
  `data_inicio` date NOT NULL,
  `data_fim` date NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`produto_id`) REFERENCES `produtos`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Criação da tabela `banners`
CREATE TABLE `banners` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `nome_banner` VARCHAR(255) NOT NULL,
    `link` VARCHAR(255) NOT NULL,
    `logo` VARCHAR(255) NOT NULL,
    `titulo` VARCHAR(255) NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id VARCHAR(255) UNIQUE NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id VARCHAR(255) NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(order_id)
);


-- Inserção de dados na tabela `categorias`
INSERT INTO `categorias` (`id`, `nome`, `descricao`, `data_criacao`) VALUES
(12, 'Alimentos', 'Frutas, legumes, carnes, laticínios e mais.', '2024-08-22 03:21:25'),
(13, 'Produtos de Limpeza', 'Detergentes, desinfetantes e produtos para limpeza geral.', '2024-08-22 03:21:25'),
(14, 'Higiene Pessoal', 'Sabonetes, shampoos e produtos de cuidados pessoais.', '2024-08-22 03:21:25'),
(15, 'Mercearia', 'Enlatados, produtos secos e snacks.', '2024-08-22 03:21:25'),
(16, 'Bebidas Alcoólicas', 'Cervejas, vinhos e destilados.', '2024-08-22 03:21:25'),
(17, 'Produtos para Bebês', 'Fraldas, alimentos e cuidados para bebês.', '2024-08-22 03:21:25'),
(18, 'Farmácia e Saúde', 'Medicamentos, suplementos e primeiros socorros.', '2024-08-22 03:21:25'),
(19, 'Congelados', 'Comidas prontas e produtos congelados.', '2024-08-22 03:21:25'),
(20, 'Padaria', 'Pães frescos, bolos e tortas.', '2024-08-22 03:21:25');

-- Inserção de dados na tabela `produtos`
INSERT INTO `produtos` (`id`, `nome`, `descricao`, `preco`, `quantidade_estoque`, `categoria_id`, `data_criacao`, `imagem`) VALUES
(4, 'CERVEJA SKOL', 'Este é um teste de cerveja skol', 15.00, 10, 16, '2024-08-21 16:49:57', 'https://cf.shopee.com.br/file/676cbf40cc4e59fd1a91d5fd63dcab5e'),
(5, 'Feijão Carioca Cores Kicaldo Em Pacote Sem Glúten 1 Kg', 'O que você precisa saber sobre este produto\r\n\r\n    Unidades por kit: 1\r\n    Tipo de embalagem: Pacote\r\n    Peso líquido: 1 kg\r\n    Sem glúten.\r\n    Embalagem de 1 kg.', 6.89, 20, 12, '2024-08-21 21:13:19', 'img/66c6586f07313.webp'),
(7, 'Óleo De Soja Liza Pet 900ml', 'Teste', 7.15, 20, 15, '2024-08-21 21:21:42', 'https://img.kalunga.com.br/800x800/885048.jpg'),
(8, 'Detergente Neutro Ypê 500ml', 'Teste', 3.69, 30, 13, '2024-08-21 21:23:20', 'https://a-static.mlcdn.com.br/618x463/detergente-neutro-ype-500ml/brasfigueiredo/16386942520/11cb146c7a1e4da432ba90a84c742582.jpg'),
(9, 'Sabonete Líquido Íntimo Feminino Vichy 200ml', 'Sabonete íntimo feminino Vichy', 45.00, 15, 14, '2024-08-21 21:26:43', 'https://cf.shopee.com.br/file/84a0b7657b5a0cf3f0a8a03b93a396b2'),
(10, 'Papel Higiênico Neve Folha Dupla', 'Papel Higiênico Neve Folha Dupla', 11.50, 25, 14, '2024-08-21 21:31:04', 'https://cf.shopee.com.br/file/8365834a663391b7f63e1dc0aef5646a'),
(11, 'Leite Condensado Moça 395g', 'Leite Condensado Moça 395g', 6.20, 50, 15, '2024-08-21 21:32:51', 'https://cf.shopee.com.br/file/99c35a10a5728c6a4d4c5e0ebaa8f0e2'),
(12, 'Pão de Forma Pullman 680g', 'Pão de Forma Pullman 680g', 8.30, 30, 20, '2024-08-21 21:35:12', 'https://cf.shopee.com.br/file/82fa056a84b67d8b76f71792f2c3c52a');

-- Inserindo uma promoção para um produto específico
INSERT INTO `promocoes` (`titulo`, `descricao`, `imagem`, `produto_id`, `data_inicio`, `data_fim`) VALUES
('Super Desconto em Cerveja', 'Desconto especial em Cerveja Skol', 'https://cf.shopee.com.br/file/676cbf40cc4e59fd1a91d5fd63dcab5e', 4, '2024-08-22', '2024-08-31'),
('Promoção de Feijão', 'Feijão Carioca com preço promocional', 'img/66c6586f07313.webp', 5, '2024-08-22', '2024-08-31'),
('Óleo em Oferta', 'Óleo De Soja Liza com desconto', 'https://img.kalunga.com.br/800x800/885048.jpg', 7, '2024-08-22', '2024-08-31');
