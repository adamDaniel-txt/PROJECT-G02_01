<?php
session_start();
require 'app/db.php';
require 'app/menu_functions.php';
require 'app/permission.php';
require 'app/staff_functions.php';

// Check if user have permission
if (!hasPermission('view_dashboard')) {
    header('Location: index.php');
    exit();
}

// Additional check for staff management permission
if (!hasPermission('manage_staff')) {
    header('Location: dashboard.php');
    exit();
}

$message = '';
$message_type = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_staff'])) {
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $password = $_POST['password'] ?? 'password';

        if (empty($username) || empty($email)) {
            $message = 'Username and email are required.';
            $message_type = 'error';
        } elseif (emailExists($pdo, $email)) {
            $message = 'Email already exists.';
            $message_type = 'error';
        } elseif (usernameExists($pdo, $username)) {
            $message = 'Username already exists.';
            $message_type = 'error';
        } else {
            $data = [
                'username' => $username,
                'email' => $email,
                'password' => hash('sha256', $password),
                'email_verified' => 1,
                'is_active' => 1,
                'profile_picture' => NULL
            ];
            if (addStaff($pdo, $data)) {
                $message = 'Staff member added successfully!';
                $message_type = 'success';
            } else {
                $message = 'Failed to add staff member.';
                $message_type = 'error';
            }
        }
    } elseif (isset($_POST['update_staff'])) {
        $id = intval($_POST['staff_id']);
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);

        if (empty($username) || empty($email)) {
            $message = 'Username and email are required.';
            $message_type = 'error';
        } elseif (emailExists($pdo, $email, $id)) {
            $message = 'Email already exists.';
            $message_type = 'error';
        } elseif (usernameExists($pdo, $username, $id)) {
            $message = 'Username already exists.';
            $message_type = 'error';
        } else {
            $data = [
                'username' => $username,
                'email' => $email
            ];
            if (updateStaff($pdo, $id, $data)) {
                $message = 'Staff member updated successfully!';
                $message_type = 'success';
            } else {
                $message = 'Failed to update staff member.';
                $message_type = 'error';
            }
        }
    } elseif (isset($_POST['ban_staff'])) {
        $id = intval($_POST['staff_id']);
        $reason = trim($_POST['ban_reason'] ?? '');
        if (banStaff($pdo, $id, $reason)) {
            $message = 'Staff member has been banned/suspended successfully!';
            $message_type = 'success';
        } else {
            $message = 'Failed to ban staff member.';
            $message_type = 'error';
        }
    } elseif (isset($_POST['unban_staff'])) {
        $id = intval($_POST['staff_id']);
        if (unbanStaff($pdo, $id)) {
            $message = 'Staff member has been restored successfully!';
            $message_type = 'success';
        } else {
            $message = 'Failed to restore staff member.';
            $message_type = 'error';
        }
    } elseif (isset($_POST['delete_staff'])) {
        $id = intval($_POST['staff_id']);
        if (deleteStaff($pdo, $id)) {
            $message = 'Staff member deleted successfully!';
            $message_type = 'success';
        } else {
            $message = 'Failed to delete staff member.';
            $message_type = 'error';
        }
    } elseif (isset($_POST['reset_password'])) {
        $id = intval($_POST['staff_id']);
        if (resetStaffPassword($pdo, $id)) {
            $message = 'Password reset successfully! New password: password';
            $message_type = 'success';
        } else {
            $message = 'Failed to reset password.';
            $message_type = 'error';
        }
    }
}

