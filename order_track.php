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

$user_id = $_SESSION['user_id'];

// Get active orders (not completed or cancelled)
$active_orders = [];
$all_orders = getCustomerOrders($pdo, $user_id, 50);

foreach ($all_orders as $order) {
    $status = $order['current_status'] ?? 'pending';
    if (!in_array($status, ['completed', 'cancelled'])) {
        $active_orders[] = $order;
    }
}

// Get specific order if tracking code is provided
$tracking_code = $_GET['code'] ?? '';
$tracked_order = null;

if ($tracking_code) {
    // Search by order ID or pickup code
    if (strpos($tracking_code, 'KTB-') === 0) {
        $order_id = str_replace('KTB-', '', $tracking_code);
        $tracked_order = getOrderDetails($pdo, $order_id, $user_id);
    } else {
        // Search by pickup code
        $stmt = $pdo->prepare("
            SELECT o.* FROM orders o
            WHERE o.pickup_code = ? AND o.user_id = ?
        ");
        $stmt->execute([strtoupper($tracking_code), $user_id]);
        $tracked_order = $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Order - Kafe Tiga Belas</title>
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/css/main.css" rel="stylesheet">
</head>
<body class="index-page">
    <header id="header" class="header fixed-top">
        <div class="branding d-flex align-items-center">
            <div class="container position-relative d-flex align-items-center justify-content-between">
                <a href="menu.php" class="d-flex align-items-center">
                    <i class="bi bi-arrow-left-circle-fill fs-3"></i>
                    <span class="ms-2">Back to Menu</span>
                </a>
                <a href="index.php" class="logo d-flex align-items-center me-auto me-xl-0">
                    <h1 class="sitename">Kafe Tiga Belas</h1>
                    <h1 style="font-size: 3em; transform: rotate(4deg); color: var(--accent-color);"
                        class="sitename">&nbsp;&nbsp;Track Order</h1>
                </a>
            </div>
        </div>
    </header>

    <main class="main">
        <div class="container py-5" style="margin-top: 100px;">
            <!-- Track Order Form -->
            <div class="tracking-card">
                <h2 class="mb-4">Track Your Order</h2>
                <form method="GET" class="row g-3">
                    <div class="col-md-8">
                        <input type="text"
                               name="code"
                               class="form-control tracking-input"
                               placeholder="Enter Order Number (e.g., KTB-000001) or Pickup Code"
                               value="<?php echo htmlspecialchars($tracking_code); ?>"
                               required>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary btn-lg w-100">
                            <i class="bi bi-search me-2"></i>Track Order
                        </button>
                    </div>
                </form>
                <p class="mt-3 mb-0">
                    <i class="bi bi-info-circle me-1"></i>
                    Enter your order number (e.g., KTB-000001) or the 6-digit pickup code
                </p>
            </div>

            <?php if ($tracking_code && $tracked_order): ?>
                <!-- Order Tracking Results -->
                <?php
                $order_id = $tracked_order['id'];
                $status = $tracked_order['current_status'] ?? 'pending';
                $status_display = getOrderStatusDisplay($status);
                $order_items = getOrderItems($pdo, $order_id);
                $status_history = getOrderStatusHistory($pdo, $order_id);

                // Determine progress step
                $status_steps = ['pending', 'confirmed', 'preparing', 'ready', 'completed'];
                $current_step = array_search($status, $status_steps);
                $current_step = $current_step !== false ? $current_step : 0;
                ?>

                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <h3 class="card-title">
                                    Order #KTB-<?php echo str_pad($order_id, 6, '0', STR_PAD_LEFT); ?>
                                    <span class="badge bg-<?php echo $status_display['class']; ?> ms-2">
                                        <?php echo $status_display['text']; ?>
                                    </span>
                                </h3>

                                <!-- Progress Tracker -->
                                <div class="progress-tracker">
                                    <?php foreach ($status_steps as $index => $step): ?>
                                    <?php $step_display = getOrderStatusDisplay($step); ?>
                                    <div class="progress-step">
                                        <div class="step-icon <?php echo $index <= $current_step ? 'active' : ''; ?>">
                                            <i class="bi <?php echo $step_display['icon']; ?>"></i>
                                        </div>
                                        <div class="step-label <?php echo $index <= $current_step ? 'active' : ''; ?>">
                                            <?php echo $step_display['text']; ?>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>

                                <!-- Order Info -->
                                <div class="row mt-4">
                                    <div class="col-md-6">
                                        <h6>Order Information</h6>
                                        <p class="mb-2"><strong>Order Date:</strong> <?php echo date('M j, Y g:i A', strtotime($tracked_order['created_at'])); ?></p>
                                        <p class="mb-2"><strong>Items:</strong> <?php echo count($order_items); ?> items</p>
                                        <p class="mb-2"><strong>Total:</strong> RM <?php echo number_format($tracked_order['total_amount'], 2); ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <h6>Estimated Timeline</h6>
                                        <?php if ($tracked_order['estimated_ready_time']): ?>
                                        <p class="mb-2">
                                            <strong>Ready by:</strong>
                                            <?php echo date('g:i A', strtotime($tracked_order['estimated_ready_time'])); ?>
                                            (<?php echo time_ago($tracked_order['estimated_ready_time']); ?>)
                                        </p>
                                        <?php endif; ?>
                                        <?php if ($tracked_order['pickup_code']): ?>
                                        <p class="mb-2">
                                            <strong>Pickup Code:</strong>
                                            <span class="badge bg-info"><?php echo $tracked_order['pickup_code']; ?></span>
                                        </p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="card bg-dark">
                                    <div class="card-body">
                                        <h6>Order Actions</h6>
                                        <div class="d-grid gap-2">
                                            <a href="order_details.php?order_id=<?php echo $order_id; ?>"
                                               class="btn btn-outline-primary">
                                                <i class="bi bi-eye me-2"></i>View Details
                                            </a>
                                            <a href="receipt.php?order_id=<?php echo $order_id; ?>"
                                               class="btn btn-outline-secondary">
                                                <i class="bi bi-receipt me-2"></i>View Receipt
                                            </a>
                                            <?php if ($status === 'ready'): ?>
                                            <div class="alert alert-success mt-3">
                                                <i class="bi bi-bell me-2"></i>
                                                Your order is ready for pickup!
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            <?php elseif ($tracking_code && !$tracked_order): ?>
                <!-- No Order Found -->
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    No order found with tracking code "<?php echo htmlspecialchars($tracking_code); ?>"
                    <br>
                    <small class="d-block mt-1">Please check your order number or pickup code and try again.</small>
                </div>
            <?php endif; ?>

            <!-- Active Orders -->
            <?php if (empty($tracking_code) && !empty($active_orders)): ?>
            <div class="mt-5">
                <h3 class="mb-4">Your Active Orders</h3>
                <div class="row">
                    <?php foreach ($active_orders as $order): ?>
                    <?php
                    $order_status = $order['current_status'] ?? 'pending';
                    $status_display = getOrderStatusDisplay($order_status);
                    ?>
                    <div class="col-md-6 mb-3">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h5 class="card-title mb-0">
                                        #KTB-<?php echo str_pad($order['id'], 6, '0', STR_PAD_LEFT); ?>
                                    </h5>
                                    <span class="badge bg-<?php echo $status_display['class']; ?>">
                                        <?php echo $status_display['text']; ?>
                                    </span>
                                </div>
                                <p class="card-text mb-2">
                                    <small>
                                        <?php echo date('M j, g:i A', strtotime($order['created_at'])); ?> •
                                        <?php echo $order['item_count']; ?> items
                                    </small>
                                </p>
                                <p class="card-text mb-3">
                                    <strong>Total: RM <?php echo number_format($order['total_amount'], 2); ?></strong>
                                </p>
                                <?php if ($order['estimated_ready_time']): ?>
                                <p class="card-text">
                                    <i class="bi bi-clock me-1"></i>
                                    Ready in <?php echo time_ago($order['estimated_ready_time']); ?>
                                </p>
                                <?php endif; ?>
                                <a href="order_details.php?order_id=<?php echo $order['id']; ?>"
                                   class="btn btn-outline-primary btn-sm">
                                    Track This Order
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </main>

    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
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
        return "$hours hour" . ($hours > 1 ? 's' : '');
    } else {
        return "$minutes minute" . ($minutes > 1 ? 's' : '');
    }
}
?>
