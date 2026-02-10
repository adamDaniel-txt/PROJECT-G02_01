<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require 'app/db.php';
require 'app/order_functions.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
    exit();
}

$order_id = $_GET['order_id'] ?? 0;
$user_id = $_SESSION['user_id'];

// Get order details
$order = getOrderDetails($pdo, $order_id, $user_id);

if (!$order) {
    header('Location: order_history.php');
    exit();
}

// Get order items and status history
$order_items = getOrderItems($pdo, $order_id);
$status_history = getOrderStatusHistory($pdo, $order_id);
$current_status = $order['current_status'] ?? 'pending';
$status_display = getOrderStatusDisplay($current_status);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order #<?php echo str_pad($order_id, 6, '0', STR_PAD_LEFT); ?> - Kafe Tiga Belas</title>
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/css/main.css" rel="stylesheet">
</head>
<body class="index-page">
    <header id="header" class="header fixed-top">
        <div class="branding d-flex align-items-center">
            <div class="container position-relative d-flex align-items-center justify-content-between">
                <a href="order_history.php" class="d-flex align-items-center">
                    <i class="bi bi-arrow-left-circle-fill fs-3"></i>
                </a>
                <a href="index.php" class="logo d-flex align-items-center me-auto me-xl-0">
                    <h1 class="sitename">Kafe Tiga Belas</h1>
                    <h1 style="font-size: 3em; transform: rotate(4deg); color: var(--accent-color);"
                        class="sitename">&nbsp;&nbsp;Order Details</h1>
                </a>
            </div>
        </div>
    </header>

    <main class="main">
        <div class="container py-5" style="margin-top: 100px;">
            <div class="row">
                <div class="col-lg-8">
                    <!-- Order Status Card -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <h5 class="card-title mb-1">Order Status</h5>
                                    <h2 class="mb-0">
                                        <span class="badge bg-<?php echo $status_display['class']; ?>">
                                            <i class="bi <?php echo $status_display['icon']; ?> me-2"></i>
                                            <?php echo $status_display['text']; ?>
                                        </span>
                                    </h2>
                                </div>
                                <div class="text-end">
                                    <h6 class="mb-1">Order #</h6>
                                    <h4 class="mb-0">KTB-<?php echo str_pad($order_id, 6, '0', STR_PAD_LEFT); ?></h4>
                                </div>
                            </div>

                            <?php if ($order['estimated_ready_time']): ?>
                            <div class="alert alert-info">
                                <i class="bi bi-clock me-2"></i>
                                <strong>Estimated Ready Time:</strong>
                                <?php echo date('g:i A', strtotime($order['estimated_ready_time'])); ?>
                                (<?php echo time_ago($order['estimated_ready_time']); ?>)
                            </div>
                            <?php endif; ?>

                            <?php if ($order['pickup_code']): ?>
                            <div class="alert alert-success">
                                <i class="bi bi-key me-2"></i>
                                <strong>Pickup Code:</strong>
                                <span class="display-6"><?php echo $order['pickup_code']; ?></span>
                                <small class="d-block mt-1">Show this code when picking up your order</small>
                            </div>
                            <?php endif; ?>

                            <!-- Status Timeline -->
                            <h6 class="mt-4 mb-3">Order Progress</h6>
                            <div class="status-timeline">
                                <?php
                                $status_flow = ['pending', 'confirmed', 'preparing', 'ready', 'completed'];
                                $current_index = array_search($current_status, $status_flow);
                                $current_index = $current_index !== false ? $current_index : 0;

                                foreach ($status_flow as $index => $status):
                                    $step_display = getOrderStatusDisplay($status);
                                    $is_active = $index <= $current_index;
                                    $is_current = $index === $current_index;
                                ?>
                                <div class="status-step <?php echo $is_active ? 'active' : ''; ?>">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="mb-1">
                                                <i class="bi <?php echo $step_display['icon']; ?> me-2"></i>
                                                <?php echo $step_display['text']; ?>
                                            </h6>
                                            <?php
                                            $step_log = array_filter($status_history, function($log) use ($status) {
                                                return $log['status'] === $status;
                                            });
                                            $step_log = reset($step_log);
                                            ?>
                                            <?php if ($step_log): ?>
                                            <small>
                                                <?php echo date('M j, g:i A', strtotime($step_log['created_at'])); ?>
                                                <?php if ($step_log['notes']): ?>
                                                <br><?php echo htmlspecialchars($step_log['notes']); ?>
                                                <?php endif; ?>
                                            </small>
                                            <?php endif; ?>
                                        </div>
                                        <?php if ($is_active): ?>
                                        <div>
                                            <i class="bi bi-check-circle-fill text-success"></i>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Order Items -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="card-title mb-3">Order Items</h5>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Item</th>
                                            <th class="text-center">Quantity</th>
                                            <th class="text-end">Price</th>
                                            <th class="text-end">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($order_items as $item): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <?php if ($item['image_url']): ?>
                                                    <img src="<?php echo htmlspecialchars($item['image_url']); ?>"
                                                         alt="<?php echo htmlspecialchars($item['name']); ?>"
                                                         class="item-image me-3">
                                                    <?php endif; ?>
                                                    <div>
                                                        <h6 class="mb-1"><?php echo htmlspecialchars($item['name']); ?></h6>
                                                        <?php if ($item['description']): ?>
                                                        <small><?php echo htmlspecialchars($item['description']); ?></small>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center"><?php echo $item['quantity']; ?></td>
                                            <td class="text-end">RM <?php echo number_format($item['price'], 2); ?></td>
                                            <td class="text-end">RM <?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="3" class="text-end"><strong>Total Amount</strong></td>
                                            <td class="text-end"><strong>RM <?php echo number_format($order['total_amount'], 2); ?></strong></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <!-- Order Summary Card -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="card-title mb-3">Order Summary</h5>
                            <div class="mb-3">
                                <h6 class="mb-2">Order Information</h6>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Order Number:</span>
                                    <strong>#KTB-<?php echo str_pad($order_id, 6, '0', STR_PAD_LEFT); ?></strong>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Order Date:</span>
                                    <strong><?php echo date('M j, Y g:i A', strtotime($order['created_at'])); ?></strong>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Payment Method:</span>
                                    <strong>Card Payment</strong>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span>Payment Status:</span>
                                    <span class="badge bg-success">Paid</span>
                                </div>
                            </div>

                            <hr>

                            <div class="mb-3">
                                <h6 class="mb-2">Customer Information</h6>
                                <p class="mb-1"><strong>Name:</strong> <?php echo htmlspecialchars($order['username']); ?></p>
                                <p class="mb-0"><strong>Email:</strong> <?php echo htmlspecialchars($order['email']); ?></p>
                            </div>

                            <hr>

                            <div class="mb-3">
                                <h6 class="mb-2">Store Information</h6>
                                <p class="mb-1">Kafe Tiga Belas</p>
                                <p class="mb-1">83, Jalan Lawan Pedang 13/27</p>
                                <p class="mb-1">Tadisma Business Park</p>
                                <p class="mb-1">T40100 Shah Alam, Selangor</p>
                                <p class="mb-0">Phone: 012-234 6861</p>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="card">
                        <div class="card-body">
                            <h6 class="card-title mb-3">Order Actions</h6>
                            <div class="d-grid gap-2">
                                <a href="receipt.php?order_id=<?php echo $order_id; ?>" class="btn btn-outline-primary">
                                    <i class="bi bi-receipt me-2"></i>View Receipt
                                </a>
                                <button onclick="window.print()" class="btn btn-outline-secondary">
                                    <i class="bi bi-printer me-2"></i>Print Order
                                </button>
                                <a href="menu.php" class="btn btn-primary">
                                    <i class="bi bi-cup-hot me-2"></i>Order Again
                                </a>
                                <?php if ($current_status === 'pending'): ?>
                                <button onclick="cancelOrder(<?php echo $order_id; ?>)" class="btn btn-outline-danger">
                                    <i class="bi bi-x-circle me-2"></i>Cancel Order
                                </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Support Card -->
                    <div class="card mt-4">
                        <div class="card-body">
                            <h6 class="card-title mb-3">Need Help?</h6>
                            <p class="card-text">If you have any questions about your order, please contact us:</p>
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <i class="bi bi-telephone me-2"></i>
                                    <strong>Phone:</strong> 012-234 6861
                                </li>
                                <li class="mb-2">
                                    <i class="bi bi-envelope me-2"></i>
                                    <strong>Email:</strong> tigabelasmedia@gmail.com
                                </li>
                                <li>
                                    <i class="bi bi-clock me-2"></i>
                                    <strong>Hours:</strong> 8:00 AM - 10:00 PM
                                </li>
                            </ul>
                            <p class="card-text small">
                                Please have your order number ready when contacting support.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
        function cancelOrder(orderId) {
            if (confirm('Are you sure you want to cancel this order? This action cannot be undone.')) {
                fetch('app/cancel_order.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        order_id: orderId,
                        reason: 'Customer requested cancellation'
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Order cancelled successfully');
                        location.reload();
                    } else {
                        alert('Error: ' + (data.message || 'Failed to cancel order'));
                    }
                });
            }
        }
    </script>
</body>
</html>

<?php
function time_ago($datetime) {
    $time = strtotime($datetime);
    $now = time();
    $diff = $time - $now;

    if ($diff < 0) return 'ready now';

    $minutes = floor($diff / 60);
    $hours = floor($minutes / 60);

    if ($hours > 0) {
        return "in $hours hour" . ($hours > 1 ? 's' : '');
    } else {
        return "in $minutes minute" . ($minutes > 1 ? 's' : '');
    }
}
?>