$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$staff = getAllStaff($pdo);
$banned_count = getBannedStaffCount($pdo);
$active_count = getActiveStaffCount($pdo);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Management</title>
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
                <a href="customers.php" class="nav-item">
                    <i class="bi bi-people"></i>
                    <span>Customers</span>
                </a>
                <?php endif; ?>
                <?php if (hasPermission('manage_staff')): ?>
                <a href="staff.php" class="nav-item active">
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
            <div class="p-4">
                <?php if ($message): ?>
                <div class="alert alert-<?php echo $message_type === 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show">
                    <?php echo htmlspecialchars($message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>

                <div class="top-bar d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h3 mb-0">
                        <i class="bi bi-people me-2"></i>Staff Management
                    </h1>
                    <div class="d-flex gap-2">
                        <div class="badge bg-primary p-2">
                            <i class="bi bi-person-badge"></i> Total: <?php echo count($staff); ?>
                        </div>
                        <div class="badge bg-success p-2">
                            <i class="bi bi-check-circle"></i> Active: <?php echo $active_count; ?>
                        </div>
                        <div class="badge bg-danger p-2">
                            <i class="bi bi-ban"></i> Banned: <?php echo $banned_count; ?>
                        </div>
                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addStaffModal">
                            <i class="bi bi-plus-lg"></i> Add Staff
                        </button>
                    </div>
                </div>

                <div class="filter-buttons">
                    <a href="?filter=all" class="btn btn-sm <?php echo $filter == 'all' ? 'btn-primary' : 'btn-outline-primary'; ?>">
                        <i class="bi bi-list"></i> All Staff
                    </a>
                    <a href="?filter=active" class="btn btn-sm <?php echo $filter == 'active' ? 'btn-success' : 'btn-outline-success'; ?>">
                        <i class="bi bi-check-circle"></i> Active
                    </a>
                    <a href="?filter=banned" class="btn btn-sm <?php echo $filter == 'banned' ? 'btn-danger' : 'btn-outline-danger'; ?>">
                        <i class="bi bi-ban"></i> Banned
                    </a>
                </div>

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
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $filtered_staff = array_filter($staff, function($staff_member) use ($filter) {
                                        if ($filter == 'active') return $staff_member['is_active'] == 1;
                                        if ($filter == 'banned') return $staff_member['is_active'] == 0;
                                        return true;
                                    });
                                    if (empty($filtered_staff)):
                                    ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-4">
                                            <i class="bi bi-person-badge fs-1 d-block mb-3 text-muted"></i>
                                            <p class="text-muted">No staff members found.</p>
                                        </td>
                                    </tr>
                                    <?php else: ?>
                                    <?php foreach ($filtered_staff as $staff_member): ?>
                                    <tr class="<?php echo $staff_member['is_active'] == 0 ? 'banned-row' : ''; ?>">
                                        <td>
                                            <?php if (!empty($staff_member['profile_picture'])): ?>
                                            <img src="<?php echo htmlspecialchars($staff_member['profile_picture']); ?>"
                                                alt="Avatar"
                                                class="customer-avatar"
                                                onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                            <i class="bi bi-person-circle avatar-icon" style="display:none;"></i>
                                            <?php else: ?>
                                            <i class="bi bi-person-circle avatar-icon"></i>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($staff_member['username']); ?></td>
                                        <td><?php echo htmlspecialchars($staff_member['email']); ?></td>
                                        <td>
                                            <?php if ($staff_member['is_active'] == 1): ?>
                                            <span class="badge bg-success status-badge">
                                                <i class="bi bi-check-circle"></i> Active
                                            </span>
                                            <?php else: ?>
                                            <span class="badge bg-danger status-badge"
                                                title="Banned: <?php echo htmlspecialchars($staff_member['ban_reason'] ?? 'No reason provided'); ?>
                                                Banned at: <?php echo $staff_member['banned_at'] ? date('d/m/Y H:i', strtotime($staff_member['banned_at'])) : 'Unknown'; ?>">
                                                <i class="bi bi-ban"></i> Banned
                                            </span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="action-buttons">
                                            <button type="button" class="btn btn-sm btn-warning"
                                                onclick="openEditModal(<?php echo htmlspecialchars(json_encode($staff_member)); ?>)"
                                                data-bs-toggle="tooltip" title="Manage Staff">
                                                <i class="bi bi-gear"></i> Manage Staff
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

    <!-- Add Staff Modal -->
    <div class="modal fade" id="addStaffModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-person-plus me-2"></i>Add New Staff Member
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="" id="addStaffForm">
                        <input type="hidden" name="add_staff" value="1">
                        <div class="info-box mb-3">
                            <i class="bi bi-info-circle me-2"></i>
                            Fill in the details to create a new staff account.
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Username</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person"></i></span>
                                <input type="text" class="form-control" name="username" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Email</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                <input type="email" class="form-control" name="email" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-key"></i></span>
                                <input type="text" class="form-control" name="password" value="password" readonly>
                            </div>
                            <small class="text-muted">Default password is "password". Staff can change it later.</small>
                        </div>
                        <div class="text-end">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-plus-lg"></i> Add Staff
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Combined Edit/Manage Modal -->
    <div class="modal fade" id="manageStaffModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-gear me-2"></i>Manage Staff: <span id="modal_staff_name"></span>
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
                            <form method="POST" action="" id="editStaffForm">
                                <input type="hidden" name="staff_id" id="edit_staff_id">
                                <input type="hidden" name="update_staff" value="1">
                                <div class="info-box">
                                    <i class="bi bi-info-circle me-2"></i>
                                    Edit basic staff information below.
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
                                        <i class="bi bi-check-circle"></i> Update Staff
                                    </button>
                                </div>
                            </form>
                        </div>
                        <div class="tab-pane fade" id="security" role="tabpanel">
                            <div class="info-box">
                                <i class="bi bi-shield-lock me-2"></i>
                                Manage staff account security settings.
                            </div>
                            <div class="card mb-3">
                                <div class="card-header">
                                    <i class="bi bi-key me-2"></i>Password Management
                                </div>
                                <div class="card-body">
                                    <form method="POST" action="" id="resetPasswordForm">
                                        <input type="hidden" name="staff_id" id="reset_staff_id">
                                        <input type="hidden" name="reset_password" value="1">
                                        <p>Reset the staff member's password to the default value.</p>
                                        <div class="alert alert-info">
                                            <i class="bi bi-info-circle"></i>
                                            New password will be: <strong>password</strong>
                                        </div>
                                        <button type="submit" class="btn btn-warning" onclick="return confirm('Are you sure you want to reset this staff member\'s password?')">
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
                                        <input type="hidden" name="staff_id" id="ban_staff_id">
                                        <input type="hidden" name="ban_staff" value="1">
                                        <div class="mb-3">
                                            <label class="form-label">Reason for ban (optional):</label>
                                            <textarea class="form-control" name="ban_reason" rows="2"
                                                placeholder="Enter reason for banning this staff member..."></textarea>
                                        </div>
                                        <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to ban this staff member?')">
                                            <i class="bi bi-ban"></i> Ban Staff
                                        </button>
                                    </form>
                                    <form method="POST" action="" id="unbanForm" style="display: none;">
                                        <input type="hidden" name="staff_id" id="unban_staff_id">
                                        <input type="hidden" name="unban_staff" value="1">
                                        <button type="submit" class="btn btn-success" onclick="return confirm('Are you sure you want to restore this staff member?')">
                                            <i class="bi bi-check-circle"></i> Restore Staff
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
                                    <i class="bi bi-trash"></i> Delete Staff Account
                                </h5>
                                <p>This action will permanently delete the staff member and all associated data including:</p>
                                <ul class="mb-3">
                                    <li>Order history and details</li>
                                    <li>Feedback and reviews</li>
                                    <li>Cart items</li>
                                    <li>Personal information</li>
                                </ul>
                                <form method="POST" action="" id="deleteStaffForm" onsubmit="return confirmDelete()">
                                    <input type="hidden" name="staff_id" id="delete_staff_id">
                                    <input type="hidden" name="delete_staff" value="1">
                                    <div class="mb-3">
                                        <label class="form-label">Type "DELETE" to confirm:</label>
                                        <input type="text" class="form-control" id="confirm_delete"
                                            placeholder="Enter DELETE to confirm" required>
                                    </div>
                                    <button type="submit" class="btn btn-danger" id="deleteButton" disabled>
                                        <i class="bi bi-trash"></i> Permanently Delete Staff
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
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });

        let currentStaff = null;

        function openEditModal(staff) {
            currentStaff = staff;
            document.getElementById('modal_staff_name').textContent = staff.username;
            document.getElementById('edit_staff_id').value = staff.id;
            document.getElementById('edit_username').value = staff.username;
            document.getElementById('edit_email').value = staff.email;

            const statusDisplay = document.getElementById('current_status_display');
            if (staff.is_active == 1) {
                statusDisplay.innerHTML = '<span class="badge bg-success"><i class="bi bi-check-circle"></i> Active Account</span>';
            } else {
                statusDisplay.innerHTML = '<span class="badge bg-danger"><i class="bi bi-ban"></i> Banned Account</span>' +
                    (staff.ban_reason ? `<br><small class="text-muted">Reason: ${staff.ban_reason}</small>` : '') +
                    (staff.banned_at ? `<br><small class="text-muted">Banned at: ${new Date(staff.banned_at).toLocaleString()}</small>` : '');
            }

            document.getElementById('reset_staff_id').value = staff.id;
            document.getElementById('ban_staff_id').value = staff.id;
            document.getElementById('unban_staff_id').value = staff.id;

            const banStatus = document.getElementById('banStatus');
            if (staff.is_active == 1) {
                banStatus.innerHTML = '<div class="alert alert-success">Account is currently <strong>ACTIVE</strong></div>';
                document.getElementById('banForm').style.display = 'block';
                document.getElementById('unbanForm').style.display = 'none';
            } else {
                banStatus.innerHTML = '<div class="alert alert-danger">Account is currently <strong>BANNED</strong></div>';
                document.getElementById('banForm').style.display = 'none';
                document.getElementById('unbanForm').style.display = 'block';
            }

            document.getElementById('delete_staff_id').value = staff.id;
            document.getElementById('confirm_delete').value = '';
            document.getElementById('deleteButton').disabled = true;

            var modal = new bootstrap.Modal(document.getElementById('manageStaffModal'));
            modal.show();
        }

        document.getElementById('confirm_delete')?.addEventListener('input', function(e) {
            const deleteButton = document.getElementById('deleteButton');
            deleteButton.disabled = e.target.value !== 'DELETE';
        });

        function confirmDelete() {
            return confirm('⚠️ FINAL WARNING: Are you absolutely sure you want to permanently delete this staff member? This action cannot be undone!');
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
