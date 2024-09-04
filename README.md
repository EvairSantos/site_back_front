Projeto Mercadinho

<!-- -->
📜 Descrição

O Mercadinho é uma aplicação web que permite gerenciar um carrinho de compras e processar pagamentos com integração ao Mercado Pago. Oferece uma interface intuitiva para adicionar produtos, verificar disponibilidade de estoque e finalizar compras de forma segura.
🚀 Funcionalidades

    Gerenciamento de Carrinho: Adicione e remova itens do carrinho.
    Verificação de Estoque: Confirme a disponibilidade de produtos em estoque.
    Pagamento: Integração com Mercado Pago para processamento seguro de pagamentos.
    Responsividade: Interface adaptada para dispositivos móveis e desktops.

🛠 Tecnologias Utilizadas

    Frontend: HTML, CSS, JavaScript
    Backend: PHP
    Banco de Dados: MySQL
    Servidor Web: Apache
    Integração de Pagamento: Mercado Pago

📦 Pré-requisitos

Certifique-se de ter as seguintes ferramentas instaladas:

    PHP (8.1 ou superior)
    MySQL
    Apache
    Conta no Mercado Pago

📝 Configuração
1. Clonar o Repositório

bash

git clone https://github.com/seu-usuario/mercadinho.git
cd mercadinho

2. Configurar o Banco de Dados

    Crie um banco de dados MySQL chamado mercadinho.
    Importe o esquema de banco de dados localizado em database/schema.sql.

3. Configurar o Servidor Web

    Configure o Apache para servir o projeto. Utilize um arquivo .conf para configurar o VirtualHost.

4. Configurar as Credenciais do Mercado Pago

    Substitua YOUR_ACCESS_TOKEN no arquivo pagamento/pag.php pelo seu token de acesso do Mercado Pago.

php

function sendToMercadoPago($data) {
    $url = 'https://api.mercadopago.com/checkout/preferences?access_token=YOUR_ACCESS_TOKEN';
    $headers = [
        'Content-Type: application/json',
        'Authorization: Bearer YOUR_ACCESS_TOKEN'
    ];
    // Restante do código...
}

📂 Estrutura do Projeto

    public/ - Contém arquivos públicos acessíveis pela web.
        form_carrinho.js - JavaScript para manipulação do carrinho.
        index.html - Página principal.

    pagamento/ - Scripts de pagamento.
        pag.php - Integração com o Mercado Pago.

    Core/ - Configuração e banco de dados.
        Database.php - Configuração da conexão.

    database/ - Scripts de banco de dados.
        schema.sql - Esquema do banco de dados.

🚀 Uso

    Adicionar Itens ao Carrinho

    Utilize a interface para adicionar produtos ao carrinho.

    Verificar Estoque e Processar Pagamento

    Clique no botão de checkout para verificar o estoque e processar o pagamento. Se o estoque estiver disponível, você será redirecionado para o Mercado Pago.

💡 Contribuição

Contribuições são bem-vindas! Para contribuir:

    Faça um fork do repositório.
    Crie uma branch para sua feature (git checkout -b feature/nome-da-feature).
    Faça suas alterações e teste.
    Envie um pull request detalhando suas mudanças.

📜 Licença

Este projeto está licenciado sob a Licença MIT. Veja o arquivo LICENSE para mais detalhes.
📧 Contato

Dúvidas ou sugestões? Abra uma issue no GitHub.
