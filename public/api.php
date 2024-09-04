<?php
// Inclui o arquivo de configuração do banco de dados
require_once '../src/Core/Database.php';

// Obtém a conexão com o banco de dados
$pdo = \Core\Database::getInstance()->getConnection();

// Função para obter os produtos
function getProdutos($pdo) {
    $stmt = $pdo->query("SELECT * FROM produtos");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Função para obter as categorias
function getCategorias($pdo) {
    $stmt = $pdo->query("SELECT * FROM categorias");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Função para obter os banners
function getBanners($pdo) {
    $stmt = $pdo->query("SELECT * FROM banners");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Função para obter o histórico de cliques
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

// Inicia a sessão e verifica se o usuário está autenticado
session_start();
if (session_status() === PHP_SESSION_ACTIVE) {
    session_regenerate_id(true);
}

$userId = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : null;

// Obtém os dados
$produtos = getProdutos($pdo);
$categorias = getCategorias($pdo);
$banners = getBanners($pdo);

// Obtém o histórico de produtos mais clicados para o usuário autenticado
$historicoIds = [];
if ($userId) {
    $historicoIds = getUserHistory($pdo, $userId);
}

// Enviar resposta JSON
header('Content-Type: application/json');
echo json_encode([
    'produtos' => $produtos,
    'categorias' => $categorias,
    'banners' => $banners,
    'historicoIds' => $historicoIds
]);
?>
