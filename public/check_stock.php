<?php
// Inclui o arquivo de configuração do banco de dados
require_once '../Core/Database.php';

// Obtém a conexão com o banco de dados
$pdo = \Core\Database::getInstance()->getConnection();

// Verifica se os dados foram enviados via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtém os dados do carrinho do POST
    $cart = json_decode(file_get_contents('php://input'), true);

    // Verifica se o JSON foi decodificado corretamente e é um array
    if (json_last_error() === JSON_ERROR_NONE && is_array($cart)) {
        // Inicializa o array de status do estoque
        $stock_status = [];
        $products = [];

        foreach ($cart as $item) {
            // Verifica se os dados do item estão definidos
            if (isset($item['id']) && isset($item['quantity']) && is_int($item['quantity']) && $item['quantity'] > 0) {
                // Prepara a consulta para obter os dados do produto
                $stmt = $pdo->prepare("SELECT nome, preco, quantidade_estoque FROM produtos WHERE id = :id");
                $stmt->execute(['id' => $item['id']]);
                $product = $stmt->fetch(PDO::FETCH_ASSOC);

                // Verifica se o produto foi encontrado
                if ($product) {
                    // Verifica se há estoque suficiente
                    $in_stock = $product['quantidade_estoque'] >= $item['quantity'];
                    $stock_status[] = [
                        'id' => $item['id'],
                        'name' => $product['nome'],
                        'price' => $product['preco'],
                        'available_quantity' => $product['quantidade_estoque'],
                        'in_stock' => $in_stock
                    ];
                    $products[] = [
                        'title' => $product['nome'],
                        'quantity' => $item['quantity'],
                        'unit_price' => $product['preco']
                    ];
                } else {
                    // Produto não encontrado
                    $stock_status[] = [
                        'id' => $item['id'],
                        'name' => 'Produto não encontrado',
                        'available_quantity' => 0,
                        'in_stock' => false
                    ];
                }
            } else {
                // Dados inválidos no item
                $stock_status[] = [
                    'id' => $item['id'] ?? 'Desconhecido',
                    'name' => 'Dados inválidos',
                    'available_quantity' => 0,
                    'in_stock' => false
                ];
            }
        }

        // Retorna o status do estoque em formato JSON
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'stock_status' => $stock_status,
            'products' => $products // Adiciona informações dos produtos
        ]);
    } else {
        // Dados de entrada inválidos
        header('Content-Type: application/json', true, 400);
        echo json_encode([
            'success' => false,
            'message' => 'Dados de entrada inválidos.'
        ]);
    }
} else {
    // Método não permitido
    header('Content-Type: application/json', true, 405);
    echo json_encode([
        'success' => false,
        'message' => 'Método não permitido.'
    ]);
}
?>
