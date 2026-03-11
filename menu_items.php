<?php
session_start();
require 'app/db.php';
require 'app/menu_functions.php';
require 'app/permission.php';

// Check if user has permission
if (!hasPermission('view_dashboard')) {
    header('Location: index.php');
    exit();
}

$message = '';
$message_type = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_item'])) {
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
            $message_type = 'danger';
        }
    } elseif (isset($_POST['update_item'])) {
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
            $message_type = 'danger';
        }
    } elseif (isset($_POST['delete_item'])) {
        $id = intval($_POST['item_id']);
        if (deleteMenuItem($pdo, $id)) {
            $message = 'Menu item deleted successfully!';
            $message_type = 'success';
        } else {
            $message = 'Failed to delete menu item.';
            $message_type = 'danger';
        }
    }
}

// Get all menu items for display
$menu_items = getAllMenuItems($pdo, null, false);
$categories = getMenuCategories($pdo);

// Get menu summary statistics
$menu_summary = getMenuSummary($pdo);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Items</title>
    <link rel="stylesheet" href="assets/css/dashboard.css">
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
                <a href="#" class="nav-item active">
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
                        <i class="bi bi-box me-2"></i>Menu Management
                    </h1>
                    <div class="d-flex gap-2">
                        <a href="menu.php" target="_blank">
                            <button class="btn btn-primary btn-sm">
                                <i class="bi bi-eye me-1"></i>View Menu
                            </button>
                        </a>
                        <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addItemModal">
                            <i class="bi bi-plus-circle me-1"></i>Add New Item
                        </button>
                    </div>
                </div>

                <!-- Menu Summary Cards -->
                <div class="row mb-4">
                    <div class="col-md-3 col-sm-6">
                        <div class="stat-card total">
                            <h5 class="mb-2">Total Items</h5>
                            <h3><?php echo $menu_summary['total_items'] ?? 0; ?></h3>
                            <small><i class="bi bi-box-seam me-1"></i>All menu items</small>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="stat-card ready">
                            <h5 class="mb-2">Available</h5>
                            <h3><?php echo $menu_summary['available_items'] ?? 0; ?></h3>
                            <small><i class="bi bi-check-circle me-1"></i>Active for sale</small>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="stat-card cancelled">
                            <h5 class="mb-2">Unavailable</h5>
                            <h3><?php echo $menu_summary['unavailable_items'] ?? 0; ?></h3>
                            <small><i class="bi bi-x-circle me-1"></i>Currently hidden</small>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="stat-card pending">
                            <h5 class="mb-2">Categories</h5>
                            <h3><?php echo $menu_summary['total_categories'] ?? 0; ?></h3>
                            <small><i class="bi bi-tags me-1"></i>Different types</small>
                        </div>
                    </div>
                </div>

                <!-- Menu Items Table -->
                <div class="card">
                    <div class="card-header">
                        <i class="bi bi-table me-2"></i>Menu Items List
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Image</th>
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
                                        <td colspan="8" class="text-center text-muted py-4">
                                            <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                                            <p class="mt-2">No menu items found. Add your first item!</p>
                                        </td>
                                    </tr>
                                    <?php else: ?>
                                        <?php foreach ($menu_items as $item): ?>
                                        <tr class="<?php echo !$item['is_available'] ? 'banned-row' : ''; ?>">
                                            <td class="order-number">#<?php echo $item['id']; ?></td>
                                            <td>
                                                <?php if ($item['image_url']): ?>
                                                <img src="<?php echo htmlspecialchars($item['image_url']); ?>"
                                                     alt="<?php echo htmlspecialchars($item['name']); ?>"
                                                     class="menu-image"
                                                     style="width: 60px; height: 60px; object-fit: cover;">
                                                <?php else: ?>
                                                <div class="avatar-icon" style="width: 60px; height: 60px;">
                                                    <i class="bi bi-cup"></i>
                                                </div>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div>
                                                    <strong><?php echo htmlspecialchars($item['name']); ?></strong>
                                                    <?php if ($item['description']): ?>
                                                    <div class="text-muted small" style="font-size: 0.85rem;">
                                                        <?php echo htmlspecialchars(substr($item['description'], 0, 40)); ?>
                                                        <?php if (strlen($item['description']) > 40): ?>...<?php endif; ?>
                                                    </div>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-info"><?php echo htmlspecialchars($item['category']); ?></span>
                                            </td>
                                            <td class="fw-bold">$<?php echo number_format($item['price'], 2); ?></td>
                                            <td>
                                                <?php if ($item['is_available']): ?>
                                                <span class="badge bg-success">Available</span>
                                                <?php else: ?>
                                                <span class="badge bg-danger">Unavailable</span>
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
                        <h5 class="modal-title">
                            <i class="bi bi-plus-circle me-2"></i>Add New Menu Item
                        </h5>
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
                                <input type="number" class="form-control" name="price" step="0.01" min="0" required>
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
                        <h5 class="modal-title">
                            <i class="bi bi-pencil me-2"></i>Edit Menu Item
                        </h5>
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
                                <input type="number" class="form-control" name="price" id="editItemPrice" step="0.01" min="0" required>
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
    </script>
</body>
</html>
