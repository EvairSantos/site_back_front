<!-- Certifique-se de incluir jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function() {
    // Função para carregar produtos
    function loadProducts(categoriaId) {
        $.ajax({
            url: 'categoria.php',
            type: 'GET',
            data: { id: categoriaId },
            dataType: 'json',
            success: function(data) {
                if (data.length > 0) {
                    var productHtml = '';
                    $.each(data, function(index, produto) {
                        productHtml += '<div class="product-item">';
                        productHtml += '<img src="' + produto.imagem_link + '" alt="' + produto.nome + '">';
                        productHtml += '<h3>' + produto.nome + '</h3>';
                        productHtml += '<p>R$ ' + parseFloat(produto.preco).toFixed(2).replace('.', ',') + '</p>';
                        productHtml += '<a href="produto.php?id=' + produto.id + '">Ver Produto</a>';
                        productHtml += '</div>';
                    });
                    $('#products-container').html(productHtml);
                } else {
                    $('#products-container').html('<p>Nenhum produto encontrado para esta categoria.</p>');
                }
            },
            error: function(xhr, status, error) {
                console.error('Erro ao carregar produtos:', status, error);
                $('#products-container').html('<p>Ocorreu um erro ao carregar os produtos.</p>');
            }
        });
    }

    // Obtém o ID da categoria da URL
    var urlParams = new URLSearchParams(window.location.search);
    var categoriaId = urlParams.get('id');
    
    if (categoriaId) {
        loadProducts(categoriaId);
    }
});
</script>

<!-- Adicione um container para os produtos -->
<div id="products-container"></div>
