<?php
session_start();
require 'app/db.php';
require 'app/dashboard_functions.php';
require 'app/permission.php';

// Check if user has permission
if (!hasPermission('view_dashboard')) {
    header('Location: index.php');
    exit();
}

$message = '';
$message_type = '';

// Get dashboard data
$sales_summary = getDashboardSalesSummary($pdo);
$recent_orders = getRecentOrders($pdo, 5);
$menu_summary = getDashboardMenuSummary($pdo);
$popular_items = getPopularMenuItems($pdo, 5);
$feedback_summary = getDashboardFeedbackSummary($pdo);
$recent_feedbacks = getRecentFeedbacks($pdo, 5);
$order_status_dist = getOrderStatusDistribution($pdo);
$daily_revenue = getDailyRevenue($pdo, 7);
$category_revenue = getCategoryRevenue($pdo);

// Prepare chart data
$chart_labels = [];
$chart_revenue = [];
$chart_orders = [];
foreach ($daily_revenue as $row) {
    $chart_labels[] = date('M d', strtotime($row['date']));
    $chart_revenue[] = floatval($row['revenue'] ?? 0);
    $chart_orders[] = intval($row['orders'] ?? 0);
}

$status_labels = [];
$status_counts = [];
foreach ($order_status_dist as $row) {
    $status_labels[] = ucfirst($row['status']);
    $status_counts[] = intval($row['count']);
}

