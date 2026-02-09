<?php
session_start();
require 'db.php';
require 'order_functions.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);
$order_id = $data['order_id'] ?? 0;
$reason = $data['reason'] ?? 'Customer requested cancellation';

if (!$order_id) {
    echo json_encode(['success' => false, 'message' => 'No order specified']);
    exit();
}

// Verify the order belongs to the user
$order = getOrderDetails($pdo, $order_id, $_SESSION['user_id']);
if (!$order) {
    echo json_encode(['success' => false, 'message' => 'Order not found']);
    exit();
}

// Check if order can be cancelled (only pending orders)
$current_status = $order['current_status'] ?? 'pending';
if ($current_status !== 'pending') {
    echo json_encode(['success' => false, 'message' => 'Order cannot be cancelled at this stage']);
    exit();
}

// Update order status to cancelled
$success = updateOrderStatus($pdo, $order_id, 'cancelled', $reason);

if ($success) {
    // Optional: Send cancellation email
    echo json_encode(['success' => true, 'message' => 'Order cancelled successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to cancel order']);
}
?>
