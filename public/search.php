<?php
// Inclui o arquivo de configuração do banco de dados
require_once '../src/Core/Database.php';

// Obtém a conexão com o banco de dados
$pdo = \Core\Database::getInstance()->getConnection();

// Obtém o termo de pesquisa da requisição
$query = isset($_GET['search']) ? $_GET['search'] : '';

// Verifica se o termo de pesquisa não está vazio
if (!empty($query)) {
    // Prepara a consulta para buscar produtos que correspondem ao termo de pesquisa
    $stmt = $pdo->prepare("
        SELECT * 
        FROM produtos 
        WHERE nome LIKE :query
    ");
    $stmt->bindValue(':query', '%' . $query . '%', PDO::PARAM_STR);
    $stmt->execute();
    $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Retorna os produtos como JSON
    header('Content-Type: application/json');
    echo json_encode($produtos);
} else {
    // Retorna uma resposta vazia se não houver termos de pesquisa
    header('Content-Type: application/json');
    echo json_encode([]);
}
?>
