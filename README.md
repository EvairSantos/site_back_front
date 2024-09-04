<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>README - Mercadinho</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            width: 80%;
            margin: auto;
            overflow: hidden;
        }
        header {
            background: #333;
            color: #fff;
            padding-top: 30px;
            min-height: 70px;
            border-bottom: #bbb 1px solid;
            text-align: center;
        }
        header h1 {
            margin: 0;
            font-size: 2em;
        }
        .content {
            padding: 20px;
            background: #fff;
            margin-top: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        ul {
            list-style: none;
            padding: 0;
        }
        ul li {
            padding: 5px 0;
        }
        .highlight {
            background: #e0f7fa;
            transition: background 0.3s ease;
        }
        .highlight:hover {
            background: #b2ebf2;
        }
        .animated {
            opacity: 0;
            transition: opacity 1s ease-in-out;
        }
        .animated.visible {
            opacity: 1;
        }
    </style>
</head>
<body>

<header>
    <h1>Mercadinho</h1>
</header>

<div class="container">
    <div class="content" id="description">
        <h2>📜 Descrição</h2>
        <p>O <strong>Mercadinho</strong> é uma aplicação web que permite gerenciar um carrinho de compras e processar pagamentos com integração ao Mercado Pago. Oferece uma interface intuitiva para adicionar produtos, verificar disponibilidade de estoque e finalizar compras de forma segura.</p>
    </div>

    <div class="content" id="features">
        <h2>🚀 Funcionalidades</h2>
        <ul>
            <li>Gerenciamento de Carrinho: Adicione e remova itens do carrinho.</li>
            <li>Verificação de Estoque: Confirme a disponibilidade de produtos em estoque.</li>
            <li>Pagamento: Integração com Mercado Pago para processamento seguro de pagamentos.</li>
            <li>Responsividade: Interface adaptada para dispositivos móveis e desktops.</li>
        </ul>
    </div>

    <div class="content" id="technologies">
        <h2>🛠 Tecnologias Utilizadas</h2>
        <ul>
            <li>Frontend: HTML, CSS, JavaScript</li>
            <li>Backend: PHP</li>
            <li>Banco de Dados: MySQL</li>
            <li>Servidor Web: Apache</li>
            <li>Integração de Pagamento: Mercado Pago</li>
        </ul>
    </div>

    <div class="content" id="setup">
        <h2>📝 Configuração</h2>
        <ol>
            <li><strong>Clonar o Repositório</strong>
                <pre><code>git clone https://github.com/seu-usuario/mercadinho.git
cd mercadinho</code></pre>
            </li>
            <li><strong>Configurar o Banco de Dados</strong>
                <p>Crie um banco de dados MySQL chamado <code>mercadinho</code>. Importe o esquema de banco de dados localizado em <code>database/schema.sql</code>.</p>
            </li>
            <li><strong>Configurar o Servidor Web</strong>
                <p>Configure o Apache para servir o projeto. Utilize um arquivo <code>.conf</code> para configurar o VirtualHost.</p>
            </li>
            <li><strong>Configurar as Credenciais do Mercado Pago</strong>
                <pre><code>function sendToMercadoPago($data) {
    $url = 'https://api.mercadopago.com/checkout/preferences?access_token=YOUR_ACCESS_TOKEN';
    $headers = [
        'Content-Type: application/json',
        'Authorization: Bearer YOUR_ACCESS_TOKEN'
    ];
    // Restante do código...
}</code></pre>
            </li>
        </ol>
    </div>

    <div class="content" id="structure">
        <h2>📂 Estrutura do Projeto</h2>
        <ul>
            <li><strong>public/</strong> - Contém arquivos públicos acessíveis pela web.
                <ul>
                    <li><code>form_carrinho.js</code> - JavaScript para manipulação do carrinho.</li>
                    <li><code>index.html</code> - Página principal.</li>
                </ul>
            </li>
            <li><strong>pagamento/</strong> - Scripts de pagamento.
                <ul>
                    <li><code>pag.php</code> - Integração com o Mercado Pago.</li>
                </ul>
            </li>
            <li><strong>Core/</strong> - Configuração e banco de dados.
                <ul>
                    <li><code>Database.php</code> - Configuração da conexão.</li>
                </ul>
            </li>
            <li><strong>database/</strong> - Scripts de banco de dados.
                <ul>
                    <li><code>schema.sql</code> - Esquema do banco de dados.</li>
                </ul>
            </li>
        </ul>
    </div>

    <div class="content" id="usage">
        <h2>🚀 Uso</h2>
        <ol>
            <li><strong>Adicionar Itens ao Carrinho</strong>
                <p>Utilize a interface para adicionar produtos ao carrinho.</p>
            </li>
            <li><strong>Verificar Estoque e Processar Pagamento</strong>
                <p>Clique no botão de checkout para verificar o estoque e processar o pagamento. Se o estoque estiver disponível, você será redirecionado para o Mercado Pago.</p>
            </li>
        </ol>
    </div>

    <div class="content" id="contribution">
        <h2>💡 Contribuição</h2>
        <p>Contribuições são bem-vindas! Para contribuir:</p>
        <ol>
            <li>Faça um fork do repositório.</li>
            <li>Crie uma branch para sua feature (<code>git checkout -b feature/nome-da-feature</code>).</li>
            <li>Faça suas alterações e teste.</li>
            <li>Envia um pull request detalhando suas mudanças.</li>
        </ol>
    </div>

    <div class="content" id="license">
        <h2>📜 Licença</h2>
        <p>Este projeto está licenciado sob a Licença MIT. Veja o arquivo <code>LICENSE</code> para mais detalhes.</p>
    </div>

    <div class="content" id="contact">
        <h2>📧 Contato</h2>
        <p>Dúvidas ou sugestões? Abra uma <a href="https://github.com/seu-usuario/mercadinho/issues" target="_blank">issue</a> no GitHub.</p>
    </div>
</div>

<script>
    // Função para adicionar animação de rolagem
    document.addEventListener('DOMContentLoaded', function () {
        const sections = document.querySelectorAll('.content');

        function revealOnScroll() {
            const triggerBottom = window.innerHeight / 5 * 4;

            sections.forEach(section => {
                const sectionTop = section.getBoundingClientRect().top;

                if (sectionTop < triggerBottom) {
                    section.classList.add('visible');
                } else {
                    section.classList.remove('visible');
                }
            });
        }

        revealOnScroll();
        window.addEventListener('scroll', revealOnScroll);
    });
</script>

</body>
</html>
