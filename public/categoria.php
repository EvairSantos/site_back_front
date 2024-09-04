<?php
// Inclui o arquivo de configuração do banco de dados
require_once '../src/Core/Database.php';
include 'header.php';

// Obtém a conexão com o banco de dados
$pdo = \Core\Database::getInstance()->getConnection();

// Verifica se o ID da categoria foi passado na URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $categoriaId = intval($_GET['id']);

    // Função para obter os produtos da categoria específica
    function getProdutosPorCategoria($pdo, $categoriaId) {
        $stmt = $pdo->prepare("SELECT * FROM produtos WHERE categoria_id = :categoria_id");
        $stmt->bindParam(':categoria_id', $categoriaId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtém os produtos da categoria
    $produtos = getProdutosPorCategoria($pdo, $categoriaId);

    // Pega o nome da categoria
    $stmt = $pdo->prepare("SELECT nome FROM categorias WHERE id = :categoria_id LIMIT 1");
    $stmt->bindParam(':categoria_id', $categoriaId, PDO::PARAM_INT);
    $stmt->execute();
    $categoria = $stmt->fetch(PDO::FETCH_ASSOC);
    $categoriaNome = $categoria ? htmlspecialchars($categoria['nome'], ENT_QUOTES, 'UTF-8') : 'Categoria Desconhecida';
} else {
    // Redireciona para a página inicial se o ID da categoria não for válido
    header('Location: index.php');
    exit;
}


?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produtos - <?= $categoriaNome ?></title>
    
    <!-- Inclua seus arquivos CSS e JS aqui -->
</head>

<body>
<style>
 /*FORMATAÇÃO PARA PRODUTOS*/
/* Estilos Gerais para Dispositivos Móveis */
@media only screen and (max-width: 768px) {

/* Container dos produtos */
.product-container {
    display: flex;
    flex-wrap: wrap;
    padding: 0; /* Remove padding interno */
    gap: 0; /* Remove o espaço entre os produtos */
}

/* Cada produto individual */
.product {
    width: 50%; /* Ocupa 50% da largura do container */
    box-sizing: border-box; /* Inclui padding e border na largura do elemento */
    background-color: #ffffff;
    border: 1px solid rgba(0, 0, 0, 0.2);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); /* Borda suave preta */
    overflow: hidden;
    display: flex;
    flex-direction: column;
    align-items: stretch;
    text-align: left;
    padding: 4px; /* Padding interno do produto */
    position: relative;
    margin-bottom: 1px; /* Pequeno espaço entre produtos */
    min-height: 300px; /* Altura mínima ajustada */
}
}


/* Estilos Gerais para Imagens de Produtos */
.product img {
    width: 100%; /* Ocupa 100% da largura do contêiner */
    height: auto; /* Ajusta a altura proporcionalmente à largura */
    object-fit: contain; /* Garante que a imagem se ajuste sem cortar */
    aspect-ratio: 4/3; /* Mantém uma proporção de 4:3 para todas as imagens */
    display: block; /* Remove o espaçamento em torno da imagem */
    margin-top: 0; /* Garante que a imagem alinhe com a borda superior do produto */
}

/* Informações do produto */
.product-info {
    display: flex;
    flex-direction: column;
    align-items: stretch;
    margin-top: 8px;
    padding-bottom: 50px;
    justify-content: space-between; /* Garante o alinhamento adequado */
    flex-grow: 1; /* Preenche o espaço disponível */
}

/* Título do produto */
.product-info h3 {
    font-size: 16px;
    margin-bottom: 6px;
    text-align: left;
    margin-top: 0; /* Alinha o título com a borda superior do produto */
}

/* Preço do produto */
.price {
    font-size: 20px;
    font-weight: bold;
    color: #28a745;
    margin-bottom: 8px;
    margin-top: auto; /* Move o preço para a parte inferior da seção info */
}

/* Botão Adicionar ao Carrinho */
.add-to-cart {
    position: absolute;
    bottom: 8px;
    left: 8px;
    right: 8px;
    padding: 12px;
    font-size: 16px;
    color: #fff;
    background-color: #007bff;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    text-align: center;
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
    z-index: 2;
}
/* Seletor de Quantidade */
    .quantity-selector {
        position: absolute;
        bottom: 8px;
        left: 8px;
        right: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-top: 8px;
        z-index: 1;
    }

    /* Botões de aumentar e diminuir a quantidade */
    .quantity-decrease,
    .quantity-increase {
        background-color: #007bff;
        color: white;
        border: none;
        padding: 8px 12px;
        cursor: pointer;
        font-size: 22px;
        border-radius: 4px;
    }

    /* Campo de entrada para a quantidade */
    .quantity-selector input {
        width: 60px;
        text-align: center;
        border: 1px solid #27ec5c;
        border-radius: 4px;
        padding: 4px;
        font-size: 16px;
    }

