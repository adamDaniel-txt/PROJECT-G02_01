<?php
// Include your existing database configuration
require_once __DIR__ . '/db.php';

// Initialize variables
$message = '';
$message_type = ''; // 'success' or 'error'

// Handle feedback submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_feedback'])) {
    $name = trim($_POST['name'] ?? 'Anonymous');
    $email = trim($_POST['email'] ?? '');
    $feedback_text = trim($_POST['feedback'] ?? '');
    $rating = intval($_POST['rating'] ?? 3);

    // Validate rating
    if ($rating < 1 || $rating > 5) {
        $rating = 3; // Default to 3 if invalid
    }

    // Validate feedback text
    if (empty($feedback_text)) {
        $message = "Please enter your feedback.";
        $message_type = 'error';
    } elseif (strlen($feedback_text) < 5) {
        $message = "Feedback must be at least 5 characters long.";
        $message_type = 'error';
    } else {
        try {
            // Use your existing $pdo connection from db.php

            // Prepare and execute insert statement
            $stmt = $pdo->prepare("
                INSERT INTO feedbacks (user_name, user_email, feedback_text, rating, created_at)
                VALUES (:name, :email, :feedback, :rating, NOW())
            ");

            $stmt->execute([
                ':name' => $name ?: 'Anonymous',
                ':email' => $email ?: null,
                ':feedback' => $feedback_text,
                ':rating' => $rating
            ]);

            $message = "Thank you for your feedback!";
            $message_type = 'success';

            // Clear form data after successful submission
            $_POST = [];

        } catch(PDOException $e) {
            $message = "Sorry, there was an error submitting your feedback. Please try again.";
            $message_type = 'error';
            error_log("Feedback submission error: " . $e->getMessage());
        }
    }
}

// Function to get all feedbacks
function getAllFeedbacks($pdo) {
    try {
        $stmt = $pdo->query("
            SELECT * FROM feedbacks
            ORDER BY created_at DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        error_log("Error fetching feedbacks: " . $e->getMessage());
        return [];
    }
}

// Function to get star display HTML
function getStarDisplay($rating) {
    $html = '';
    // Display filled stars
    for ($i = 1; $i <= $rating; $i++) {
        $html .= '<i class="bi bi-star-fill"></i>';
    }
    // Display empty stars
    for ($i = $rating + 1; $i <= 5; $i++) {
        $html .= '<i class="bi bi-star"></i>';
    }
    $html .= '<span class="ms-2 text-light small">' . $rating . '/5</span>';
    return $html;
}

// Function to format date nicely
function formatFeedbackDate($dateString) {
    $timestamp = strtotime($dateString);
    $now = time();
    $diff = $now - $timestamp;

    // If within 24 hours, show relative time
    if ($diff < 86400) {
        if ($diff < 3600) {
            $minutes = floor($diff / 60);
            return ($minutes < 1) ? 'Just now' : $minutes . ' min ago';
        }
        $hours = floor($diff / 3600);
        return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
    }

    // Otherwise show date
    return date('M j, Y', $timestamp);
}

/**************************
 * DASHBOARD FUNCTIONS BELOW
 **************************/

// Get all feedback for dashboard with optional filters
function getAllFeedbackDashboard($pdo, $limit = null, $orderBy = 'created_at DESC') {
    try {
        $sql = "SELECT * FROM feedbacks ORDER BY $orderBy";

        if ($limit) {
            $sql .= " LIMIT :limit";
        }

        $stmt = $pdo->prepare($sql);

        if ($limit) {
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        error_log("Error fetching dashboard feedback: " . $e->getMessage());
        return [];
    }
}

// Get feedback by ID (for dashboard)
function getFeedbackById($pdo, $id) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM feedbacks WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        error_log("Error fetching feedback by ID: " . $e->getMessage());
        return false;
    }
}

// Delete feedback by ID (for dashboard)
function deleteFeedback($pdo, $id) {
    try {
        $stmt = $pdo->prepare("DELETE FROM feedbacks WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    } catch(PDOException $e) {
        error_log("Error deleting feedback: " . $e->getMessage());
        return false;
    }
}

// Get feedback statistics (for dashboard)
function getFeedbackStats($pdo) {
    $stats = [];

    try {
        // Total feedback count
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM feedbacks");
        $stats['total'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Average rating
        $stmt = $pdo->query("SELECT AVG(rating) as average_rating FROM feedbacks");
        $stats['average_rating'] = round($stmt->fetch(PDO::FETCH_ASSOC)['average_rating'], 1);

        // Rating distribution
        $stmt = $pdo->query("
            SELECT rating, COUNT(*) as count
            FROM feedbacks
            GROUP BY rating
            ORDER BY rating DESC
        ");
        $stats['distribution'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Recent feedback count (last 30 days)
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as recent_count
            FROM feedbacks
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
        ");
        $stmt->execute();
        $stats['recent'] = $stmt->fetch(PDO::FETCH_ASSOC)['recent_count'];

    } catch(PDOException $e) {
        error_log("Error getting feedback stats: " . $e->getMessage());
        // Return default values
        $stats['total'] = 0;
        $stats['average_rating'] = 0;
        $stats['distribution'] = [];
        $stats['recent'] = 0;
    }

    return $stats;
}

// Format rating stars for dashboard (different style)
function formatRatingDashboard($rating) {
    $stars = '';
    for ($i = 1; $i <= 5; $i++) {
        if ($i <= $rating) {
            $stars .= '<i class="bi bi-star-fill text-warning"></i>';
        } else {
            $stars .= '<i class="bi bi-star text-secondary"></i>';
        }
    }
    return $stars;
}

// Format date in a readable way for dashboard
function formatFeedbackDateDashboard($date) {
    $timestamp = strtotime($date);
    $now = time();
    $diff = $now - $timestamp;

    if ($diff < 3600) { // Less than 1 hour
        $minutes = floor($diff / 60);
        return ($minutes < 1) ? 'Just now' : $minutes . ' minutes ago';
    } elseif ($diff < 86400) { // Less than 1 day
        $hours = floor($diff / 3600);
        return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 604800) { // Less than 1 week
        $days = floor($diff / 86400);
        return $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
    } else {
        return date('M j, Y', $timestamp);
    }
}
?>
