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
            'role_id' => trim($_POST['role_id']),
            'is_active' => isset($_POST['is_active']) ? 1 : 0
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
            'role_id' => trim($_POST['role_id']),
            'is_active' => isset($_POST['is_active']) ? 1 : 0
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
    <title>Products</title>

    <!-- Same CSS as dashboard -->
    <link rel="stylesheet" href="assets/css/dashStyle.css">

    <!-- Bootstrap Icons only (Font Awesome removed) -->
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/vendor/aos/aos.css" rel="stylesheet">
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
                        <i class="bi bi-people me-2"></i>Staff
                    </h1>
                </div>

                <!-- Insert Function -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Staff List</h5>

                        <?php if (empty($staff_members)): ?>
                            <p class="text-muted">No staff found.</p>
                        <?php else: ?>
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
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
                                            <td><?= $staff['id']; ?></td>
                                            <td><?= htmlspecialchars($staff['username']); ?></td>
                                            <td><?= htmlspecialchars($staff['email']); ?></td>
                                            <td><?= $staff['role_name']; ?></td>
                                            <td>
                                                <?php if ($staff['is_active']): ?>
                                                    <span class="badge bg-success">Active</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">Banned</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-primary"
                                                    onclick='openStaffModal(<?= json_encode($staff); ?>)'>
                                                    Manage
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Modal -->
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
                                    <li class="nav-item">
                                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#editInfo">
                                            Edit Info
                                        </button>
                                    </li>
                                    <li class="nav-item">
                                        <button class="nav-link text-danger" data-bs-toggle="tab" data-bs-target="#dangerTab">
                                            Delete
                                        </button>
                                    </li>
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
                                                <label class="form-label">Role</label>
                                                <select class="form-select" name="role_id" id="edit_staff_role" required>
                                                    <option value="1">Admin</option>
                                                    <option value="2">Staff</option>
                                                </select>
                                            </div>

                                            <div class="mb-3">
                                                <label>Status</label><br>
                                                <span id="edit_staff_status_badge" class="badge"></span>
                                            </div>

                                            <button type="submit" class="btn btn-primary">
                                                Update Staff
                                            </button>
                                        </form>
                                    </div>

                                    <!-- Security Tab -->
                                    <div class="tab-pane fade" id="securityTab">

                                        <!-- Reset Password -->
                                        <form method="POST" class="mb-3">
                                            <input type="hidden" name="reset_staff_password" value="1">
                                            <input type="hidden" name="staff_id" id="reset_staff_id">

                                            <button type="submit" class="btn btn-warning w-100">
                                                <i class="bi bi-key"></i> Reset Password to Default ("password")
                                            </button>
                                        </form>

                                        <!-- Ban / Unban -->
                                        <form method="POST" id="banForm" class="mb-2">
                                            <input type="hidden" name="ban_staff" value="1">
                                            <input type="hidden" name="staff_id" id="ban_staff_id">

                                            <button type="submit" class="btn btn-danger w-100">
                                                Ban Staff
                                            </button>
                                        </form>

                                        <form method="POST" id="unbanForm" class="mb-2">
                                            <input type="hidden" name="unban_staff" value="1">
                                            <input type="hidden" name="staff_id" id="unban_staff_id">

                                            <button type="submit" class="btn btn-success w-100">
                                                Restore Staff
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
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    function openStaffModal(staff) {

        // Set basic info
        document.getElementById('edit_staff_id').value = staff.id;
        document.getElementById('edit_staff_username').value = staff.username;
        document.getElementById('edit_staff_email').value = staff.email;
        document.getElementById('edit_staff_role').value = staff.role_id;

        // Set hidden IDs for actions
        document.getElementById('reset_staff_id').value = staff.id;
        document.getElementById('ban_staff_id').value = staff.id;
        document.getElementById('unban_staff_id').value = staff.id;
        document.getElementById('delete_staff_id').value = staff.id;

        // Status badge
        const statusBadge = document.getElementById('edit_staff_status_badge');
        if (staff.is_active == 1) {
            statusBadge.className = "badge bg-success";
            statusBadge.innerText = "Active";

            document.getElementById('banForm').style.display = "block";
            document.getElementById('unbanForm').style.display = "none";
        } else {
            statusBadge.className = "badge bg-danger";
            statusBadge.innerText = "Banned";

            document.getElementById('banForm').style.display = "none";
            document.getElementById('unbanForm').style.display = "block";
        }

        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('manageStaffModal'));
        modal.show();
    }
    </script>

    <script>
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
</script>
</body>
</html>
