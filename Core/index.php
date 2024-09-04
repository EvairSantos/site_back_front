<?php
require_once 'Database.php';

use Core\Database;

// Obtendo a conexão PDO
$db = Database::getInstance()->getConnection();

// Função para buscar dados com possibilidade de filtro por nome
function fetchData($table, $searchTerm = '') {
    global $db;
    if ($searchTerm) {
        $stmt = $db->prepare("SELECT * FROM $table WHERE nome LIKE :searchTerm");
        $stmt->execute([':searchTerm' => "%$searchTerm%"]);
    } else {
        $stmt = $db->prepare("SELECT * FROM $table");
        $stmt->execute();
    }
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Função para deletar um registro
function deleteRecord($table, $id) {
    global $db;
    $stmt = $db->prepare("DELETE FROM $table WHERE id = :id");
    return $stmt->execute([':id' => $id]);
}

// Função para atualizar um registro
function updateRecord($table, $data, $id) {
    global $db;
    $set = [];
    foreach ($data as $key => $value) {
        $set[] = "$key = :$key";
    }
    $setSql = implode(', ', $set);
    $stmt = $db->prepare("UPDATE $table SET $setSql WHERE id = :id");
    $data['id'] = $id;
    return $stmt->execute($data);
}

// Função para inserir um novo produto
function insertProduct($data) {
    global $db;
    $stmt = $db->prepare("INSERT INTO produtos (nome, descricao, preco, quantidade_estoque, categoria_id, imagem) VALUES (:nome, :descricao, :preco, :quantidade_estoque, :categoria_id, :imagem)");
    return $stmt->execute($data);
}

// Função para inserir uma nova promoção
function insertPromotion($data) {
    global $db;
    $stmt = $db->prepare("INSERT INTO promocoes (titulo, descricao, imagem, produto_id, data_inicio, data_fim) VALUES (:titulo, :descricao, :imagem, :produto_id, :data_inicio, :data_fim)");
    return $stmt->execute($data);
}

// Função para inserir uma nova categoria
function insertCategory($data) {
    global $db;
    $stmt = $db->prepare("INSERT INTO categorias (nome, descricao, imagem_link) VALUES (:nome, :descricao, :imagem_link)");
    return $stmt->execute($data);
}

// Função para inserir um novo banner
function insertBanner($data) {
    global $db;
    $stmt = $db->prepare("INSERT INTO banners (titulo, descricao, imagem_link, data_inicio, data_fim) VALUES (:titulo, :descricao, :imagem_link, :data_inicio, :data_fim)");
    return $stmt->execute($data);
}

// Buscar dados das tabelas
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
$products = fetchData('produtos', $searchTerm);
$categories = fetchData('categorias');
$promocoes = fetchData('promocoes');
$banners = fetchData('banners');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $table = $_POST['table'];
        $id = $_POST['id'];
        
        if ($_POST['action'] === 'delete') {
            deleteRecord($table, $id);
        } elseif ($_POST['action'] === 'update') {
            $data = [];
            foreach ($_POST as $key => $value) {
                if ($key !== 'action' && $key !== 'id' && $key !== 'table') {
                    $data[$key] = $value;
                }
            }
            updateRecord($table, $data, $id);
        } elseif ($_POST['action'] === 'insert') {
            if ($table === 'produtos') {
                $data = [
                    'nome' => $_POST['nome'],
                    'descricao' => $_POST['descricao'],
                    'preco' => $_POST['preco'],
                    'quantidade_estoque' => $_POST['quantidade_estoque'],
                    'categoria_id' => $_POST['categoria_id'],
                    'imagem' => $_POST['imagem']
                ];
                insertProduct($data);
            } elseif ($table === 'promocoes') {
                $data = [
                    'titulo' => $_POST['titulo'],
                    'descricao' => $_POST['descricao'],
                    'imagem' => $_POST['imagem'],
                    'produto_id' => $_POST['produto_id'],
                    'data_inicio' => $_POST['data_inicio'],
                    'data_fim' => $_POST['data_fim']
                ];
                insertPromotion($data);
            } elseif ($table === 'categorias') {  // Adicionando a categoria
                $data = [
                    'nome' => $_POST['nome'],
                    'descricao' => $_POST['descricao'],
                    'imagem_link' => $_POST['imagem_link']
                ];
                insertCategory($data);
            } elseif ($table === 'banners') {  // Adicionando um banner
                $data = [
                    'titulo' => $_POST['titulo'],
                    'descricao' => $_POST['descricao'],
                    'imagem_link' => $_POST['imagem_link'],
                    'data_inicio' => $_POST['data_inicio'],
                    'data_fim' => $_POST['data_fim']
                ];
                insertBanner($data);
            }
        }
        // Redirecionar após POST
        header('Location: index.php');
        exit();
    }
}

