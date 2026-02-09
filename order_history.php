<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require 'app/db.php';
require 'app/order_functions.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?redirect=order_history.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Get customer orders
$orders = getCustomerOrders($pdo, $user_id, 20);

// Pagination
$page = $_GET['page'] ?? 1;
$limit = 10;
$offset = ($page - 1) * $limit;
$total_orders = count($orders);
$total_pages = ceil($total_orders / $limit);
$paged_orders = array_slice($orders, $offset, $limit);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order History - Kafe Tiga Belas</title>
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
                        class="sitename">&nbsp;&nbsp;My Orders</h1>
                </a>
            </div>
        </div>
    </header>

    <main class="main">
        <div class="container py-5" style="margin-top: 100px;">
            <div class="row">
                <div class="col-lg-3">
                    <!-- Sidebar Navigation -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="card-title">Order Management</h5>
                            <ul class="nav nav-pills flex-column">
                                <li class="nav-item">
                                    <a class="nav-link active" href="order_history.php">
                                        <i class="bi bi-clock-history me-2"></i>Order History
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="order_track.php">
                                        <i class="bi bi-truck me-2"></i>Track Order
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="profile.php">
                                        <i class="bi bi-person me-2"></i>My Profile
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Stats Summary -->
                    <div class="card">
                        <div class="card-body">
                            <h6 class="card-subtitle mb-3">Order Summary</h6>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Total Orders:</span>
                                <strong><?php echo count($orders); ?></strong>
                            </div>
                            <?php
                            $status_counts = [];
                            foreach ($orders as $order) {
                                $status = $order['current_status'] ?? 'pending';
                                $status_counts[$status] = ($status_counts[$status] ?? 0) + 1;
                            }
                            ?>
                            <?php foreach ($status_counts as $status => $count): ?>
                            <?php $display = getOrderStatusDisplay($status); ?>
                            <div class="d-flex justify-content-between mb-2">
                                <span>
                                    <i class="bi <?php echo $display['icon']; ?> me-1"></i>
                                    <?php echo $display['text']; ?>:
                                </span>
                                <strong><?php echo $count; ?></strong>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <div class="col-lg-9">
                    <!-- Orders List -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2>Order History</h2>
                        <div class="btn-group">
                            <button type="button" class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
                                Filter by Status
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="?status=all">All Orders</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="?status=pending">Pending</a></li>
                                <li><a class="dropdown-item" href="?status=preparing">Preparing</a></li>
                                <li><a class="dropdown-item" href="?status=ready">Ready</a></li>
                                <li><a class="dropdown-item" href="?status=completed">Completed</a></li>
                            </ul>
                        </div>
                    </div>

                    <?php if (empty($paged_orders)): ?>
                        <div class="empty-state">
                            <i class="bi bi-cart display-1 opacity-25 mb-3"></i>
                            <h3>No Orders Yet</h3>
                            <p class="mb-4">You haven't placed any orders yet. Start by exploring our menu!</p>
                            <a href="menu.php" class="btn btn-primary">
                                <i class="bi bi-cup-hot me-2"></i>Browse Menu
                            </a>
                        </div>
                    <?php else: ?>
                        <!-- Orders List -->
                        <?php foreach ($paged_orders as $order): ?>
                        <?php
                        $order_status = $order['current_status'] ?? 'pending';
                        $status_display = getOrderStatusDisplay($order_status);
                        ?>
                        <div class="order-card">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <div class="d-flex align-items-center mb-2">
                                        <h5 class="order-number mb-0 me-3">
                                            #KTB-<?php echo str_pad($order['id'], 6, '0', STR_PAD_LEFT); ?>
                                        </h5>
                                        <span class="badge bg-<?php echo $status_display['class']; ?> status-badge">
                                            <i class="bi <?php echo $status_display['icon']; ?> me-1"></i>
                                            <?php echo $status_display['text']; ?>
                                        </span>
                                    </div>

                                    <div class="d-flex flex-wrap gap-3 mb-2">
                                        <div>
                                            <small>Order Date</small>
                                            <div><?php echo date('M j, Y g:i A', strtotime($order['created_at'])); ?></div>
                                        </div>
                                        <div>
                                            <small>Items</small>
                                            <div><?php echo $order['item_count']; ?> items</div>
                                        </div>
                                        <div>
                                            <small>Total</small>
                                            <div class="fw-bold">RM <?php echo number_format($order['total_amount'], 2); ?></div>
                                        </div>
                                    </div>

                                    <?php if ($order['estimated_ready_time']): ?>
                                    <div class="time-remaining">
                                        <i class="bi bi-clock me-1"></i>
                                        Estimated ready:
                                        <?php echo date('g:i A', strtotime($order['estimated_ready_time'])); ?>
                                        (<?php echo time_ago($order['estimated_ready_time']); ?>)
                                    </div>
                                    <?php endif; ?>

                                    <?php if ($order['pickup_code']): ?>
                                    <div class="mt-2">
                                        <span class="badge bg-info">
                                            <i class="bi bi-key me-1"></i>
                                            Pickup Code: <?php echo $order['pickup_code']; ?>
                                        </span>
                                    </div>
                                    <?php endif; ?>
                                </div>

                                <div class="col-md-4 text-end">
                                    <div class="d-flex flex-column gap-2">
                                        <a href="order_details.php?order_id=<?php echo $order['id']; ?>"
                                           class="btn btn-outline-primary btn-sm">
                                            <i class="bi bi-eye me-1"></i>View Details
                                        </a>
                                        <a href="receipt.php?order_id=<?php echo $order['id']; ?>"
                                           class="btn btn-outline-secondary btn-sm">
                                            <i class="bi bi-receipt me-1"></i>View Receipt
                                        </a>
                                        <?php if ($order_status === 'pending'): ?>
                                        <button class="btn btn-outline-danger btn-sm"
                                                onclick="cancelOrder(<?php echo $order['id']; ?>)">
                                            <i class="bi bi-x-circle me-1"></i>Cancel Order
                                        </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>

                        <!-- Pagination -->
                        <?php if ($total_pages > 1): ?>
                        <nav aria-label="Page navigation" class="mt-4">
                            <ul class="pagination justify-content-center">
                                <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $page - 1; ?>">Previous</a>
                                </li>

                                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                </li>
                                <?php endfor; ?>

                                <li class="page-item <?php echo $page >= $total_pages ? 'disabled' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $page + 1; ?>">Next</a>
                                </li>
                            </ul>
                        </nav>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <footer id="footer" class="footer">
        <div class="container copyright text-center mt-4">
            <p>© <span>Copyright</span> <strong class="px-1 sitename">Kafe Tiga Belas</strong> <span>All Rights Reserved</span></p>
        </div>
    </footer>

    <!-- Cancel Order Modal -->
    <div class="modal fade" id="cancelModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Cancel Order</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to cancel this order? This action cannot be undone.</p>
                    <div class="mb-3">
                        <label class="form-label">Reason for cancellation (optional):</label>
                        <textarea class="form-control" id="cancelReason" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Keep Order</button>
                    <button type="button" class="btn btn-danger" onclick="confirmCancel()">Cancel Order</button>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
        let cancelOrderId = null;

        function cancelOrder(orderId) {
            cancelOrderId = orderId;
            const modal = new bootstrap.Modal(document.getElementById('cancelModal'));
            modal.show();
        }

        function confirmCancel() {
            if (!cancelOrderId) return;

            const reason = document.getElementById('cancelReason').value;

            fetch('app/cancel_order.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    order_id: cancelOrderId,
                    reason: reason
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error: ' + (data.message || 'Failed to cancel order'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while cancelling the order');
            });
        }
    </script>
</body>
</html>

<?php
// Helper function for time display
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
