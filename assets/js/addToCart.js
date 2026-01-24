let cart = [];
const cartCount = document.querySelector('.icon-cart span');
const addButtons = document.querySelectorAll('.btn-add-to-cart');

addButtons.forEach(btn => {
btn.addEventListener('click', () => {
const name = btn.dataset.name;
const price = parseFloat(btn.dataset.price);

const existingItem = cart.find(item => item.name === name);
if (existingItem) {
    existingItem.quantity += 1;
} else {
    cart.push({ name, price, quantity: 1 });
}

cartCount.textContent = cart.reduce((total, item) => total + item.quantity, 0);
alert(`${name} added to cart!`); // Optional feedback
});
});

const cartSidebar = document.getElementById('cart-sidebar');
const cartOverlay = document.getElementById('cart-overlay');
const cartItemsContainer = document.getElementById('cart-items');
const cartTotal = document.getElementById('cart-total');

document.querySelector('.icon-cart').addEventListener('click', () => {
cartSidebar.classList.add('open');
cartOverlay.classList.add('open');
updateCartDisplay();
});

document.getElementById('close-cart').addEventListener('click', closeCart);
cartOverlay.addEventListener('click', closeCart);

function closeCart() {
    cartSidebar.classList.remove('open');
    cartOverlay.classList.remove('open');
}

function updateCartDisplay() {
    cartItemsContainer.innerHTML = '';
    if (cart.length === 0) {
        cartItemsContainer.innerHTML = '<p class="empty-cart">Your cart is empty.</p>';
        cartTotal.textContent = 'RM0.00';
        cartCount.textContent = '0';
        return;
    }

    let total = 0;
    let totalItems = 0;

    cart.forEach((item, index) => {
    const itemEl = document.createElement('div');
    itemEl.classList.add('cart-item');
    itemEl.innerHTML = `
        <div class="cart-item-info">
        <strong>${item.name}</strong><br>
        <small>RM${item.price.toFixed(2)} each</small>
      </div>
      <div class="quantity-controls">
        <button class="qty-btn minus" data-index="${index}">-</button>
        <span class="quantity">${item.quantity}</span>
        <button class="qty-btn plus" data-index="${index}">+</button>
      </div>
      <div class="item-total">${(item.price * item.quantity).toFixed(2)}</div>
    `;
    cartItemsContainer.appendChild(itemEl);

    total += item.price * item.quantity;
    totalItems += item.quantity;
    });

    cartTotal.textContent = 'RM' + total.toFixed(2);
    cartCount.textContent = totalItems;
}

// Delegate Reviews for quantity buttons (since they are created dynamically)
cartItemsContainer.addEventListener('click', (e) => {
if (e.target.classList.contains('qty-btn')) {
    const index = parseInt(e.target.dataset.index);
    const item = cart[index];

    if (e.target.classList.contains('plus')) {
        item.quantity += 1;
    } else if (e.target.classList.contains('minus')) {
        if (item.quantity > 1) {
            item.quantity -= 1;
        } else {
            // Remove item if quantity reaches 0
            cart.splice(index, 1);
        }
    }

    updateCartDisplay(); // Refresh the cart view
}
})

async function checkout() {
    const cartData = {
        total: calculateTotal(), // Function to get sum of items
        items: cartItems       // Array of items in the cart
    };

    const response = await fetch('save_order.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(cartData)
    });

    const result = await response.json();
    if (result.success) {
        alert('Order placed successfully! Order ID: ' + result.order_id);
        window.location.href = 'profile.php'; // Redirect to see order history
    } else {
        alert(result.message);
    }
};
