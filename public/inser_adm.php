<?php
session_start(); // Inicie a sessão no início do arquivo PHP

require_once '../src/Core/Database.php';

$db = Core\Database::getInstance();
$connection = $db->getConnection();

function ensureUploadDirectoryExists($directory) {
    if (!is_dir($directory)) {
        mkdir($directory, 0777, true);
    }
}

$uploadDir = 'img/';
ensureUploadDirectoryExists($uploadDir);

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

function isImage($file) {
    $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $fileMimeType = mime_content_type($file['tmp_name']);
    return in_array($fileMimeType, $allowedMimeTypes);
}

$message = '';
$messageType = '';

if (isset($_POST['add_product'])) {
    $name = $_POST['product_name'];
    $description = $_POST['product_description'];
    $price = $_POST['product_price']; // Este já é o preço final calculado
    $quantity = $_POST['product_quantity'];
    $category_id = $_POST['product_category_id'];
    $image_url = $_POST['url_imagem_produto'];

    if (isset($_FILES['imagem_produto']) && $_FILES['imagem_produto']['error'] === UPLOAD_ERR_OK) {
        $image = $_FILES['imagem_produto'];
        if (isImage($image)) {
            $image_name = uniqid() . '.' . pathinfo($image['name'], PATHINFO_EXTENSION);
            $image_destination = $uploadDir . $image_name;

            if (move_uploaded_file($image['tmp_name'], $image_destination)) {
                $image_url = $image_destination;
            } else {
                $message = 'Erro ao carregar a imagem.';
                $messageType = 'error';
                $image_url = null;
            }
        } else {
            $message = 'Por favor, envie apenas imagens.';
            $messageType = 'error';
            $image_url = null;
        }
    }

    $result = $db->insert('produtos', [
        'nome' => $name,
        'descricao' => $description,
        'preco' => $price, // Preço já com a porcentagem incluída
        'quantidade_estoque' => $quantity,
        'categoria_id' => $category_id,
        'imagem' => $image_url
    ]);

    if ($result) {
        $_SESSION['message'] = 'Operação realizada com sucesso!';
        $_SESSION['message_type'] = 'success';
    } else {
        $_SESSION['message'] = 'Erro ao realizar a operação.';
        $_SESSION['message_type'] = 'error';
    }

    header("Location: " . $_SERVER['PHP_SELF']); // Voltar para a mesma página
    exit();
}

if (isset($_POST['remove_product'])) {
    $product_id = $_POST['product_id'];
    $product = $db->select('produtos', ['id' => $product_id])[0] ?? null;
    
    if ($product) {
        $image_path = $product['imagem'] ?? null;
        if ($db->delete('produtos', ['id' => $product_id])) {
            if ($image_path && file_exists($image_path)) {
                unlink($image_path);
            }
            $_SESSION['message'] = 'Produto removido com sucesso!';
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = 'Erro ao remover o produto.';
            $_SESSION['message_type'] = 'error';
        }
    }

    header("Location: " . $_SERVER['PHP_SELF']); // Voltar para a mesma página
    exit();
}

if (isset($_POST['add_category'])) {
    $category_name = $_POST['category_name'];
    $result = $db->insert('categorias', ['nome' => $category_name]);

    if ($result) {
        $_SESSION['message'] = 'Categoria adicionada com sucesso!';
        $_SESSION['message_type'] = 'success';
    } else {
        $_SESSION['message'] = 'Erro ao adicionar a categoria.';
        $_SESSION['message_type'] = 'error';
    }

    header("Location: " . $_SERVER['PHP_SELF']); // Voltar para a mesma página
    exit();
}

if (isset($_POST['remove_category'])) {
    $id = $_POST['category_id'];
    $result = $db->delete('categorias', ['id' => $id]);
    if ($result) {
        $_SESSION['message'] = 'Categoria removida com sucesso!';
        $_SESSION['message_type'] = 'success';
    } else {
        $_SESSION['message'] = 'Erro ao remover a categoria.';
        $_SESSION['message_type'] = 'error';
    }

    header("Location: " . $_SERVER['PHP_SELF']); // Voltar para a mesma página
    exit();
}

if (isset($_POST['edit_product'])) {
    $productId = $_POST['product_id'];
    $name = $_POST['product_name'];
    $description = $_POST['product_description'];
    $price = $_POST['product_price'];
    $quantity = $_POST['product_quantity'];
    $category_id = $_POST['product_category_id'];
    $image_url = $_POST['url_imagem_produto'];

    if (isset($_FILES['imagem_produto']) && $_FILES['imagem_produto']['error'] === UPLOAD_ERR_OK) {
        $image = $_FILES['imagem_produto'];
        if (isImage($image)) {
            $image_name = uniqid() . '.' . pathinfo($image['name'], PATHINFO_EXTENSION);
            $image_destination = $uploadDir . $image_name;

            if (move_uploaded_file($image['tmp_name'], $image_destination)) {
                $image_url = $image_destination;
            } else {
                $_SESSION['message'] = 'Erro ao carregar a imagem.';
                $_SESSION['message_type'] = 'error';
            }
        } else {
            $_SESSION['message'] = 'Por favor, envie apenas imagens.';
            $_SESSION['message_type'] = 'error';
        }
    }

    $result = $db->update('produtos', [
        'nome' => $name,
        'descricao' => $description,
        'preco' => $price,
        'quantidade_estoque' => $quantity,
        'categoria_id' => $category_id,
        'imagem' => $image_url
    ], ['id' => $productId]);

    if ($result) {
        $_SESSION['message'] = 'Produto atualizado com sucesso!';
        $_SESSION['message_type'] = 'success';
    } else {
        $_SESSION['message'] = 'Erro ao atualizar o produto.';
        $_SESSION['message_type'] = 'error';
    }

    header("Location: " . $_SERVER['PHP_SELF']); // Voltar para a mesma página
    exit();
}

