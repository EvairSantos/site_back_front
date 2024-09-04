document.addEventListener('DOMContentLoaded', function() {
    let cart = loadCartFromLocalStorage();
    const shoppingCart = document.getElementById('shopping-cart');
    const cartIcon = document.getElementById('cart-icon');
    const cartCount = document.getElementById('cart-count');
    const clearCartButton = document.getElementById('clear-cart');
    const finalizeOrderButton = document.getElementById('finalize-order');
    const errorMessageElement = document.getElementById('error-message'); // Elemento de mensagem de erro

    let lastRequestTime = 0; // Tempo da última solicitação

    function showError(message) {
        errorMessageElement.textContent = message;
        errorMessageElement.style.display = 'block';
        setTimeout(() => {
            errorMessageElement.style.display = 'none';
        }, 5000);
    }

    function toggleCart() {
        shoppingCart.classList.toggle('active');
    }

    cartIcon.addEventListener('click', toggleCart);

    document.addEventListener('click', function(event) {
        if (!shoppingCart.contains(event.target) && event.target !== cartIcon) {
            shoppingCart.classList.remove('active');
        }
    });

    function updateCartCount() {
        const totalCount = cart.reduce((acc, item) => acc + item.quantity, 0);
        cartCount.textContent = totalCount;
    }

    function updateTotalPrice() {
        const totalPriceElement = document.getElementById('total-price');
        const totalPrice = cart.reduce((acc, item) => acc + item.price * item.quantity, 0);
        totalPriceElement.textContent = `Valor Total R$ ${totalPrice.toFixed(2)}`;
    }

    function updateCartUI() {
        const cartItemsContainer = document.querySelector('#cart-items .cart-body');
        cartItemsContainer.innerHTML = '';

        cart.forEach(item => {
            const newRow = document.createElement('div');
            newRow.classList.add('cart-item');
            newRow.innerHTML = `
                <div class="cart-item-header">
                    <span class="cart-item-name">${item.name}</span>
                </div>
                <div class="cart-item-content">
                    <div class="cart-item-image">
                        <img src="${item.image}" alt="${item.name}" class="product-image">
                    </div>
                    <div class="cart-item-price">R$ ${(item.price * item.quantity).toFixed(2)}</div>
                </div>
                <div class="cart-item-footer">
                    <div class="cart-item-quantity">
                        <button class="quantity-decrease" data-product-id="${item.id}">-</button>
                        <input type="number" value="${item.quantity}" min="1" max="99" data-product-id="${item.id}" class="quantity-input">
                        <button class="quantity-increase" data-product-id="${item.id}">+</button>
                    </div>
                    <button class="remove-from-cart" data-id="${item.id}">REMOVER</button>
                </div>
            `;
            cartItemsContainer.appendChild(newRow);
        });
        


        updateCartCount();
        updateTotalPrice();
        addRemoveListeners();
        addQuantityChangeListeners();
        saveCartToLocalStorage();
        updateAddToCartButtonVisibility();
    }

    function addProductToCart(id, name, price, quantity, image) {
        const existingProduct = cart.find(item => item.id === id);

        if (existingProduct) {
            existingProduct.quantity += quantity;
        } else {
            cart.push({ id, name, price, quantity, image });
        }

        updateCartUI();
        showNotification(`Produto "${name}" adicionado ao carrinho!`);
    }

    function updateAddToCartButtonVisibility() {
        const addToCartButtons = document.querySelectorAll('.add-to-cart');
        addToCartButtons.forEach(button => {
            const productId = button.getAttribute('data-id');
            const isInCart = cart.some(item => item.id === productId);
            button.style.display = isInCart ? 'none' : 'block';
        });
    }

    const addToCartButtons = document.querySelectorAll('.add-to-cart');
    addToCartButtons.forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.getAttribute('data-id');
            const productName = this.getAttribute('data-name');
            const productPrice = parseFloat(this.getAttribute('data-price'));
            const productStock = parseInt(this.getAttribute('data-stock'), 10);
            const productImage = this.getAttribute('data-image');

            const quantitySelector = this.nextElementSibling;

            if (quantitySelector && quantitySelector.classList.contains('quantity-selector')) {
                this.style.display = 'none';
                quantitySelector.classList.remove('hidden');

                const quantityInput = quantitySelector.querySelector('input[type="number"]');
                quantityInput.setAttribute('max', productStock);

                if (quantityInput) {
                    const quantity = parseInt(quantityInput.value, 10);

                    if (quantity > 0 && quantity <= productStock) {
                        addProductToCart(productId, productName, productPrice, quantity, productImage);
                    } else {
                        showError('Quantidade inválida ou fora de estoque.');
                    }
                } else {
                    console.error('Input de quantidade não encontrado dentro do quantity-selector.');
                }
            } else {
                console.error('Elemento quantity-selector não encontrado ou não é um quantity-selector válido.');
            }
        });
    });

    function removeFromCart(event) {
        event.stopPropagation(); // Previne o fechamento do carrinho ao clicar no "X"
        const id = event.target.dataset.id;
        cart = cart.filter(item => item.id !== id);
        updateCartUI();
    }

    function clearCart() {
        cart = [];
        updateCartUI();
    }
    // Definição da função addRemoveListeners
