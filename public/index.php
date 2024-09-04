<?php
// Inclui o arquivo de configuração do banco de dados
require_once '../Core/Database.php';

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
</header>

    <!-- Container para exibir os resultados -->
    <div id="search-results"></div>


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

<!-- Promoções Exclusivas -->
<section class="promocoes-section">
    <!-- Banner Principal -->
    <div class="promocoes-banner">
        <div class="carousel-wrapper">
            <div class="carousel-container">
                <!-- Itens do Carrossel -->
                <?php
                // Função para buscar banners
                function buscarBanners($pdo, $bannersNomes) {
                    $banners = [];
                    foreach ($bannersNomes as $nomeBanner) {
                        $stmt = $pdo->prepare("SELECT * FROM banners WHERE nome_banner = :nome_banner LIMIT 1");
                        $stmt->bindParam(':nome_banner', $nomeBanner);
                        $stmt->execute();
                        $banner = $stmt->fetch(PDO::FETCH_ASSOC);
                        if ($banner) {
                            $banners[] = $banner;
                        }
                    }
                    return $banners;
                }

                // Array contendo os nomes dos banners
                $bannersNomes = ['BANNER_PROMOCAO1', 'BANNER_PROMOCAO2', 'BANNER_PROMOCAO3', 'BANNER_PROMOCAO4'];
                $banners = buscarBanners($pdo, $bannersNomes);
                $bannerCount = count($banners);

                // Exibir banners
                foreach ($banners as $banner):
                    $bannerLink = htmlspecialchars($banner['link'], ENT_QUOTES, 'UTF-8');
                    $bannerTitle = htmlspecialchars($banner['titulo'], ENT_QUOTES, 'UTF-8');
                ?>
                    <div class="carousel-slide">
                        <a href="<?= $bannerLink ?>" target="_blank">
                            <img src="<?= $bannerLink ?>" alt="<?= $bannerTitle ?>">
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
            <button class="carousel-control left" aria-label="Previous Slide">&#9664;</button>
            <button class="carousel-control right" aria-label="Next Slide">&#9654;</button>
            <div class="carousel-indicators">
                <?php for ($i = 0; $i < $bannerCount; $i++): ?>
                    <span class="indicator" data-slide="<?= $i ?>"></span>
                <?php endfor; ?>
            </div>
        </div>
    </div>
</section>





<!-- Promoções -->
<section class="promocoes-section">
    <div class="promocoes-container">
      <!--  <h2 class="promocoes-text">Promoções Especiais</h2>-->
        <div class="promocoes-carrossel-wrapper">
            <div class="promocoes-carrossel">
                <!-- Aqui estarão os itens do carrossel -->
                <?php
                // Função para buscar promoções
                function buscarPromocoes($pdo) {
                    $sql = "SELECT * FROM promocoes WHERE data_inicio <= CURDATE() AND data_fim >= CURDATE()";
                    return $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
                }

                $promocoes = buscarPromocoes($pdo);

                // Exibir promoções
                foreach ($promocoes as $promocao):
                    $produtoId = htmlspecialchars($promocao['produto_id'], ENT_QUOTES, 'UTF-8');
                    $produtoUrl = "produto.php?id=" . $produtoId;
                    $imagem = htmlspecialchars($promocao['imagem'], ENT_QUOTES, 'UTF-8');
                    $titulo = htmlspecialchars($promocao['titulo'], ENT_QUOTES, 'UTF-8');
                    $descricao = htmlspecialchars($promocao['descricao'], ENT_QUOTES, 'UTF-8');
                ?>
                    <div class="promocao-item">
                        <a href="<?= $produtoUrl ?>" class="product-link" data-product-id="<?= $produtoId ?>">
                            <img src="<?= $imagem ?>" alt="<?= $titulo ?>">
                            <div class="promocao-info">
                                <h3><?= $titulo ?></h3>
                                <p><?= $descricao ?></p>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>


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

<!-- Contêiner para carregar o conteúdo do produto -->
<div id="produto-container"></div>
<!-- Seção Inspirado no Último Visto -->

