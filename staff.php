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
    // Add Staff
    if (isset($_POST['add_staff'])) {
        $data = [
            'username' => trim($_POST['username']),
            'email' => trim($_POST['email']),
            'password' => $_POST['password'],
        ];
        if (addStaff($pdo, $data)) {
            $message = 'Staff added successfully!';
            $message_type = 'success';
        } else {
            $message = 'Failed to add staff.';
            $message_type = 'error';
        }
    // Update Staff
    } elseif (isset($_POST['update_staff'])) {
        $id = intval($_POST['staff_id']);
        $data = [
            'username' => trim($_POST['username']),
            'email' => trim($_POST['email']),
            'role_id' => 2,
            'is_active' => 1
        ];
        if (updateStaff($pdo, $id, $data)) {
            $message = 'Staff updated successfully!';
            $message_type = 'success';
        } else {
            $message = 'Failed to update staff.';
            $message_type = 'error';
        }
    // Ban Staff (Deactivate)
    } elseif (isset($_POST['ban_staff'])) {
        $id = intval($_POST['staff_id']);
        if (deactivateStaff($pdo, $id)) {
            $message = 'Staff has been deactivated successfully!';
            $message_type = 'success';
        } else {
            $message = 'Failed to deactivate staff.';
            $message_type = 'error';
        }
    // Unban Staff (Activate)
    } elseif (isset($_POST['unban_staff'])) {
        $id = intval($_POST['staff_id']);
        if (activateStaff($pdo, $id)) {
            $message = 'Staff has been activated successfully!';
            $message_type = 'success';
        } else {
            $message = 'Failed to activate staff.';
            $message_type = 'error';
        }
    // Delete Staff
    } elseif (isset($_POST['delete_staff'])) {
        $id = intval($_POST['staff_id']);
        $reason = trim($_POST['delete_reason']);
        if (empty($reason)) {
            $message = 'You must provide a reason for deleting this staff.';
            $message_type = 'error';
        } elseif ($id == $_SESSION['user_id']) {
            $message = 'You cannot delete your own account.';
            $message_type = 'error';
        } else {
            if (deleteStaff($pdo, $id, $reason)) {
                $message = 'Staff deleted successfully!';
                $message_type = 'success';
            } else {
                $message = 'Failed to delete staff.';
                $message_type = 'error';
            }
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
    } elseif (isset($_POST['permanent_delete'])) {
        $id = intval($_POST['staff_id']);
        if (permanentlyDeleteStaff($pdo, $id)) {
            $message = 'Staff removed from list';
            $message_type = 'success';
        } else {
            $message = 'Failed to permanently remove staff.';
            $message_type = 'error';
        }
    }
}

// Get filter from URL
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';

// Get all staff
$staff_members = getAllStaff($pdo);