function addRemoveListeners() {
    const removeButtons = document.querySelectorAll('.remove-from-cart');
    removeButtons.forEach(button => {
        button.addEventListener('click', removeFromCart);
    });
}
function sendCartToServer() {
    if (cart.length === 0) {
        alert('O carrinho está vazio.');
        return;
    }

    fetch('check_stock.php?timestamp=' + new Date().getTime(), {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(cart)
    })
    .then(response => {
        if (response.headers.get('Content-Type').includes('application/json')) {
            return response.json();
        } else {
            return response.text().then(text => { throw new Error(`Unexpected response format: ${text}`); });
        }
    })
    .then(data => {
        if (data.success) {
            const outOfStockItems = data.stock_status.filter(item => !item.in_stock);
            if (outOfStockItems.length > 0) {
                let message = 'Alguns produtos não estão disponíveis ou têm quantidade insuficiente:\n';
                outOfStockItems.forEach(item => {
                    message += `Produto "${item.name}": disponível apenas ${item.available_quantity} unidades.\n`;
                });

                showError(message);
            } else {
                errorMessageElement.style.display = 'none';

                // Enviar o carrinho para o pagamento com informações detalhadas dos produtos
                return fetch('pagamento/pag.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        cart: cart,
                        products: data.products // Envia os detalhes dos produtos
                    })
                });
            }
        } else {
            throw new Error('Erro ao verificar o estoque.');
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            showError(data.error);
        } else if (data.url) {
            window.location.href = data.url; // Redireciona para o checkout do Mercado Pago
            clearCart();
        } else {
            showError('Erro ao processar o pagamento.');
        }
    })
    .catch(error => {
        showError('Erro ao enviar o carrinho para o servidor.');
        console.error('Erro ao enviar o carrinho para o servidor:', error);
    });
}

    

    function addQuantityChangeListeners() {
        const quantityInputs = document.querySelectorAll('.quantity-input');
        quantityInputs.forEach(input => {
            input.addEventListener('change', function() {
                const id = this.getAttribute('data-product-id');
                const newQuantity = parseInt(this.value, 10);
                const product = cart.find(item => item.id === id);

                if (product) {
                    if (newQuantity >= 1) {
                        const currentTime = new Date().getTime();
                        if (currentTime - lastRequestTime > 500) { // Reduzido para 500 milissegundos
                            lastRequestTime = currentTime;

                            fetch('check_stock.php', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json'
                                },
                                body: JSON.stringify([{ id, quantity: newQuantity }])
                            }).then(response => response.json()).then(data => {
                                if (data.success) {
                                    const stockInfo = data.stock_status.find(item => item.id === id);

                                    if (stockInfo && newQuantity <= stockInfo.available_quantity) {
                                        product.quantity = newQuantity;
                                        updateCartUI();
                                    } else {
                                        showError('Quantidade solicitada excede o estoque disponível.');
                                        this.value = product.quantity; // Reverte para a quantidade anterior
                                    }
                                } else {
                                    showError('Erro ao verificar o estoque.');
                                }
                            }).catch(error => {
                                showError('Erro ao verificar o estoque.');
                                console.error('Erro ao verificar o estoque:', error);
                            });
                        } else {
                            showError('Aguarde um momento antes de tentar novamente.');
                        }
                    } else {
                        showError('Quantidade mínima é 1.');
                        this.value = product.quantity; // Reverte para a quantidade anterior
                    }
                }
            });
        });

        const quantityIncreaseButtons = document.querySelectorAll('.quantity-increase');
        quantityIncreaseButtons.forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-product-id');
                const quantityInput = this.previousElementSibling;

                if (quantityInput) {
                    let quantity = parseInt(quantityInput.value, 10);
                    const product = cart.find(item => item.id === id);

                    if (product) {
                        const maxQuantity = parseInt(quantityInput.getAttribute('max'), 10);

                        if (quantity < maxQuantity) {
                            const currentTime = new Date().getTime();
                            if (currentTime - lastRequestTime > 500) { // Ajustado para 500 milissegundos
                                lastRequestTime = currentTime;

                                fetch('check_stock.php', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json'
                                    },
                                    body: JSON.stringify([{ id, quantity: quantity + 1 }])
                                }).then(response => response.json()).then(data => {
                                    if (data.success) {
                                        const stockInfo = data.stock_status.find(item => item.id === id);

                                        if (stockInfo && quantity < stockInfo.available_quantity) {
                                            quantity++;
                                            quantityInput.value = quantity;
                                            product.quantity = quantity;
                                            updateCartUI();
                                        } else {
                                            showError('Quantidade solicitada excede o estoque disponível.');
                                        }
                                    } else {
                                        showError('Erro ao verificar o estoque.');
                                    }
                                }).catch(error => {
                                    showError('Erro ao verificar o estoque.');
                                    console.error('Erro ao verificar o estoque:', error);
                                });
                            } else {
                                showError('Aguarde um momento antes de tentar novamente.');
                            }
                        }
                    }
                }
            });
        });

        const quantityDecreaseButtons = document.querySelectorAll('.quantity-decrease');
        quantityDecreaseButtons.forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-product-id');
                const quantityInput = this.nextElementSibling;

                if (quantityInput) {
                    let quantity = parseInt(quantityInput.value, 10);
                    const product = cart.find(item => item.id === id);

                    if (product) {
                        if (quantity > 1) {
                            const currentTime = new Date().getTime();
                            if (currentTime - lastRequestTime > 500) { // Ajustado para 500 milissegundos
                                lastRequestTime = currentTime;

                                fetch('check_stock.php', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json'
                                    },
                                    body: JSON.stringify([{ id, quantity: quantity - 1 }])
                                }).then(response => response.json()).then(data => {
                                    if (data.success) {
                                        const stockInfo = data.stock_status.find(item => item.id === id);

                                        if (stockInfo && quantity - 1 >= 1) {
                                            quantity--;
                                            quantityInput.value = quantity;
                                            product.quantity = quantity;
                                            updateCartUI();
                                        } else {
                                            showError('Quantidade mínima é 1.');
                                        }
                                    } else {
                                        showError('Erro ao verificar o estoque.');
                                    }
                                }).catch(error => {
                                    showError('Erro ao verificar o estoque.');
                                    console.error('Erro ao verificar o estoque:', error);
                                });
                            } else {
                                showError('Aguarde um momento antes de tentar novamente.');
                            }
                        }
                    }
                }
            });
        });
    }

    function showNotification(message) {
        const notification = document.createElement('div');
        notification.className = 'notification';
        notification.innerText = message;

        document.body.appendChild(notification);

        setTimeout(() => {
            notification.classList.add('show');
        }, 10);

        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }

    function saveCartToLocalStorage() {
        localStorage.setItem('cart', JSON.stringify(cart));
    }

    function loadCartFromLocalStorage() {
        const storedCart = localStorage.getItem('cart');
        return storedCart ? JSON.parse(storedCart) : [];
    }

    clearCartButton.addEventListener('click', function() {
        clearCart();
        showNotification('Carrinho limpo com sucesso!');
    });

    finalizeOrderButton.addEventListener('click', function() {
        sendCartToServer();
    });

    // Inicializar o carrinho e atualizar a interface do usuário
    updateCartUI();
});
