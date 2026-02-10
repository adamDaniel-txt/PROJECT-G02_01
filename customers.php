<?php
session_start();
require 'app/db.php';
require 'app/menu_functions.php';

// Simple authentication - in real app, use proper authentication
$is_staff = true; // You'll replace this with real auth later

if (!$is_staff) {
    header('Location: index.php');
    exit();
}

$message = '';
$message_type = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_item'])) {
        // Add new item
        $data = [
            'name' => trim($_POST['name']),
            'description' => trim($_POST['description']),
            'price' => floatval($_POST['price']),
            'category' => trim($_POST['category']),
            'image_url' => trim($_POST['image_url']),
            'is_available' => isset($_POST['is_available']) ? 1 : 0
        ];

        if (addMenuItem($pdo, $data)) {
            $message = 'Menu item added successfully!';
            $message_type = 'success';
        } else {
            $message = 'Failed to add menu item.';
            $message_type = 'error';
        }
    } elseif (isset($_POST['update_item'])) {
        // Update item
        $id = intval($_POST['item_id']);
        $data = [
            'name' => trim($_POST['name']),
            'description' => trim($_POST['description']),
            'price' => floatval($_POST['price']),
            'category' => trim($_POST['category']),
            'image_url' => trim($_POST['image_url']),
            'is_available' => isset($_POST['is_available']) ? 1 : 0
        ];

        if (updateMenuItem($pdo, $id, $data)) {
            $message = 'Menu item updated successfully!';
            $message_type = 'success';
        } else {
            $message = 'Failed to update menu item.';
            $message_type = 'error';
        }
    } elseif (isset($_POST['delete_item'])) {
        // Delete item
        $id = intval($_POST['item_id']);
        if (deleteMenuItem($pdo, $id)) {
            $message = 'Menu item deleted successfully!';
            $message_type = 'success';
        } else {
            $message = 'Failed to delete menu item.';
            $message_type = 'error';
        }
    }
}

// Get all menu items for display
$menu_items = getAllMenuItems($pdo, null, false); // Get all including unavailable
$categories = getMenuCategories($pdo);
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

                <a href="#" class="nav-item active">
                    <i class="bi bi-people"></i>
                    <span>Customers</span>
                </a>

                <a href="orders.php" class="nav-item">
                    <i class="bi bi-receipt"></i>
                    <span>Orders</span>
                </a>

                <a href="feedback.php" class="nav-item">
                    <i class="bi bi-chat-left-text"></i>
                    <span>Feedback</span>
                </a>
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
                        <i class="bi bi-people me-2"></i>Customers
                    </h1>
                </div>

                <!-- Insert Function -->





            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
