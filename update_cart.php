<?php
session_start();
require 'app/db.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    echo json_encode(['success' => false, 'message' => 'Login required']);
    exit;
}

if (!$data || !isset($data['action']) || !isset($data['item_id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
    exit;
}

$action = $data['action'];
$item_id = (int)$data['item_id'];

try {
    if ($action === 'add') {
        $stmt = $pdo->prepare("INSERT INTO cart_items (user_id, menu_item_id, quantity) 
                               VALUES (?, ?, 1) 
                               ON DUPLICATE KEY UPDATE quantity = quantity + 1");
        $stmt->execute([$user_id, $item_id]);
    } 
    elseif ($action === 'update') {
        $quantity = max(1, (int)$data['quantity']);
        $stmt = $pdo->prepare("UPDATE cart_items SET quantity = ? 
                               WHERE user_id = ? AND menu_item_id = ?");
        $stmt->execute([$quantity, $user_id, $item_id]);
    } 
    elseif ($action === 'remove') {
        $stmt = $pdo->prepare("DELETE FROM cart_items 
                               WHERE user_id = ? AND menu_item_id = ?");
        $stmt->execute([$user_id, $item_id]);
    }

    // Fetch updated cart and return it
    $stmt = $pdo->prepare("SELECT c.*, m.name, m.price, m.image_url 
                           FROM cart_items c 
                           JOIN menu_items m ON c.menu_item_id = m.id 
                           WHERE c.user_id = ?");
    $stmt->execute([$user_id]);
    $cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Calculate totals
    $total_price = 0;
    $total_items = 0;
    foreach ($cart_items as $item) {
        $total_price += $item['price'] * $item['quantity'];
        $total_items += $item['quantity'];
    }
    
    echo json_encode([
        'success' => true,
        'cart_items' => $cart_items,
        'total_price' => number_format($total_price, 2),
        'total_items' => $total_items
    ]);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>