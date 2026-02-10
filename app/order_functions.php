<?php
function createOrder($pdo, $user_id, $cart_items, $total, $payment_id) {
    try {
        $pdo->beginTransaction();

        // Create order record
        $stmt = $pdo->prepare("
            INSERT INTO orders (user_id, total_amount, payment_id, status, created_at)
            VALUES (?, ?, ?, 'pending', NOW())
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

        // Create initial status log
        $stmt = $pdo->prepare("
            INSERT INTO order_status_logs (order_id, status, notes, created_at)
            VALUES (?, 'pending', 'Order created and payment confirmed', NOW())
        ");
        $stmt->execute([$order_id]);

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

function getCustomerOrders($pdo, $user_id, $limit = 10) {
    // Cast limit to integer for safety
    $limit = (int)$limit;

    $stmt = $pdo->prepare("
        SELECT o.*,
               COUNT(oi.id) as item_count,
               (SELECT status FROM order_status_logs WHERE order_id = o.id ORDER BY created_at DESC LIMIT 1) as current_status
        FROM orders o
        LEFT JOIN order_items oi ON o.id = oi.order_id
        WHERE o.user_id = ?
        GROUP BY o.id
        ORDER BY o.created_at DESC
        LIMIT " . $limit . "
    ");
    $stmt->execute([$user_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getOrderDetails($pdo, $order_id, $user_id = null) {
    $sql = "
        SELECT o.*,
               u.username,
               u.email,
               (SELECT status FROM order_status_logs WHERE order_id = o.id ORDER BY created_at DESC LIMIT 1) as current_status
        FROM orders o
        LEFT JOIN users u ON o.user_id = u.id
        WHERE o.id = ?
    ";

    $params = [$order_id];

    if ($user_id) {
        $sql .= " AND o.user_id = ?";
        $params[] = $user_id;
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getOrderItems($pdo, $order_id) {
    $stmt = $pdo->prepare("
        SELECT oi.*, mi.name, mi.description, mi.image_url
        FROM order_items oi
        JOIN menu_items mi ON oi.menu_item_id = mi.id
        WHERE oi.order_id = ?
        ORDER BY oi.id
    ");
    $stmt->execute([$order_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getOrderStatusHistory($pdo, $order_id) {
    $stmt = $pdo->prepare("
        SELECT * FROM order_status_logs
        WHERE order_id = ?
        ORDER BY created_at ASC
    ");
    $stmt->execute([$order_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function updateOrderStatus($pdo, $order_id, $status, $notes = null) {
    try {
        $pdo->beginTransaction();

        // Update main order status
        $stmt = $pdo->prepare("UPDATE orders SET status = ?, updated_at = NOW() WHERE id = ?");
        $stmt->execute([$status, $order_id]);

        // Log status change
        $stmt = $pdo->prepare("
            INSERT INTO order_status_logs (order_id, status, notes, created_at)
            VALUES (?, ?, ?, NOW())
        ");
        $stmt->execute([$order_id, $status, $notes]);

        // Set estimated ready time for "preparing" status
        if ($status === 'preparing') {
            $ready_time = date('Y-m-d H:i:s', strtotime('+15 minutes'));
            $stmt = $pdo->prepare("UPDATE orders SET estimated_ready_time = ? WHERE id = ?");
            $stmt->execute([$ready_time, $order_id]);
        }

        // Generate pickup code for "ready" status
        if ($status === 'ready') {
            $pickup_code = strtoupper(substr(md5($order_id . time()), 0, 6));
            $stmt = $pdo->prepare("UPDATE orders SET pickup_code = ? WHERE id = ?");
            $stmt->execute([$pickup_code, $order_id]);
        }

        $pdo->commit();
        return true;

    } catch (Exception $e) {
        $pdo->rollBack();
        error_log("Status update error: " . $e->getMessage());
        return false;
    }
}

function getOrderStatusDisplay($status) {
    $statuses = [
        'pending' => ['text' => 'Pending', 'class' => 'warning', 'icon' => 'bi-hourglass'],
        'confirmed' => ['text' => 'Confirmed', 'class' => 'info', 'icon' => 'bi-check-circle'],
        'preparing' => ['text' => 'Preparing', 'class' => 'primary', 'icon' => 'bi-cup-hot'],
        'ready' => ['text' => 'Ready', 'class' => 'success', 'icon' => 'bi-bell'],
        'completed' => ['text' => 'Completed', 'class' => 'secondary', 'icon' => 'bi-check2-all'],
        'cancelled' => ['text' => 'Cancelled', 'class' => 'danger', 'icon' => 'bi-x-circle'],
    ];

    return $statuses[$status] ?? ['text' => 'Unknown', 'class' => 'dark', 'icon' => 'bi-question-circle'];
}

function getOrdersWithFilters($pdo, $status = 'all', $date_from = '', $date_to = '', $search = '') {
    $sql = "
        SELECT o.*,
               u.username,
               u.email,
               u.profile_pic,
               COUNT(oi.id) as item_count,
               (SELECT status FROM order_status_logs WHERE order_id = o.id ORDER BY created_at DESC LIMIT 1) as current_status
        FROM orders o
        LEFT JOIN users u ON o.user_id = u.id
        LEFT JOIN order_items oi ON o.id = oi.order_id
        WHERE 1=1
    ";

    $params = [];

    if ($status !== 'all') {
        $sql .= " AND o.status = ?";
        $params[] = $status;
    }

    if ($date_from) {
        $sql .= " AND DATE(o.created_at) >= ?";
        $params[] = $date_from;
    }

    if ($date_to) {
        $sql .= " AND DATE(o.created_at) <= ?";
        $params[] = $date_to;
    }

    if ($search) {
        $sql .= " AND (
            o.id LIKE ? OR
            u.username LIKE ? OR
            u.email LIKE ? OR
            o.payment_id LIKE ? OR
            o.pickup_code LIKE ?
        )";
        $search_param = "%$search%";
        $params = array_merge($params, [$search_param, $search_param, $search_param, $search_param, $search_param]);
    }

    $sql .= " GROUP BY o.id ORDER BY o.created_at DESC LIMIT 100";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getOrderForDashboard($pdo, $order_id) {
    $stmt = $pdo->prepare("
        SELECT o.*,
               u.username,
               u.email,
               u.profile_pic,
               (SELECT status FROM order_status_logs WHERE order_id = o.id ORDER BY created_at DESC LIMIT 1) as current_status
        FROM orders o
        LEFT JOIN users u ON o.user_id = u.id
        WHERE o.id = ?
    ");
    $stmt->execute([$order_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
