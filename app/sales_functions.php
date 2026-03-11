<?php
/**
 * Sales Report Functions
 * Handles all sales-related database operations and exports
 */

/**
 * Get sales data with date range filter
 */
function getSalesData($pdo, $start_date, $end_date) {
    $sql = "SELECT
                o.id as order_id,
                o.total_amount,
                o.status,
                o.created_at,
                o.payment_id,
                o.pickup_code,
                o.user_id,
                u.username,
                u.email,
                COUNT(oi.id) as item_count
            FROM orders o
            LEFT JOIN users u ON o.user_id = u.id
            LEFT JOIN order_items oi ON o.id = oi.order_id
            WHERE DATE(o.created_at) BETWEEN :start_date AND :end_date
            GROUP BY o.id
            ORDER BY o.created_at DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':start_date' => $start_date,
        ':end_date' => $end_date
    ]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Get sales summary statistics
 */
function getSalesSummary($pdo, $start_date, $end_date) {
    $sql = "SELECT
                COUNT(DISTINCT o.id) as total_orders,
                SUM(o.total_amount) as total_revenue,
                AVG(o.total_amount) as avg_order_value,
                SUM(CASE WHEN o.status = 'completed' THEN 1 ELSE 0 END) as completed_orders,
                SUM(CASE WHEN o.status = 'pending' THEN 1 ELSE 0 END) as pending_orders,
                SUM(CASE WHEN o.status = 'cancelled' THEN 1 ELSE 0 END) as cancelled_orders
            FROM orders o
            WHERE DATE(o.created_at) BETWEEN :start_date AND :end_date";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':start_date' => $start_date,
        ':end_date' => $end_date
    ]);

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * Get chart data for daily sales trend
 */
function getChartData($pdo, $start_date, $end_date) {
    $sql = "SELECT
                DATE(created_at) as date,
                COUNT(id) as orders,
                SUM(total_amount) as revenue
            FROM orders
            WHERE DATE(created_at) BETWEEN :start_date AND :end_date
            GROUP BY DATE(created_at)
            ORDER BY date ASC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':start_date' => $start_date,
        ':end_date' => $end_date
    ]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Get revenue by category data
 */
function getCategoryData($pdo, $start_date, $end_date) {
    $sql = "SELECT
                mi.category,
                COUNT(oi.id) as items_sold,
                SUM(oi.quantity * oi.price) as category_revenue
            FROM order_items oi
            JOIN menu_items mi ON oi.menu_item_id = mi.id
            JOIN orders o ON oi.order_id = o.id
            WHERE DATE(o.created_at) BETWEEN :start_date AND :end_date
            GROUP BY mi.category
            ORDER BY category_revenue DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':start_date' => $start_date,
        ':end_date' => $end_date
    ]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Get detailed order information for modal
 */
function getOrderDetails($pdo, $order_id) {
    $sql = "SELECT
                o.id,
                o.total_amount,
                o.status,
                o.created_at,
                o.payment_id,
                o.pickup_code,
                o.notes,
                u.username,
                u.email,
                oi.menu_item_id,
                oi.quantity,
                oi.price,
                mi.name as item_name
            FROM orders o
            LEFT JOIN users u ON o.user_id = u.id
            JOIN order_items oi ON o.id = oi.order_id
            JOIN menu_items mi ON oi.menu_item_id = mi.id
            WHERE o.id = :order_id";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([':order_id' => $order_id]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Download sales report as CSV
 */
function downloadSalesReportCSV($pdo, $start_date, $end_date) {
    $sales_data = getSalesData($pdo, $start_date, $end_date);

    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="sales_report_' . $start_date . '_to_' . $end_date . '.csv"');
    header('Pragma: no-cache');
    header('Expires: 0');

    $output = fopen('php://output', 'w');

    // CSV Header
    fputcsv($output, [
        'Order ID',
        'Date',
        'Customer',
        'Email',
        'Items',
        'Total Amount',
        'Status',
        'Payment ID',
        'Pickup Code'
    ]);

    // CSV Data
    foreach ($sales_data as $row) {
        fputcsv($output, [
            $row['order_id'],
            $row['created_at'],
            $row['username'] ?? 'Guest',
            $row['email'] ?? 'N/A',
            $row['item_count'],
            $row['total_amount'],
            $row['status'],
            $row['payment_id'],
            $row['pickup_code'] ?? 'N/A'
        ]);
    }

    fclose($output);
    exit();
}

/**
 * Prepare chart data for JavaScript
 */
function prepareChartData($chart_data) {
    $labels = [];
    $revenue = [];
    $orders = [];

    foreach ($chart_data as $row) {
        $labels[] = date('M d', strtotime($row['date']));
        $revenue[] = floatval($row['revenue'] ?? 0);
        $orders[] = intval($row['orders'] ?? 0);
    }

    return [
        'labels' => $labels,
        'revenue' => $revenue,
        'orders' => $orders
    ];
}

/**
 * Prepare category data for JavaScript
 */
function prepareCategoryData($category_data) {
    $labels = [];
    $revenue = [];

    foreach ($category_data as $row) {
        $labels[] = $row['category'];
        $revenue[] = floatval($row['category_revenue'] ?? 0);
    }

    return [
        'labels' => $labels,
        'revenue' => $revenue
    ];
}

/**
 * Get default date range (last 30 days)
 */
function getDefaultDateRange() {
    return [
        'start_date' => date('Y-m-d', strtotime('-30 days')),
        'end_date' => date('Y-m-d')
    ];
}

/**
 * Validate date range
 */
function validateDateRange($start_date, $end_date) {
    if (empty($start_date) || empty($end_date)) {
        return false;
    }

    $start = strtotime($start_date);
    $end = strtotime($end_date);

    if ($start > $end) {
        return false;
    }

    // Max range: 1 year
    if (($end - $start) > (365 * 24 * 60 * 60)) {
        return false;
    }

    return true;
}
?>
