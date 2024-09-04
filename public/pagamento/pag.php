<?php
// Inclui o arquivo de configuração do banco de dados
require_once '../../Core/Database.php';

// Inclui a biblioteca do Mercado Pago
require_once '../../vendor/autoload.php';

// Configuração do Mercado Pago
require_once 'config.php';

try {
    // Inicializa o Mercado Pago com o token de acesso
    MercadoPago\SDK::setAccessToken($acess_token);
} catch (Exception $e) {
    echo json_encode(['error' => 'Erro ao configurar Mercado Pago: ' . $e->getMessage()]);
    exit();
}

// Verifica se os dados do carrinho foram enviados via POST
$input = file_get_contents('php://input');
$cart = json_decode($input, true);

// Valida o carrinho
if (empty($cart) || !is_array($cart)) {
    echo json_encode(['error' => 'Carrinho vazio ou inválido']);
    exit();
}

// Cria um array para os itens do pagamento
$items = [];
$totalAmount = 0;

// Adiciona os itens ao array
foreach ($cart as $item) {
    if (isset($item['id'], $item['name'], $item['price'], $item['quantity']) &&
        is_numeric($item['price']) && is_numeric($item['quantity'])) {
        
        $mpItem = new MercadoPago\Item();
        $mpItem->title = $item['name'];
        $mpItem->quantity = $item['quantity'];
        $mpItem->unit_price = $item['price'];
        $mpItem->currency_id = 'BRL';
        
        $items[] = $mpItem;
        $totalAmount += $item['price'] * $item['quantity'];
    }
}

// Cria a preferência de pagamento
$preference = new MercadoPago\Preference();
$preference->items = $items;

// Define o URL de retorno e notificação
$preference->back_urls = [
    "success" => "https://saas.techxx.com.br/success.php",
    "failure" => "https://saas.techxx.com.br/failure.php",
    "pending" => "https://saas.techxx.com.br/pending.php"
];
$preference->auto_return = "approved";
$preference->notification_url = $notification_url;

// Salva a preferência e obtém o ID
try {
    $preference->save();
    $preferenceId = $preference->id;
    echo json_encode(['url' => "https://www.mercadopago.com.br/checkout/v1/redirect?pref_id=$preferenceId"]);
} catch (Exception $e) {
    echo json_encode(['error' => 'Erro ao salvar a preferência: ' . $e->getMessage()]);
}
exit();
?>
    