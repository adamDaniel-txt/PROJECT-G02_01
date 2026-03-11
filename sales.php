<?php
session_start();
require 'app/db.php';
require 'app/permission.php';
require 'app/sales_functions.php';

// Check if user has permission
if (!hasPermission('view_dashboard')) {
    header('Location: index.php');
    exit();
}
// Get current user data
$current_user = null;
if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT username, profile_picture FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $current_user = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Set profile picture (keep as-is from database, can be NULL or path)
$profile_picture = $current_user['profile_picture'] ?? null;
$username = $current_user['username'] ?? 'USER';

$message = '';
$message_type = '';

// Get date range from URL or use default
$default_dates = getDefaultDateRange();
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : $default_dates['start_date'];
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : $default_dates['end_date'];

// Validate date range
if (!validateDateRange($start_date, $end_date)) {
    $start_date = $default_dates['start_date'];
    $end_date = $default_dates['end_date'];
}

// Handle CSV download request
if (isset($_GET['download']) && $_GET['download'] == 'csv') {
    downloadSalesReportCSV($pdo, $start_date, $end_date);
    exit();
}

// Get sales data
$sales_data = getSalesData($pdo, $start_date, $end_date);
$sales_summary = getSalesSummary($pdo, $start_date, $end_date);
$chart_data = getChartData($pdo, $start_date, $end_date);
$category_data = getCategoryData($pdo, $start_date, $end_date);

// Prepare data for JavaScript
$chart_prep = prepareChartData($chart_data);
$category_prep = prepareCategoryData($category_data);

