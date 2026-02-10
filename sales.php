<?php
session_start();
require 'app/db.php';
require 'app/sales_functions.php';
require 'app/order_functions.php';
require 'app/permission.php';

// Check if user have permission
if (!hasPermission('view_dashboard')) {
    header('Location: index.php');
    exit();
}

// Get filter parameters
$period = $_GET['period'] ?? 'today';
$date_from = $_GET['date_from'] ?? '';
$date_to = $_GET['date_to'] ?? '';
$category_filter = $_GET['category'] ?? 'all';

// Set default dates based on period
if ($period === 'today') {
    $date_from = date('Y-m-d');
    $date_to = date('Y-m-d');
} elseif ($period === 'yesterday') {
    $date_from = date('Y-m-d', strtotime('-1 day'));
    $date_to = date('Y-m-d', strtotime('-1 day'));
} elseif ($period === 'this_week') {
    $date_from = date('Y-m-d', strtotime('monday this week'));
    $date_to = date('Y-m-d', strtotime('sunday this week'));
} elseif ($period === 'last_week') {
    $date_from = date('Y-m-d', strtotime('monday last week'));
    $date_to = date('Y-m-d', strtotime('sunday last week'));
} elseif ($period === 'this_month') {
    $date_from = date('Y-m-d', strtotime('first day of this month'));
    $date_to = date('Y-m-d', strtotime('last day of this month'));
} elseif ($period === 'last_month') {
    $date_from = date('Y-m-d', strtotime('first day of last month'));
    $date_to = date('Y-m-d', strtotime('last day of last month'));
} elseif ($period === 'this_year') {
    $date_from = date('Y-01-01');
    $date_to = date('Y-12-31');
}

// Get sales data
$sales_data = getSalesData($pdo, $date_from, $date_to, $category_filter);
$sales_summary = getSalesSummary($pdo, $date_from, $date_to);
$top_items = getTopSellingItems($pdo, $date_from, $date_to, 5);
$hourly_sales = getHourlySales($pdo, $date_from, $date_to);
$category_sales = getSalesByCategory($pdo, $date_from, $date_to);