<section class="inspired-section">
    <h2 class="form-text">Inspirado no Último Visto</h2>
    <div class="banner-container">
        <?php
        // Array contendo os nomes dos banners que você deseja buscar
        $bannersNomes = ['BANNER_INSPIRADOS1', 'BANNER_INSPIRADOS2', 'BANNER_INSPIRADOS3', 'BANNER_INSPIRADOS4'];

        // Loop para buscar e exibir cada banner
        foreach ($bannersNomes as $nomeBanner) {
            // Preparar e executar a consulta para buscar o banner
            $stmt = $pdo->prepare("SELECT * FROM banners WHERE nome_banner = :nome_banner LIMIT 1");
            $stmt->bindParam(':nome_banner', $nomeBanner);
            $stmt->execute();
            $banner = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verificar se o banner foi encontrado
            if ($banner): 
                $bannerLink = htmlspecialchars($banner['link'], ENT_QUOTES, 'UTF-8');
                $bannerTitle = htmlspecialchars($banner['titulo'], ENT_QUOTES, 'UTF-8');
            ?>
                <a href="<?= $bannerLink ?>" target="_blank">
                    <img src="<?= $bannerLink ?>" alt="<?= $bannerTitle ?>">
                </a>
            <?php else: ?>
                <!-- Exibição padrão ou mensagem caso o banner não seja encontrado -->
            <?php endif;
        }
        ?>
    </div>

    <div class="carousel-container">
    <div class="product-carousel">
        <?php
        if (isset($lastViewedProducts) && is_array($lastViewedProducts) && count($lastViewedProducts) > 0) {
            // Embaralha os produtos para garantir aleatoriedade
            shuffle($lastViewedProducts);

            // Limita o número de produtos a 10
            $produtosParaExibir = array_slice($lastViewedProducts, 0, 10);

            foreach ($produtosParaExibir as $produto) {
                $estoque = getStock($pdo, $produto['id']);
                $estoqueIndisponivel = $estoque <= 0;
        ?>
                <div class="product <?= $estoqueIndisponivel ? 'out-of-stock' : '' ?>">
                    <a href="#" class="product-link" data-product-id="<?= htmlspecialchars($produto['id'], ENT_QUOTES, 'UTF-8') ?>">
                        <img src="<?= htmlspecialchars($produto['imagem'], ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($produto['nome'], ENT_QUOTES, 'UTF-8') ?>" class="product-image">
                        <h3 class="product-name"><?= htmlspecialchars($produto['nome'], ENT_QUOTES, 'UTF-8') ?></h3>
                    </a>
                    <div class="product-info">
                        <p class="price">R$ <?= number_format($produto['preco'], 2, ',', '.') ?></p>
                        <?php if ($estoqueIndisponivel): ?>
                            <div class="out-of-stock-text">SEM ESTOQUE</div>
                        <?php else: ?>
                            <button class="add-to-cart" 
                                    data-id="<?= htmlspecialchars($produto['id'], ENT_QUOTES, 'UTF-8') ?>" 
                                    data-name="<?= htmlspecialchars($produto['nome'], ENT_QUOTES, 'UTF-8') ?>" 
                                    data-price="<?= htmlspecialchars($produto['preco'], ENT_QUOTES, 'UTF-8') ?>" 
                                    data-stock="<?= $estoque ?>"
                                    data-image="<?= htmlspecialchars($produto['imagem'], ENT_QUOTES, 'UTF-8') ?>">Adicionar ao Carrinho</button>
                            <div class="quantity-selector hidden">
                                <button class="quantity-decrease" data-product-id="<?= htmlspecialchars($produto['id'], ENT_QUOTES, 'UTF-8') ?>">-</button>
                                <input type="number" value="1" min="1" data-product-id="<?= htmlspecialchars($produto['id'], ENT_QUOTES, 'UTF-8') ?>">
                                <button class="quantity-increase" data-product-id="<?= htmlspecialchars($produto['id'], ENT_QUOTES, 'UTF-8') ?>">+</button>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
        <?php
            }
        } else {
            echo "<p>Nenhum produto visualizado recentemente.</p>";
        }
        ?>
    </div>
</div>

