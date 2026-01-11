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
    $html .= '<span class="ms-2 text-muted small">' . $rating . '/5</span>';
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
?>
