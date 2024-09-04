<?php
session_start();

// Recebe os dados do carrinho enviados via AJAX
$cart = json_decode(file_get_contents('php://input'), true);

// Armazena o carrinho na sessão
$_SESSION['cart'] = $cart;

http_response_code(200); // Retorna um status 200 para confirmar que a operação foi bem-sucedida
?>