<!-- Seção Semelhantes ao que te interessa -->
<section class="related-section">
    <h2>Semelhantes ao que te interessa</h2>
    <div class="product-container">
        <?php if (isset($filteredRelatedProducts) && is_array($filteredRelatedProducts) && count($filteredRelatedProducts) > 0): ?>
            <?php
            // Embaralha os produtos para garantir aleatoriedade
            shuffle($filteredRelatedProducts);

            // Limita o número de produtos a 10
            $produtosParaExibir = array_slice($filteredRelatedProducts, 0, 10);

            foreach ($produtosParaExibir as $produto):
                $estoque = getStock($pdo, $produto['id']);
                $estoqueIndisponivel = $estoque <= 0;
                ?>
                <div class="product <?= $estoqueIndisponivel ? 'out-of-stock' : '' ?>">
                    <img src="<?= htmlspecialchars($produto['imagem'], ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($produto['nome'], ENT_QUOTES, 'UTF-8') ?>" loading="lazy" class="product-image">
                    <div class="product-info">
                        <h3><?= htmlspecialchars($produto['nome'], ENT_QUOTES, 'UTF-8') ?></h3>
                        <?php if ($estoqueIndisponivel): ?>
                            <div class="out-of-stock-text">SEM ESTOQUE</div>
                            <p class="price">R$ <?= number_format($produto['preco'], 2, ',', '.') ?></p>
                        <?php else: ?>
                            <p class="price">R$ <?= number_format($produto['preco'], 2, ',', '.') ?></p>
                            <button class="add-to-cart" 
                                    data-id="<?= htmlspecialchars($produto['id'], ENT_QUOTES, 'UTF-8') ?>" 
                                    data-name="<?= htmlspecialchars($produto['nome'], ENT_QUOTES, 'UTF-8') ?>" 
                                    data-price="<?= htmlspecialchars($produto['preco'], ENT_QUOTES, 'UTF-8') ?>" 
                                    data-stock="<?= $estoque ?>"
                                    data-image="<?= htmlspecialchars($produto['imagem'], ENT_QUOTES, 'UTF-8') ?>">Adicionar ao Carrinho</button>
                            <div class="quantity-selector hidden">
                                <button class="quantity-decrease" data-product-id="<?= htmlspecialchars($produto['id'], ENT_QUOTES, 'UTF-8') ?>">-</button>
                                <input type="number" value="1" min="1" data-product-id="<?= htmlspecialchars($produto['id'], ENT_QUOTES, 'UTF-8') ?>">
                                <button class="quantity-increase" data-product-id="<?= htmlspecialchars($produto['id'], ENT_QUOTES, 'UTF-8') ?>">+</button>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Nenhum produto relacionado encontrado.</p>
        <?php endif; ?>
    </div>
</section>


<?php
// Array contendo os nomes dos banners que você deseja buscar
$bannersNomes = ['MERCADO_PLAY.1', 'MERCADO_PLAY.2', 'MERCADO_PLAY.3', 'MERCADO_PLAY.4'];

// Inicializar variáveis para uso posterior
$bannerLink = '';
$bannerLogo = '';
$bannerTitulo = '';
$bannerNome = '';
$bannerActionLink = ''; // Inicializa a variável

// Loop para buscar e exibir cada banner
foreach ($bannersNomes as $nomeBanner) {
    // Preparar e executar a consulta para buscar o banner
    $stmt = $pdo->prepare("SELECT * FROM banners WHERE nome_banner = :nome_banner LIMIT 1");
    $stmt->bindParam(':nome_banner', $nomeBanner);
    $stmt->execute();
    $banner = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verificar se o banner foi encontrado
    if ($banner) {
        // Atualizar variáveis com dados do banner
        $bannerLink = htmlspecialchars($banner['link'], ENT_QUOTES, 'UTF-8');
        $bannerTitulo = htmlspecialchars($banner['titulo'], ENT_QUOTES, 'UTF-8');
        $bannerLogo = htmlspecialchars($banner['logo'], ENT_QUOTES, 'UTF-8');
        $bannerNome = htmlspecialchars($banner['nome_banner'], ENT_QUOTES, 'UTF-8');
        $bannerActionLink = $bannerTitulo; // Usar o valor de 'titulo' como link de ação
        
        // Saia do loop após encontrar o banner desejado
        break;
    }
}
?>

<!-- Seção Mercado Play -->
<section class="mercado-play-section" style="background-image: url('<?php echo $bannerLink; ?>');">
    <div class="banner-logo-text">
        <!-- Exibir o logo do banner -->
        <img class="logo" src="<?php echo $bannerLogo; ?>" alt="<?php echo $bannerNome; ?>">
        <div class="text">
