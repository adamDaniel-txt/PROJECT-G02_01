<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require 'app/db.php';
require 'app/order_functions.php';
require 'app/permission.php';

// Check if user have permission
if (!hasPermission('view_dashboard')) {
    header('Location: index.php');
    exit();
}

$message = '';
$message_type = '';

// Handle status updates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_status'])) {
        $order_id = intval($_POST['order_id']);
        $new_status = trim($_POST['status']);
        $notes = trim($_POST['notes'] ?? '');

        if (updateOrderStatus($pdo, $order_id, $new_status, $notes)) {
            $message = 'Order status updated successfully!';
            $message_type = 'success';
        } else {
            $message = 'Failed to update order status.';
            $message_type = 'error';
        }
    } elseif (isset($_POST['cancel_order'])) {
        $order_id = intval($_POST['order_id']);
        $reason = trim($_POST['cancel_reason'] ?? 'Staff cancelled order');

        if (updateOrderStatus($pdo, $order_id, 'cancelled', $reason)) {
            $message = 'Order cancelled successfully!';
            $message_type = 'success';
        } else {
            $message = 'Failed to cancel order.';
            $message_type = 'error';
        }
    }
}

// Get filter parameters
$status_filter = $_GET['status'] ?? 'all';
$date_from = $_GET['date_from'] ?? '';
$date_to = $_GET['date_to'] ?? '';
$search = $_GET['search'] ?? '';

// Get orders with filters
$orders = getOrdersWithFilters($pdo, $status_filter, $date_from, $date_to, $search);

// Order statistics
$total_orders = count($orders);
$pending_count = 0;
$preparing_count = 0;
$ready_count = 0;
$completed_count = 0;
$cancelled_count = 0;

