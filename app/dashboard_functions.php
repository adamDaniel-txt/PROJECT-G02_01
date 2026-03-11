<?php
/**
 * Dashboard Functions
 * Handles all dashboard overview and summary data
 */

/**
 * Get sales summary for dashboard
 */
function getDashboardSalesSummary($pdo) {
    $sql = "SELECT
                COUNT(DISTINCT o.id) as total_orders,
                SUM(o.total_amount) as total_revenue,
                SUM(CASE WHEN o.status = 'completed' THEN 1 ELSE 0 END) as completed_orders,
                SUM(CASE WHEN o.status = 'pending' THEN 1 ELSE 0 END) as pending_orders,
                SUM(CASE WHEN o.status = 'cancelled' THEN 1 ELSE 0 END) as cancelled_orders,
                AVG(o.total_amount) as avg_order_value
            FROM orders o
            WHERE DATE(o.created_at) >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * Get recent orders for dashboard
 */
function getRecentOrders($pdo, $limit = 5) {
    $sql = "SELECT
                o.id,
                o.total_amount,
                o.status,
                o.created_at,
                o.pickup_code,
                u.username,
                COUNT(oi.id) as item_count
            FROM orders o
            LEFT JOIN users u ON o.user_id = u.id
            LEFT JOIN order_items oi ON o.id = oi.order_id
            GROUP BY o.id
            ORDER BY o.created_at DESC
            LIMIT :limit";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Get menu items summary for dashboard
 */
function getDashboardMenuSummary($pdo) {
    $sql = "SELECT
                COUNT(*) as total_items,
                SUM(CASE WHEN is_available = 1 THEN 1 ELSE 0 END) as available_items,
                SUM(CASE WHEN is_available = 0 THEN 1 ELSE 0 END) as unavailable_items,
                COUNT(DISTINCT category) as total_categories
            FROM menu_items";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * Get popular menu items for dashboard
 */
function getPopularMenuItems($pdo, $limit = 5) {
    $sql = "SELECT
                mi.id,
                mi.name,
                mi.category,
                mi.price,
                mi.image_url,
                mi.is_available,
                COUNT(oi.id) as times_ordered,
                SUM(oi.quantity) as total_quantity_sold
            FROM menu_items mi
            LEFT JOIN order_items oi ON mi.id = oi.menu_item_id
            LEFT JOIN orders o ON oi.order_id = o.id
            WHERE o.status != 'cancelled'
            GROUP BY mi.id
            ORDER BY total_quantity_sold DESC
            LIMIT :limit";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Get feedback summary for dashboard
 */
function getDashboardFeedbackSummary($pdo) {
    $sql = "SELECT
                COUNT(*) as total_feedbacks,
                AVG(rating) as avg_rating,
                SUM(CASE WHEN rating >= 4 THEN 1 ELSE 0 END) as positive_feedbacks,
                SUM(CASE WHEN rating <= 2 THEN 1 ELSE 0 END) as negative_feedbacks
            FROM feedbacks";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * Get recent feedbacks for dashboard
 */
function getRecentFeedbacks($pdo, $limit = 5) {
    $sql = "SELECT
                f.id,
                f.feedback_text,
                f.rating,
                f.created_at,
                u.username,
                u.email
            FROM feedbacks f
            LEFT JOIN users u ON f.user_id = u.id
            ORDER BY f.created_at DESC
            LIMIT :limit";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Get order status distribution for dashboard chart
 */
function getOrderStatusDistribution($pdo) {
    $sql = "SELECT
                status,
                COUNT(*) as count
            FROM orders
            GROUP BY status";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Get daily revenue for last 7 days
 */
function getDailyRevenue($pdo, $days = 7) {
    $sql = "SELECT
                DATE(created_at) as date,
                COUNT(*) as orders,
                SUM(total_amount) as revenue
            FROM orders
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL :days DAY)
            GROUP BY DATE(created_at)
            ORDER BY date ASC";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':days', $days, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Get category revenue breakdown
 */
function getCategoryRevenue($pdo) {
    $sql = "SELECT
                mi.category,
                COUNT(oi.id) as items_sold,
                SUM(oi.quantity * oi.price) as revenue
            FROM order_items oi
            JOIN menu_items mi ON oi.menu_item_id = mi.id
            JOIN orders o ON oi.order_id = o.id
            WHERE o.status != 'cancelled'
            GROUP BY mi.category
            ORDER BY revenue DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
