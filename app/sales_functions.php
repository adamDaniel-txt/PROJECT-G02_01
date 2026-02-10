<?php
function getSalesData($pdo, $date_from, $date_to, $category = 'all') {
    $sql = "
        SELECT
            o.id as order_id,
            o.created_at as order_date,
            o.total_amount as order_total,
            o.status,
            u.username as customer_name,
            u.email as customer_email,
            mi.name as item_name,
            mi.description as item_description,
            mi.category,
            mi.price as unit_price,
            oi.quantity,
            (oi.price * oi.quantity) as total
        FROM orders o
        JOIN order_items oi ON o.id = oi.order_id
        JOIN menu_items mi ON oi.menu_item_id = mi.id
        LEFT JOIN users u ON o.user_id = u.id
        WHERE DATE(o.created_at) BETWEEN ? AND ?
    ";

    $params = [$date_from, $date_to];

    if ($category !== 'all') {
        $sql .= " AND mi.category = ?";
        $params[] = $category;
    }

    $sql .= " ORDER BY o.created_at DESC, o.id DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getSalesSummary($pdo, $date_from, $date_to) {
    // Get order statistics
    $sql_orders = "
        SELECT
            COUNT(DISTINCT o.id) as total_orders,
            COUNT(DISTINCT o.user_id) as unique_customers,
            SUM(o.total_amount) as total_sales,
            AVG(o.total_amount) as average_order_value
        FROM orders o
        WHERE DATE(o.created_at) BETWEEN ? AND ?
        AND o.status NOT IN ('cancelled')
    ";

    $stmt = $pdo->prepare($sql_orders);
    $stmt->execute([$date_from, $date_to]);
    $order_stats = $stmt->fetch(PDO::FETCH_ASSOC);

    // Get total items sold
    $sql_items = "
        SELECT SUM(oi.quantity) as total_items_sold
        FROM order_items oi
        JOIN orders o ON oi.order_id = o.id
        WHERE DATE(o.created_at) BETWEEN ? AND ?
        AND o.status NOT IN ('cancelled')
    ";

    $stmt = $pdo->prepare($sql_items);
    $stmt->execute([$date_from, $date_to]);
    $item_stats = $stmt->fetch(PDO::FETCH_ASSOC);

    // Calculate average items per order
    $avg_items_per_order = 0;
    if ($order_stats['total_orders'] > 0 && $item_stats['total_items_sold'] > 0) {
        $avg_items_per_order = $item_stats['total_items_sold'] / $order_stats['total_orders'];
    }

    return [
        'total_orders' => $order_stats['total_orders'] ?? 0,
        'unique_customers' => $order_stats['unique_customers'] ?? 0,
        'total_sales' => $order_stats['total_sales'] ?? 0,
        'average_order_value' => round($order_stats['average_order_value'] ?? 0, 2),
        'total_items_sold' => $item_stats['total_items_sold'] ?? 0,
        'avg_items_per_order' => round($avg_items_per_order, 1)
    ];
}

function getTopSellingItems($pdo, $date_from, $date_to, $limit = 10) {
    // Convert limit to integer
    $limit = (int)$limit;

    $sql = "
        SELECT
            mi.id,
            mi.name,
            mi.image_url,
            mi.category,
            mi.price,
            SUM(oi.quantity) as total_quantity,
            SUM(oi.price * oi.quantity) as total_revenue
        FROM order_items oi
        JOIN menu_items mi ON oi.menu_item_id = mi.id
        JOIN orders o ON oi.order_id = o.id
        WHERE DATE(o.created_at) BETWEEN ? AND ?
        AND o.status NOT IN ('cancelled')
        GROUP BY mi.id, mi.name, mi.category
        ORDER BY total_quantity DESC
        LIMIT " . $limit . "
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$date_from, $date_to]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getHourlySales($pdo, $date_from, $date_to) {
    $sql = "
        SELECT
            HOUR(o.created_at) as hour,
            SUM(o.total_amount) as total_sales,
            COUNT(o.id) as order_count
        FROM orders o
        WHERE DATE(o.created_at) BETWEEN ? AND ?
        AND o.status NOT IN ('cancelled')
        GROUP BY HOUR(o.created_at)
        ORDER BY hour
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$date_from, $date_to]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fill in missing hours with zero values
    $hourly_data = [];
    for ($hour = 8; $hour <= 22; $hour++) { // Cafe hours: 8 AM to 10 PM
        $found = false;
        foreach ($results as $row) {
            if ($row['hour'] == $hour) {
                $hourly_data[] = [
                    'hour' => sprintf('%02d:00', $hour),
                    'total_sales' => $row['total_sales'] ?? 0,
                    'order_count' => $row['order_count'] ?? 0
                ];
                $found = true;
                break;
            }
        }
        if (!$found) {
            $hourly_data[] = [
                'hour' => sprintf('%02d:00', $hour),
                'total_sales' => 0,
                'order_count' => 0
            ];
        }
    }

    return $hourly_data;
}

