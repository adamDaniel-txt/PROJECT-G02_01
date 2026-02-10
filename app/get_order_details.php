<?php
session_start();
require 'db.php';
require 'order_functions.php';

// Simple authentication
if (!isset($_SESSION['user_id'])) {
    echo '<div class="alert alert-danger">Access denied</div>';
    exit();
}

$order_id = $_GET['order_id'] ?? 0;

if (!$order_id) {
    echo '<div class="alert alert-danger">No order specified</div>';
    exit();
}

// Get order details
$order = getOrderForDashboard($pdo, $order_id);
if (!$order) {
    echo '<div class="alert alert-warning">Order not found</div>';
    exit();
}

// Get order items and status history
$order_items = getOrderItems($pdo, $order_id);
$status_history = getOrderStatusHistory($pdo, $order_id);
$current_status = $order['current_status'] ?? 'pending';
$status_display = getOrderStatusDisplay($current_status);
?>
<div class="order-details">
    <!-- Order Header -->
    <div class="row mb-4">
        <div class="col-md-6">
            <h6 class="text-muted mb-2">Order Information</h6>
            <p class="mb-1"><strong>Order #:</strong> KTB-<?php echo str_pad($order_id, 6, '0', STR_PAD_LEFT); ?></p>
            <p class="mb-1"><strong>Date:</strong> <?php echo date('F j, Y g:i A', strtotime($order['created_at'])); ?></p>
            <p class="mb-1"><strong>Status:</strong>
                <span class="badge bg-<?php echo $status_display['class']; ?>">
                    <?php echo $status_display['text']; ?>
                </span>
            </p>
            <?php if ($order['pickup_code']): ?>
            <p class="mb-1"><strong>Pickup Code:</strong>
                <span class="badge bg-info"><?php echo $order['pickup_code']; ?></span>
            </p>
            <?php endif; ?>
            <?php if ($order['estimated_ready_time']): ?>
            <p class="mb-1"><strong>Estimated Ready:</strong>
                <?php echo date('g:i A', strtotime($order['estimated_ready_time'])); ?>
            </p>
            <?php endif; ?>
        </div>
        <div class="col-md-6">
            <h6 class="text-muted mb-2">Customer Information</h6>
            <div class="d-flex align-items-center mb-2">
                <?php if ($order['profile_pic']): ?>
                <img src="<?php echo htmlspecialchars($order['profile_pic']); ?>"
                     alt="Customer"
                     class="rounded-circle me-2" width="40" height="40">
                <?php endif; ?>
                <div>
                    <p class="mb-1"><strong>Name:</strong> <?php echo htmlspecialchars($order['username'] ?? 'Guest'); ?></p>
                    <p class="mb-1"><strong>Email:</strong> <?php echo htmlspecialchars($order['email'] ?? 'Not provided'); ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Order Items -->
    <h6 class="text-muted mb-3">Order Items</h6>
    <div class="table-responsive mb-4">
        <table class="table table-sm">
            <thead>
                <tr>
                    <th>Item</th>
                    <th class="text-center">Qty</th>
                    <th class="text-end">Price</th>
                    <th class="text-end">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($order_items as $item): ?>
                <tr>
                    <td>
                        <div><?php echo htmlspecialchars($item['name']); ?></div>
                        <?php if ($item['description']): ?>
                        <small class="text-muted"><?php echo htmlspecialchars(substr($item['description'], 0, 50)); ?>...</small>
                        <?php endif; ?>
                    </td>
                    <td class="text-center"><?php echo $item['quantity']; ?></td>
                    <td class="text-end">RM <?php echo number_format($item['price'], 2); ?></td>
                    <td class="text-end">RM <?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                </tr>
                <?php endforeach; ?>
                <tr class="table-light">
                    <td colspan="3" class="text-end"><strong>Total Amount</strong></td>
                    <td class="text-end"><strong>RM <?php echo number_format($order['total_amount'], 2); ?></strong></td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Status History -->
    <h6 class="text-muted mb-3">Status History</h6>
    <div class="status-history">
        <?php if (empty($status_history)): ?>
            <p class="text-muted">No status history available.</p>
        <?php else: ?>
            <?php foreach ($status_history as $log): ?>
            <div class="d-flex mb-2">
                <div class="me-3">
                    <?php
                    $log_display = getOrderStatusDisplay($log['status']);
                    ?>
                    <i class="bi <?php echo $log_display['icon']; ?>"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between">
                        <strong><?php echo $log_display['text']; ?></strong>
                        <small class="text-muted"><?php echo date('M j, g:i A', strtotime($log['created_at'])); ?></small>
                    </div>
                    <?php if ($log['notes']): ?>
                    <small class="text-muted"><?php echo htmlspecialchars($log['notes']); ?></small>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Payment Information -->
    <hr class="my-4">
    <div class="row">
        <div class="col-md-6">
            <h6 class="text-muted mb-2">Payment Information</h6>
            <p class="mb-1"><strong>Payment ID:</strong> <?php echo $order['payment_id']; ?></p>
            <p class="mb-1"><strong>Amount:</strong> RM <?php echo number_format($order['total_amount'], 2); ?></p>
            <p class="mb-0"><strong>Payment Status:</strong> <span class="badge bg-success">Paid</span></p>
        </div>
        <div class="col-md-6">
            <h6 class="text-muted mb-2">Store Information</h6>
            <p class="mb-1">Kafe Tiga Belas</p>
            <p class="mb-1">83, Jalan Lawan Pedang 13/27</p>
            <p class="mb-1">Tadisma Business Park</p>
            <p class="mb-0">T40100 Shah Alam, Selangor</p>
        </div>
    </div>
</div>
