<?php
// Inclui o arquivo de configuração do banco de dados
require_once '../src/Core/Database.php';

// Obtém a conexão com o banco de dados
$pdo = \Core\Database::getInstance()->getConnection();

// Função para obter o estoque disponível de um produto
function getStock($pdo, $produtoId) {
    $stmt = $pdo->prepare("SELECT quantidade_estoque FROM produtos WHERE id = :produto_id");
    $stmt->bindParam(':produto_id', $produtoId, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchColumn();
}

// Função para obter o histórico de cliques ou compras do usuário
function getUserHistory($pdo, $userId) {
    $stmt = $pdo->prepare("
        SELECT produto_id 
        FROM cliques 
        WHERE usuario_id = :user_id 
        GROUP BY produto_id 
        ORDER BY COUNT(*) DESC 
        LIMIT 30
    ");
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

// Função para obter produtos no carrinho do usuário
function getCartItems($pdo, $userId) {
    $stmt = $pdo->prepare("
        SELECT produto_id 
        FROM carrinho 
        WHERE usuario_id = :user_id
    ");
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

// Inicia a sessão e verifica se o usuário está autenticado
session_start();

// Protege contra ataques de sessão
if (session_status() === PHP_SESSION_ACTIVE) {
    session_regenerate_id(true);
}

$userId = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : null;

// Obtém os produtos e categorias
$stmt = $pdo->query("SELECT * FROM produtos");
$produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->query("SELECT * FROM categorias");
$categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtém o histórico de produtos mais clicados ou comprados para o usuário autenticado
$historicoIds = [];
if ($userId) {
    $historicoIds = getUserHistory($pdo, $userId);
}

// Obtém os produtos no carrinho
$cartItems = [];
if ($userId) {
    $cartItems = getCartItems($pdo, $userId);
}

// Debug: Verifique se $cartItems está correto
// var_dump($cartItems);

// Filtra os produtos com base no histórico (exibir primeiro os mais clicados/comprados)
usort($produtos, function($a, $b) use ($historicoIds) {
    $posA = array_search($a['id'], $historicoIds);
    $posB = array_search($b['id'], $historicoIds);
    return ($posA === false ? PHP_INT_MAX : $posA) - ($posB === false ? PHP_INT_MAX : $posB);
});

// Remove produtos que estão no carrinho da lista de produtos relacionados
$filteredRelatedProducts = array_filter($produtos, function($produto) use ($cartItems) {
    return !in_array($produto['id'], $cartItems);
});

// Debug: Verifique se $filteredRelatedProducts está correto
// var_dump($filteredRelatedProducts);

// Obtém o banner (substitua o ID conforme necessário)
$stmt = $pdo->prepare("SELECT * FROM banners WHERE id = :banner_id");
$stmt->bindValue(':banner_id', 1, PDO::PARAM_INT); // Ajuste o ID conforme necessário
$stmt->execute();
$banner = $stmt->fetch(PDO::FETCH_ASSOC);

// Verifica se o banner foi encontrado e é um array antes de acessar seus índices
$bannerNome = $bannerLink = $bannerLogo = $bannerTitulo = '';
if (is_array($banner)) {
    $bannerNome = htmlspecialchars($banner['nome_banner'], ENT_QUOTES, 'UTF-8');
    $bannerLink = htmlspecialchars($banner['link'], ENT_QUOTES, 'UTF-8');
    $bannerLogo = htmlspecialchars($banner['logo'], ENT_QUOTES, 'UTF-8');
    $bannerTitulo = htmlspecialchars($banner['titulo'], ENT_QUOTES, 'UTF-8');
}

// Obtém os últimos produtos vistos ou aleatórios na primeira visita
$lastViewedProducts = [];
if ($userId) {
    $stmt = $pdo->prepare("
        SELECT * 
        FROM produtos 
        WHERE id IN (
            SELECT produto_id 
            FROM cliques 
            WHERE usuario_id = :user_id 
            ORDER BY MAX(data_clique) DESC 
            LIMIT 6
        )
    ");
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmt->execute();
    $lastViewedProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $lastViewedProducts = $pdo->query("SELECT * FROM produtos ORDER BY RAND() LIMIT 6")->fetchAll(PDO::FETCH_ASSOC);
}

$banners = $pdo->query("SELECT nome_banner, link FROM banners")->fetchAll(PDO::FETCH_ASSOC);

?>


<!DOCTYPE html>
<html lang="pt-br">
<script>
        function addStylesheet(href) {
            var link = document.createElement('link');
            link.rel = 'stylesheet';
            link.type = 'text/css';
            link.href = href;

            // Adiciona manipuladores de eventos para verificar se o CSS foi carregado com sucesso
            link.onload = function() {
                console.log('CSS carregado com sucesso:', href);
            };
            link.onerror = function() {
                console.error('Erro ao carregar CSS:', href);
            };

            document.head.appendChild(link);
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Lista de arquivos CSS
            var cssFiles = [
                'css/form_categorias.css',
                'css/form_menu_categoria.css',
                'css/form_play.css',
                'css/form_promcoes.css',
                'css/form_vistos.css',
                'css/form_produtos.css',
                'css/styler.css',
                'css/form_carrinho.css'
            ];

            // Adiciona cada arquivo CSS à página
            cssFiles.forEach(function(file) {
                addStylesheet(file);
            });
        });
    </script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>S A Super Mercado</title>
    <meta name="description" content="S A Super Mercado oferece uma ampla variedade de produtos frescos e de alta qualidade. Encontre frutas, verduras, carnes, pães e muito mais com preços imbatíveis. Faça suas compras online com facilidade!">
    <meta name="keywords" content="supermercado, compras online, frutas, verduras, carnes, pães, mercado, alimentos frescos">
    <meta name="author" content="S A Super Mercado">
    <meta property="og:title" content="S A Super Mercado">
    <meta property="og:description" content="Encontre tudo o que você precisa para sua casa no S A Super Mercado. Produtos frescos, ofertas especiais e uma experiência de compra conveniente.">
    <meta property="og:image" content="public/img/icone.png">
    <meta property="og:url" content="https://saas.techxx.com.br">
    <meta property="og:type" content="website">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="S A Super Mercado">
    <meta name="twitter:description" content="Compre alimentos frescos e muito mais no S A Super Mercado. Acesse nosso site para ofertas e novidades!">
    <meta name="twitter:image" content="public/img/icone.png">
    <link rel="icon" href="public/img/icone.png" type="image/png">
</head>
<body>
<header class="header">
    <div class="header-container">
        <!-- Logo Section -->
        <div class="logo">
            <img src="img/logo-banner.png" class="logo-img" alt="🧺S A Super">
        </div>

        <!-- Search Bar Section -->
        <form id="search-form" action="search_results.php" method="get">
            <div class="search-container">
                <input type="text" id="search-input" name="query" placeholder="Digite o que você quer encontrar...">
                <button type="submit" class="search-icon">
                    <img src="img/search-icon.png" alt="Buscar">
                </button>
            </div>
            <input type="hidden" id="search-type" name="type" value="product">
        </form>

        <!-- Cart Notification Section -->
        <div class="cart-notification">
            <img id="cart-icon" src="img/cart-icon.png" alt="Carrinho">
            <span id="cart-count" class="cart-count">0</span>
        </div>
    </div>
<!-- Seção com degradê -->
<div class="gradient-section">
    <!-- Seção 5: Categorias -->
    <section class="new-categories-section">
        <div class="new-categories-carousel">
            <div class="new-categories-list">
                <?php foreach ($categorias as $categoria): ?>
                    <div class="new-category-item">
                        <a href="categoria.php?id=<?= htmlspecialchars($categoria['id'], ENT_QUOTES, 'UTF-8') ?>" class="new-category-link">
                            <div class="new-category-image-wrapper">
                                <img src="<?= htmlspecialchars($categoria['imagem_link'], ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($categoria['nome'], ENT_QUOTES, 'UTF-8') ?>" class="new-category-image">
                            </div>
                            <p class="new-category-name"><?= htmlspecialchars($categoria['nome'], ENT_QUOTES, 'UTF-8') ?></p>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
</div>

<main>
        <!-- Carrinho de Compras -->
        <div id="shopping-cart" class="shopping-cart">
            <!-- Botão para fechar o carrinho -->
            <button id="close-cart" class="close-cart" aria-label="Fechar Carrinho">&times;</button>
            
            <!-- Título do carrinho -->
            <h2 class="cart-title">Carrinho de Compras</h2>
            
            <!-- Área de itens do carrinho -->
            <div id="cart-items" class="cart-items">
                <!-- Mensagem de erro (escondida por padrão) -->
                <div id="error-message" class="error-message" style="display: none;"></div>
                
                <!-- Corpo do carrinho onde os itens serão inseridos -->
                <div class="cart-body">
                    <!-- Exemplo de item no carrinho -->
                    <div class="cart-item">
                        <!-- Cabeçalho do item -->
                        <div class="item-header">
                            <div class="name-box">Nome do Produto</div>
                        </div>
                        
                        <!-- Conteúdo do item -->
                        <div class="item-content">
                            <!-- Imagem do produto -->
                            <div class="photo-cell">
                                <img src="path/to/photo.jpg" alt="Produto" class="product-image">
                            </div>
                            
                            <!-- Detalhes do item -->
                            <div class="details-cell">
                                <!-- Preço do item -->
                                <div class="price">R$ 15,00</div>
                                
                                <!-- Quantidade do item -->
                                <div class="quantity-container">
                                    <button class="quantity-decrease">-</button>
                                    <input type="number" class="quantity-input" value="1" min="1">
                                    <button class="quantity-increase">+</button>
                                </div>
                                
                                <!-- Botão para remover o item do carrinho -->
                                <button class="remove-from-cart">&times;</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Resumo do carrinho -->
            <div class="cart-summary">
            <!-- Preço total -->
            <p class="total-price" id="total-price">Total: R$ 0,00</p>
            
            <!-- Contêiner para os botões de ação -->
            <div class="cart-summary-buttons">
                <button class="clear-cart" id="clear-cart">Limpar Carrinho</button>
                <button class="finalize-order" id="finalize-order">Finalizar Pedido</button>
            </div>
        </div>

</div>
<script src="form_carrinho.js"></script>
<SCRIPT>
    document.addEventListener("DOMContentLoaded", function () {
    // Verifica se existe um ID de categoria na URL
    const urlParams = new URLSearchParams(window.location.search);
    const categoriaId = urlParams.get('id');

    if (categoriaId) {
        fetch(`api.php?id=${categoriaId}`)
            .then(response => response.json())
            .then(data => {
                // Aqui você pode manipular os dados e renderizar os produtos da categoria na página
                console.log(data.produtos);
                // Exemplo: Atualizar o DOM com produtos
            })
            .catch(error => console.error('Erro ao carregar os produtos:', error));
    }
});
 
</SCRIPT>


</header>