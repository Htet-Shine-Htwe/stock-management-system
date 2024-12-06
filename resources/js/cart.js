class Cart {
    constructor(cartKey) {
        this.cartKey = cartKey;
        this.cart = this.loadCart();
    }

    // Load the cart from localStorage
    loadCart() {
        const storedCart = localStorage.getItem(this.cartKey);
        return storedCart ? JSON.parse(storedCart) : [];
    }

    // Save the cart to localStorage
    saveCart() {
        localStorage.setItem(this.cartKey, JSON.stringify(this.cart));
    }

    // Add a product to the cart
    addToCart(id, name, price, image, quantity = 1) {
        const existingItemIndex = this.cart.findIndex(item => item.id === id);

        if (existingItemIndex !== -1) {
            // If the item exists, update the quantity
            this.cart[existingItemIndex].quantity += quantity;
        } else {
            // Add new item to the cart
            const newItem = { id, name, price, image, quantity };
            this.cart.push(newItem);
        }

        this.saveCart();
        this.updateCartUI();
    }

    // Remove a product from the cart
    removeFromCart(id) {
        this.cart = this.cart.filter(item => item.id !== id);
        this.saveCart();
        this.updateCartUI();
    }

     // Check if an item is in the cart
     isItemInCart(id) {
        return this.cart.some(item => item.id === id);
    }

    // Clear the entire cart
    clearCart() {
        this.cart = [];
        this.saveCart();
        this.updateCartUI();
    }

    getItems(){
        return this.cart;
        
    }

    // Update the UI with the current cart items
    updateCartUI() {
        const cartItemsContainer = document.getElementById('cart-items-container');
        cartItemsContainer.innerHTML = ''; // Clear existing cart items

        if (this.cart.length === 0) {
            cartItemsContainer.innerHTML = '<p>Your cart is empty!</p>';
            document.getElementById('total-price').innerText = '0.00';
        } else {
            let totalPrice = 0;
            this.cart.forEach(item => {
                const itemPrice = item.price * item.quantity;
                totalPrice += itemPrice;

                const cartItemHtml = `
                    <div class="cart-item row mb-3">
                        
                        <div class="col-9">
                            <h5>${item.name}</h5>
                            <p>$${item.price} x ${item.quantity}</p>
                        </div>
                        <div class="col-3 text-end">
                            <button class="btn btn-outline-danger btn-sm remove-item-btn" data-id="${item.id}">Remove</button>
                            <button class="btn btn-outline-info btn-sm increase-quantity-btn" data-id="${item.id}">+</button>
                            <button class="btn btn-outline-info btn-sm decrease-quantity-btn" data-id="${item.id}">-</button>
                        </div>
                    </div>
                `;

                cartItemsContainer.innerHTML += cartItemHtml;
            });

            document.getElementById('total-price').innerText = totalPrice.toFixed(2);
        }

        // Attach event listeners to update item quantity
        document.querySelectorAll('.remove-item-btn').forEach(button => {
            button.addEventListener('click', event => {
                event.stopPropagation();
                const productId = event.target.getAttribute('data-id');
               
                const button = document.querySelector(`.add-stock[data-id="${productId}"]`);
                button.textContent = "+ Add to Cart";
                button.disabled = false;
                this.removeFromCart(productId);
                showNotification('Product removed from cart!', 'error');

            });
        });

        document.querySelectorAll('.increase-quantity-btn').forEach(button => {
            button.addEventListener('click', event => {
                event.stopPropagation();
                const productId = event.target.getAttribute('data-id');
                const item = this.cart.find(i => i.id === productId);
                if (item) {
                    item.quantity++;
                    this.saveCart();
                    this.updateCartUI();
                }
            });
        });

        document.querySelectorAll('.decrease-quantity-btn').forEach(button => {
            button.addEventListener('click', event => {
                event.stopPropagation();
                const productId = event.target.getAttribute('data-id');
                const item = this.cart.find(i => i.id === productId);
                if (item && item.quantity > 1) {
                    item.quantity--;
                    this.saveCart();
                    this.updateCartUI();
                }
            });
        });
    }
}

export default Cart;
