<?php
session_start();
require 'app/db.php';
require 'app/menu_functions.php';
require 'app/permission.php';
require 'app/customer_functions.php';

// Check if user have permission
if (!hasPermission('view_dashboard')) {
    header('Location: index.php');
    exit();
}

// Handle AJAX requests
if (isset($_GET['ajax'])) {
    if ($_GET['ajax'] == 'customer_orders') {
        handleCustomerOrdersAjax($pdo);
    } elseif ($_GET['ajax'] == 'order_details') {
        handleOrderDetailsAjax($pdo);
    }
    exit();
}

$message = '';
$message_type = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_customer'])) {
        $id = intval($_POST['customer_id']);
        $data = [
            'username' => trim($_POST['username']),
            'email' => trim($_POST['email'])
        ];
        if (updateCustomer($pdo, $id, $data)) {
            $message = 'Customer updated successfully!';
            $message_type = 'success';
        } else {
            $message = 'Failed to update customer.';
            $message_type = 'error';
        }
    } elseif (isset($_POST['ban_customer'])) {
        $id = intval($_POST['customer_id']);
        $reason = trim($_POST['ban_reason'] ?? '');
        if (banCustomer($pdo, $id, $reason)) {
            $message = 'Customer has been banned/suspended successfully!';
            $message_type = 'success';
        } else {
            $message = 'Failed to ban customer.';
            $message_type = 'error';
        }
    } elseif (isset($_POST['unban_customer'])) {
        $id = intval($_POST['customer_id']);
        if (unbanCustomer($pdo, $id)) {
            $message = 'Customer has been restored successfully!';
            $message_type = 'success';
        } else {
            $message = 'Failed to restore customer.';
            $message_type = 'error';
        }
    } elseif (isset($_POST['delete_customer'])) {
        $id = intval($_POST['customer_id']);
        if (deleteCustomer($pdo, $id)) {
            $message = 'Customer deleted successfully!';
            $message_type = 'success';
        } else {
            $message = 'Failed to delete customer.';
            $message_type = 'error';
        }
    } elseif (isset($_POST['reset_password'])) {
        $id = intval($_POST['customer_id']);
        if (resetCustomerPassword($pdo, $id)) {
            $message = 'Password reset successfully! New password: password';
            $message_type = 'success';
        } else {
            $message = 'Failed to reset password.';
            $message_type = 'error';
        }
    }
}

// Get filter from URL
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Get all customers
$customers = getAllCustomers($pdo);

// Get statistics
$banned_count = getBannedCustomersCount($pdo);
$active_count = getActiveCustomersCount($pdo);

// Apply filters
if ($filter == 'active') {
    $customers = array_filter($customers, function($c) { return $c['is_active'] == 1; });
} elseif ($filter == 'banned') {
    $customers = array_filter($customers, function($c) { return $c['is_active'] == 0; });
}

if ($search) {
    $customers = array_filter($customers, function($c) use ($search) {
        return stripos($c['username'], $search) !== false || stripos($c['email'], $search) !== false;
    });
}

