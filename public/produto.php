<?php
// Incluindo a classe Database
require '../src/Core/Database.php';

// Obtendo a instância do banco de dados
$db = \Core\Database::getInstance();
$pdo = $db->getConnection();

// Verifica se o ID do produto foi fornecido
if (isset($_GET['id'])) {
    $productId = $_GET['id'];

    try {
        // Recupera as informações do produto e a quantidade em estoque
        $stmt = $pdo->prepare('
            SELECT p.id, p.nome, p.imagem, p.descricao, p.preco, COALESCE(SUM(e.quantidade), 0) AS estoque
            FROM produtos p
            LEFT JOIN estoque e ON p.id = e.produto_id
            WHERE p.id = ?
            GROUP BY p.id, p.nome, p.imagem, p.descricao, p.preco
        ');
        $stmt->execute([$productId]);
        $produto = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verifica se o produto foi encontrado
        if ($produto) {
            $estoque = $produto['estoque'];
            $estoqueIndisponivel = $estoque <= 0;
            ?>
            <style>
                .produto-info {
                    display: flex;
                    flex-direction: row;
                    align-items: flex-end; /* Alinha as informações com a borda inferior */
                    justify-content: center;
                    margin: 20px auto;
                    border: 1px solid #ddd;
                    border-radius: 0; /* Cantos quadrados */
                    padding: 20px;
                    background-color: #fff;
                    max-width: 900px; /* Largura máxima do contêiner */
                    height: 400px; /* Altura fixa maior para o contêiner */
                    position: relative; /* Para posicionamento absoluto interno */
                }

                .produto-imagem-container {
                    flex: 1;
                    margin-right: 20px;
                    width: 350px; /* Largura da imagem */
                    height: 350px; /* Altura da imagem */
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    overflow: hidden;
                    position: relative;
                }

                .produto-imagem {
                    max-width: 100%;
                    max-height: 100%;
                    object-fit: contain; /* Ajusta a imagem para caber no contêiner sem cortar */
                    border: none; /* Remove a borda da imagem */
                    background: transparent; /* Define o fundo como transparente */
                    transition: transform 0.3s ease-in-out; /* Transição suave para o zoom */
                }

                .produto-imagem-container:hover .produto-imagem {
                    transform: scale(1.5); /* Aplica o zoom na imagem */
                    cursor: zoom-in;
                }

                .produto-dados {
                    flex: 1;
                    margin-left: 20px; /* Alinha à direita da imagem */
                    border: 1px solid #ddd; /* Borda leve */
                    border-radius: 4px; /* Bordas arredondadas */
                    padding: 20px;
                    background: rgba(255, 255, 255, 0.8); /* Fundo branco transparente */
                    display: flex;
                    flex-direction: column;
                    justify-content: space-between; /* Espaço entre os elementos */
                    height: 350px; /* Altura igual à da imagem para alinhar as bordas */
                }

                .produto-dados h1 {
                    margin: 0;
                    font-size: 1.8rem; /* Tamanho da fonte */
                    line-height: 1.2;
                    word-break: break-word; /* Quebra o texto longo */
                }

                .estoque-indisponivel {
                    color: red;
                    font-weight: bold;
                    margin-top: 10px;
                }

                .add-to-cart {
                    display: inline-block;
                    padding: 10px 20px;
                    background-color: #007bff;
                    color: white;
                    border: none;
                    border-radius: 5px;
                    cursor: pointer;
                    margin-top: 10px;
                }

                .add-to-cart:hover {
                    background-color: #0056b3;
                }

                .quantity-selector {
                    margin-top: 10px;
                }

                .quantity-decrease, .quantity-increase {
                    padding: 5px 10px;
                    border: 1px solid #ccc;
                    cursor: pointer;
                }

                input[type="number"] {
                    width: 50px;
                    text-align: center;
                }

                .zoom-overlay {
                    position: absolute;
                    top: 0;
                    left: 100%;
                    width: 400px; /* Largura da área de zoom */
                    height: 400px; /* Altura da área de zoom */
                    overflow: hidden;
                    border: 1px solid #ddd; /* Borda leve para a área de zoom */
                    background: rgba(255, 255, 255, 0.7); /* Fundo branco transparente */
                    display: none; /* Inicialmente escondido */
                    z-index: 10; /* Fica sobre o retângulo de informações */
                    cursor: zoom-out;
                }

                .produto-imagem-container:hover .zoom-overlay {
                    display: block;
                }

                .zoom-overlay img {
                    position: absolute;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    object-fit: cover; /* Ajusta o zoom da imagem */
                    transform: scale(1.5);
                }
                @media (max-width: 768px) {
                    .produto-info {
                        flex-direction: column;
                        padding: 10px; /* Adiciona espaço interno */
                        margin-left: 10px; /* Espaço entre a borda do retângulo e o limite do layout */
                        margin-right: 10px; /* Espaço entre a borda do retângulo e o limite do layout */
                    }

                    .produto-imagem-container,
                    .produto-dados {
                        width: 100%;
                        margin-right: 0;
                        margin-left: 0;
                    }

                    .produto-imagem-container {
                        margin-bottom: 20px; /* Espaço entre a imagem e os dados */
                        height: 300px; /* Altura fixa para a área da imagem */
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        overflow: hidden;
                        background-color: transparent; /* Remove o fundo */
                    }

                    .produto-imagem {
                        max-width: 100%; /* Garante que a imagem não exceda a largura da área */
                        max-height: 100%; /* Garante que a imagem não exceda a altura da área */
                        object-fit: contain; /* Ajusta a imagem para caber dentro da área sem cortar */
                        width: auto;
                        height: auto;
                    }

                    .zoom-overlay {
                        display: none; /* Remove a área de zoom no mobile */
                    }
                }
            </style>
            <div class="produto-info">
                <div class="produto-imagem-container">
                    <img src="<?= htmlspecialchars($produto['imagem'], ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($produto['nome'], ENT_QUOTES, 'UTF-8') ?>" class="produto-imagem">
                    <div class="zoom-overlay">
                        <img src="<?= htmlspecialchars($produto['imagem'], ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($produto['nome'], ENT_QUOTES, 'UTF-8') ?>">
                    </div>
                </div>
                <div class="produto-dados">
                    <h1><?= htmlspecialchars($produto['nome'], ENT_QUOTES, 'UTF-8') ?></h1>
                    <p>Descrição: <?= htmlspecialchars($produto['descricao'], ENT_QUOTES, 'UTF-8') ?></p>
                    <p>Preço: R$ <?= number_format($produto['preco'], 2, ',', '.') ?></p>
                    <?php if ($estoqueIndisponivel): ?>
                        <div class="estoque-indisponivel">SEM ESTOQUE</div>
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
        } else {
            echo 'Produto não encontrado.';
        }
    } catch (Exception $e) {
        echo 'Erro ao recuperar informações do produto: ', $e->getMessage();
    }
} else {
    echo 'ID do produto não fornecido.';
}
?>