// For CSV/Excel export
if (isset($_GET['export'])) {
    $export_type = $_GET['export'];
    if ($export_type === 'csv') {
        exportSalesToCSV($sales_data, $date_from, $date_to);
        exit();
    } elseif ($export_type === 'excel') {
        exportSalesToExcel($sales_data, $sales_summary, $date_from, $date_to);
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Reports</title>

    <!-- Same CSS as dashboard -->
    <link rel="stylesheet" href="assets/css/dashStyle.css">

    <!-- Bootstrap Icons only -->
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/vendor/aos/aos.css" rel="stylesheet">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        .card { border-radius: 10px; border: none; }
        .stat-card {
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 15px;
            color: white;
        }
        .stat-card.total { background: #6f42c1; }
        .stat-card.orders { background: #0d6efd; }
        .stat-card.avg { background: #198754; }
        .stat-card.items { background: #fd7e14; }
        .table-actions { white-space: nowrap; }
        .chart-container {
            position: relative;
            height: 300px;
            margin-bottom: 20px;
        }
        .badge-sales { font-size: 0.75rem; }
        .sales-card {
            transition: transform 0.2s;
        }
        .sales-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .top-item {
            display: flex;
            align-items: center;
            padding: 10px;
            border-bottom: 1px solid #eee;
        }
        .top-item:last-child {
            border-bottom: none;
        }
        .top-item img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 8px;
            margin-right: 10px;
        }
        .export-buttons .btn {
            margin-right: 5px;
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

                <a href="#" class="nav-item active">
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
                <!-- Header -->
                <div class="top-bar d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h3 mb-0">
                        <i class="bi bi-cart3 me-2"></i>Sales Reports
                    </h1>
                    <div class="export-buttons">
                        <button class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#exportModal">
                            <i class="bi bi-download me-1"></i>Export
                        </button>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#filterModal">
                            <i class="bi bi-funnel me-1"></i>Filter
                        </button>
                    </div>
                </div>

                <!-- Date Range Display -->
                <div class="alert alert-info mb-4">
                    <i class="bi bi-calendar me-2"></i>
                    <strong>Reporting Period:</strong>
                    <?php echo date('F j, Y', strtotime($date_from)); ?>
                    to
                    <?php echo date('F j, Y', strtotime($date_to)); ?>
                    <?php if ($category_filter !== 'all'): ?>
                        | <strong>Category:</strong> <?php echo ucfirst($category_filter); ?>
                    <?php endif; ?>
                </div>

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="stat-card total">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h3 class="mb-0">RM <?php echo number_format($sales_summary['total_sales'] ?? 0, 2); ?></h3>
                                    <small>Total Sales</small>
                                </div>
                                <i class="bi bi-cash-coin display-6 opacity-50"></i>
                            </div>
                            <div class="mt-2">
                                <small>
                                    <i class="bi bi-arrow-up me-1"></i>
                                    <?php echo $sales_summary['total_orders'] ?? 0; ?> orders
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card orders">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h3 class="mb-0"><?php echo $sales_summary['total_orders'] ?? 0; ?></h3>
                                    <small>Total Orders</small>
                                </div>
                                <i class="bi bi-receipt display-6 opacity-50"></i>
                            </div>
                            <div class="mt-2">
                                <small>
                                    <i class="bi bi-people me-1"></i>
                                    <?php echo $sales_summary['unique_customers'] ?? 0; ?> customers
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card avg">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h3 class="mb-0">RM <?php echo number_format($sales_summary['average_order_value'] ?? 0, 2); ?></h3>
                                    <small>Avg Order Value</small>
                                </div>
                                <i class="bi bi-graph-up display-6 opacity-50"></i>
                            </div>
                            <div class="mt-2">
                                <small>
                                    <i class="bi bi-bag me-1"></i>
                                    <?php echo $sales_summary['avg_items_per_order'] ?? 0; ?> items/order
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card items">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h3 class="mb-0"><?php echo $sales_summary['total_items_sold'] ?? 0; ?></h3>
                                    <small>Items Sold</small>
                                </div>
                                <i class="bi bi-cup-hot display-6 opacity-50"></i>
                            </div>
                            <div class="mt-2">
                                <small>
                                    <i class="bi bi-tags me-1"></i>
                                    <?php echo count($category_sales); ?> categories
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts Row -->
                <div class="row mb-4">
                    <!-- Daily Sales Chart -->
                    <div class="col-md-8">
                        <div class="card shadow-sm h-100">
                            <div class="card-body">
                                <h6 class="card-title mb-3">
                                    <i class="bi bi-bar-chart me-2"></i>Daily Sales Trend
                                </h6>
                                <div class="chart-container">
                                    <canvas id="dailySalesChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Category Distribution -->
                    <div class="col-md-4">
                        <div class="card shadow-sm h-100">
                            <div class="card-body">
                                <h6 class="card-title mb-3">
                                    <i class="bi bi-pie-chart me-2"></i>Sales by Category
                                </h6>
                                <div class="chart-container">
                                    <canvas id="categoryChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Hourly Sales & Top Items -->
                <div class="row mb-4">
                    <!-- Hourly Sales -->
                    <div class="col-md-6">
                        <div class="card shadow-sm h-100">
                            <div class="card-body">
                                <h6 class="card-title mb-3">
                                    <i class="bi bi-clock me-2"></i>Hourly Sales Performance
                                </h6>
                                <div class="chart-container">
                                    <canvas id="hourlySalesChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Top Selling Items -->
                    <div class="col-md-6">
                        <div class="card shadow-sm h-100">
                            <div class="card-body">
                                <h6 class="card-title mb-3">
                                    <i class="bi bi-trophy me-2"></i>Top Selling Items
                                </h6>
                                <div style="max-height: 250px; overflow-y: auto;">
                                    <?php if (empty($top_items)): ?>
                                        <p class="text-muted text-center py-4">No sales data available</p>
                                    <?php else: ?>
                                        <?php foreach ($top_items as $index => $item): ?>
                                            <div class="top-item">
                                                <span class="badge bg-primary me-2">#<?php echo $index + 1; ?></span>
                                                <?php if ($item['image_url']): ?>
                                                    <img src="<?php echo htmlspecialchars($item['image_url']); ?>"
                                                         alt="<?php echo htmlspecialchars($item['name']); ?>">
                                                <?php endif; ?>
                                                <div class="flex-grow-1">
                                                    <div class="d-flex justify-content-between">
                                                        <strong><?php echo htmlspecialchars($item['name']); ?></strong>
                                                        <span class="fw-bold"><?php echo $item['total_quantity']; ?> sold</span>
                                                    </div>
                                                    <div class="d-flex justify-content-between">
                                                        <small class="text-muted">RM <?php echo number_format($item['total_revenue'], 2); ?></small>
                                                        <small class="text-muted"><?php echo $item['category']; ?></small>
                                                    </div>
                                                    <div class="progress mt-1" style="height: 5px;">
                                                        <div class="progress-bar"
                                                             style="width: <?php echo min(100, ($item['total_quantity'] / ($top_items[0]['total_quantity'] ?? 1)) * 100); ?>%">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sales Data Table -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="card-title mb-0">
                                <i class="bi bi-table me-2"></i>Detailed Sales Data
                            </h6>
                            <div class="text-muted">
                                Showing <?php echo count($sales_data); ?> records
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>Order ID</th>
                                        <th>Customer</th>
                                        <th>Items</th>
                                        <th>Category</th>
                                        <th>Quantity</th>
                                        <th>Unit Price</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($sales_data)): ?>
                                        <tr>
                                            <td colspan="9" class="text-center text-muted py-4">
                                                No sales data found for the selected period.
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($sales_data as $sale): ?>
                                            <tr class="sales-card">
                                                <td>
                                                    <?php echo date('M j, Y', strtotime($sale['order_date'])); ?><br>
                                                    <small class="text-muted">
                                                        <?php echo date('g:i A', strtotime($sale['order_date'])); ?>
                                                    </small>
                                                </td>
                                                <td>
                                                    <a href="orders.php?search=<?php echo $sale['order_id']; ?>"
                                                       class="text-decoration-none">
                                                        #KTB-<?php echo str_pad($sale['order_id'], 6, '0', STR_PAD_LEFT); ?>
                                                    </a>
                                                </td>
                                                <td>
                                                    <?php echo htmlspecialchars($sale['customer_name'] ?? 'Guest'); ?><br>
                                                    <small class="text-muted">
                                                        <?php echo htmlspecialchars($sale['customer_email'] ?? ''); ?>
                                                    </small>
                                                </td>
                                                <td>
                                                    <?php echo htmlspecialchars($sale['item_name']); ?>
                                                    <?php if ($sale['item_description']): ?>
                                                        <br>
                                                        <small class="text-muted">
                                                            <?php echo htmlspecialchars(substr($sale['item_description'], 0, 30)); ?>...
                                                        </small>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <span class="badge bg-info badge-sales">
                                                        <?php echo htmlspecialchars($sale['category']); ?>
                                                    </span>
                                                </td>
                                                <td class="text-center"><?php echo $sale['quantity']; ?></td>
                                                <td class="fw-bold">RM <?php echo number_format($sale['unit_price'], 2); ?></td>
                                                <td class="fw-bold text-success">RM <?php echo number_format($sale['total'], 2); ?></td>
                                                <td>
                                                    <?php
                                                    $status_display = getOrderStatusDisplay($sale['status']);
                                                    ?>
                                                    <span class="badge bg-<?php echo $status_display['class']; ?> badge-sales">
                                                        <?php echo $status_display['text']; ?>
                                                    </span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <td colspan="5" class="text-end"><strong>Grand Total</strong></td>
                                        <td class="text-center"><strong><?php echo $sales_summary['total_items_sold'] ?? 0; ?></strong></td>
                                        <td></td>
                                        <td class="fw-bold text-success">RM <?php echo number_format($sales_summary['total_sales'] ?? 0, 2); ?></td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Modal -->
    <div class="modal fade" id="filterModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="GET" action="sales.php">
                    <div class="modal-header">
                        <h5 class="modal-title">Filter Sales Report</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Quick Period</label>
                            <select class="form-select" name="period" id="periodSelect" onchange="updateDateRange()">
                                <option value="today" <?php echo $period === 'today' ? 'selected' : ''; ?>>Today</option>
                                <option value="yesterday" <?php echo $period === 'yesterday' ? 'selected' : ''; ?>>Yesterday</option>
                                <option value="this_week" <?php echo $period === 'this_week' ? 'selected' : ''; ?>>This Week</option>
                                <option value="last_week" <?php echo $period === 'last_week' ? 'selected' : ''; ?>>Last Week</option>
                                <option value="this_month" <?php echo $period === 'this_month' ? 'selected' : ''; ?>>This Month</option>
                                <option value="last_month" <?php echo $period === 'last_month' ? 'selected' : ''; ?>>Last Month</option>
                                <option value="this_year" <?php echo $period === 'this_year' ? 'selected' : ''; ?>>This Year</option>
                                <option value="custom">Custom Range</option>
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Date From</label>
                                <input type="date" class="form-control" name="date_from" id="dateFrom"
                                       value="<?php echo htmlspecialchars($date_from); ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Date To</label>
                                <input type="date" class="form-control" name="date_to" id="dateTo"
                                       value="<?php echo htmlspecialchars($date_to); ?>">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Category Filter</label>
                            <select class="form-select" name="category">
                                <option value="all" <?php echo $category_filter === 'all' ? 'selected' : ''; ?>>All Categories</option>
                                <option value="Coffee" <?php echo $category_filter === 'Coffee' ? 'selected' : ''; ?>>Coffee</option>
                                <option value="Tea" <?php echo $category_filter === 'Tea' ? 'selected' : ''; ?>>Tea</option>
                                <option value="Non-Coffee" <?php echo $category_filter === 'Non-Coffee' ? 'selected' : ''; ?>>Non-Coffee Drinks</option>
                                <option value="Refreshing" <?php echo $category_filter === 'Refreshing' ? 'selected' : ''; ?>>Refreshing</option>
                            </select>
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

    <!-- Export Modal -->
    <div class="modal fade" id="exportModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Export Sales Report</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        Export data for: <?php echo date('F j, Y', strtotime($date_from)); ?> to <?php echo date('F j, Y', strtotime($date_to)); ?>
                    </div>
                    <div class="d-grid gap-2">
                        <a href="sales.php?<?php echo http_build_query($_GET); ?>&export=csv"
                           class="btn btn-outline-success btn-lg">
                            <i class="bi bi-filetype-csv me-2"></i>Export as CSV
                        </a>
                        <a href="sales.php?<?php echo http_build_query($_GET); ?>&export=excel"
                           class="btn btn-outline-primary btn-lg">
                            <i class="bi bi-filetype-xlsx me-2"></i>Export as Excel
                        </a>
                        <!-- <button class="btn btn-outline-secondary btn-lg" onclick="printReport()"> -->
                        <!--     <i class="bi bi-printer me-2"></i>Print Report -->
                        <!-- </button> -->
                    </div>
                    <div class="mt-3">
                        <small class="text-muted">
                            <i class="bi bi-clock me-1"></i>
                            CSV: Best for data analysis | Excel: Best for reporting
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Date range update function
        function updateDateRange() {
            const period = document.getElementById('periodSelect').value;
            const today = new Date().toISOString().split('T')[0];

            if (period === 'today') {
                document.getElementById('dateFrom').value = today;
                document.getElementById('dateTo').value = today;
            } else if (period === 'yesterday') {
                const yesterday = new Date();
                yesterday.setDate(yesterday.getDate() - 1);
                document.getElementById('dateFrom').value = yesterday.toISOString().split('T')[0];
                document.getElementById('dateTo').value = yesterday.toISOString().split('T')[0];
            } else if (period === 'this_week') {
                const now = new Date();
                const monday = new Date(now.setDate(now.getDate() - now.getDay() + 1));
                const sunday = new Date(now.setDate(now.getDate() - now.getDay() + 7));
                document.getElementById('dateFrom').value = monday.toISOString().split('T')[0];
                document.getElementById('dateTo').value = sunday.toISOString().split('T')[0];
            }
        }

        // Print report function
        /* function printReport() { */
        /*     window.print(); */
        /* } */

        // Initialize Charts
        document.addEventListener('DOMContentLoaded', function() {
            // Daily Sales Chart
            <?php
            $daily_sales = getDailySales($pdo, $date_from, $date_to);
            $labels = array_column($daily_sales, 'date');
            $data = array_column($daily_sales, 'total_sales');
            ?>
            const dailySalesCtx = document.getElementById('dailySalesChart').getContext('2d');
            new Chart(dailySalesCtx, {
                type: 'line',
                data: {
                    labels: <?php echo json_encode($labels); ?>,
                    datasets: [{
                        label: 'Daily Sales (RM)',
                        data: <?php echo json_encode($data); ?>,
                        borderColor: '#6f42c1',
                        backgroundColor: 'rgba(111, 66, 193, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'RM ' + value;
                                }
                            }
                        }
                    }
                }
            });

            // Category Chart
            <?php
            $cat_labels = array_column($category_sales, 'category');
            $cat_data = array_column($category_sales, 'total_sales');
            $cat_colors = ['#0dcaf0', '#198754', '#fd7e14', '#6f42c1', '#20c997', '#ffc107'];
            ?>
            const categoryCtx = document.getElementById('categoryChart').getContext('2d');
            new Chart(categoryCtx, {
                type: 'doughnut',
                data: {
                    labels: <?php echo json_encode($cat_labels); ?>,
                    datasets: [{
                        data: <?php echo json_encode($cat_data); ?>,
                        backgroundColor: <?php echo json_encode(array_slice($cat_colors, 0, count($cat_labels))); ?>,
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    label += 'RM ' + context.parsed;
                                    return label;
                                }
                            }
                        }
                    }
                }
            });

            // Hourly Sales Chart
            <?php
            $hour_labels = array_column($hourly_sales, 'hour');
            $hour_data = array_column($hourly_sales, 'total_sales');
            ?>
            const hourlySalesCtx = document.getElementById('hourlySalesChart').getContext('2d');
            new Chart(hourlySalesCtx, {
                type: 'bar',
                data: {
                    labels: <?php echo json_encode($hour_labels); ?>,
                    datasets: [{
                        label: 'Sales by Hour (RM)',
                        data: <?php echo json_encode($hour_data); ?>,
                        backgroundColor: 'rgba(13, 202, 240, 0.5)',
                        borderColor: '#0dcaf0',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'RM ' + value;
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
</body>
</html>
