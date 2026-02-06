// Cart management class
class CartManager {
    constructor() {
        this.cartCount = document.getElementById('cart-count');
        this.cartSidebar = document.getElementById('cart-sidebar');
        this.cartOverlay = document.getElementById('cart-overlay');
        this.cartItemsContainer = document.getElementById('cart-items');
        this.cartTotal = document.getElementById('cart-total');

        this.init();
    }

    init() {
        // Event listeners
        this.setupEventListeners();
    }

    setupEventListeners() {
        // Event delegation for add to cart buttons
        document.addEventListener('click', (e) => this.handleClick(e));

        // Make functions available globally
        window.updateCartQuantity = (menuItemId, quantity) => this.updateCartQuantity(menuItemId, quantity);
        window.proceedToCheckout = () => this.proceedToCheckout();
        window.openCart = () => this.openCart();
        window.closeCart = () => this.closeCart();
    }

    handleClick(e) {
        // Add to cart button
        if (e.target.classList.contains('btn-add-to-cart') ||
            e.target.closest('.btn-add-to-cart')) {

            const button = e.target.classList.contains('btn-add-to-cart')
                ? e.target
                : e.target.closest('.btn-add-to-cart');

            const id = button.dataset.id;
            const name = button.dataset.name;

            this.addToCart(id, name);
            e.preventDefault();
        }

        // Open cart sidebar
        if (e.target.closest('.icon-cart')) {
            this.openCart();
        }

        // Close cart
        if (e.target.id === 'close-cart' || e.target === this.cartOverlay) {
            this.closeCart();
        }

        // Quantity buttons inside cart
        if (e.target.classList.contains('qty-btn')) {
            const button = e.target;
            const itemId = button.dataset.itemId;
            const action = button.dataset.action;

            if (itemId && action) {
                this.handleQuantityButton(itemId, action);
            }
        }

        // Remove item button
        if (e.target.classList.contains('remove-item-btn') ||
            e.target.closest('.remove-item-btn')) {

            const button = e.target.classList.contains('remove-item-btn')
                ? e.target
                : e.target.closest('.remove-item-btn');

            const itemId = button.dataset.itemId;
        }
    }

    handleQuantityButton(itemId, action) {
        // This will be handled by the inline onclick events from PHP
        // But we keep this for future expansion
        console.log(`Quantity ${action} for item ${itemId}`);
    }

    // Add item to cart via AJAX
    addToCart(menuItemId, itemName) {
        const formData = new FormData();
        formData.append('action', 'add');
        formData.append('menu_item_id', menuItemId);
        formData.append('quantity', 1);

        fetch('', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Update cart count
                this.cartCount.textContent = data.cart_count;

                // Show notification
                this.showNotification(`${itemName} added to cart!`);

                // Refresh cart if sidebar is open
                if (this.cartSidebar.classList.contains('open')) {
                    this.loadCartItems();
                }
            } else {
                this.showNotification('Failed to add item to cart.', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            this.showNotification('An error occurred.', 'error');
        });
    }

    // Load cart items via AJAX
    loadCartItems() {
        const formData = new FormData();
        formData.append('action', 'get_cart');

        fetch('', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            this.updateCartDisplay(data);
        })
        .catch(error => {
            console.error('Error loading cart:', error);
            this.showNotification('Failed to load cart items.', 'error');
        });
    }

    // Update cart quantity
    updateCartQuantity(menuItemId, quantity) {
        const formData = new FormData();
        formData.append('action', 'update');
        formData.append('menu_item_id', menuItemId);
        formData.append('quantity', quantity);

        fetch('', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                this.cartCount.textContent = data.cart_count;
                this.loadCartItems();
            }
        })
        .catch(error => {
            console.error('Error updating cart:', error);
            this.showNotification('Failed to update quantity.', 'error');
        });
    }

    // Update cart display
    updateCartDisplay(data) {
        this.cartItemsContainer.innerHTML = '';

        if (!data.cart_items || data.cart_items.length === 0) {
            this.cartItemsContainer.innerHTML = `
            <div class="text-center py-5">
                <i class="bi bi-cart display-1 text-muted opacity-25"></i>
                <p class="text-muted mt-3">Your cart is empty</p>
            </div>
        `;
            this.cartTotal.textContent = 'RM 0.00';
            return;
        }

        let itemsHTML = '';

        data.cart_items.forEach((item, index) => {
            const itemTotal = item.price * item.quantity;
            const quantity = Number(item.quantity); // Convert to number

            itemsHTML += `
            <div class="cart-item" data-item-id="${item.menu_item_id}">
                <div class="cart-item-info">
                    <strong>${this.escapeHtml(item.name)}</strong>
                    <p class="mb-1">RM ${parseFloat(item.price).toFixed(2)} each</p>
                    ${item.category ? `<small class="text-muted">${this.escapeHtml(item.category)}</small>` : ''}
                </div>
                <div class="cart-item-controls">
                    <div class="quantity-controls">
                        <button class="qty-btn minus" onclick="updateCartQuantity(${item.menu_item_id}, ${quantity - 1})">-</button>
                        <span class="quantity">${item.quantity}</span>
                        <button class="qty-btn plus" onclick="updateCartQuantity(${item.menu_item_id}, ${quantity + 1})">+</button>
                    </div>
                    <div class="item-total">RM ${itemTotal.toFixed(2)}</div>
                </div>
            </div>
        `;
        });

        this.cartItemsContainer.innerHTML = itemsHTML;
        this.cartTotal.textContent = 'RM ' + parseFloat(data.cart_total).toFixed(2);
    }

    // Open cart sidebar
    openCart() {
        this.cartSidebar.classList.add('open');
        this.cartOverlay.classList.add('open');
        this.loadCartItems();
    }

    // Close cart sidebar
    closeCart() {
        this.cartSidebar.classList.remove('open');
        this.cartOverlay.classList.remove('open');
    }

    // Show notification
    showNotification(message, type = 'success') {
        // Remove existing notifications
        document.querySelectorAll('.cart-notification').forEach(el => el.remove());

        const notification = document.createElement('div');
        notification.className = 'cart-notification';

        const icon = type === 'success'
            ? 'bi-check-circle-fill text-success'
            : 'bi-exclamation-circle-fill text-danger';

        notification.innerHTML = `
            <div class="notification-content">
                <i class="bi ${icon} me-2"></i>
                ${this.escapeHtml(message)}
            </div>
        `;

        document.body.appendChild(notification);

        // Auto remove after 3 seconds
        setTimeout(() => {
            notification.style.animation = 'slideOut 0.3s ease';
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 300);
        }, 3000);
    }

    // Proceed to checkout
    proceedToCheckout() {
        // First, check if cart is empty
        if (parseInt(this.cartCount.textContent) === 0) {
            this.showNotification('Your cart is empty!', 'error');
            return;
        }

        // You can implement your checkout logic here
        // For now, show a message
        alert('Checkout functionality would be implemented here.\n\nYou would be redirected to a checkout page.');

        // Example redirect:
        // window.location.href = 'checkout.php';
    }

    // Helper: Escape HTML to prevent XSS
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
}

// Initialize cart manager when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.cartManager = new CartManager();
});
