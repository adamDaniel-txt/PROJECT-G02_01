<?php
session_start();

error_log("=== CART DEBUG ===");
error_log("Session user_id: " . ($_SESSION['user_id'] ?? 'NOT SET'));
error_log("POST data: " . file_get_contents('php://input'));

require 'app/db.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login to checkout']);
    exit();
}

// Get the JSON data sent from your JavaScript
$data = json_decode(file_get_contents('php://input'), true);

if (!$data || empty($data['items'])) {
    echo json_encode(['success' => false, 'message' => 'Cart is empty']);
    exit();
}

try {
    // Start a transaction (so if one part fails, nothing is saved)
    $pdo->beginTransaction();

    // 1. Create the main Order entry
    $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_price, status) VALUES (?, ?, 'pending')");
    $stmt->execute([$_SESSION['user_id'], $data['total']]);
    
    // Get the newly created Order ID
    $orderId = $pdo->lastInsertId();

    // 2. Insert each item into order_items
    $itemStmt = $pdo->prepare("INSERT INTO order_items (order_id, menu_item_id, quantity, price_at_purchase) VALUES (?, ?, ?, ?)");

    foreach ($data['items'] as $item) {
        $itemStmt->execute([
            $orderId,
            $item['id'],
            $item['quantity'],
            $item['price']
        ]);
    }

    $pdo->commit();
    echo json_encode(['success' => true, 'order_id' => $orderId]);

} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}