/* Texto "SEM ESTOQUE" */
.out-of-stock-text {
    color: red;
    font-weight: bold;
    font-size: 16px;
    margin-bottom: 8px;
}

/* Produtos fora de estoque */
.out-of-stock {
    opacity: 0.5;
}

/* Remove as bordas ao redor dos produtos */
.product-container .product {
    border: none;
}




/* Estilos Gerais para Desktop */
@media only screen and (min-width: 769px) {

/* Container dos produtos */
.product-container {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); /* Ajuste o tamanho mínimo conforme necessário */
    gap: 16px; /* Espaço entre os produtos */
    padding: 0 100px; /* Margem de 100px nas laterais da sessão */
}

/* Cada produto individual */
.product {
    background-color: #ffffff;
    border: 1px solid rgba(0, 0, 0, 0.1); /* Borda fina ao redor do produto */
    display: flex;
    flex-direction: column;
    align-items: stretch;
    text-align: left;
    padding: 16px;
    position: relative;
    border-radius: 4px; /* Cantos ligeiramente arredondados */
    transition: box-shadow 0.3s ease; /* Efeito de transição */
    min-width: 220px; /* Define a largura mínima para os produtos */
}

/* Sombra ao passar o mouse */
.product:hover {
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Sombra suave */
}

/* Estilos Gerais para Imagens de Produtos */
.product img {
    width: 100%; /* Ocupa 100% da largura do contêiner */
    height: auto; /* Ajusta a altura proporcionalmente à largura */
    object-fit: contain; /* Garante que a imagem se ajuste sem cortar */
    aspect-ratio: 4/3; /* Mantém uma proporção de 4:3 para todas as imagens */
    display: block; /* Remove o espaçamento em torno da imagem */
    margin-top: 0; /* Garante que a imagem alinhe com a borda superior do produto */
}

/* Informações do produto */
.product-info {
    display: flex;
    flex-direction: column;
    align-items: stretch;
    margin-top: 8px;
    padding-bottom: 50px; /* Espaço para o botão "Adicionar ao Carrinho" */
    justify-content: flex-start; /* Alinha os itens no início da seção */
    flex-grow: 1; /* Preenche o espaço disponível */
}

/* Título do produto */
.product-info h3 {
    font-size: 18px; /* Tamanho maior para o título */
    margin-bottom: 6px;
    text-align: left;
    margin-top: 0; /* Remove a margem superior do título */
    align-self: flex-start; /* Alinha o título no início da seção */
}

/* Preço do produto */
.price {
    font-size: 20px; /* Tamanho maior para o preço */
    font-weight: bold;
    color: #28a745;
    margin-bottom: 8px;
    margin-top: 0; /* Remove a margem superior para alinhar com o título */
}

/* Botão Adicionar ao Carrinho */
.add-to-cart {
    position: absolute;
    bottom: 8px;
    left: 8px;
    right: 8px;
    padding: 12px;
    font-size: 16px;
    color: #fff;
    background-color: #007bff;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    text-align: center;
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
    z-index: 2;
}

/* Seletor de Quantidade */
.quantity-selector {
    position: absolute;
    bottom: 8px;
    left: 8px;
    right: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-top: 8px;
    z-index: 1;
}

/* Botões de aumentar e diminuir a quantidade */
.quantity-decrease,
.quantity-increase {
    background-color: #007bff;
    color: white;
    border: none;
    padding: 8px 12px;
    cursor: pointer;
    font-size: 18px;
    border-radius: 4px;
}

/* Campo de entrada para a quantidade */
.quantity-selector input {
    width: 60px;
    text-align: center;
    border: 1px solid #ced4da;
    border-radius: 4px;
    padding: 4px;
    font-size: 16px;
}

/* Texto "SEM ESTOQUE" */
.out-of-stock-text {
    color: red;
    font-weight: bold;
    font-size: 16px;
    margin-bottom: 8px;
}

/* Produtos fora de estoque */
.out-of-stock {
    opacity: 0.5;
}
}
/* Título do produto */
.product-info h3 {
    font-size: 16px; /* Ajusta o tamanho da fonte do título */
    margin-bottom: 6px;
    text-align: left; /* Alinha o título à esquerda */
}
    </style>
<h1>Produtos da Categoria: <?= $categoriaNome ?></h1>

<div class="product-list">
    <?php if (!empty($produtos)): ?>
        <?php foreach ($produtos as $produto): ?>
            <?php 
                // Verifica o estoque do produto
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
        <p>Nenhum produto encontrado nesta categoria.</p>
    <?php endif; ?>
</div>

</body>
</html>
