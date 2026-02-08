<?php
function createOrder($pdo, $user_id, $cart_items, $total, $payment_id) {
    try {
        // Start transaction
        $pdo->beginTransaction();

        // Create order record
        $stmt = $pdo->prepare("
            INSERT INTO orders (user_id, total_amount, payment_id, status, created_at)
            VALUES (?, ?, ?, 'completed', NOW())
        ");
        $stmt->execute([$user_id, $total, $payment_id]);
        $order_id = $pdo->lastInsertId();

        // Insert order items
        $stmt = $pdo->prepare("
            INSERT INTO order_items (order_id, menu_item_id, quantity, price)
            VALUES (?, ?, ?, ?)
        ");

        foreach ($cart_items as $item) {
            $stmt->execute([
                $order_id,
                $item['menu_item_id'],
                $item['quantity'],
                $item['price']
            ]);
        }

        $pdo->commit();
        return $order_id;

    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
}

function clearCart($pdo, $user_id) {
    if ($user_id) {
        $stmt = $pdo->prepare("DELETE FROM carts WHERE user_id = ?");
        $stmt->execute([$user_id]);
    } else {
        $session_id = session_id();
        $stmt = $pdo->prepare("DELETE FROM carts WHERE session_id = ?");
        $stmt->execute([$session_id]);
    }
}
