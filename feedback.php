<?php
session_start();
require 'app/db.php';
require 'app/feedback_functions.php';
require 'app/permission.php';

// Check if user have permission
if (!hasPermission('view_dashboard')) {
    header('Location: index.php');
    exit();
}

$message = '';
$message_type = '';

// Handle delete action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_feedback'])) {
    $id = intval($_POST['feedback_id']);
    if (deleteFeedback($pdo, $id)) {
        $message = 'Feedback deleted successfully!';
        $message_type = 'success';
    } else {
        $message = 'Failed to delete feedback.';
        $message_type = 'danger';
    }
}

// Get filter parameters
$orderBy = 'created_at DESC';
$ratingFilter = isset($_GET['rating']) ? intval($_GET['rating']) : null;
$searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';

// Get sort parameter
if (isset($_GET['sort'])) {
    switch ($_GET['sort']) {
        case 'rating_high':
            $orderBy = 'rating DESC, created_at DESC';
            break;
        case 'rating_low':
            $orderBy = 'rating ASC, created_at DESC';
            break;
        case 'newest':
            $orderBy = 'created_at DESC';
            break;
        case 'oldest':
            $orderBy = 'created_at ASC';
            break;
    }
}

// Get all feedback using your existing function
$feedback_items = getAllFeedbacks($pdo);

// Apply sorting
usort($feedback_items, function($a, $b) use ($orderBy) {
    if ($orderBy === 'rating DESC, created_at DESC') {
        if ($a['rating'] == $b['rating']) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        }
        return $b['rating'] - $a['rating'];
    } elseif ($orderBy === 'rating ASC, created_at DESC') {
        if ($a['rating'] == $b['rating']) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        }
        return $a['rating'] - $b['rating'];
    } elseif ($orderBy === 'created_at ASC') {
        return strtotime($a['created_at']) - strtotime($b['created_at']);
    } else {
        return strtotime($b['created_at']) - strtotime($a['created_at']);
    }
});

// Apply filters
if ($ratingFilter) {
    $feedback_items = array_filter($feedback_items, function($item) use ($ratingFilter) {
        return $item['rating'] == $ratingFilter;
    });
}

if ($searchQuery) {
    $feedback_items = array_filter($feedback_items, function($item) use ($searchQuery) {
        return stripos($item['feedback_text'], $searchQuery) !== false ||
               stripos($item['display_name'], $searchQuery) !== false;
    });
}

// Get statistics using the new function
$stats = getFeedbackStats($pdo);