// Handle AJAX request for order details
if (isset($_GET['ajax']) && $_GET['ajax'] == 'order_details' && isset($_GET['order_id'])) {
    header('Content-Type: application/json');
    header('Cache-Control: no-cache, no-store, must-revalidate');
    $order_details = getOrderDetails($pdo, intval($_GET['order_id']));
    echo json_encode($order_details);
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Report</title>
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
            <a href="profile.php" class="logo" style="text-decoration: none; color: inherit;">
                <div class="d-flex align-items-center">
                    <?php if (!empty($profile_picture) && $profile_picture !== 'default-profile.png' && file_exists($profile_picture)): ?>
                        <img src="<?php echo htmlspecialchars($profile_picture); ?>"
                             alt="Profile"
                             class="rounded-circle me-3"
                             style="width: 40px; height: 40px; object-fit: cover; border: 2px solid #458588;">
                    <?php else: ?>
                        <i class="rounded-circle bi bi-person-circle avatar-icon me-3"
                           style="font-size: 40px; color: #458588;"></i>
                    <?php endif; ?>
                    <span class="logo-text text-uppercase fw-bold"><?php echo htmlspecialchars(strtoupper($username)); ?></span>
                </div>
            </a>
            <nav class="nav-section">
                <div class="nav-label">Main Menu</div>
                <a href="dashboard.php" class="nav-item">
                    <i class="bi bi-house"></i>
                    <span>Home</span>
                </a>
                <a href="#" class="nav-item active">
                    <i class="bi bi-graph-up"></i>
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
                        <i class="bi bi-cart3 me-2"></i>Sales Report
                    </h1>
                    <div class="d-flex gap-2 align-items-center">
                        <div class="filter-wrapper">
                            <button class="btn btn-primary btn-sm" id="filterBtn" type="button">
                                <i class="bi bi-funnel me-1"></i>Filter
                            </button>
                            <div class="filter-dropdown" id="filterDropdown">
                                <form method="GET" class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label">Start Date</label>
                                        <input type="date" name="start_date" class="form-control" value="<?php echo htmlspecialchars($start_date); ?>" required>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">End Date</label>
                                        <input type="date" name="end_date" class="form-control" value="<?php echo htmlspecialchars($end_date); ?>" required>
                                    </div>
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary w-100">
                                            <i class="bi bi-search me-1"></i>Apply Filter
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <?php if (hasPermission('manage_staff')): ?>
                            <div class="dropdown">
                                <button class="btn btn-success btn-sm dropdown-toggle" type="button" id="downloadDropdown" data-bs-toggle="dropdown">
                                    <i class="bi bi-download me-1"></i>Download
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item" href="?download=csv&start_date=<?php echo $start_date; ?>&end_date=<?php echo $end_date; ?>">
                                            <i class="bi bi-file-earmark-spreadsheet me-2"></i>CSV
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="#" onclick="printPDF(); return false;">
                                            <i class="bi bi-file-earmark-pdf me-2"></i>PDF (Print)
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Sales Summary Cards -->
                <div class="row mb-4">
                    <div class="col-md-3 col-sm-6">
                        <div class="stat-card total">
                            <h5 class="mb-2">Total Revenue</h5>
                            <h3>$<?php echo number_format($sales_summary['total_revenue'] ?? 0, 2); ?></h3>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="stat-card completed">
                            <h5 class="mb-2">Total Orders</h5>
                            <h3><?php echo $sales_summary['total_orders'] ?? 0; ?></h3>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="stat-card pending">
                            <h5 class="mb-2">Pending Orders</h5>
                            <h3><?php echo $sales_summary['pending_orders'] ?? 0; ?></h3>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="stat-card cancelled">
                            <h5 class="mb-2">Cancelled Orders</h5>
                            <h3><?php echo $sales_summary['cancelled_orders'] ?? 0; ?></h3>
                        </div>
                    </div>
                </div>

                <!-- Charts Row -->
                <div class="row mb-4">
                    <div class="col-md-8">
                        <div class="chart-container">
                            <div class="chart-header">
                                <i class="bi bi-graph-up me-2"></i>Daily Sales Trend
                            </div>
                            <canvas id="salesChart"></canvas>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="chart-container">
                            <div class="chart-header">
                                <i class="bi bi-pie-chart me-2"></i>Revenue by Category
                            </div>
                            <canvas id="categoryChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Sales Data Table -->
                <div class="card">
                    <div class="card-header">
                        <i class="bi bi-table me-2"></i>Sales Data (<?php echo $start_date; ?> to <?php echo $end_date; ?>)
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped">
                                <thead class="table-light">
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Date</th>
                                        <th>Customer</th>
                                        <th>Email</th>
                                        <th>Items</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Pickup Code</th>
                                        <th class="table-actions">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (count($sales_data) > 0): ?>
                                        <?php foreach ($sales_data as $sale): ?>
                                        <tr class="<?php echo $sale['status'] == 'cancelled' ? 'banned-row' : ''; ?>">
                                            <td class="order-number">#<?php echo $sale['order_id']; ?></td>
                                            <td><?php echo date('M d, Y H:i', strtotime($sale['created_at'])); ?></td>
                                            <td><?php echo htmlspecialchars($sale['username'] ?? 'Guest'); ?></td>
                                            <td class="text-muted"><?php echo htmlspecialchars($sale['email'] ?? 'N/A'); ?></td>
                                            <td><?php echo $sale['item_count']; ?></td>
                                            <td class="fw-bold">$<?php echo number_format($sale['total_amount'], 2); ?></td>
                                            <td>
                                                <span class="badge <?php
                                                    echo $sale['status'] == 'completed' ? 'bg-success' :
                                                        ($sale['status'] == 'pending' ? 'bg-warning' :
                                                        ($sale['status'] == 'cancelled' ? 'bg-danger' : 'bg-info'));
                                                ?>">
                                                    <?php echo ucfirst($sale['status']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo $sale['pickup_code'] ?? 'N/A'; ?></td>
                                            <td class="table-actions">
                                                <button class="btn btn-outline-primary btn-sm view-order-btn" data-order-id="<?php echo $sale['order_id']; ?>" type="button">
                                                    <i class="bi bi-eye"></i> View
                                                </button>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="9" class="text-center text-muted">No sales data found for the selected period.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Hidden Print Area for PDF -->
    <div id="printArea">
        <div style="text-align: center; border-bottom: 2px solid #3c3836; padding-bottom: 10px; margin-bottom: 20px;">
            <h1 style="color: #458588; margin: 0;">TIGA BELAS CAFE</h1>
            <h2 style="color: #3c3836; margin: 5px 0;">Sales Report</h2>
            <p style="color: #665c54; margin: 5px 0;">
                <strong>Period:</strong> <?php echo date('M d, Y', strtotime($start_date)); ?> - <?php echo date('M d, Y', strtotime($end_date)); ?>
            </p>
            <p style="color: #665c54; margin: 5px 0;">
                <strong>Generated:</strong> <?php echo date('M d, Y H:i:s'); ?>
            </p>
        </div>

        <div style="background: #ebdbb2; border: 2px solid #3c3836; padding: 15px; margin-bottom: 20px;">
            <div style="display: flex; justify-content: space-between;">
                <div style="text-align: center; flex: 1; border-right: 1px solid #3c3836;">
                    <h3 style="color: #b16286; margin: 0;">$<?php echo number_format($sales_summary['total_revenue'] ?? 0, 2); ?></h3>
                    <p style="margin: 5px 0 0 0; font-size: 11px; color: #665c54;">Total Revenue</p>
                </div>
                <div style="text-align: center; flex: 1; border-right: 1px solid #3c3836;">
                    <h3 style="color: #b16286; margin: 0;"><?php echo $sales_summary['total_orders'] ?? 0; ?></h3>
                    <p style="margin: 5px 0 0 0; font-size: 11px; color: #665c54;">Total Orders</p>
                </div>
                <div style="text-align: center; flex: 1; border-right: 1px solid #3c3836;">
                    <h3 style="color: #b16286; margin: 0;"><?php echo $sales_summary['pending_orders'] ?? 0; ?></h3>
                    <p style="margin: 5px 0 0 0; font-size: 11px; color: #665c54;">Pending Orders</p>
                </div>
                <div style="text-align: center; flex: 1;">
                    <h3 style="color: #b16286; margin: 0;"><?php echo $sales_summary['cancelled_orders'] ?? 0; ?></h3>
                    <p style="margin: 5px 0 0 0; font-size: 11px; color: #665c54;">Cancelled Orders</p>
                </div>
            </div>
        </div>

        <h3 style="color: #458588; border-bottom: 2px solid #3c3836; padding-bottom: 5px;">Order Details</h3>
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
            <thead>
                <tr style="background: #d5c4a1;">
                    <th style="border: 1px solid #3c3836; padding: 8px; text-align: left;">Order ID</th>
                    <th style="border: 1px solid #3c3836; padding: 8px; text-align: left;">Date</th>
                    <th style="border: 1px solid #3c3836; padding: 8px; text-align: left;">Customer</th>
                    <th style="border: 1px solid #3c3836; padding: 8px; text-align: left;">Items</th>
                    <th style="border: 1px solid #3c3836; padding: 8px; text-align: left;">Total</th>
                    <th style="border: 1px solid #3c3836; padding: 8px; text-align: left;">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sales_data as $row):
                    $status_class = 'print-status-other';
                    if ($row['status'] == 'completed') $status_class = 'print-status-completed';
                    elseif ($row['status'] == 'pending') $status_class = 'print-status-pending';
                    elseif ($row['status'] == 'cancelled') $status_class = 'print-status-cancelled';
                ?>
                <tr>
                    <td style="border: 1px solid #3c3836; padding: 8px;">#<?php echo $row['order_id']; ?></td>
                    <td style="border: 1px solid #3c3836; padding: 8px;"><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                    <td style="border: 1px solid #3c3836; padding: 8px;"><?php echo htmlspecialchars($row['username'] ?? 'Guest'); ?></td>
                    <td style="border: 1px solid #3c3836; padding: 8px;"><?php echo $row['item_count']; ?></td>
                    <td style="border: 1px solid #3c3836; padding: 8px;">$<?php echo number_format($row['total_amount'], 2); ?></td>
                    <td style="border: 1px solid #3c3836; padding: 8px;" class="<?php echo $status_class; ?>">
                        <?php echo ucfirst($row['status']); ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php if (count($category_data) > 0): ?>
        <h3 style="color: #458588; border-bottom: 2px solid #3c3836; padding-bottom: 5px; margin-top: 30px;">Revenue by Category</h3>
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #d5c4a1;">
                    <th style="border: 1px solid #3c3836; padding: 8px; text-align: left;">Category</th>
                    <th style="border: 1px solid #3c3836; padding: 8px; text-align: left;">Items Sold</th>
                    <th style="border: 1px solid #3c3836; padding: 8px; text-align: left;">Revenue</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($category_data as $cat): ?>
                <tr>
                    <td style="border: 1px solid #3c3836; padding: 8px;"><?php echo htmlspecialchars($cat['category']); ?></td>
                    <td style="border: 1px solid #3c3836; padding: 8px;"><?php echo $cat['items_sold']; ?></td>
                    <td style="border: 1px solid #3c3836; padding: 8px;">$<?php echo number_format($cat['category_revenue'], 2); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>

        <div style="text-align: center; margin-top: 30px; padding-top: 10px; border-top: 2px solid #3c3836; font-size: 10px; color: #665c54;">
            <p>Tiga Belas Cafe - Sales Report</p>
            <p>This is a system-generated report. For inquiries, contact support.</p>
        </div>
    </div>

    <!-- Order Details Modal -->
    <div class="modal fade" id="orderDetailsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-receipt me-2"></i>Order #<span id="modalOrderId"></span> Details
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="orderDetailsContent">
                        <div class="text-center">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Filter Dropdown Toggle
        const filterBtn = document.getElementById('filterBtn');
        const filterDropdown = document.getElementById('filterDropdown');

        if (filterBtn && filterDropdown) {
            filterBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                filterDropdown.classList.toggle('show');
            });

            document.addEventListener('click', function(e) {
                if (!filterDropdown.contains(e.target) && e.target !== filterBtn) {
                    filterDropdown.classList.remove('show');
                }
            });
        }

        // Sales Chart
        const salesCtx = document.getElementById('salesChart');
        if (salesCtx) {
            new Chart(salesCtx, {
                type: 'line',
                data: {
                    labels: <?php echo json_encode($chart_prep['labels']); ?>,
                    datasets: [{
                        label: 'Revenue ($)',
                        data: <?php echo json_encode($chart_prep['revenue']); ?>,
                        borderColor: '#458588',
                        backgroundColor: 'rgba(69, 133, 136, 0.2)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4,
                        yAxisID: 'y'
                    }, {
                        label: 'Orders',
                        data: <?php echo json_encode($chart_prep['orders']); ?>,
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

        // Category Chart
        const categoryCtx = document.getElementById('categoryChart');
        if (categoryCtx) {
            new Chart(categoryCtx, {
                type: 'doughnut',
                data: {
                    labels: <?php echo json_encode($category_prep['labels']); ?>,
                    datasets: [{
                        data: <?php echo json_encode($category_prep['revenue']); ?>,
                        backgroundColor: [
                            '#458588',
                            '#98971a',
                            '#d79921',
                            '#cc241d',
                            '#b16286',
                            '#665c54'
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

        // Order Details Modal
        const orderDetailsModal = new bootstrap.Modal(document.getElementById('orderDetailsModal'));
        const viewOrderBtns = document.querySelectorAll('.view-order-btn');

        viewOrderBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const orderId = this.getAttribute('data-order-id');
                document.getElementById('modalOrderId').textContent = orderId;

                fetch('?ajax=order_details&order_id=' + orderId)
                    .then(response => response.json())
                    .then(data => {
                        const content = document.getElementById('orderDetailsContent');

                        if (data && data.length > 0) {
                            const order = data[0];
                            let itemsHtml = '';
                            let totalItems = 0;

                            data.forEach(item => {
                                totalItems += parseInt(item.quantity);
                                itemsHtml += `
                                    <tr>
                                        <td>${item.item_name}</td>
                                        <td class="text-center">${item.quantity}</td>
                                        <td class="text-end">$${parseFloat(item.price).toFixed(2)}</td>
                                        <td class="text-end">$${(item.quantity * item.price).toFixed(2)}</td>
                                    </tr>
                                `;
                            });

                            let statusBadge = 'bg-info';
                            if (order.status === 'completed') statusBadge = 'bg-success';
                            else if (order.status === 'pending') statusBadge = 'bg-warning';
                            else if (order.status === 'cancelled') statusBadge = 'bg-danger';

                            content.innerHTML = `
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <p><strong>Customer:</strong> ${order.username || 'Guest'}</p>
                                        <p><strong>Email:</strong> ${order.email || 'N/A'}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Order Date:</strong> ${new Date(order.created_at).toLocaleString()}</p>
                                        <p><strong>Status:</strong> <span class="badge ${statusBadge}">${order.status.toUpperCase()}</span></p>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <p><strong>Payment ID:</strong> <small>${order.payment_id || 'N/A'}</small></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Pickup Code:</strong> ${order.pickup_code || 'N/A'}</p>
                                    </div>
                                </div>
                                ${order.notes ? `<div class="alert alert-info"><strong>Notes:</strong> ${order.notes}</div>` : ''}
                                <h6 class="mb-3"><i class="bi bi-list-ul me-2"></i>Order Items (${totalItems} items)</h6>
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Item</th>
                                                <th class="text-center">Qty</th>
                                                <th class="text-end">Price</th>
                                                <th class="text-end">Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            ${itemsHtml}
                                        </tbody>
                                        <tfoot>
                                            <tr class="fw-bold">
                                                <td colspan="3" class="text-end">Total:</td>
                                                <td class="text-end">$${parseFloat(order.total_amount).toFixed(2)}</td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            `;
                        } else {
                            content.innerHTML = '<div class="alert alert-danger">Order not found.</div>';
                        }

                        orderDetailsModal.show();
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        document.getElementById('orderDetailsContent').innerHTML = '<div class="alert alert-danger">Failed to load order details.</div>';
                        orderDetailsModal.show();
                    });
            });
        });
    });

    // Print to PDF Function
    function printPDF() {
        window.print();
    }
    </script>
</body>
</html>