function getSalesByCategory($pdo, $date_from, $date_to) {
    $sql = "
        SELECT
            mi.category,
            SUM(oi.quantity) as total_quantity,
            SUM(oi.price * oi.quantity) as total_sales,
            COUNT(DISTINCT o.id) as order_count
        FROM order_items oi
        JOIN menu_items mi ON oi.menu_item_id = mi.id
        JOIN orders o ON oi.order_id = o.id
        WHERE DATE(o.created_at) BETWEEN ? AND ?
        AND o.status NOT IN ('cancelled')
        GROUP BY mi.category
        ORDER BY total_sales DESC
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$date_from, $date_to]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getDailySales($pdo, $date_from, $date_to) {
    // First, get order-level daily statistics
    $sql = "
        SELECT
            DATE(o.created_at) as date,
            SUM(o.total_amount) as total_sales,
            COUNT(DISTINCT o.id) as order_count
        FROM orders o
        WHERE DATE(o.created_at) BETWEEN ? AND ?
        AND o.status NOT IN ('cancelled')
        GROUP BY DATE(o.created_at)
        ORDER BY date
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$date_from, $date_to]);
    $order_results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Second, get item counts per day
    $sql_items = "
        SELECT
            DATE(o.created_at) as date,
            SUM(oi.quantity) as item_count
        FROM orders o
        JOIN order_items oi ON o.id = oi.order_id
        WHERE DATE(o.created_at) BETWEEN ? AND ?
        AND o.status NOT IN ('cancelled')
        GROUP BY DATE(o.created_at)
    ";

    $stmt = $pdo->prepare($sql_items);
    $stmt->execute([$date_from, $date_to]);
    $item_results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Create an associative array for item counts
    $item_counts = [];
    foreach ($item_results as $item) {
        $item_counts[$item['date']] = $item['item_count'];
    }

    // Fill in missing dates with zero values
    $daily_data = [];
    $current = strtotime($date_from);
    $end = strtotime($date_to);

    while ($current <= $end) {
        $date = date('Y-m-d', $current);
        $found = false;

        foreach ($order_results as $row) {
            if ($row['date'] == $date) {
                $daily_data[] = [
                    'date' => date('M j', strtotime($date)),
                    'full_date' => $date,
                    'total_sales' => $row['total_sales'] ?? 0,
                    'order_count' => $row['order_count'] ?? 0,
                    'item_count' => $item_counts[$date] ?? 0
                ];
                $found = true;
                break;
            }
        }

        if (!$found) {
            $daily_data[] = [
                'date' => date('M j', $current),
                'full_date' => $date,
                'total_sales' => 0,
                'order_count' => 0,
                'item_count' => 0
            ];
        }

        $current = strtotime('+1 day', $current);
    }

    return $daily_data;
}

function exportSalesToCSV($sales_data, $date_from, $date_to) {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="sales_report_' . $date_from . '_to_' . $date_to . '.csv"');

    $output = fopen('php://output', 'w');

    // Add BOM for Excel compatibility
    fwrite($output, "\xEF\xBB\xBF");

    // Header row
    fputcsv($output, [
        'Order ID', 'Order Date', 'Customer Name', 'Customer Email',
        'Item Name', 'Category', 'Quantity', 'Unit Price', 'Total',
        'Order Status', 'Order Total'
    ]);

    // Data rows
    foreach ($sales_data as $sale) {
        fputcsv($output, [
            'KTB-' . str_pad($sale['order_id'], 6, '0', STR_PAD_LEFT),
            $sale['order_date'],
            $sale['customer_name'] ?? 'Guest',
            $sale['customer_email'] ?? '',
            $sale['item_name'],
            $sale['category'],
            $sale['quantity'],
            number_format($sale['unit_price'], 2),
            number_format($sale['total'], 2),
            ucfirst($sale['status']),
            number_format($sale['order_total'], 2)
        ]);
    }

    fclose($output);
    exit();
}