// Get current filter values for display
$currentSort = $_GET['sort'] ?? 'newest';
$currentRating = $_GET['rating'] ?? '';
$currentSearch = $_GET['search'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback Management</title>
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
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
                <a href="#" class="nav-item active">
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
                        <i class="bi bi-chat-left-text me-2"></i>Feedback Management
                    </h1>
                    <div class="d-flex gap-2 align-items-center">
                        <span class="text-muted me-2">
                            <i class="bi bi-star-fill text-warning"></i> Avg: <?php echo $stats['average_rating']; ?>/5
                        </span>
                        <div class="filter-wrapper">
                            <button class="btn btn-primary btn-sm" id="filterBtn" type="button">
                                <i class="bi bi-funnel me-1"></i>Filter
                            </button>
                            <div class="filter-dropdown" id="filterDropdown">
                                <form method="GET" class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label">Sort by</label>
                                        <select class="form-select" name="sort">
                                            <option value="newest" <?php echo $currentSort == 'newest' ? 'selected' : ''; ?>>Newest First</option>
                                            <option value="oldest" <?php echo $currentSort == 'oldest' ? 'selected' : ''; ?>>Oldest First</option>
                                            <option value="rating_high" <?php echo $currentSort == 'rating_high' ? 'selected' : ''; ?>>Highest Rating</option>
                                            <option value="rating_low" <?php echo $currentSort == 'rating_low' ? 'selected' : ''; ?>>Lowest Rating</option>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Filter by Rating</label>
                                        <select class="form-select" name="rating">
                                            <option value="">All Ratings</option>
                                            <?php for ($i = 5; $i >= 1; $i--): ?>
                                            <option value="<?php echo $i; ?>" <?php echo $currentRating == $i ? 'selected' : ''; ?>>
                                                <?php echo str_repeat('★', $i); ?> (<?php echo $i; ?> stars)
                                            </option>
                                            <?php endfor; ?>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Search</label>
                                        <input type="text" class="form-control" name="search"
                                               placeholder="Search feedback or user..."
                                               value="<?php echo htmlspecialchars($currentSearch); ?>">
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

                <!-- Statistics Cards (UNCHANGED) -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="stat-card total">
                            <div class="text-center">
                                <h1 class="display-5"><?php echo $stats['total']; ?></h1>
                                <p class="mb-0">Total Feedback</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card preparing">
                            <div class="text-center">
                                <h1 class="display-5"><?php echo $stats['recent']; ?></h1>
                                <p class="mb-0">Last 30 Days</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card ready">
                            <div class="text-center">
                                <h1 class="display-5"><?php echo $stats['average_rating']; ?></h1>
                                <p class="mb-0">Average Rating</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card total">
                            <div class="text-center">
                                <h1 class="display-5"><?php echo count($feedback_items); ?></h1>
                                <p class="mb-0">Displayed</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filter Summary -->
                <?php if ($currentRating || $currentSearch || $currentSort !== 'newest'): ?>
                <div class="alert alert-info mb-4">
                    <i class="bi bi-funnel me-2"></i>
                    <strong>Active Filters:</strong>
                    <?php
                    $filters = [];
                    if ($currentSort !== 'newest') $filters[] = "Sort: " . ucfirst(str_replace('_', ' ', $currentSort));
                    if ($currentRating) $filters[] = "Rating: " . $currentRating . " stars";
                    if ($currentSearch) $filters[] = "Search: \"" . htmlspecialchars($currentSearch) . "\"";
                    echo implode(', ', $filters);
                    ?>
                    <a href="feedback.php" class="float-end">Clear filters</a>
                </div>
                <?php endif; ?>

                <!-- Feedback Table -->
                <div class="card">
                    <div class="card-header">
                        <i class="bi bi-table me-2"></i>Feedback List
                    </div>
                    <div class="card-body">
                        <?php if (empty($feedback_items)): ?>
                        <div class="text-center text-muted py-5">
                            <i class="bi bi-chat-left-text display-1 opacity-25"></i>
                            <h4 class="mt-3">No feedback found</h4>
                            <p>Try adjusting your filters or check back later.</p>
                        </div>
                        <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover table-striped">
                                <thead class="table-light">
                                    <tr>
                                        <th>User</th>
                                        <th>Rating</th>
                                        <th>Feedback</th>
                                        <th>Submitted</th>
                                        <th class="table-actions">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($feedback_items as $feedback): ?>
                                    <tr class="<?php echo $feedback['rating'] <= 2 ? 'banned-row' : ''; ?>">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <?php if (isset($feedback['profile_picture']) && $feedback['profile_picture']): ?>
                                                <img src="<?php echo htmlspecialchars($feedback['profile_picture']); ?>"
                                                     alt="User"
                                                     class="customer-avatar me-2">
                                                <?php else: ?>
                                                <div class="avatar-icon me-2">
                                                    <i class="bi bi-person"></i>
                                                </div>
                                                <?php endif; ?>
                                                <div>
                                                    <strong><?php echo htmlspecialchars($feedback['display_name']); ?></strong>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="rating-stars">
                                                <?php
                                                for ($i = 1; $i <= 5; $i++):
                                                ?>
                                                <i class="bi bi-star<?php echo $i <= $feedback['rating'] ? '-fill' : ''; ?> <?php echo $i <= $feedback['rating'] ? 'text-warning' : 'text-muted'; ?>"></i>
                                                <?php endfor; ?>
                                                <span class="badge bg-warning rating-badge ms-1">
                                                    <?php echo $feedback['rating']; ?>/5
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="feedback-text" id="feedback-text-<?php echo $feedback['id']; ?>">
                                                <?php echo nl2br(htmlspecialchars($feedback['feedback_text'])); ?>
                                            </div>
                                            <?php if (strlen($feedback['feedback_text']) > 150): ?>
                                            <a href="#" class="read-more" onclick="toggleFeedbackText(<?php echo $feedback['id']; ?>); return false;">Read more</a>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-muted small">
                                            <?php echo date('M j, Y', strtotime($feedback['created_at'])); ?>
                                            <br>
                                            <small>
                                                <?php echo date('g:i A', strtotime($feedback['created_at'])); ?>
                                            </small>
                                        </td>
                                        <td class="table-actions">
                                            <button class="btn btn-sm btn-outline-primary"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#viewFeedbackModal"
                                                    data-feedback-id="<?php echo $feedback['id']; ?>"
                                                    data-feedback-name="<?php echo htmlspecialchars($feedback['display_name']); ?>"
                                                    data-feedback-rating="<?php echo $feedback['rating']; ?>"
                                                    data-feedback-text="<?php echo htmlspecialchars($feedback['feedback_text']); ?>"
                                                    data-feedback-date="<?php echo date('F j, Y g:i A', strtotime($feedback['created_at'])); ?>"
                                                    onclick="viewFeedback(this)">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            <form method="POST" class="d-inline" onsubmit="return confirm('Delete this feedback? This action cannot be undone.');">
                                                <input type="hidden" name="feedback_id" value="<?php echo $feedback['id']; ?>">
                                                <button type="submit" name="delete_feedback" class="btn btn-sm btn-outline-danger">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
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

    <!-- View Feedback Modal -->
    <div class="modal fade" id="viewFeedbackModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-chat-left-text me-2"></i>Feedback Details
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">User</label>
                            <div id="viewUserName" class="p-2 bg-light rounded"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Submitted</label>
                            <div id="viewDate" class="p-2 bg-light rounded"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Rating</label>
                        <div id="viewRating" class="p-2 bg-light rounded"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Feedback</label>
                        <div id="viewFeedbackText" class="p-3 bg-light rounded" style="min-height: 150px; max-height: 300px; overflow-y: auto;"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <form method="POST" onsubmit="return confirm('Delete this feedback?');">
                        <input type="hidden" name="feedback_id" id="deleteFeedbackId">
                        <button type="submit" name="delete_feedback" class="btn btn-danger btn-sm">
                            <i class="bi bi-trash me-1"></i>Delete
                        </button>
                    </form>
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
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

    function viewFeedback(button) {
        const feedbackId = button.getAttribute('data-feedback-id');
        const userName = button.getAttribute('data-feedback-name');
        const rating = button.getAttribute('data-feedback-rating');
        const feedbackText = button.getAttribute('data-feedback-text');
        const date = button.getAttribute('data-feedback-date');

        document.getElementById('viewUserName').textContent = userName;
        document.getElementById('viewDate').textContent = date;
        document.getElementById('deleteFeedbackId').value = feedbackId;

        let stars = '';
        for (let i = 1; i <= 5; i++) {
            if (i <= rating) {
                stars += '<i class="bi bi-star-fill text-warning fs-5"></i>';
            } else {
                stars += '<i class="bi bi-star text-muted fs-5"></i>';
            }
        }
        stars += ` <span class="ms-2 fs-6">(${rating}/5)</span>`;
        document.getElementById('viewRating').innerHTML = stars;
        document.getElementById('viewFeedbackText').innerHTML = feedbackText.replace(/\n/g, '<br>');
    }

    function toggleFeedbackText(feedbackId) {
        const element = document.getElementById(`feedback-text-${feedbackId}`);
        const link = element.nextElementSibling;
        if (element.classList.contains('expanded')) {
            element.classList.remove('expanded');
            link.textContent = 'Read more';
        } else {
            element.classList.add('expanded');
            link.textContent = 'Show less';
        }
    }
    </script>
</body>
</html>
