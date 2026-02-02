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
    <style>
        .card { border-radius: 10px; border: none; }
        .table-actions { white-space: nowrap; }
        .price-input { max-width: 150px; }
        .availability-badge { font-size: 0.75rem; }
        .menu-image {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
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

                <a href="analytics.php" class="nav-item">
                    <i class="bi bi-bar-chart"></i>
                    <span>Analytics</span>
                </a>

                <a href="sales.php" class="nav-item">
                    <i class="bi bi-cart3"></i>
                    <span>Sales</span>
                </a>

                <a href="#" class="nav-item active">
                    <i class="bi bi-box"></i>
                    <span>Menu Items</span>
                </a>

                <a href="customers.php" class="nav-item">
                    <i class="bi bi-people"></i>
                    <span>Customers</span>
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
                        <i class="bi bi-box me-2"></i>Menu Management
                    </h1>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addItemModal">
                        <i class="bi bi-plus-circle me-1"></i>Add New Item
                    </button>
                </div>

                <!-- Menu Items Table -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Category</th>
                                        <th>Price</th>
                                        <th>Status</th>
                                        <th>Last Updated</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($menu_items)): ?>
                                        <tr>
                                            <td colspan="7" class="text-center text-muted py-4">
                                                No menu items found. Add your first item!
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($menu_items as $item): ?>
                                            <tr>
                                                <td><?php echo $item['id']; ?></td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <?php if ($item['image_url']): ?>
                                                            <img src="<?php echo htmlspecialchars($item['image_url']); ?>"
                                                                 alt="<?php echo htmlspecialchars($item['name']); ?>"
                                                                 class="menu-image me-3">
                                                        <?php endif; ?>
                                                        <div>
                                                            <strong><?php echo htmlspecialchars($item['name']); ?></strong>
                                                            <?php if ($item['description']): ?>
                                                                <div class="text-muted small">
                                                                    <?php echo htmlspecialchars(substr($item['description'], 0, 50)); ?>...
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-info"><?php echo htmlspecialchars($item['category']); ?></span>
                                                </td>
                                                <td class="fw-bold"><?php echo formatPrice($item['price']); ?></td>
                                                <td>
                                                    <?php if ($item['is_available']): ?>
                                                        <span class="badge bg-success availability-badge">Available</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-danger availability-badge">Unavailable</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-muted small">
                                                    <?php echo date('M j, Y', strtotime($item['updated_at'])); ?>
                                                </td>
                                                <td class="table-actions">
                                                    <button class="btn btn-sm btn-outline-primary"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#editItemModal"
                                                            data-item-id="<?php echo $item['id']; ?>"
                                                            data-item-name="<?php echo htmlspecialchars($item['name']); ?>"
                                                            data-item-description="<?php echo htmlspecialchars($item['description']); ?>"
                                                            data-item-price="<?php echo $item['price']; ?>"
                                                            data-item-category="<?php echo htmlspecialchars($item['category']); ?>"
                                                            data-item-image="<?php echo htmlspecialchars($item['image_url']); ?>"
                                                            data-item-available="<?php echo $item['is_available']; ?>"
                                                            onclick="editItem(this)">
                                                        <i class="bi bi-pencil"></i>
                                                    </button>
                                                    <form method="POST" class="d-inline" onsubmit="return confirm('Delete this item?');">
                                                        <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                                                        <button type="submit" name="delete_item" class="btn btn-sm btn-outline-danger">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
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

    <!-- Add Item Modal -->
    <div class="modal fade" id="addItemModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Add New Menu Item</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Name *</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="2"></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Price (RM) *</label>
                                <input type="number" class="form-control price-input" name="price" step="0.01" min="0" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Category *</label>
                                <select class="form-select" name="category" required>
                                    <option value="">Select Category</option>
                                    <option value="Coffee">Coffee</option>
                                    <option value="Tea">Tea</option>
                                    <option value="Non-Coffee">Non-Coffee Drinks</option>
                                    <option value="Refreshing">Refreshing</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Image URL (optional)</label>
                            <input type="url" class="form-control" name="image_url" placeholder="https://example.com/image.jpg">
                        </div>
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" name="is_available" id="addAvailable" checked>
                            <label class="form-check-label" for="addAvailable">Available for sale</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="add_item" class="btn btn-primary">Add Item</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Item Modal -->
    <div class="modal fade" id="editItemModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <input type="hidden" name="item_id" id="editItemId">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Menu Item</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Name *</label>
                            <input type="text" class="form-control" name="name" id="editItemName" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" id="editItemDescription" rows="2"></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Price (RM) *</label>
                                <input type="number" class="form-control price-input" name="price" id="editItemPrice" step="0.01" min="0" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Category *</label>
                                <select class="form-select" name="category" id="editItemCategory" required>
                                    <option value="">Select Category</option>
                                    <option value="Coffee">Coffee</option>
                                    <option value="Tea">Tea</option>
                                    <option value="Non-Coffee">Non-Coffee Drinks</option>
                                    <option value="Refreshing">Refreshing</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Image URL (optional)</label>
                            <input type="url" class="form-control" name="image_url" id="editItemImage" placeholder="https://example.com/image.jpg">
                        </div>
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" name="is_available" id="editItemAvailable">
                            <label class="form-check-label" for="editItemAvailable">Available for sale</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="update_item" class="btn btn-primary">Update Item</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function editItem(button) {
            const itemId = button.getAttribute('data-item-id');
            const itemName = button.getAttribute('data-item-name');
            const itemDescription = button.getAttribute('data-item-description');
            const itemPrice = button.getAttribute('data-item-price');
            const itemCategory = button.getAttribute('data-item-category');
            const itemImage = button.getAttribute('data-item-image');
            const itemAvailable = button.getAttribute('data-item-available') === '1';

            document.getElementById('editItemId').value = itemId;
            document.getElementById('editItemName').value = itemName;
            document.getElementById('editItemDescription').value = itemDescription;
            document.getElementById('editItemPrice').value = itemPrice;
            document.getElementById('editItemCategory').value = itemCategory;
            document.getElementById('editItemImage').value = itemImage;
            document.getElementById('editItemAvailable').checked = itemAvailable;
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
