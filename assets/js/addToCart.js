const cartCount = document.querySelector('.icon-cart span');
const addButtons = document.querySelectorAll('.btn-add-to-cart');
const cartSidebar = document.getElementById('cart-sidebar');
const cartOverlay = document.getElementById('cart-overlay');
const cartItemsContainer = document.getElementById('cart-items');
const cartTotal = document.getElementById('cart-total');

// Function to update cart UI (Matching Original Design)
function updateCartUI(cartData) {
    console.log('Updating cart UI:', cartData);
    
    // Update cart count badge
    cartCount.textContent = cartData.total_items;
    
    // Update cart items list
    if (cartData.cart_items.length === 0) {
        cartItemsContainer.innerHTML = '<p class="empty-cart">Your cart is empty.</p>';
    } else {
        cartItemsContainer.innerHTML = cartData.cart_items.map(item => {
            const itemTotal = (parseFloat(item.price) * item.quantity).toFixed(2);
            return `
            <div class="cart-item">
                <div class="cart-item-info">
                    <h6>${item.name}</h6>
                    <span>RM ${parseFloat(item.price).toFixed(2)}</span>
                </div>
                <div class="quantity-controls">
                    <button class="qty-btn" onclick="updateQuantity(${item.menu_item_id}, ${item.quantity - 1})">-</button>
                    <span class="quantity">${item.quantity}</span>
                    <button class="qty-btn" onclick="updateQuantity(${item.menu_item_id}, ${item.quantity + 1})">+</button>
                </div>
                <div class="item-total">RM ${itemTotal}</div>
            </div>
            `;
        }).join('');
    }
    
    // Update total price
    cartTotal.textContent = `RM ${cartData.total_price}`;
}

// Add to Cart
addButtons.forEach(btn => {
    btn.addEventListener('click', function() {
        const menuItemId = this.dataset.id;
        const originalText = this.innerHTML;
        
        console.log('Adding item:', menuItemId); // Debug
        
        // Show loading state
        this.disabled = true;
        this.innerHTML = '<i class="bi bi-hourglass-split"></i> Adding...';
        
        fetch('update_cart.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'add', item_id: menuItemId })
        })
        .then(res => {
            console.log('Response status:', res.status); // Debug
            return res.json();
        })
        .then(data => {
            console.log('Response data:', data); // Debug
            
            if(data.success) {
                // Update UI without reload!
                updateCartUI(data);
                
                // Show success feedback
                this.innerHTML = '<i class="bi bi-check-circle-fill"></i> Added!';
                setTimeout(() => {
                    this.innerHTML = originalText;
                    this.disabled = false;
                }, 1500);
            } else {
                alert(data.message || "Failed to add item!");
                this.innerHTML = originalText;
                this.disabled = false;
            }
        })
        .catch(err => {
            console.error('Cart error:', err); // This is what's showing your error
            alert('Failed to add item. Check console for details.');
            this.innerHTML = originalText;
            this.disabled = false;
        });
    });
});

// Sidebar Toggle
document.querySelector('.icon-cart').addEventListener('click', () => {
    cartSidebar.classList.add('open');
    cartOverlay.classList.add('open');
});

document.getElementById('close-cart').addEventListener('click', closeCart);
cartOverlay.addEventListener('click', closeCart);

function closeCart() {
    cartSidebar.classList.remove('open');
    cartOverlay.classList.remove('open');
}

// Update Quantity
function updateQuantity(itemId, newQty) {
    console.log('Updating quantity:', itemId, newQty);
    
    // If quantity drops to 0 or below, remove the item
    if (newQty < 1) {
        if (confirm('Remove this item from cart?')) {
            removeFromCart(itemId);
        }
        return;
    }

    fetch('update_cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ 
            action: 'update', 
            item_id: itemId, 
            quantity: newQty 
        })
    })
    .then(res => res.json())
    .then(data => {
        console.log('Update response:', data);
        if (data.success) {
            updateCartUI(data);
        } else {
            alert('Failed to update quantity');
        }
    })
    .catch(err => {
        console.error('Update error:', err);
        alert('Error updating quantity');
    });
}

// Remove Item
function removeFromCart(itemId) {
    console.log('Removing item:', itemId);
    
    fetch('update_cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ 
            action: 'remove', 
            item_id: itemId 
        })
    })
    .then(res => res.json())
    .then(data => {
        console.log('Remove response:', data);
        if (data.success) {
            updateCartUI(data);
            // Show feedback
            alert('Item removed from cart');
        } else {
            alert('Failed to remove item');
        }
    })
    .catch(err => {
        console.error('Remove error:', err);
        alert('Error removing item');
    });
}