// Adicionar uma rota para busca de produtos no início do script
if (isset($_GET['action']) && $_GET['action'] === 'search_products') {
    $searchTerm = isset($_GET['term']) ? $_GET['term'] : '';
    $products = fetchData('produtos', $searchTerm);
    
    header('Content-Type: application/json');
    echo json_encode($products);
    exit();
}
?>



<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administração do Mercadinho</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Administração do Mercadinho</h1>

    <h2>Produtos</h2>
    <form method="GET" action="index.php">
        <input type="text" name="search" placeholder="Pesquisar por nome" value="<?php echo htmlspecialchars($searchTerm); ?>">
        <button type="submit">Pesquisar</button>
    </form>
    
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Descrição</th>
                <th>Preço</th>
                <th>Quantidade em Estoque</th>
                <th>Categoria ID</th>
                <th>Data de Criação</th>
                <th>Imagem</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $product): ?>
            <tr>
                <form method="POST">
                    <td><?php echo htmlspecialchars($product['id']); ?></td>
                    <td><input type="text" name="nome" value="<?php echo htmlspecialchars($product['nome']); ?>"></td>
                    <td><input type="text" name="descricao" value="<?php echo htmlspecialchars($product['descricao']); ?>"></td>
                    <td><input type="text" name="preco" value="<?php echo htmlspecialchars($product['preco']); ?>"></td>
                    <td><input type="text" name="quantidade_estoque" value="<?php echo htmlspecialchars($product['quantidade_estoque']); ?>"></td>
                    <td><input type="text" name="categoria_id" value="<?php echo htmlspecialchars($product['categoria_id']); ?>"></td>
                    <td><?php echo htmlspecialchars($product['data_criacao']); ?></td>
                    <td><input type="text" name="imagem" value="<?php echo htmlspecialchars($product['imagem']); ?>"></td>
                    <td>
                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($product['id']); ?>">
                        <input type="hidden" name="table" value="produtos">
                        <button type="submit" name="action" value="update">Atualizar</button>
                        <button type="submit" name="action" value="delete">Excluir</button>
                    </td>
                </form>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h3>Adicionar Novo Produto</h3>
<form method="POST" id="productForm">
    <input type="text" id="productName" name="nome" placeholder="Nome" required>
    <button type="button" id="searchButton">Pesquisar</button>
    <input type="text" name="descricao" placeholder="Descrição" id="productDescription">
    <input type="text" name="preco" placeholder="Preço" required id="productPrice">
    <input type="text" name="quantidade_estoque" placeholder="Quantidade em Estoque" required>
    
    <select name="categoria_id" required>
        <option value="">Selecione uma categoria</option>
        <?php foreach ($categories as $category): ?>
            <option value="<?php echo htmlspecialchars($category['id']); ?>">
                <?php echo htmlspecialchars($category['nome']); ?>
            </option>
        <?php endforeach; ?>
    </select>
    
    <input type="text" id="imageURL" name="imagem" placeholder="Imagem URL" required>
    <input type="hidden" name="table" value="produtos">
    <button type="submit" name="action" value="insert">Inserir Produto</button>
</form>

<div id="selectedImagesContainer">
    <h4>Imagens Selecionadas:</h4>
    <div id="selectedImages" style="display: flex; flex-wrap: wrap;"></div>
</div>

<div id="productList">
    <h4>Selecione um produto:</h4>
    <ul id="productList"></ul>
</div>





    <h2>Categorias</h2>
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Descrição</th>
                <th>Data de Criação</th>
                <th>Imagem Link</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($categories as $category): ?>
            <tr>
                <form method="POST">
                    <td><?php echo htmlspecialchars($category['id']); ?></td>
                    <td><input type="text" name="nome" value="<?php echo htmlspecialchars($category['nome']); ?>"></td>
                    <td><input type="text" name="descricao" value="<?php echo htmlspecialchars($category['descricao']); ?>"></td>
                    <td><?php echo htmlspecialchars($category['data_criacao']); ?></td>
                    <td><input type="text" name="imagem_link" value="<?php echo htmlspecialchars($category['imagem_link']); ?>"></td>
                    <td>
                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($category['id']); ?>">
                        <input type="hidden" name="table" value="categorias">
                        <button type="submit" name="action" value="update">Atualizar</button>
                        <button type="submit" name="action" value="delete">Excluir</button>
                    </td>
                </form>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <h3>Adicionar Nova Categoria</h3>