foreach ($orders as $order) {
    switch ($order['current_status']) {
        case 'pending': $pending_count++; break;
        case 'preparing': $preparing_count++; break;
        case 'ready': $ready_count++; break;
        case 'completed': $completed_count++; break;
        case 'cancelled': $cancelled_count++; break;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders Management</title>

    <!-- Same CSS as dashboard -->
    <link rel="stylesheet" href="assets/css/dashStyle.css">

    <!-- Bootstrap Icons only -->
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/vendor/aos/aos.css" rel="stylesheet">
    <style>
        .card { border-radius: 10px; border: none; }
        .table-actions { white-space: nowrap; }
        .order-card {
            border-left: 4px solid;
            transition: transform 0.2s;
        }
        .order-card.pending { border-left-color: #ffc107; }
        .order-card.confirmed { border-left-color: #0dcaf0; }
        .order-card.preparing { border-left-color: #0d6efd; }
        .order-card.ready { border-left-color: #198754; }
        .order-card.completed { border-left-color: #6c757d; }
        .order-card.cancelled { border-left-color: #dc3545; }
        .stat-card {
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            color: white;
        }
        .stat-card.pending { background: #ffc107; }
        .stat-card.preparing { background: #0d6efd; }
        .stat-card.ready { background: #198754; }
        .stat-card.completed { background: #6c757d; }
        .stat-card.cancelled { background: #dc3545; }
        .order-number {
            font-family: monospace;
            font-size: 0.8em;
            color: #0d6efd;
        }
        .time-remaining {
            font-size: 0.9em;
            color: #6c757d;
        }
        .customer-avatar {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            object-fit: cover;
        }
    </style>
</head>

<body>
    <div class="app-container">

        <!-- Side Bar-->
        <aside class="sidebar" id="sidebar">
            <div class="logo">
                <div class="logo-text">Dashboard</div>
            </div>

            <nav class="nav-section">
                <div class="nav-label">Main Menu</div>
                <a href="dashboard.php" class="nav-item">
                    <i class="bi bi-graph-up"></i>
                    <span>Dashboard</span>
                </a>

                <a href="sales.php" class="nav-item">
                    <i class="bi bi-cart3"></i>
                    <span>Sales</span>
                </a>

                <a href="menu_items.php" class="nav-item">
                    <i class="bi bi-box"></i>
                    <span>Menu Items</span>
                </a>

                <?php if (hasPermission('manage_customers')): ?>
                <a href="customers.php" class="nav-item">
                    <i class="bi bi-people"></i>
                    <span>Customers</span>
                </a>
                <?php elseif (hasPermission('manage_staff')): ?>
                <a href="staff.php" class="nav-item">
                    <i class="bi bi-people"></i>
                    <span>Staff</span>
                </a>
                <?php endif; ?>

                <a href="#" class="nav-item active">
                    <i class="bi bi-receipt"></i>
                    <span>Orders</span>
                </a>

                <?php if (hasPermission('manage_feedback')): ?>
                <a href="feedback.php" class="nav-item">
                    <i class="bi bi-chat-left-text"></i>
                    <span>Feedback</span>
                </a>
                <?php endif; ?>
            </nav>

            <div class="sidebar-footer">
                <a href="profile.php" class="nav-item">
                    <i class="bi bi-person"></i>
                    <span>Profile</span>
                </a>
                <a href="index.php" class="nav-item">
                    <i class="bi bi-box-arrow-right"></i>
                    <span>Go Home</span>
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="main-content">

            <div class="col-md-13 col-lg-12 p-4">
                <!-- Messages -->
                <?php if ($message): ?>
                    <div class="alert alert-<?php echo $message_type === 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show">
                        <?php echo htmlspecialchars($message); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="top-bar d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h3 mb-0">
                        <i class="bi bi-receipt me-2"></i>Order Management
                    </h1>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#filterModal">
                        <i class="bi bi-funnel me-1"></i>Filter Orders
                    </button>
                </div>

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-md-2">
                        <div class="stat-card pending">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h3 class="mb-0"><?php echo $pending_count; ?></h3>
                                    <small>Pending</small>
                                </div>
                                <i class="bi bi-hourglass display-6 opacity-50"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="stat-card preparing">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h3 class="mb-0"><?php echo $preparing_count; ?></h3>
                                    <small>Preparing</small>
                                </div>
                                <i class="bi bi-cup-hot display-6 opacity-50"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="stat-card ready">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h3 class="mb-0"><?php echo $ready_count; ?></h3>
                                    <small>Ready</small>
                                </div>
                                <i class="bi bi-bell display-6 opacity-50"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="stat-card completed">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h3 class="mb-0"><?php echo $completed_count; ?></h3>
                                    <small>Completed</small>
                                </div>
                                <i class="bi bi-check2-all display-6 opacity-50"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="stat-card cancelled">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h3 class="mb-0"><?php echo $cancelled_count; ?></h3>
                                    <small>Cancelled</small>
                                </div>
                                <i class="bi bi-x-circle display-6 opacity-50"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="stat-card" style="background: #6f42c1;">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h3 class="mb-0"><?php echo $total_orders; ?></h3>
                                    <small>Total</small>
                                </div>
                                <i class="bi bi-receipt display-6 opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filter Summary -->
                <?php if ($status_filter !== 'all' || $date_from || $date_to || $search): ?>
                <div class="alert alert-info mb-4">
                    <i class="bi bi-funnel me-2"></i>
                    <strong>Active Filters:</strong>
                    <?php
                    $filters = [];
                    if ($status_filter !== 'all') $filters[] = "Status: " . ucfirst($status_filter);
                    if ($date_from) $filters[] = "From: " . htmlspecialchars($date_from);
                    if ($date_to) $filters[] = "To: " . htmlspecialchars($date_to);
                    if ($search) $filters[] = "Search: \"" . htmlspecialchars($search) . "\"";
                    echo implode(', ', $filters);
                    ?>
                    <a href="orders.php" class="float-end">Clear filters</a>
                </div>
                <?php endif; ?>

                <!-- Orders Table/Cards -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <?php if (empty($orders)): ?>
                            <div class="text-center text-muted py-5">
                                <i class="bi bi-receipt display-1 opacity-25"></i>
                                <h4 class="mt-3">No orders found</h4>
                                <p>Try adjusting your filters or check back later.</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Order ID</th>
                                            <th>Customer</th>
                                            <th>Items</th>
                                            <th>Total</th>
                                            <th>Status</th>
                                            <th>Order Time</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($orders as $order): ?>
                                            <?php
                                            $status_display = getOrderStatusDisplay($order['current_status']);
                                            $order_items = getOrderItems($pdo, $order['id']);
                                            ?>
                                            <tr class="order-card <?php echo $order['current_status']; ?>">
                                                <td>
                                                    <strong class="order-number">
                                                        #KTB-<?php echo str_pad($order['id'], 6, '0', STR_PAD_LEFT); ?>
                                                    </strong>
                                                    <?php if ($order['pickup_code']): ?>
                                                        <br>
                                                        <small class="text-muted">
                                                            Code: <?php echo $order['pickup_code']; ?>
                                                        </small>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <?php if ($order['profile_picture']): ?>
                                                            <img src="<?php echo htmlspecialchars($order['profile_picture']); ?>"
                                                                 alt="Customer"
                                                                 class="customer-avatar me-2">
                                                        <?php else: ?>
                                                            <div class="customer-avatar bg-secondary d-flex align-items-center justify-content-center me-2">
                                                                <i class="bi bi-person text-white"></i>
                                                            </div>
                                                        <?php endif; ?>
                                                        <div>
                                                            <div><?php echo htmlspecialchars($order['username'] ?? 'Guest'); ?></div>
                                                            <small class="text-muted">
                                                                <?php echo htmlspecialchars($order['email'] ?? ''); ?>
                                                            </small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <?php if (!empty($order_items)): ?>
                                                        <?php
                                                        $item_names = array_map(function($item) {
                                                            return $item['name'] . ' (x' . $item['quantity'] . ')';
                                                        }, array_slice($order_items, 0, 2));

                                                        echo implode('<br>', $item_names);
                                                        if (count($order_items) > 2) {
                                                            echo '<br><small class="text-muted">+' . (count($order_items) - 2) . ' more</small>';
                                                        }
                                                        ?>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="fw-bold">RM <?php echo number_format($order['total_amount'], 2); ?></td>
                                                <td>
                                                    <span class="badge bg-<?php echo $status_display['class']; ?>">
                                                        <i class="bi <?php echo $status_display['icon']; ?> me-1"></i>
                                                        <?php echo $status_display['text']; ?>
                                                    </span>
                                                    <?php if ($order['estimated_ready_time']): ?>
                                                        <br>
                                                        <small class="time-remaining">
                                                            Est: <?php echo date('g:i A', strtotime($order['estimated_ready_time'])); ?>
                                                        </small>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php echo date('M j, Y', strtotime($order['created_at'])); ?><br>
                                                    <small class="text-muted">
                                                        <?php echo date('g:i A', strtotime($order['created_at'])); ?>
                                                    </small>
                                                </td>
                                                <td class="table-actions">
                                                    <button class="btn btn-sm btn-outline-success"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#updateStatusModal"
                                                            onclick="updateOrderStatusModal(<?php echo $order['id']; ?>, '<?php echo $order['current_status']; ?>')">
                                                        <i class="bi bi-pencil"></i> Status
                                                    </button>
                                                    <?php if ($order['current_status'] !== 'cancelled' && $order['current_status'] !== 'completed'): ?>
                                                        <button class="btn btn-sm btn-outline-danger"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#cancelOrderModal"
                                                                onclick="cancelOrderModal(<?php echo $order['id']; ?>)">
                                                            <i class="bi bi-x-circle"></i> Cancel
                                                        </button>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Modal -->
    <div class="modal fade" id="filterModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="GET" action="orders.php">
                    <div class="modal-header">
                        <h5 class="modal-title">Filter Orders</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status">
                                <option value="all" <?php echo $status_filter === 'all' ? 'selected' : ''; ?>>All Statuses</option>
                                <option value="pending" <?php echo $status_filter === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                <option value="confirmed" <?php echo $status_filter === 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                                <option value="preparing" <?php echo $status_filter === 'preparing' ? 'selected' : ''; ?>>Preparing</option>
                                <option value="ready" <?php echo $status_filter === 'ready' ? 'selected' : ''; ?>>Ready</option>
                                <option value="completed" <?php echo $status_filter === 'completed' ? 'selected' : ''; ?>>Completed</option>
                                <option value="cancelled" <?php echo $status_filter === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Date From</label>
                                <input type="date" class="form-control" name="date_from" value="<?php echo htmlspecialchars($date_from); ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Date To</label>
                                <input type="date" class="form-control" name="date_to" value="<?php echo htmlspecialchars($date_to); ?>">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Search (Order ID, Customer Name, Email)</label>
                            <input type="text" class="form-control" name="search" value="<?php echo htmlspecialchars($search); ?>"
                                   placeholder="Search orders...">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Apply Filters</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Update Status Modal -->
    <div class="modal fade" id="updateStatusModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <input type="hidden" name="order_id" id="updateOrderId">
                    <div class="modal-header">
                        <h5 class="modal-title">Update Order Status</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Order ID</label>
                            <input type="text" class="form-control" id="updateOrderNumber" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">New Status *</label>
                            <select class="form-select" name="status" id="updateStatus" required>
                                <option value="pending">Pending</option>
                                <option value="confirmed">Confirmed</option>
                                <option value="preparing">Preparing</option>
                                <option value="ready">Ready</option>
                                <option value="completed">Completed</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Notes (Optional)</label>
                            <textarea class="form-control" name="notes" rows="3"
                                      placeholder="Add notes about this status change..."></textarea>
                        </div>
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            <small>
                                • "Preparing" will set estimated ready time to 15 minutes from now<br>
                                • "Ready" will generate a pickup code for the customer
                            </small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="update_status" class="btn btn-primary">Update Status</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Cancel Order Modal -->
    <div class="modal fade" id="cancelOrderModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <input type="hidden" name="order_id" id="cancelOrderId">
                    <div class="modal-header">
                        <h5 class="modal-title">Cancel Order</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Order ID</label>
                            <input type="text" class="form-control" id="cancelOrderNumber" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Reason for Cancellation *</label>
                            <textarea class="form-control" name="cancel_reason" rows="3" required
                                      placeholder="Enter reason for cancelling this order..."></textarea>
                        </div>
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            This action cannot be undone. The customer will be notified.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Keep Order</button>
                        <button type="submit" name="cancel_order" class="btn btn-danger">Cancel Order</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Function to view order details
        function viewOrder(orderId) {
            const orderNumber = '#KTB-' + orderId.toString().padStart(6, '0');
            document.getElementById('viewOrderNumber').textContent = orderNumber;

            // Load order details via AJAX
            fetch('app/get_order_details.php?order_id=' + orderId)
                .then(response => response.text())
                .then(html => {
                    document.getElementById('viewOrderContent').innerHTML = html;
                })
                .catch(error => {
                    document.getElementById('viewOrderContent').innerHTML =
                        '<div class="alert alert-danger">Error loading order details. Please try again.</div>';
                });
        }

        // Function to update order status modal
        function updateOrderStatusModal(orderId, currentStatus) {
            const orderNumber = '#KTB-' + orderId.toString().padStart(6, '0');
            document.getElementById('updateOrderId').value = orderId;
            document.getElementById('updateOrderNumber').value = orderNumber;
            document.getElementById('updateStatus').value = currentStatus;
        }

        // Function to cancel order modal
        function cancelOrderModal(orderId) {
            const orderNumber = '#KTB-' + orderId.toString().padStart(6, '0');
            document.getElementById('cancelOrderId').value = orderId;
            document.getElementById('cancelOrderNumber').value = orderNumber;
        }

        // Auto-dismiss alerts after 5 seconds
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>
</body>
</html>
