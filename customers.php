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

// Additional check for customer management permission
if (!hasPermission('manage_customers')) {
    header('Location: dashboard.php');
    exit();
}

$message = '';
$message_type = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_customer'])) {
        // Update customer
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
        // Ban customer
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
        // Unban customer
        $id = intval($_POST['customer_id']);
        if (unbanCustomer($pdo, $id)) {
            $message = 'Customer has been restored successfully!';
            $message_type = 'success';
        } else {
            $message = 'Failed to restore customer.';
            $message_type = 'error';
        }
    } elseif (isset($_POST['delete_customer'])) {
        // Delete customer
        $id = intval($_POST['customer_id']);
        if (deleteCustomer($pdo, $id)) {
            $message = 'Customer deleted successfully!';
            $message_type = 'success';
        } else {
            $message = 'Failed to delete customer.';
            $message_type = 'error';
        }
    } elseif (isset($_POST['reset_password'])) {
        // Reset customer password
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

// Get all customers
$customers = getAllCustomers($pdo);

// Get statistics
$banned_count = getBannedCustomersCount($pdo);
$active_count = getActiveCustomersCount($pdo);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Management</title>
    <!-- External CSS -->
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <!-- Bootstrap Icons -->
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <!-- Bootstrap CSS -->
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
                <a href="customers.php" class="nav-item active">
                    <i class="bi bi-people"></i>
                    <span>Customers</span>
                </a>
                <?php endif; ?>
                <?php if (hasPermission('manage_staff')): ?>
                <a href="staff.php" class="nav-item">
                    <i class="bi bi-person-badge"></i>
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
            <div class="p-4">
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
                    <!-- Statistics Cards -->
                    <div class="d-flex gap-2">
                        <div class="badge bg-primary p-2">
                            <i class="bi bi-people"></i> Total: <?php echo count($customers); ?>
                        </div>
                        <div class="badge bg-success p-2">
                            <i class="bi bi-check-circle"></i> Active: <?php echo $active_count; ?>
                        </div>
                        <div class="badge bg-danger p-2">
                            <i class="bi bi-ban"></i> Banned: <?php echo $banned_count; ?>
                        </div>
                    </div>
                </div>

                <!-- Filter Buttons -->
                <div class="filter-buttons">
                    <a href="?filter=all" class="btn btn-sm <?php echo $filter == 'all' ? 'btn-primary' : 'btn-outline-primary'; ?>">
                        <i class="bi bi-list"></i> All Customers
                    </a>
                    <a href="?filter=active" class="btn btn-sm <?php echo $filter == 'active' ? 'btn-success' : 'btn-outline-success'; ?>">
                        <i class="bi bi-check-circle"></i> Active
                    </a>
                    <a href="?filter=banned" class="btn btn-sm <?php echo $filter == 'banned' ? 'btn-danger' : 'btn-outline-danger'; ?>">
                        <i class="bi bi-ban"></i> Banned
                    </a>
                </div>

                <!-- Customers Table -->
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
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
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $filtered_customers = array_filter($customers, function($customer) use ($filter) {
                                        if ($filter == 'active') return $customer['is_active'] == 1;
                                        if ($filter == 'banned') return $customer['is_active'] == 0;
                                        return true;
                                    });
                                    if (empty($filtered_customers)):
                                    ?>
                                    <tr>
                                        <td colspan="9" class="text-center py-4">
                                            <i class="bi bi-people fs-1 d-block mb-3 text-muted"></i>
                                            <p class="text-muted">No customers found.</p>
                                        </td>
                                    </tr>
                                    <?php else: ?>
                                    <?php foreach ($filtered_customers as $customer): ?>
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
                                        <td class="action-buttons">
                                            <button type="button" class="btn btn-sm btn-info"
                                                onclick="viewOrders(<?php echo $customer['id']; ?>)"
                                                data-bs-toggle="tooltip" title="View Orders">
                                                <i class="bi bi-receipt"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-warning"
                                                onclick="openEditModal(<?php echo htmlspecialchars(json_encode($customer)); ?>)"
                                                data-bs-toggle="tooltip" title="Manage Customer">
                                                <i class="bi bi-gear"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
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
                    <!-- Tabs Navigation -->
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
                    <!-- Tab Content -->
                    <div class="tab-content p-4">
                        <!-- Edit Info Tab -->
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
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-circle"></i> Update Customer
                                    </button>
                                </div>
                            </form>
                        </div>
                        <!-- Security Tab -->
                        <div class="tab-pane fade" id="security" role="tabpanel">
                            <div class="info-box">
                                <i class="bi bi-shield-lock me-2"></i>
                                Manage customer account security settings.
                            </div>
                            <!-- Reset Password Section -->
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
                                        <button type="submit" class="btn btn-warning" onclick="return confirm('Are you sure you want to reset this customer\'s password?')">
                                            <i class="bi bi-arrow-repeat"></i> Reset Password
                                        </button>
                                    </form>
                                </div>
                            </div>
                            <!-- Ban/Unban Section -->
                            <div class="card">
                                <div class="card-header">
                                    <i class="bi bi-ban me-2"></i>Account Status
                                </div>
                                <div class="card-body">
                                    <div id="banStatus"></div>
                                    <!-- Ban Form -->
                                    <form method="POST" action="" id="banForm" style="display: none;">
                                        <input type="hidden" name="customer_id" id="ban_customer_id">
                                        <input type="hidden" name="ban_customer" value="1">
                                        <div class="mb-3">
                                            <label class="form-label">Reason for ban (optional):</label>
                                            <textarea class="form-control" name="ban_reason" rows="2"
                                                placeholder="Enter reason for banning this customer..."></textarea>
                                        </div>
                                        <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to ban this customer?')">
                                            <i class="bi bi-ban"></i> Ban Customer
                                        </button>
                                    </form>
                                    <!-- Unban Form -->
                                    <form method="POST" action="" id="unbanForm" style="display: none;">
                                        <input type="hidden" name="customer_id" id="unban_customer_id">
                                        <input type="hidden" name="unban_customer" value="1">
                                        <button type="submit" class="btn btn-success" onclick="return confirm('Are you sure you want to restore this customer?')">
                                            <i class="bi bi-check-circle"></i> Restore Customer
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <!-- Danger Zone Tab -->
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
                                    <button type="submit" class="btn btn-danger" id="deleteButton" disabled>
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
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
        // Current customer data
        let currentCustomer = null;
        // Open combined edit modal
        function openEditModal(customer) {
            currentCustomer = customer;
            // Set modal title
            document.getElementById('modal_customer_name').textContent = customer.username;
            // Set Edit Info tab values
            document.getElementById('edit_customer_id').value = customer.id;
            document.getElementById('edit_username').value = customer.username;
            document.getElementById('edit_email').value = customer.email;
            // Set current status display
            const statusDisplay = document.getElementById('current_status_display');
            if (customer.is_active == 1) {
                statusDisplay.innerHTML = '<span class="badge bg-success"><i class="bi bi-check-circle"></i> Active Account</span>';
            } else {
                statusDisplay.innerHTML = '<span class="badge bg-danger"><i class="bi bi-ban"></i> Banned Account</span>' +
                    (customer.ban_reason ? `<br><small class="text-muted">Reason: ${customer.ban_reason}</small>` : '') +
                    (customer.banned_at ? `<br><small class="text-muted">Banned at: ${new Date(customer.banned_at).toLocaleString()}</small>` : '');
            }
            // Set Security tab values
            document.getElementById('reset_customer_id').value = customer.id;
            document.getElementById('ban_customer_id').value = customer.id;
            document.getElementById('unban_customer_id').value = customer.id;
            // Show/hide appropriate ban/unban forms
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
            // Set Danger Zone values
            document.getElementById('delete_customer_id').value = customer.id;
            // Reset delete confirmation
            document.getElementById('confirm_delete').value = '';
            document.getElementById('deleteButton').disabled = true;
            // Show modal
            var modal = new bootstrap.Modal(document.getElementById('manageCustomerModal'));
            modal.show();
        }
        // Delete confirmation with "DELETE" text
        document.getElementById('confirm_delete')?.addEventListener('input', function(e) {
            const deleteButton = document.getElementById('deleteButton');
            deleteButton.disabled = e.target.value !== 'DELETE';
        });
        function confirmDelete() {
            return confirm('⚠️ FINAL WARNING: Are you absolutely sure you want to permanently delete this customer? This action cannot be undone!');
        }
        // View orders function
        function viewOrders(customerId) {
            window.location.href = 'orders.php?customer_id=' + customerId;
        }
        // Handle tab switching from URL hash
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