<form method="POST" id="categoryForm">
    <input type="text" id="categoryName" name="nome" placeholder="Nome da Categoria" required>
    <button type="button" id="searchCategoryButton">Buscar Banners no Google</button>
    <input type="text" name="descricao" placeholder="Descrição da Categoria" id="categoryDescription">
    <input type="text" id="categoryImageURL" name="imagem_link" placeholder="Imagem URL" required>
    <input type="hidden" name="table" value="categorias">
    <button type="submit" name="action" value="insert">Inserir Categoria</button>
</form>

<div id="selectedCategoryImagesContainer">
    <h4>Imagens Selecionadas:</h4>
    <div id="selectedCategoryImages" style="display: flex; flex-wrap: wrap;"></div>
</div>

<script>
document.getElementById('searchCategoryButton').addEventListener('click', function() {
    const categoryName = document.getElementById('categoryName').value;
    if (categoryName) {
        // Criando a query refinada de Google Hacking para alta resolução
        const query = encodeURIComponent(`intitle:banner inurl:categoria OR produto OR mercado OR supermercado "${categoryName}" filetype:jpg imagesize:1600x900`);
        const url = `https://www.google.com/search?tbm=isch&q=${query}`;
        window.open(url, '_blank');

        alert('Procure as imagens no Google e cole as URLs das imagens desejadas.');
    } else {
        alert('Por favor, insira o nome da categoria.');
    }
});

    document.getElementById('categoryImageURL').addEventListener('input', function() {
        const imageURL = this.value;
        if (imageURL) {
            const imgElement = document.createElement('img');
            imgElement.src = imageURL;
            imgElement.style.maxWidth = '150px';
            imgElement.style.margin = '10px';

            const selectedCategoryImages = document.getElementById('selectedCategoryImages');
            selectedCategoryImages.appendChild(imgElement);
        }
    });
