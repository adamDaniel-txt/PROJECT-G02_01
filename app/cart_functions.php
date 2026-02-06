<?php
// app/cart_functions.php

/**
 * Get current user's cart identifier
 */
function getCartIdentifier() {
    // If user is logged in, use user_id
    if (isset($_SESSION['user_id'])) {
        return ['type' => 'user', 'id' => $_SESSION['user_id']];
    }

    // If guest, use session_id
    if (!isset($_SESSION['guest_session_id'])) {
        $_SESSION['guest_session_id'] = session_id();
    }

    return ['type' => 'guest', 'id' => $_SESSION['guest_session_id']];
}

/**
 * Add item to cart
 */
function addToCart($pdo, $menu_item_id, $quantity = 1) {
    $cartIdentifier = getCartIdentifier();

    try {
        // Check if item already exists in cart
        if ($cartIdentifier['type'] === 'user') {
            $stmt = $pdo->prepare("SELECT * FROM carts WHERE user_id = ? AND menu_item_id = ?");
            $stmt->execute([$cartIdentifier['id'], $menu_item_id]);
        } else {
            $stmt = $pdo->prepare("SELECT * FROM carts WHERE session_id = ? AND menu_item_id = ?");
            $stmt->execute([$cartIdentifier['id'], $menu_item_id]);
        }

        $existingItem = $stmt->fetch();

        if ($existingItem) {
            // Update quantity
            if ($cartIdentifier['type'] === 'user') {
                $stmt = $pdo->prepare("UPDATE carts SET quantity = quantity + ? WHERE user_id = ? AND menu_item_id = ?");
                $stmt->execute([$quantity, $cartIdentifier['id'], $menu_item_id]);
            } else {
                $stmt = $pdo->prepare("UPDATE carts SET quantity = quantity + ? WHERE session_id = ? AND menu_item_id = ?");
                $stmt->execute([$quantity, $cartIdentifier['id'], $menu_item_id]);
            }
        } else {
            // Insert new item
            if ($cartIdentifier['type'] === 'user') {
                $stmt = $pdo->prepare("INSERT INTO carts (user_id, menu_item_id, quantity) VALUES (?, ?, ?)");
                $stmt->execute([$cartIdentifier['id'], $menu_item_id, $quantity]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO carts (session_id, menu_item_id, quantity) VALUES (?, ?, ?)");
                $stmt->execute([$cartIdentifier['id'], $menu_item_id, $quantity]);
            }
        }

        return true;
    } catch (PDOException $e) {
        error_log("Cart add error: " . $e->getMessage());
        return false;
    }
}

/**
 * Remove item from cart
 */
function removeFromCart($pdo, $menu_item_id) {
    $cartIdentifier = getCartIdentifier();

    try {
        if ($cartIdentifier['type'] === 'user') {
            $stmt = $pdo->prepare("DELETE FROM carts WHERE user_id = ? AND menu_item_id = ?");
            $stmt->execute([$cartIdentifier['id'], $menu_item_id]);
        } else {
            $stmt = $pdo->prepare("DELETE FROM carts WHERE session_id = ? AND menu_item_id = ?");
            $stmt->execute([$cartIdentifier['id'], $menu_item_id]);
        }

        return true;
    } catch (PDOException $e) {
        error_log("Cart remove error: " . $e->getMessage());
        return false;
    }
}

/**
 * Update item quantity in cart
 */
function updateCartQuantity($pdo, $menu_item_id, $quantity) {
    if ($quantity <= 0) {
        return removeFromCart($pdo, $menu_item_id);
    }

    $cartIdentifier = getCartIdentifier();

    try {
        if ($cartIdentifier['type'] === 'user') {
            $stmt = $pdo->prepare("UPDATE carts SET quantity = ? WHERE user_id = ? AND menu_item_id = ?");
            $stmt->execute([$quantity, $cartIdentifier['id'], $menu_item_id]);
        } else {
            $stmt = $pdo->prepare("UPDATE carts SET quantity = ? WHERE session_id = ? AND menu_item_id = ?");
            $stmt->execute([$quantity, $cartIdentifier['id'], $menu_item_id]);
        }

        return true;
    } catch (PDOException $e) {
        error_log("Cart update error: " . $e->getMessage());
        return false;
    }
}

/**
 * Get all items in current user's cart
 */
function getCartItems($pdo) {
    $cartIdentifier = getCartIdentifier();

    try {
        if ($cartIdentifier['type'] === 'user') {
            $stmt = $pdo->prepare("
                SELECT c.*, m.name, m.price, m.image_url, m.category
                FROM carts c
                JOIN menu_items m ON c.menu_item_id = m.id
                WHERE c.user_id = ?
                ORDER BY c.created_at DESC
            ");
            $stmt->execute([$cartIdentifier['id']]);
        } else {
            $stmt = $pdo->prepare("
                SELECT c.*, m.name, m.price, m.image_url, m.category
                FROM carts c
                JOIN menu_items m ON c.menu_item_id = m.id
                WHERE c.session_id = ?
                ORDER BY c.created_at DESC
            ");
            $stmt->execute([$cartIdentifier['id']]);
        }

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Cart get error: " . $e->getMessage());
        return [];
    }
}

/**
 * Get cart total count
 */
function getCartCount($pdo) {
    $cartItems = getCartItems($pdo);
    $total = 0;

    foreach ($cartItems as $item) {
        $total += $item['quantity'];
    }

    return $total;
}

/**
 * Get cart total price
 */
function getCartTotal($pdo) {
    $cartItems = getCartItems($pdo);
    $total = 0;

    foreach ($cartItems as $item) {
        $total += $item['price'] * $item['quantity'];
    }

    return $total;
}

/**
 * Clear cart
 */
function clearCart($pdo) {
    $cartIdentifier = getCartIdentifier();

    try {
        if ($cartIdentifier['type'] === 'user') {
            $stmt = $pdo->prepare("DELETE FROM carts WHERE user_id = ?");
            $stmt->execute([$cartIdentifier['id']]);
        } else {
            $stmt = $pdo->prepare("DELETE FROM carts WHERE session_id = ?");
            $stmt->execute([$cartIdentifier['id']]);
        }

        return true;
    } catch (PDOException $e) {
        error_log("Cart clear error: " . $e->getMessage());
        return false;
    }
}

/**
 * Transfer guest cart to user cart (when guest logs in)
 */
function transferGuestCartToUser($pdo, $guest_session_id, $user_id) {
    try {
        // Check if guest has cart items
        $stmt = $pdo->prepare("SELECT * FROM carts WHERE session_id = ?");
        $stmt->execute([$guest_session_id]);
        $guestItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($guestItems)) {
            return true;
        }

        // Transfer each item
        foreach ($guestItems as $item) {
            // Check if user already has this item
            $checkStmt = $pdo->prepare("SELECT * FROM carts WHERE user_id = ? AND menu_item_id = ?");
            $checkStmt->execute([$user_id, $item['menu_item_id']]);
            $existingItem = $checkStmt->fetch();

            if ($existingItem) {
                // Update quantity
                $updateStmt = $pdo->prepare("UPDATE carts SET quantity = quantity + ? WHERE user_id = ? AND menu_item_id = ?");
                $updateStmt->execute([$item['quantity'], $user_id, $item['menu_item_id']]);
            } else {
                // Insert new item
                $insertStmt = $pdo->prepare("INSERT INTO carts (user_id, menu_item_id, quantity) VALUES (?, ?, ?)");
                $insertStmt->execute([$user_id, $item['menu_item_id'], $item['quantity']]);
            }

            // Remove guest item
            $deleteStmt = $pdo->prepare("DELETE FROM carts WHERE id = ?");
            $deleteStmt->execute([$item['id']]);
        }

        return true;
    } catch (PDOException $e) {
        error_log("Cart transfer error: " . $e->getMessage());
        return false;
    }
}
?>