function exportSalesToExcel($sales_data, $sales_summary, $date_from, $date_to) {
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="sales_report_' . $date_from . '_to_' . $date_to . '.xls"');

    echo '<html><head>';
    echo '<meta charset="UTF-8">';
    echo '<style>';
    echo 'td, th { border: 1px solid #ddd; padding: 8px; }';
    echo 'th { background-color: #f2f2f2; }';
    echo '.summary { background-color: #e8f4ff; }';
    echo '.total { background-color: #d4edda; font-weight: bold; }';
    echo '</style>';
    echo '</head><body>';

    echo '<h2>Kafe Tiga Belas - Sales Report</h2>';
    echo '<p>Period: ' . date('F j, Y', strtotime($date_from)) . ' to ' . date('F j, Y', strtotime($date_to)) . '</p>';
    echo '<p>Generated: ' . date('F j, Y g:i A') . '</p>';

    // Summary section
    echo '<h3>Sales Summary</h3>';
    echo '<table>';
    echo '<tr class="summary"><th>Metric</th><th>Value</th></tr>';
    echo '<tr><td>Total Sales</td><td>RM ' . number_format($sales_summary['total_sales'], 2) . '</td></tr>';
    echo '<tr><td>Total Orders</td><td>' . $sales_summary['total_orders'] . '</td></tr>';
    echo '<tr><td>Unique Customers</td><td>' . $sales_summary['unique_customers'] . '</td></tr>';
    echo '<tr><td>Average Order Value</td><td>RM ' . number_format($sales_summary['average_order_value'], 2) . '</td></tr>';
    echo '<tr><td>Total Items Sold</td><td>' . $sales_summary['total_items_sold'] . '</td></tr>';
    echo '<tr><td>Average Items per Order</td><td>' . $sales_summary['avg_items_per_order'] . '</td></tr>';
    echo '</table><br>';

    // Detailed sales data
    echo '<h3>Detailed Sales Data</h3>';
    echo '<table>';
    echo '<tr><th>Order ID</th><th>Order Date</th><th>Customer</th><th>Item</th>';
    echo '<th>Category</th><th>Qty</th><th>Unit Price</th><th>Total</th><th>Status</th></tr>';

    $grand_total = 0;
    $grand_items = 0;

    foreach ($sales_data as $sale) {
        echo '<tr>';
        echo '<td>KTB-' . str_pad($sale['order_id'], 6, '0', STR_PAD_LEFT) . '</td>';
        echo '<td>' . $sale['order_date'] . '</td>';
        echo '<td>' . htmlspecialchars($sale['customer_name'] ?? 'Guest') . '</td>';
        echo '<td>' . htmlspecialchars($sale['item_name']) . '</td>';
        echo '<td>' . $sale['category'] . '</td>';
        echo '<td>' . $sale['quantity'] . '</td>';
        echo '<td>RM ' . number_format($sale['unit_price'], 2) . '</td>';
        echo '<td>RM ' . number_format($sale['total'], 2) . '</td>';
        echo '<td>' . ucfirst($sale['status']) . '</td>';
        echo '</tr>';

        $grand_total += $sale['total'];
        $grand_items += $sale['quantity'];
    }

    // Grand total row
    echo '<tr class="total">';
    echo '<td colspan="5"><strong>GRAND TOTAL</strong></td>';
    echo '<td><strong>' . $grand_items . '</strong></td>';
    echo '<td></td>';
    echo '<td><strong>RM ' . number_format($grand_total, 2) . '</strong></td>';
    echo '<td></td>';
    echo '</tr>';

    echo '</table>';
    echo '</body></html>';
    exit();
}
?>