</script>


    <!-- Seção para gerenciar Promoções -->
    <h2>Promoções</h2>
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Título</th>
                <th>Descrição</th>
                <th>Imagem</th>
                <th>Produto</th>
                <th>Data de Início</th>
                <th>Data de Fim</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($promocoes as $promocao): ?>
            <tr>
                <form method="POST">
                    <td><?php echo htmlspecialchars($promocao['id']); ?></td>
                    <td><input type="text" name="titulo" value="<?php echo htmlspecialchars($promocao['titulo']); ?>"></td>
                    <td><textarea name="descricao"><?php echo htmlspecialchars($promocao['descricao']); ?></textarea></td>
                    <td><input type="text" name="imagem" value="<?php echo htmlspecialchars($promocao['imagem']); ?>"></td>
                    <td>
                        <select name="produto_id" onchange="updateProductDetails()">
                            <?php foreach ($products as $product): ?>
                                <option value="<?php echo $product['id']; ?>"
                                        data-nome="<?php echo htmlspecialchars($product['nome']); ?>"
                                        data-imagem="<?php echo htmlspecialchars($product['imagem']); ?>"
                                        <?php echo $promocao['produto_id'] == $product['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($product['nome']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <td><input type="date" name="data_inicio" value="<?php echo htmlspecialchars($promocao['data_inicio']); ?>"></td>
                    <td><input type="date" name="data_fim" value="<?php echo htmlspecialchars($promocao['data_fim']); ?>"></td>
                    <td>
                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($promocao['id']); ?>">
                        <input type="hidden" name="table" value="promocoes">
                        <button type="submit" name="action" value="update">Atualizar</button>
                        <button type="submit" name="action" value="delete">Excluir</button>
                    </td>
                </form>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h3>Adicionar Nova Promoção</h3>
    <form method="POST">
        <input type="text" name="titulo" placeholder="Título da Promoção" required>
        <textarea name="descricao" placeholder="Descrição da Promoção" required></textarea>
        <input type="text" name="imagem" placeholder="URL da Imagem da Promoção" required>
        
        <select name="produto_id" required>
            <option value="">Selecione um Produto</option>
            <?php foreach ($products as $product): ?>
                <option value="<?php echo htmlspecialchars($product['id']); ?>">
                    <?php echo htmlspecialchars($product['nome']); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <input type="date" name="data_inicio" placeholder="Data de Início" required>
        <input type="date" name="data_fim" placeholder="Data de Fim" required>

        <input type="hidden" name="table" value="promocoes">
        <button type="submit" name="action" value="insert">Inserir Promoção</button>
    </form>
    <h2>Banners</h2>

    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome do Banner</th>
                <th>Link</th>
                <th>Logo</th>
                <th>Título</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($banners as $banner): ?>
            <tr>
                <form method="POST">
                    <td><?php echo htmlspecialchars($banner['id']); ?></td>
                    <td><input type="text" name="nome_banner" value="<?php echo htmlspecialchars($banner['nome_banner']); ?>"></td>
                    <td><input type="text" name="link" value="<?php echo htmlspecialchars($banner['link']); ?>"></td>
                    <td><input type="text" name="logo" value="<?php echo htmlspecialchars($banner['logo']); ?>"></td>
                    <td><input type="text" name="titulo" value="<?php echo htmlspecialchars($banner['titulo']); ?>"></td>
                    <td>
                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($banner['id']); ?>">
                        <input type="hidden" name="table" value="banners">
                        <button type="submit" name="action" value="update">Atualizar</button>
                        <button type="submit" name="action" value="delete">Excluir</button>
                    </td>
                </form>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h3>Adicionar Novo Banner</h3>
    <form method="POST">
        <input type="text" name="nome_banner" placeholder="Nome do Banner" required>
        <input type="text" name="link" placeholder="Link" required>
        <input type="text" name="logo" placeholder="Logo" required>
        <input type="text" name="titulo" placeholder="Título" required>
        <input type="hidden" name="action" value="insert">
        <button type="submit">Inserir Banner</button>
    </form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('searchButton').addEventListener('click', function() {
        const productName = document.getElementById('productName').value;

        if (productName) {
            fetch(`https://api.mercadolibre.com/sites/MLB/search?q=${encodeURIComponent(productName)}`)
                .then(response => response.json())
                .then(data => {
                    const productList = document.getElementById('productList');
                    productList.innerHTML = '';
                    const selectedImagesContainer = document.getElementById('selectedImagesContainer');
                    const selectedImages = document.getElementById('selectedImages');
                    selectedImages.innerHTML = '';
                    selectedImagesContainer.style.display = 'none';

                    if (data.results.length > 0) {
                        data.results.forEach(product => {
                            const listItem = document.createElement('li');
                            const link = document.createElement('a');
                            link.href = '#';
                            link.textContent = `${product.id}: ${product.title} - R$${product.price}`;
                            link.dataset.productId = product.id; // Armazena o ID do produto

                            link.addEventListener('click', function() {
                                fetch(`https://api.mercadolibre.com/items/${link.dataset.productId}`)
                                    .then(response => response.json())
                                    .then(productDetails => {
                                        if (productDetails.pictures && productDetails.pictures.length > 0) {
                                            selectedImages.innerHTML = ''; // Limpa imagens anteriores
                                            productDetails.pictures.forEach(picture => {
                                                const img = document.createElement('img');
                                                img.src = picture.url.replace('http://', 'https://');
                                                img.alt = productDetails.title;
                                                img.style.width = '200px';
                                                img.style.marginRight = '10px';
                                                img.style.marginBottom = '10px';
                                                img.addEventListener('click', function() {
                                                    document.getElementById('productName').value = productDetails.title;
                                                    document.getElementById('productDescription').value = productDetails.title; // Ajuste conforme necessário
                                                    document.getElementById('imageURL').value = picture.url;
                                                    document.getElementById('productPrice').value = productDetails.price; // Remove "R$" antes de atribuir
                                                });
                                                selectedImages.appendChild(img);
                                            });
                                            selectedImagesContainer.style.display = 'block';
                                        } else {
                                            alert('Nenhuma imagem disponível para este produto.');
                                        }
                                    })
                                    .catch(error => console.error('Erro ao buscar detalhes do produto:', error));
                            });

                            listItem.appendChild(link);
                            productList.appendChild(listItem);
                        });
                    } else {
                        alert('Nenhum produto encontrado com esse nome.');
                    }
                })
                .catch(error => console.error('Erro ao buscar produtos:', error));
        }
    });
});


</script>
<script>
        // Função para atualizar o nome e o link da imagem com base no produto selecionado
        function updateProductDetails() {
            var select = document.getElementById('produto_id');
            var selectedOption = select.options[select.selectedIndex];
            var nome = selectedOption.getAttribute('data-nome');
            var imagem = selectedOption.getAttribute('data-imagem');

            document.getElementById('imagem').value = imagem;
            document.getElementById('produto_nome').value = nome;
        }
    </script>
</body>
</html>