// Get statistics
$active_count = getActiveStaffCount($pdo);
$inactive_count = getInactiveStaffCount($pdo);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff</title>
    <!-- External CSS -->
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <!-- Bootstrap Icons -->
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <!-- Bootstrap CSS (Kept for Grid/Utilities, overridden by dashboard.css) -->
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
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
                <a href="#" class="nav-item active">
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
            <div class="p-4">
                <!-- Messages -->
                <?php if ($message): ?>
                <div class="alert alert-<?php echo $message_type === 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show">
                    <?php echo htmlspecialchars($message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>

                <div class="top-bar mb-4">
                    <h1 class="h3 mb-0">
                        <i class="bi bi-people me-2"></i>Staff
                    </h1>
                </div>

                <!-- Staff List Card -->
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="card-title mb-0">Staff List</h5>
                            <button class="btn btn-success"
                                data-bs-toggle="modal"
                                data-bs-target="#addStaffModal">
                                <i class="bi bi-person-plus"></i> Add Staff
                            </button>
                        </div>

                        <?php if (empty($staff_members)): ?>
                        <p class="text-muted">No staff found.</p>
                        <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Username</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($staff_members as $staff): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($staff['username']); ?></td>
                                        <td><?= htmlspecialchars($staff['email']); ?></td>
                                        <td><?= $staff['role_name']; ?></td>
                                        <td>
                                            <?php if ($staff['is_active']): ?>
                                            <span class="badge bg-success">Active</span>
                                            <?php else: ?>
                                            <span class="badge bg-secondary cursor-pointer"
                                                onclick='viewDeleteReason(<?= json_encode($staff["ban_reason"]); ?>)'>
                                                Deleted
                                            </span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($staff['is_active']): ?>
                                            <button class="btn btn-sm btn-primary"
                                                onclick='openStaffModal(<?= json_encode($staff); ?>)'>
                                                Manage
                                            </button>
                                            <?php else: ?>
                                            <form method="POST" class="d-inline">
                                                <input type="hidden" name="permanent_delete" value="1">
                                                <input type="hidden" name="staff_id" value="<?= $staff['id']; ?>">
                                                <button class="btn btn-sm btn-danger"
                                                    onclick="return confirm('Remove staff from list?')">
                                                    Remove
                                                </button>
                                            </form>
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

                <!-- Manage Staff Modal -->
                <div class="modal fade" id="manageStaffModal" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">
                                    <i class="bi bi-person-badge"></i> Manage Staff
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <!-- Tabs -->
                                <ul class="nav nav-tabs" id="staffTab" role="tablist">
                                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#editInfo">
                                        Edit Info
                                    </button>
                                    <button class="nav-link text-danger" data-bs-toggle="tab" data-bs-target="#dangerTab">
                                        Delete
                                    </button>
                                </ul>
                                <div class="tab-content mt-3">
                                    <!-- Edit Info Tab -->
                                    <div class="tab-pane fade show active" id="editInfo">
                                        <form method="POST">
                                            <input type="hidden" name="update_staff" value="1">
                                            <input type="hidden" name="staff_id" id="edit_staff_id">
                                            <div class="mb-3">
                                                <label class="form-label">Username</label>
                                                <input type="text" class="form-control"
                                                    name="username" id="edit_staff_username" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Email</label>
                                                <input type="email" class="form-control"
                                                    name="email" id="edit_staff_email" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Status</label><br>
                                                <span id="edit_staff_status_badge" class="badge"></span>
                                            </div>
                                            <button type="submit" class="btn btn-primary">
                                                Update Staff
                                            </button>
                                        </form>
                                    </div>
                                    <!-- Danger Zone -->
                                    <div class="tab-pane fade" id="dangerTab">
                                        <div class="alert alert-danger">
                                            <strong>Warning:</strong> This action is permanent.
                                        </div>
                                        <form method="POST" id="deleteStaffForm">
                                            <input type="hidden" name="delete_staff" value="1">
                                            <input type="hidden" name="staff_id" id="delete_staff_id">
                                            <div class="mb-3">
                                                <label class="form-label">Reason for deleting this staff:</label>
                                                <textarea name="delete_reason"
                                                    class="form-control"
                                                    rows="3"
                                                    required></textarea>
                                            </div>
                                            <button type="button"
                                                class="btn btn-danger w-100"
                                                onclick="confirmDelete()">
                                                Delete Staff
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Delete Reason Modal -->
                <div class="modal fade" id="reasonModal" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">
                                    <i class="bi bi-info-circle"></i> Delete Reason
                                </h5>
                                <button class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <p id="deleteReasonText"></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Add Staff Modal -->
                <div class="modal fade" id="addStaffModal" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">
                                    <i class="bi bi-person-plus"></i> Add New Staff
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <form method="POST">
                                    <input type="hidden" name="add_staff" value="1">
                                    <div class="mb-3">
                                        <label class="form-label">Username</label>
                                        <input type="text" name="username" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Email</label>
                                        <input type="email" name="email" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Password</label>
                                        <input type="password" name="password" class="form-control" required>
                                    </div>
                                    <button type="submit" class="btn btn-success w-100">
                                        Add Staff
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function openStaffModal(staff) {
            // Set basic info
            document.getElementById('edit_staff_id').value = staff.id;
            document.getElementById('edit_staff_username').value = staff.username;
            document.getElementById('edit_staff_email').value = staff.email;
            // Set hidden IDs for actions
            document.getElementById('delete_staff_id').value = staff.id;

            // Status badge
            const statusBadge = document.getElementById('edit_staff_status_badge');
            if (staff.is_active == 1) {
                statusBadge.className = "badge bg-success";
                statusBadge.innerText = "Active";
            } else {
                statusBadge.className = "badge bg-danger";
                statusBadge.innerText = "Deleted";
            }
            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('manageStaffModal'));
            modal.show();
        }

        function confirmDelete() {
            const form = document.getElementById('deleteStaffForm');
            const reason = form.querySelector('textarea[name="delete_reason"]').value.trim();
            if (reason === "") {
                alert("Please provide a reason before deleting.");
                return;
            }
            const confirmAction = confirm("Are you sure you want to delete this staff?");
            if (confirmAction) {
                form.submit();
            }
        }

        function viewDeleteReason(reason) {
            document.getElementById("deleteReasonText").innerText = reason;
            const modal = new bootstrap.Modal(document.getElementById('reasonModal'));
            modal.show();
        }
    </script>
</body>
</html>