$allCategories = $db->select('categorias');
$allProducts = $db->select('produtos');

$displayCategories = false;
$displayProducts = false;

$product = null;

if (isset($_POST['show_categories'])) {
    $displayCategories = true;
}

if (isset($_POST['show_products'])) {
    $displayProducts = true;
}

if (isset($_POST['edit_product_select'])) {
    $productId = $_POST['product_id'];
    $product = $db->select('produtos', ['id' => $productId])[0] ?? null;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciamento de Produtos e Categorias</title>
    <link rel="stylesheet" href="style.css">
    <script>
        function filterOptions(inputId, selectId, data) {
            const filter = document.getElementById(inputId).value.toLowerCase();
            const select = document.getElementById(selectId);
            select.innerHTML = ''; // Clear previous options
            data.forEach(item => {
                if (item.nome.toLowerCase().includes(filter)) {
                    const option = document.createElement('option');
                    option.value = item.id;
                    option.textContent = item.nome;
                    select.appendChild(option);
                }
            });
        }

        function populateProductDetails() {
            const productSelect = document.getElementById('productSelect');
            const selectedOption = productSelect.options[productSelect.selectedIndex];

            console.log(selectedOption); // Adicione esta linha para depurar

            if (selectedOption.value) {
                document.getElementById('productId').value = selectedOption.value;
                document.getElementById('productName').value = selectedOption.getAttribute('data-nome') || '';
                document.getElementById('productDescription').value = selectedOption.getAttribute('data-descricao') || '';
                document.getElementById('productPrice').value = selectedOption.getAttribute('data-preco') || '';
                document.getElementById('productQuantity').value = selectedOption.getAttribute('data-quantidade') || '';
                document.getElementById('productCategoryId').value = selectedOption.getAttribute('data-categoria') || '';
                document.getElementById('url_imagem_produto').value = selectedOption.getAttribute('data-imagem') || '';
            }
        }


        function filterProductsForRemoval() {
            const filter = document.getElementById('productRemovalSearch').value.toLowerCase();
            const select = document.getElementById('productRemovalSelect');
            select.innerHTML = ''; // Clear previous options
            <?php foreach ($allProducts as $product): ?>
                if ("<?= htmlspecialchars($product['nome']) ?>".toLowerCase().includes(filter)) {
                    const option = document.createElement('option');
                    option.value = "<?= htmlspecialchars($product['id']) ?>";
                    option.textContent = "<?= htmlspecialchars($product['nome']) ?>";
                    select.appendChild(option);
                }
            <?php endforeach; ?>
        }

        document.addEventListener('DOMContentLoaded', () => {
            const categoryInput = document.getElementById('categorySearch');
            const productInput = document.getElementById('productSearch');
            const productRemovalInput = document.getElementById('productRemovalSearch');
            
            categoryInput.addEventListener('input', () => filterOptions('categorySearch', 'categorySelect', <?php echo json_encode($allCategories); ?>));
            productInput.addEventListener('input', () => filterOptions('productSearch', 'productSelect', <?php echo json_encode($allProducts); ?>));
            productRemovalInput.addEventListener('input', filterProductsForRemoval);

            const productSelect = document.getElementById('productSelect');
            productSelect.addEventListener('change', populateProductDetails);
        });
    </script>
    <style>
        .message {
            position: fixed;
            top: 10px;
            left: 50%;
            transform: translateX(-50%);
            padding: 10px;
            color: #fff;
            border: 1px solid transparent;
            border-radius: 5px;
            z-index: 1000;
        }

        .message.success {
            background: #4caf50;
        }

        .message.error {
            background: #f44336;
        }
    </style>
</head>
<body>

<?php if (isset($_SESSION['message'])): ?>
    <div class="message <?= $_SESSION['message_type'] ?>">
        <?= $_SESSION['message'] ?>
    </div>
    <?php unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
<?php endif; ?>

    <!-- Formulário para Adicionar Categoria -->
    <h2>Adicionar Categoria</h2>
    <form method="post">
        <input type="text" name="category_name" placeholder="Nome da Categoria" required>
        <button type="submit" name="add_category">Adicionar Categoria</button>
    </form>

    <!-- Formulário para Remover Categoria -->
    <h2>Remover Categoria</h2>
    <input type="text" id="categorySearch" placeholder="Pesquisar Categoria...">
    <form method="post">
        <select id="categorySelect" name="category_id" required>
            <option value="">Selecionar Categoria</option>
            <?php foreach ($allCategories as $category): ?>
                <option value="<?= htmlspecialchars($category['id']) ?>"><?= htmlspecialchars($category['nome']) ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit" name="remove_category">Remover Categoria</button>
    </form>

    <!--Adicionar Produto-->

    <h2>Adicionar Produto</h2>
<form method="post" enctype="multipart/form-data">
    <input type="text" name="product_name" placeholder="Nome do Produto" required>
    
    <!-- Descrição do Produto (campo não obrigatório) -->
    <textarea name="product_description" placeholder="Descrição do Produto"></textarea>

    <!-- Campo para inserir o valor base (obrigatório) -->
    <input type="number" step="0.01" id="base_price" name="base_price" placeholder="Valor Base" required>

    <!-- Campo para inserir a porcentagem de aumento (não obrigatório) -->
    <input type="number" step="0.01" id="price_percentage" name="price_percentage" placeholder="Porcentagem de Aumento (%)">

    <!-- Campo para exibir o preço final calculado -->
    <input type="number" step="0.01" id="final_price" name="product_price" placeholder="Preço Final" readonly required>

    <input type="number" name="product_quantity" placeholder="Quantidade em Estoque" required>
    <input type="text" name="url_imagem_produto" placeholder="URL da Imagem (opcional)">
    <input type="file" name="imagem_produto" accept="image/*">
    <select name="product_category_id" required>
        <option value="">Selecionar Categoria</option>
        <?php foreach ($allCategories as $category): ?>
            <option value="<?= htmlspecialchars($category['id']) ?>"><?= htmlspecialchars($category['nome']) ?></option>
        <?php endforeach; ?>
    </select>
    <button type="submit" name="add_product">Adicionar Produto</button>
</form>

    <!-- Formulário para Editar Produto -->
    <h2>Editar Produto</h2>
    <form method="post" enctype="multipart/form-data">
        <input type="text" id="productSearch" placeholder="Pesquisar Produto...">
        <select id="productSelect" name="product_id" onchange="populateProductDetails()" required>
            <option value="">Selecionar Produto</option>
            <?php foreach ($allProducts as $product): ?>
                <option value="<?= htmlspecialchars($product['id']) ?>"
                        data-nome="<?= htmlspecialchars($product['nome']) ?>"
                        data-descricao="<?= htmlspecialchars($product['descricao']) ?>"
                        data-preco="<?= htmlspecialchars($product['preco']) ?>"
                        data-quantidade="<?= htmlspecialchars($product['quantidade_estoque']) ?>"
                        data-categoria="<?= htmlspecialchars($product['categoria_id']) ?>"
                        data-imagem="<?= htmlspecialchars($product['imagem']) ?>">
                    <?= htmlspecialchars($product['nome']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <input type="hidden" id="productId" name="product_id">
        <input type="text" id="productName" name="product_name" placeholder="Nome do Produto">
        <textarea id="productDescription" name="product_description" placeholder="Descrição do Produto"></textarea>
        <input type="number" step="0.01" id="productPrice" name="product_price" placeholder="Preço">
        <input type="number" id="productQuantity" name="product_quantity" placeholder="Quantidade em Estoque">
        <input type="text" id="url_imagem_produto" name="url_imagem_produto" placeholder="URL da Imagem (opcional)">
        <input type="file" name="imagem_produto" accept="image/*">
        <select id="productCategoryId" name="product_category_id" required>
            <option value="">Selecionar Categoria</option>
            <?php foreach ($allCategories as $category): ?>
                <option value="<?= htmlspecialchars($category['id']) ?>"><?= htmlspecialchars($category['nome']) ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit" name="edit_product">Editar Produto</button>
    </form>

    <!-- Formulário para Remover Produto -->
    <h2>Remover Produto</h2>
    <input type="text" id="productRemovalSearch" placeholder="Pesquisar Produto...">
    <form method="post">
        <select id="productRemovalSelect" name="product_id" required>
            <option value="">Selecionar Produto</option>
            <?php foreach ($allProducts as $product): ?>
                <option value="<?= htmlspecialchars($product['id']) ?>"><?= htmlspecialchars($product['nome']) ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit" name="remove_product">Remover Produto</button>
    </form>
</body>

<script>
    // Função para calcular o preço final com base no valor base e na porcentagem de aumento
    document.getElementById('base_price').addEventListener('input', calculateFinalPrice);
    document.getElementById('price_percentage').addEventListener('input', calculateFinalPrice);

    function calculateFinalPrice() {
        const basePrice = parseFloat(document.getElementById('base_price').value) || 0;
        const percentage = parseFloat(document.getElementById('price_percentage').value) || 0;
        const finalPrice = basePrice + (basePrice * (percentage / 100));
        document.getElementById('final_price').value = finalPrice.toFixed(2);
    }
</script>

</html>