$category_labels = [];
$category_revenue_data = [];
foreach ($category_revenue as $row) {
    $category_labels[] = $row['category'];
    $category_revenue_data[] = floatval($row['revenue'] ?? 0);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/vendor/aos/aos.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                <a href="dashboard.php" class="nav-item active">
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
                <a href="orders.php" class="nav-item">
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
                    <?php if (hasPermission('manage_customers')): ?>
                    <span>Staff</span>
                    <?php elseif (hasPermission('manage_staff')): ?>
                    <span>Admin</span>
                    <?php endif; ?>
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
                        <i class="bi bi-graph-up me-2"></i>Main Dashboard
                    </h1>
                    <div>
                        <span class="text-muted"><?php echo date('F d, Y'); ?></span>
                    </div>
                </div>

                <!-- Sales Overview Cards -->
                <div class="row mb-4">
                    <div class="col-md-3 col-sm-6">
                        <div class="stat-card total">
                            <h5 class="mb-2">Total Revenue</h5>
                            <h3>$<?php echo number_format($sales_summary['total_revenue'] ?? 0, 2); ?></h3>
                            <small><i class="bi bi-calendar3 me-1"></i>Last 30 days</small>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="stat-card completed">
                            <h5 class="mb-2">Total Orders</h5>
                            <h3><?php echo $sales_summary['total_orders'] ?? 0; ?></h3>
                            <small><i class="bi bi-check-circle me-1"></i>Completed: <?php echo $sales_summary['completed_orders'] ?? 0; ?></small>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="stat-card pending">
                            <h5 class="mb-2">Pending Orders</h5>
                            <h3><?php echo $sales_summary['pending_orders'] ?? 0; ?></h3>
                            <small><i class="bi bi-clock me-1"></i>Needs attention</small>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="stat-card cancelled">
                            <h5 class="mb-2">Cancelled Orders</h5>
                            <h3><?php echo $sales_summary['cancelled_orders'] ?? 0; ?></h3>
                            <small><i class="bi bi-x-circle me-1"></i>Last 30 days</small>
                        </div>
                    </div>
                </div>

                <!-- Charts Row -->
                <div class="row mb-4">
                    <div class="col-md-8">
                        <div class="chart-container">
                            <div class="chart-header">
                                <i class="bi bi-graph-up me-2"></i>Daily Revenue (Last 7 Days)
                            </div>
                            <canvas id="revenueChart"></canvas>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="chart-container">
                            <div class="chart-header">
                                <i class="bi bi-pie-chart me-2"></i>Order Status
                            </div>
                            <canvas id="statusChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Main Content Grid -->
                <div class="row">
                    <!-- Recent Orders -->
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <span><i class="bi bi-receipt me-2"></i>Recent Orders</span>
                                <a href="orders.php" class="btn btn-sm btn-outline-primary">View All</a>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover table-striped">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Order ID</th>
                                                <th>Customer</th>
                                                <th>Total</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (count($recent_orders) > 0): ?>
                                                <?php foreach ($recent_orders as $order): ?>
                                                <tr>
                                                    <td class="order-number">#<?php echo $order['id']; ?></td>
                                                    <td><?php echo htmlspecialchars($order['username'] ?? 'Guest'); ?></td>
                                                    <td class="fw-bold">$<?php echo number_format($order['total_amount'], 2); ?></td>
                                                    <td>
                                                        <span class="badge <?php
                                                            echo $order['status'] == 'completed' ? 'bg-success' :
                                                                ($order['status'] == 'pending' ? 'bg-warning' :
                                                                ($order['status'] == 'cancelled' ? 'bg-danger' : 'bg-info'));
                                                        ?>">
                                                            <?php echo ucfirst($order['status']); ?>
                                                        </span>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="4" class="text-center text-muted">No recent orders</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Popular Menu Items -->
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <span><i class="bi bi-box me-2"></i>Popular Menu Items</span>
                                <a href="menu_items.php" class="btn btn-sm btn-outline-primary">View All</a>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover table-striped">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Item</th>
                                                <th>Category</th>
                                                <th>Price</th>
                                                <th>Sold</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (count($popular_items) > 0): ?>
                                                <?php foreach ($popular_items as $item): ?>
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <?php if ($item['image_url']): ?>
                                                            <img src="<?php echo htmlspecialchars($item['image_url']); ?>"
                                                                 alt="<?php echo htmlspecialchars($item['name']); ?>"
                                                                 class="menu-image me-2"
                                                                 style="width: 40px; height: 40px; object-fit: cover;">
                                                            <?php endif; ?>
                                                            <div>
                                                                <div class="fw-bold"><?php echo htmlspecialchars($item['name']); ?></div>
                                                                <?php if (!$item['is_available']): ?>
                                                                <small class="text-danger"><i class="bi bi-x-circle"></i> Unavailable</small>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td><?php echo htmlspecialchars($item['category']); ?></td>
                                                    <td>$<?php echo number_format($item['price'], 2); ?></td>
                                                    <td><?php echo $item['total_quantity_sold'] ?? 0; ?></td>
                                                </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="4" class="text-center text-muted">No menu items</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Menu Items Summary -->
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header">
                                <i class="bi bi-box-seam me-2"></i>Menu Items Summary
                            </div>
                            <div class="card-body">
                                <div class="row text-center">
                                    <div class="col-6 mb-3">
                                        <div class="stat-card total" style="padding: 10px;">
                                            <h4><?php echo $menu_summary['total_items'] ?? 0; ?></h4>
                                            <small>Total Items</small>
                                        </div>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <div class="stat-card completed" style="padding: 10px;">
                                            <h4><?php echo $menu_summary['available_items'] ?? 0; ?></h4>
                                            <small>Available</small>
                                        </div>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <div class="stat-card cancelled" style="padding: 10px;">
                                            <h4><?php echo $menu_summary['unavailable_items'] ?? 0; ?></h4>
                                            <small>Unavailable</small>
                                        </div>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <div class="stat-card pending" style="padding: 10px;">
                                            <h4><?php echo $menu_summary['total_categories'] ?? 0; ?></h4>
                                            <small>Categories</small>
                                        </div>
                                    </div>
                                </div>
                                <a href="menu_items.php" class="btn btn-primary w-100">
                                    <i class="bi bi-box me-1"></i>Manage Menu Items
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Feedback -->
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <span><i class="bi bi-chat-left-text me-2"></i>Recent Feedback</span>
                                <?php if (hasPermission('manage_feedback')): ?>
                                <a href="feedback.php" class="btn btn-sm btn-outline-primary">View All</a>
                                <?php endif; ?>
                            </div>
                            <div class="card-body">
                                <div class="mb-3 text-center">
                                    <h2 class="text-warning">
                                        <i class="bi bi-star-fill"></i>
                                        <?php echo number_format($feedback_summary['avg_rating'] ?? 0, 1); ?>
                                        <small class="text-muted">/ 5.0</small>
                                    </h2>
                                    <p class="text-muted mb-0">
                                        <?php echo $feedback_summary['total_feedbacks'] ?? 0; ?> total feedbacks
                                    </p>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-6">
                                        <div class="alert alert-success mb-0 py-2">
                                            <small><i class="bi bi-hand-thumbs-up"></i>
                                            <?php echo $feedback_summary['positive_feedbacks'] ?? 0; ?> Positive</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="alert alert-danger mb-0 py-2">
                                            <small><i class="bi bi-hand-thumbs-down"></i>
                                            <?php echo $feedback_summary['negative_feedbacks'] ?? 0; ?> Negative</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="feedback-list">
                                    <?php if (count($recent_feedbacks) > 0): ?>
                                        <?php foreach (array_slice($recent_feedbacks, 0, 3) as $feedback): ?>
                                        <div class="card mb-2" style="border: var(--border-width) solid var(--foreground);">
                                            <div class="card-body p-2">
                                                <div class="d-flex justify-content-between">
                                                    <small class="fw-bold"><?php echo htmlspecialchars($feedback['username'] ?? 'Anonymous'); ?></small>
                                                    <small class="text-warning">
                                                        <?php for($i = 0; $i < 5; $i++): ?>
                                                            <i class="bi bi-star<?php echo $i < $feedback['rating'] ? '-fill' : ''; ?>"></i>
                                                        <?php endfor; ?>
                                                    </small>
                                                </div>
                                                <p class="mb-0 text-muted" style="font-size: 0.85rem;">
                                                    <?php echo htmlspecialchars(substr($feedback['feedback_text'], 0, 50)); ?>
                                                    <?php if (strlen($feedback['feedback_text']) > 50): ?>...<?php endif; ?>
                                                </p>
                                            </div>
                                        </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <p class="text-center text-muted">No feedback yet</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Revenue Chart
        const revenueCtx = document.getElementById('revenueChart');
        if (revenueCtx) {
            new Chart(revenueCtx, {
                type: 'line',
                data: {
                    labels: <?php echo json_encode($chart_labels); ?>,
                    datasets: [{
                        label: 'Revenue ($)',
                        data: <?php echo json_encode($chart_revenue); ?>,
                        borderColor: '#458588',
                        backgroundColor: 'rgba(69, 133, 136, 0.2)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4,
                        yAxisID: 'y'
                    }, {
                        label: 'Orders',
                        data: <?php echo json_encode($chart_orders); ?>,
                        borderColor: '#b16286',
                        backgroundColor: 'rgba(177, 98, 134, 0.2)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4,
                        yAxisID: 'y1'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            position: 'left',
                            grid: {
                                color: '#665c54'
                            }
                        },
                        y1: {
                            position: 'right',
                            beginAtZero: true,
                            grid: {
                                drawOnChartArea: false
                            }
                        },
                        x: {
                            grid: {
                                color: '#665c54'
                            }
                        }
                    }
                }
            });
        }

        // Order Status Chart
        const statusCtx = document.getElementById('statusChart');
        if (statusCtx) {
            new Chart(statusCtx, {
                type: 'doughnut',
                data: {
                    labels: <?php echo json_encode($status_labels); ?>,
                    datasets: [{
                        data: <?php echo json_encode($status_counts); ?>,
                        backgroundColor: [
                            '#cc241d',
                            '#d79921',
                            '#458588',
                            '#98971a',
                            '#b16286',
                            '#689d6a'
                        ],
                        borderWidth: 2,
                        borderColor: '#3c3836'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                font: {
                                    family: "'Courier New', Courier, monospace"
                                }
                            }
                        }
                    }
                }
            });
        }
    });
    </script>
</body>
</html>
