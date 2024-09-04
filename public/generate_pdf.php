<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../vendor/autoload.php';

session_start();

// Recupera os dados do carrinho da sessão
$cartItems = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];

if (empty($cartItems)) {
    echo 'Carrinho está vazio.';
    exit;
}

// Define o fuso horário para Brasília
date_default_timezone_set('America/Sao_Paulo');
$date = date('d/m/Y');

// Gera um número aleatório no formato [XXX-XX]
$orderNumber = sprintf('%03d-%02d', mt_rand(100, 999), mt_rand(10, 99));

// Crie o conteúdo do PDF
$html = '<h1>Recibo de Compra</h1>';
$html .= '<p>Data: ' . $date . '</p>';
$html .= '<table border="1" style="width:100%; border-collapse: collapse; text-align: left;">';
$html .= '<tr><th style="padding: 8px;">Produto</th><th style="padding: 8px;">Quantidade</th><th style="padding: 8px;">Preço</th><th style="padding: 8px;">Total</th></tr>';

$total = 0;

foreach ($cartItems as $item) {
    $itemTotal = $item['price'] * $item['quantity'];
    $total += $itemTotal;

    $html .= '<tr>';
    $html .= '<td style="padding: 8px;">' . htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8') . '</td>';
    $html .= '<td style="padding: 8px;">' . intval($item['quantity']) . '</td>';
    $html .= '<td style="padding: 8px;">R$ ' . number_format($item['price'], 2, ',', '.') . '</td>';
    $html .= '<td style="padding: 8px;">R$ ' . number_format($itemTotal, 2, ',', '.') . '</td>';
    $html .= '</tr>';
}

$html .= '<tr><td colspan="3" style="text-align:right; padding: 8px;"><strong>Total:</strong></td>';
$html .= '<td style="padding: 8px;"><strong>R$ ' . number_format($total, 2, ',', '.') . '</strong></td></tr>';
$html .= '</table>';

// Crie o PDF usando mPDF
$mpdf = new \Mpdf\Mpdf(['tempDir' => __DIR__ . '/tmp/mpdf']);

// Gera um nome único para o arquivo PDF (apenas números aleatórios e a data)
$filename = $orderNumber . '_' . date('d-m-Y') . '.pdf';
$filePath = __DIR__ . '/pedidos/' . $filename;

// Salva o PDF na pasta 'pedidos'
$mpdf->WriteHTML($html);
$mpdf->Output($filePath, 'F');

// Gera o link para o arquivo PDF
$baseUrl = 'https://saas.techxx.com.br/pedidos/';
$pdfLink = $baseUrl . $filename;

// Resumo do Pedido
$pedidoResumo = '';
foreach ($cartItems as $index => $item) {
    $pedidoResumo .= ($index + 1) . '- ' . $item['name'] . ' (x' . $item['quantity'] . ")\n";
}

// Monta a mensagem do WhatsApp
$message = "Novo Pedido Recebido!\n\n";
$message .= "Número do Pedido: $orderNumber\n";
$message .= "Data: $date \n";
$message .= "------------------------------------------------------\n";
$message .= $pedidoResumo;
$message .= "-----------------------------------\n";
$message .= "Valor Total: R$ " . number_format($total, 2, ',', '.') . "\n\n";
$message .= "Link para o pedido: $pdfLink";

// Codifica a mensagem para URL
$messageEncoded = urlencode($message);
$whatsappUrl = "https://api.whatsapp.com/send?phone=5593991135066&text=$messageEncoded";

// Redireciona para o link do WhatsApp
header('Location: ' . $whatsappUrl);
exit();
?>