<!--        <span class="primary-title"><?php echo $bannerTitulo; ?></span> -->
            <span class="secondary-title">séries e filmes</span> <!-- Texto padrão para secondary-title -->
            <span class="pill" style="background-color: #00A650">grátis</span>
        </div>
        <div class="actions">
            <a class="action-link" href="<?php echo $bannerActionLink; ?>" title="Acesse o Mercado Play">
                <button type="button" class="action-button" style="background-color: #00A650">
                    <span class="button-content">Acesse o Mercado Play</span>
                </button>
            </a>
        </div>
    </div>
</section>



<!-- SESSÃO 5: Categorias -->
<section class="categories-section">
    <h2>Categorias</h2>
    <div class="categories-list">
        <?php foreach ($categorias as $categoria): ?>
            <div class="category-item">
                <a href="categoria.php?id=<?= htmlspecialchars($categoria['id'], ENT_QUOTES, 'UTF-8') ?>" class="category-link">
                    <img src="<?= htmlspecialchars($categoria['imagem_link'], ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($categoria['nome'], ENT_QUOTES, 'UTF-8') ?>" class="category-image">
                    <p class="category-name"><?= htmlspecialchars($categoria['nome'], ENT_QUOTES, 'UTF-8') ?></p>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
</section>

        <!-- SESSÃO 6: Escolha como pagar e Frete grátis -->
        <section class="payment-shipping-section">
            <div class="payment-method">
                <img src="img/mercado-pago.png" alt="Mercado Pago">
                <p>Com o Mercado Pago, você paga com cartão, boleto ou Pix. Você também pode pagar em até 12x no boleto com o Mercado Crédito.</p>
                <a href="#">Como pagar com Mercado Pago</a>
            </div>
            <div class="free-shipping">
                <img src="img/frete-gratis.png" alt="Frete Grátis">
                <p>Frete grátis a partir de R$ 79 em milhares de produtos ao se cadastrar no Mercado Livre.</p>
            </div>
        </section>
    </main>

    <footer>
        <div class="footer-container">
            <p>&copy; <?= date('Y') ?> S A Super Mercado. Todos os direitos reservados.</p>
        </div>
    </footer>

    <!-- Código do modal -->
    <div id="checkout-modal" role="dialog" aria-labelledby="modal-title" aria-hidden="true">
        <div class="modal-content">
            <button class="close" aria-label="Fechar Modal">×</button>
            <iframe id="modal-iframe" title="Checkout"></iframe>
        </div>
    </div>

    <script src="form_carrinho.js"></script>
    <script>


document.addEventListener('DOMContentLoaded', () => {
    const slides = document.querySelectorAll('.carousel-slide');
    const leftControl = document.querySelector('.carousel-control.left');
    const rightControl = document.querySelector('.carousel-control.right');
    const indicators = document.querySelectorAll('.indicator');
    let currentIndex = 0;

    const showSlide = (index) => {
        if (index >= slides.length) index = 0;
        if (index < 0) index = slides.length - 1;
        document.querySelector('.carousel-container').style.transform = `translateX(-${index * 100}%)`;

        // Update indicators
        indicators.forEach((indicator, i) => {
            indicator.classList.toggle('active', i === index);
        });

        currentIndex = index;
    };

    // Show the initial slide
    showSlide(currentIndex);

    // Event listeners for controls
    leftControl.addEventListener('click', () => {
        showSlide(currentIndex - 1);
    });

    rightControl.addEventListener('click', () => {
        showSlide(currentIndex + 1);
    });

    // Event listeners for indicators
    indicators.forEach((indicator, i) => {
        indicator.addEventListener('click', () => {
            showSlide(i);
        });
    });

    // Optional: Auto-slide functionality
    setInterval(() => {
        showSlide(currentIndex + 1);
    }, 5000); // Change slide every 5 seconds
});

</script>
<script>
    window.addEventListener('load', function() {
    // Seleciona todas as folhas de estilo linkadas (externas)
    var stylesheets = document.querySelectorAll('link[rel="stylesheet"]');

    stylesheets.forEach(function(stylesheet) {
        // Obtém o URL original do CSS
        var originalHref = stylesheet.getAttribute('href');

        // Cria uma string única baseada no tempo para forçar o recarregamento
        var uniqueString = new Date().getTime();

        // Atualiza o href do CSS com a query string única
        stylesheet.setAttribute('href', originalHref + '?v=' + uniqueString);
    });

    console.log('Cache do CSS limpo e atualizado.');
});

</script>
</body>
</html>