$displayed_count = count($customers);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Management</title>
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="app-container">
        <!-- Side Bar -->
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
                <a href="#" class="nav-item active">
                    <i class="bi bi-people"></i>
                    <span>Customers</span>
                </a>
                <?php endif; ?>
                <?php if (hasPermission('manage_staff')): ?>
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
                <!-- Messages -->
                <?php if ($message): ?>
                <div class="alert alert-<?php echo $message_type === 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show">
                    <?php echo htmlspecialchars($message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>

                <div class="top-bar d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h3 mb-0">
                        <i class="bi bi-people me-2"></i>Customer Management
                    </h1>
                    <div class="d-flex gap-2 align-items-center">
                        <div class="filter-wrapper">
                            <button class="btn btn-primary btn-sm" id="filterBtn" type="button">
                                <i class="bi bi-funnel me-1"></i>Filter
                            </button>
                            <div class="filter-dropdown" id="filterDropdown">
                                <form method="GET" class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label">Filter by Status</label>
                                        <select class="form-select" name="filter">
                                            <option value="all" <?php echo $filter == 'all' ? 'selected' : ''; ?>>All Customers</option>
                                            <option value="active" <?php echo $filter == 'active' ? 'selected' : ''; ?>>Active Only</option>
                                            <option value="banned" <?php echo $filter == 'banned' ? 'selected' : ''; ?>>Banned Only</option>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Search</label>
                                        <input type="text" class="form-control" name="search"
                                               placeholder="Search username or email..."
                                               value="<?php echo htmlspecialchars($search); ?>">
                                    </div>
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary w-100">
                                            <i class="bi bi-search me-1"></i>Apply Filter
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-md-4 col-sm-6">
                        <div class="stat-card total">
                            <h5 class="mb-2">Total Customers</h5>
                            <h3><?php echo $displayed_count; ?></h3>
                            <small><i class="bi bi-people me-1"></i>Displayed</small>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6">
                        <div class="stat-card ready">
                            <h5 class="mb-2">Active</h5>
                            <h3><?php echo $active_count; ?></h3>
                            <small><i class="bi bi-check-circle me-1"></i>Currently active</small>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6">
                        <div class="stat-card cancelled">
                            <h5 class="mb-2">Banned</h5>
                            <h3><?php echo $banned_count; ?></h3>
                            <small><i class="bi bi-ban me-1"></i>Suspended/Banned</small>
                        </div>
                    </div>
                </div>

                <!-- Filter Summary -->
                <?php if ($filter !== 'all' || $search): ?>
                <div class="alert alert-info mb-4">
                    <i class="bi bi-funnel me-2"></i>
                    <strong>Active Filters:</strong>
                    <?php
                    $filters = [];
                    if ($filter !== 'all') $filters[] = "Status: " . ucfirst($filter);
                    if ($search) $filters[] = "Search: \"" . htmlspecialchars($search) . "\"";
                    echo implode(', ', $filters);
                    ?>
                    <a href="customers.php" class="float-end">Clear filters</a>
                </div>
                <?php endif; ?>

                <!-- Customers Table -->
                <div class="card">
                    <div class="card-header">
                        <i class="bi bi-table me-2"></i>Customers List
                    </div>
                    <div class="card-body">
                        <?php if (empty($customers)): ?>
                        <div class="text-center text-muted py-5">
                            <i class="bi bi-people fs-1 d-block mb-3 opacity-25"></i>
                            <h4 class="mt-3">No customers found</h4>
                            <p>Try adjusting your filters or check back later.</p>
                        </div>
                        <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover table-striped">
                                <thead class="table-light">
                                    <tr>
                                        <th>Avatar</th>
                                        <th>Username</th>
                                        <th>Email</th>
                                        <th>Status</th>
                                        <th>Total Orders</th>
                                        <th>Total Spent</th>
                                        <th>Last Order</th>
                                        <th>Verified</th>
                                        <th class="table-actions">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($customers as $customer): ?>
                                    <tr class="<?php echo $customer['is_active'] == 0 ? 'banned-row' : ''; ?>">
                                        <td>
                                            <?php if (!empty($customer['profile_picture'])): ?>
                                            <img src="<?php echo htmlspecialchars($customer['profile_picture']); ?>"
                                                alt="Avatar"
                                                class="customer-avatar"
                                                onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                            <i class="bi bi-person-circle avatar-icon" style="display:none;"></i>
                                            <?php else: ?>
                                            <i class="bi bi-person-circle avatar-icon"></i>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($customer['username']); ?></td>
                                        <td><?php echo htmlspecialchars($customer['email']); ?></td>
                                        <td>
                                            <?php if ($customer['is_active'] == 1): ?>
                                            <span class="badge bg-success status-badge">
                                                <i class="bi bi-check-circle"></i> Active
                                            </span>
                                            <?php else: ?>
                                            <span class="badge bg-danger status-badge"
                                                title="Banned: <?php echo htmlspecialchars($customer['ban_reason'] ?? 'No reason provided'); ?>
                                                Banned at: <?php echo $customer['banned_at'] ? date('d/m/Y H:i', strtotime($customer['banned_at'])) : 'Unknown'; ?>">
                                                <i class="bi bi-ban"></i> Banned
                                            </span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge <?php echo ($customer['total_orders'] ?? 0) > 0 ? 'bg-success' : 'bg-secondary'; ?>">
                                                <?php echo $customer['total_orders'] ?? 0; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if (($customer['total_spent'] ?? 0) > 0): ?>
                                            RM <?php echo number_format($customer['total_spent'], 2); ?>
                                            <?php else: ?>
                                            -
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($customer['last_order_date']): ?>
                                            <?php echo date('d/m/Y', strtotime($customer['last_order_date'])); ?>
                                            <?php else: ?>
                                            Never
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($customer['email_verified']): ?>
                                            <span class="badge bg-success">
                                                <i class="bi bi-check-circle"></i> Verified
                                            </span>
                                            <?php else: ?>
                                            <span class="badge bg-warning">
                                                <i class="bi bi-exclamation-triangle"></i> Unverified
                                            </span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="table-actions">
                                            <button type="button" class="btn btn-sm btn-outline-warning view-orders-btn"
                                                data-customer-id="<?php echo $customer['id']; ?>"
                                                data-customer-name="<?php echo htmlspecialchars($customer['username']); ?>"
                                                data-bs-toggle="tooltip" title="View Orders">
                                                <i class="bi bi-receipt"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-primary"
                                                onclick="openEditModal(<?php echo htmlspecialchars(json_encode($customer)); ?>)"
                                                data-bs-toggle="tooltip" title="Manage Customer">
                                                <i class="bi bi-gear"></i>
                                            </button>
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

    <!-- Customer Orders Modal -->
    <div class="modal fade" id="customerOrdersModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-receipt me-2"></i>Orders for <span id="modalCustomerName"></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="customerOrdersContent">
                        <div class="text-center">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
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
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Combined Edit/Manage Modal -->
    <div class="modal fade" id="manageCustomerModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-gear me-2"></i>Manage Customer: <span id="modal_customer_name"></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0">
                    <ul class="nav nav-tabs edit-modal-tabs px-3 pt-3" id="manageTabs" role="tablist">
                        <button class="nav-link active" id="edit-tab" data-bs-toggle="tab" data-bs-target="#edit" type="button" role="tab">
                            <i class="bi bi-pencil-square"></i> Edit Info
                        </button>
                        <button class="nav-link" id="security-tab" data-bs-toggle="tab" data-bs-target="#security" type="button" role="tab">
                            <i class="bi bi-shield-lock"></i> Security
                        </button>
                        <button class="nav-link text-danger" id="danger-tab" data-bs-toggle="tab" data-bs-target="#danger" type="button" role="tab">
                            <i class="bi bi-exclamation-triangle"></i> Danger Zone
                        </button>
                    </ul>
                    <div class="tab-content p-4">
                        <div class="tab-pane fade show active" id="edit" role="tabpanel">
                            <form method="POST" action="" id="editCustomerForm">
                                <input type="hidden" name="customer_id" id="edit_customer_id">
                                <input type="hidden" name="update_customer" value="1">
                                <div class="info-box">
                                    <i class="bi bi-info-circle me-2"></i>
                                    Edit basic customer information below.
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Username</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                                        <input type="text" class="form-control" name="username" id="edit_username" required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Email</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                        <input type="email" class="form-control" name="email" id="edit_email" required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Current Status</label>
                                    <div id="current_status_display"></div>
                                </div>
                                <div class="text-end">
                                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        <i class="bi bi-check-circle"></i> Update Customer
                                    </button>
                                </div>
                            </form>
                        </div>
                        <div class="tab-pane fade" id="security" role="tabpanel">
                            <div class="info-box">
                                <i class="bi bi-shield-lock me-2"></i>
                                Manage customer account security settings.
                            </div>
                            <div class="card mb-3">
                                <div class="card-header">
                                    <i class="bi bi-key me-2"></i>Password Management
                                </div>
                                <div class="card-body">
                                    <form method="POST" action="" id="resetPasswordForm">
                                        <input type="hidden" name="customer_id" id="reset_customer_id">
                                        <input type="hidden" name="reset_password" value="1">
                                        <p>Reset the customer's password to the default value.</p>
                                        <div class="alert alert-info">
                                            <i class="bi bi-info-circle"></i>
                                            New password will be: <strong>password</strong>
                                        </div>
                                        <button type="submit" class="btn btn-warning btn-sm" onclick="return confirm('Are you sure you want to reset this customer\'s password?')">
                                            <i class="bi bi-arrow-repeat"></i> Reset Password
                                        </button>
                                    </form>
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-header">
                                    <i class="bi bi-ban me-2"></i>Account Status
                                </div>
                                <div class="card-body">
                                    <div id="banStatus"></div>
                                    <form method="POST" action="" id="banForm" style="display: none;">
                                        <input type="hidden" name="customer_id" id="ban_customer_id">
                                        <input type="hidden" name="ban_customer" value="1">
                                        <div class="mb-3">
                                            <label class="form-label">Reason for ban (optional):</label>
                                            <textarea class="form-control" name="ban_reason" rows="2"
                                                placeholder="Enter reason for banning this customer..."></textarea>
                                        </div>
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to ban this customer?')">
                                            <i class="bi bi-ban"></i> Ban Customer
                                        </button>
                                    </form>
                                    <form method="POST" action="" id="unbanForm" style="display: none;">
                                        <input type="hidden" name="customer_id" id="unban_customer_id">
                                        <input type="hidden" name="unban_customer" value="1">
                                        <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Are you sure you want to restore this customer?')">
                                            <i class="bi bi-check-circle"></i> Restore Customer
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="danger" role="tabpanel">
                            <div class="warning-box">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                <strong>Warning:</strong> Actions in this section are irreversible.
                            </div>
                            <div class="danger-zone">
                                <h5 class="mb-3">
                                    <i class="bi bi-trash"></i> Delete Customer Account
                                </h5>
                                <p>This action will permanently delete the customer and all associated data including:</p>
                                <ul class="mb-3">
                                    <li>Order history and details</li>
                                    <li>Feedback and reviews</li>
                                    <li>Cart items</li>
                                    <li>Personal information</li>
                                </ul>
                                <form method="POST" action="" id="deleteCustomerForm" onsubmit="return confirmDelete()">
                                    <input type="hidden" name="customer_id" id="delete_customer_id">
                                    <input type="hidden" name="delete_customer" value="1">
                                    <div class="mb-3">
                                        <label class="form-label">Type "DELETE" to confirm:</label>
                                        <input type="text" class="form-control" id="confirm_delete"
                                            placeholder="Enter DELETE to confirm" required>
                                    </div>
                                    <button type="submit" class="btn btn-danger btn-sm" id="deleteButton" disabled>
                                        <i class="bi bi-trash"></i> Permanently Delete Customer
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
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

    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });

    // Customer Orders Modal
    const customerOrdersModal = new bootstrap.Modal(document.getElementById('customerOrdersModal'));
    const orderDetailsModal = new bootstrap.Modal(document.getElementById('orderDetailsModal'));
    const viewOrdersBtns = document.querySelectorAll('.view-orders-btn');

    viewOrdersBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const customerId = this.getAttribute('data-customer-id');
            const customerName = this.getAttribute('data-customer-name');
            document.getElementById('modalCustomerName').textContent = customerName;

            fetch('?ajax=customer_orders&customer_id=' + customerId)
                .then(response => response.json())
                .then(orders => {
                    const content = document.getElementById('customerOrdersContent');

                    if (orders && orders.length > 0) {
                        let ordersHtml = '';
                        orders.forEach(order => {
                            let statusBadge = 'bg-info';
                            if (order.status === 'completed') statusBadge = 'bg-success';
                            else if (order.status === 'pending') statusBadge = 'bg-warning';
                            else if (order.status === 'cancelled') statusBadge = 'bg-danger';

                            ordersHtml += `
                                <tr>
                                    <td><strong>#KTB-${order.id.toString().padStart(6, '0')}</strong></td>
                                    <td>${new Date(order.created_at).toLocaleDateString()}</td>
                                    <td>${order.item_count} items</td>
                                    <td class="fw-bold">RM ${parseFloat(order.total_amount).toFixed(2)}</td>
                                    <td>
                                        <span class="badge ${statusBadge}">
                                            ${order.status.toUpperCase()}
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary view-order-detail-btn" data-order-id="${order.id}">
                                            <i class="bi bi-eye"></i> View
                                        </button>
                                    </td>
                                </tr>
                            `;
                        });

                        content.innerHTML = `
                            <div class="table-responsive">
                                <table class="table table-hover table-striped">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Order ID</th>
                                            <th>Date</th>
                                            <th>Items</th>
                                            <th>Total</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${ordersHtml}
                                    </tbody>
                                </table>
                            </div>
                            <div class="alert alert-info mb-0">
                                <i class="bi bi-info-circle me-2"></i>
                                <strong>Total Orders:</strong> ${orders.length}
                            </div>
                        `;

                        document.querySelectorAll('.view-order-detail-btn').forEach(detailBtn => {
                            detailBtn.addEventListener('click', function() {
                                const orderId = this.getAttribute('data-order-id');
                                loadOrderDetails(orderId);
                            });
                        });

                        customerOrdersModal.show();
                    } else {
                        content.innerHTML = '<div class="alert alert-warning text-center">No orders found for this customer.</div>';
                        customerOrdersModal.show();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('customerOrdersContent').innerHTML = '<div class="alert alert-danger">Failed to load orders.</div>';
                    customerOrdersModal.show();
                });
        });
    });

    // Load Order Details
    function loadOrderDetails(orderId) {
        document.getElementById('modalOrderId').textContent = orderId.toString().padStart(6, '0');

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
                                <td class="text-end">RM ${parseFloat(item.price).toFixed(2)}</td>
                                <td class="text-end">RM ${(item.quantity * item.price).toFixed(2)}</td>
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
                                        <td class="text-end">RM ${parseFloat(order.total_amount).toFixed(2)}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    `;

                    customerOrdersModal.hide();
                    orderDetailsModal.show();
                } else {
                    content.innerHTML = '<div class="alert alert-danger">Order not found.</div>';
                    orderDetailsModal.show();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('orderDetailsContent').innerHTML = '<div class="alert alert-danger">Failed to load order details.</div>';
                orderDetailsModal.show();
            });
    }

    // Open combined edit modal
    function openEditModal(customer) {
        currentCustomer = customer;
        document.getElementById('modal_customer_name').textContent = customer.username;
        document.getElementById('edit_customer_id').value = customer.id;
        document.getElementById('edit_username').value = customer.username;
        document.getElementById('edit_email').value = customer.email;

        const statusDisplay = document.getElementById('current_status_display');
        if (customer.is_active == 1) {
            statusDisplay.innerHTML = '<span class="badge bg-success"><i class="bi bi-check-circle"></i> Active Account</span>';
        } else {
            statusDisplay.innerHTML = '<span class="badge bg-danger"><i class="bi bi-ban"></i> Banned Account</span>' +
                (customer.ban_reason ? `<br><small class="text-muted">Reason: ${customer.ban_reason}</small>` : '') +
                (customer.banned_at ? `<br><small class="text-muted">Banned at: ${new Date(customer.banned_at).toLocaleString()}</small>` : '');
        }

        document.getElementById('reset_customer_id').value = customer.id;
        document.getElementById('ban_customer_id').value = customer.id;
        document.getElementById('unban_customer_id').value = customer.id;

        const banStatus = document.getElementById('banStatus');
        if (customer.is_active == 1) {
            banStatus.innerHTML = '<div class="alert alert-success">Account is currently <strong>ACTIVE</strong></div>';
            document.getElementById('banForm').style.display = 'block';
            document.getElementById('unbanForm').style.display = 'none';
        } else {
            banStatus.innerHTML = '<div class="alert alert-danger">Account is currently <strong>BANNED</strong></div>';
            document.getElementById('banForm').style.display = 'none';
            document.getElementById('unbanForm').style.display = 'block';
        }

        document.getElementById('delete_customer_id').value = customer.id;
        document.getElementById('confirm_delete').value = '';
        document.getElementById('deleteButton').disabled = true;

        var modal = new bootstrap.Modal(document.getElementById('manageCustomerModal'));
        modal.show();
    }

    document.getElementById('confirm_delete')?.addEventListener('input', function(e) {
        const deleteButton = document.getElementById('deleteButton');
        deleteButton.disabled = e.target.value !== 'DELETE';
    });

    function confirmDelete() {
        return confirm('⚠️ FINAL WARNING: Are you absolutely sure you want to permanently delete this customer? This action cannot be undone!');
    }

    document.addEventListener('DOMContentLoaded', function() {
        const hash = window.location.hash;
        if (hash) {
            const tab = document.querySelector(`[data-bs-target="${hash}"]`);
            if (tab) {
                new bootstrap.Tab(tab).show();
            }
        }
    });
    </script>
</body>
</html>
