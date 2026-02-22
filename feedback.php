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
        $message_type = 'error';
    }
}

// Handle filter form submission
$orderBy = 'created_at DESC';
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

// Get filter parameters
$ratingFilter = isset($_GET['rating']) ? intval($_GET['rating']) : null;
$searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';

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
    } else { // 'created_at DESC' (default)
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
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback Management</title>

    <!-- Same CSS as dashboard -->
    <link rel="stylesheet" href="assets/css/dashStyle.css">

    <!-- Bootstrap Icons only (Font Awesome removed) -->
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/vendor/aos/aos.css" rel="stylesheet">
    <style>
        .card { border-radius: 10px; border: none; }
        .table-actions { white-space: nowrap; }
        .feedback-text {
            max-height: 100px;
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
        }
        .feedback-text.expanded {
            max-height: none;
            -webkit-line-clamp: unset;
        }
        .read-more {
            color: #0d6efd;
            cursor: pointer;
            font-size: 0.9rem;
        }
        .rating-stars {
            font-size: 1.1rem;
        }
        .stat-card {
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 15px;
            color: white;
        }
        .stat-card.total { background: #6f42c1; }
        .stat-card.days { background: #0d6efd; }
        .stat-card.avg { background: #198754; }
        .stat-card.displayed { background: #fd7e14; }
        .rating-badge {
            font-size: 0.75rem;
            padding: 0.25em 0.6em;
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
                    <div>
                        <span class="text-muted me-3">
                            <i class="bi bi-star-fill text-warning"></i> Avg Rating: <?php echo $stats['average_rating']; ?>/5
                        </span>
                        <button class="btn btn-outline-primary" data-bs-toggle="collapse" data-bs-target="#filterSection">
                            <i class="bi bi-funnel me-1"></i>Filters
                        </button>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="stat-card total shadow-sm">
                            <div class="text-center">
                                <h1 class="display-5"><?php echo $stats['total']; ?></h1>
                                <p class="mb-0">Total Feedback</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card days shadow-sm">
                            <div class="card-body text-center">
                                <h1 class="display-5"><?php echo $stats['recent']; ?></h1>
                                <p class="mb-0">Last 30 Days</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card avg shadow-sm">
                            <div class="card-body text-center">
                                <h1 class="display-5"><?php echo $stats['average_rating']; ?></h1>
                                <p class="mb-0">Average Rating</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card displayed shadow-sm">
                            <div class="card-body text-center">
                                <h1 class="display-5"><?php echo count($feedback_items); ?></h1>
                                <p class="mb-0">Displayed</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filter Section -->
                <div class="collapse mb-4" id="filterSection">
                    <div class="card">
                        <div class="card-body">
                            <form method="GET" class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Sort by</label>
                                    <select class="form-select" name="sort">
                                        <option value="newest" <?php echo (!isset($_GET['sort']) || $_GET['sort'] == 'newest') ? 'selected' : ''; ?>>Newest First</option>
                                        <option value="oldest" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'oldest') ? 'selected' : ''; ?>>Oldest First</option>
                                        <option value="rating_high" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'rating_high') ? 'selected' : ''; ?>>Highest Rating</option>
                                        <option value="rating_low" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'rating_low') ? 'selected' : ''; ?>>Lowest Rating</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Filter by Rating</label>
                                    <select class="form-select" name="rating">
                                        <option value="">All Ratings</option>
                                        <?php for ($i = 5; $i >= 1; $i--): ?>
                                            <option value="<?php echo $i; ?>" <?php echo (isset($_GET['rating']) && $_GET['rating'] == $i) ? 'selected' : ''; ?>>
                                                <?php echo str_repeat('★', $i); ?> (<?php echo $i; ?> stars)
                                            </option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Search</label>
                                    <input type="text" class="form-control" name="search"
                                           placeholder="Search in feedback or user..."
                                           value="<?php echo htmlspecialchars($searchQuery); ?>">
                                </div>
                                <div class="col-md-1 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary w-100">Apply</button>
                                </div>
                                <?php if (isset($_GET['sort']) || isset($_GET['rating']) || $searchQuery): ?>
                                <div class="col-12">
                                    <a href="feedback.php" class="btn btn-sm btn-outline-secondary">Clear Filters</a>
                                </div>
                                <?php endif; ?>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Feedback Table -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>User</th>
                                        <th>Rating</th>
                                        <th>Feedback</th>
                                        <th>Submitted</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($feedback_items)): ?>
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-4">
                                                No feedback found.
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($feedback_items as $feedback): ?>
                                            <tr>
                                                <td><?php echo $feedback['id']; ?></td>
                                                <td>
                                                    <div>
                                                        <strong><?php echo htmlspecialchars($feedback['display_name']); ?></strong>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="rating-stars">
                                                        <?php echo formatRatingDashboard($feedback['rating']); ?>
                                                        <span class="badge bg-warning text-dark rating-badge ms-1">
                                                            <?php echo $feedback['rating']; ?>/5
                                                        </span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="feedback-text" id="feedback-text-<?php echo $feedback['id']; ?>">
                                                        <?php echo nl2br(htmlspecialchars($feedback['feedback_text'])); ?>
                                                    </div>
                                                    <?php if (strlen($feedback['feedback_text']) > 150): ?>
                                                        <a href="#" class="read-more" onclick="toggleFeedbackText(<?php echo $feedback['id']; ?>)">Read more</a>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-muted small">
                                                    <?php echo formatFeedbackDateDashboard($feedback['created_at']); ?>
                                                    <div class="text-muted">
                                                        <?php echo date('M j, Y g:i A', strtotime($feedback['created_at'])); ?>
                                                    </div>
                                                </td>
                                                <td class="table-actions">
                                                    <button class="btn btn-sm btn-outline-info"
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
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
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
                    <h5 class="modal-title">Feedback Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">User</label>
                            <div id="viewUserName" class="p-2 bg-light rounded"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Rating</label>
                            <div id="viewRating" class="p-2 bg-light rounded"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Submitted</label>
                            <div id="viewDate" class="p-2 bg-light rounded"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Feedback</label>
                        <div id="viewFeedbackText" class="p-3 bg-light rounded" style="min-height: 150px; max-height: 300px; overflow-y: auto;"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <form method="POST" onsubmit="return confirm('Delete this feedback?');">
                        <input type="hidden" name="feedback_id" id="deleteFeedbackId">
                        <button type="submit" name="delete_feedback" class="btn btn-danger">
                            <i class="bi bi-trash me-1"></i>Delete
                        </button>
                    </form>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Function to view feedback details in modal
        function viewFeedback(button) {
            const feedbackId = button.getAttribute('data-feedback-id');
            const userName = button.getAttribute('data-feedback-name');
            const rating = button.getAttribute('data-feedback-rating');
            const feedbackText = button.getAttribute('data-feedback-text');
            const date = button.getAttribute('data-feedback-date');

            document.getElementById('viewUserName').textContent = userName;
            document.getElementById('viewDate').textContent = date;
            document.getElementById('deleteFeedbackId').value = feedbackId;

            // Display rating stars
            let stars = '';
            for (let i = 1; i <= 5; i++) {
                if (i <= rating) {
                    stars += '<i class="bi bi-star-fill text-warning fs-5"></i>';
                } else {
                    stars += '<i class="bi bi-star text-secondary fs-5"></i>';
                }
            }
            stars += ` <span class="ms-2 fs-6">(${rating}/5)</span>`;
            document.getElementById('viewRating').innerHTML = stars;

            // Display feedback text with line breaks
            document.getElementById('viewFeedbackText').innerHTML = feedbackText.replace(/\n/g, '<br>');
        }

        // Toggle feedback text between truncated and full view
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

        // Auto-dismiss alerts after 5 seconds
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);

        // Initialize tooltips
        document.addEventListener('DOMContentLoaded', function() {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>
</body>
</html>
