import DataTable from "datatables.net";
import Cart from "./cart";
import { post } from "./ajax";
document.addEventListener("DOMContentLoaded", function () {

    const cartKey = 'cart';
    const cart = new Cart(cartKey);

    cart.updateCartUI();
    document.querySelectorAll('.add-to-cart-btn').forEach(button => {
        button.addEventListener('click', function (event) {
            const productId = event.target.getAttribute('data-id');
            const productName = event.target.getAttribute('data-name');
            const productPrice = parseFloat(event.target.getAttribute('data-price'));
            const productImage = event.target.getAttribute('data-image');

            // Add to the cart
            cart.addToCart(productId, productName, productPrice, productImage, 1);
        });
    });

    document.querySelector('#productTable').addEventListener('click', function (event) {
        const addToCartBtn = event.target.closest('.add-stock');

        if (addToCartBtn) {
            const productId = addToCartBtn.getAttribute('data-id');
            const productName = addToCartBtn.getAttribute('data-name');
            const quantity = addToCartBtn.getAttribute('data-quantity');
            const productPrice = parseFloat(addToCartBtn.getAttribute('data-price'));

            if (quantity <= 0) {
                showNotification('Out of stock!', 'error');
                return;
            }

            if (cart.isItemInCart(productId)) {
                addToCartBtn.textContent = "Added";
                addToCartBtn.disabled = true;
            } else {
                cart.addToCart(productId, productName, productPrice, 1); // Default quantity is 1

                addToCartBtn.textContent = "Added";
                addToCartBtn.disabled = true;
            }
        }
    });

    // Initialize cart UI when DOM is loaded
    document.addEventListener('DOMContentLoaded', () => {
        cart.updateCartUI();
        // Disable "Add to Cart" buttons for products already in cart
        document.querySelectorAll('.add-stock').forEach(button => {
            const productId = button.getAttribute('data-id');
            if (cart.isItemInCart(productId)) {
                button.textContent = "Added";
                button.disabled = true;
            }
        });
    });

    document.getElementById('place-order-btn').addEventListener('click', function () {
        const loadingIndicator = document.createElement('div');
        loadingIndicator.textContent = "Deleting...";
        loadingIndicator.classList.add('loading-indicator');
        document.body.appendChild(loadingIndicator);

        const data = {
            items: cart.getItems()
        };

        post(`purchase/order`,data).then((response) => {
            loadingIndicator.remove(); 

            if (response.ok) {
                showNotification('Order Was Successful!', 'success');
                table.draw(); 
                cart.clearCart();

            } else {
                showNotification('Failed To Order!', 'error');
            }
        }).catch((error) => {
            console.log(error)
            loadingIndicator.remove(); 
            showNotification('An error occurred. Please try again.', 'error');
        });
    });

    const table = new DataTable("#productTable", {
        serverSide: true,
        ajax: "/products/load",
        orderMulti: false,
        order: [[4, "desc"]],
        columns: [
            { data: "name" },
            { data: "price" },
            { data: "stockQuantity" },
            { data: "category" },
            {
                sortable: false,
                data: (row) => `
                     <div class="d-flex">
                    <button type="button" class="btn btn-outline-success ms-2 add-stock" 
                            data-id="${row.id}" 
                            data-name="${row.name}" 
                            data-quantity="${row.stockQuantity}"
                            data-price="${row.price}">
                        + Add to Cart
                    </button>
                </div>  
                `,
            },
        ],
        initComplete: function () {
            checkProductCart();
        },
        drawCallback: function () {
            checkProductCart();
        }
    });

    const checkProductCart = ()=>{
        document.querySelectorAll('#productTable tbody .add-stock').forEach(button => {
            const productId = button.getAttribute('data-id');  

            if (cart.isItemInCart(productId)) {
                button.textContent = "Added"; 
                button.disabled = true;    
            }
        });
    }

});
