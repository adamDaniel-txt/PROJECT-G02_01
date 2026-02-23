<?php
// app/customer_functions.php

/**
 * Get all customers with their order statistics
 */
function getAllCustomers($pdo) {
    $sql = "SELECT u.*, r.role_name,
            (SELECT COUNT(*) FROM orders WHERE user_id = u.id) as total_orders,
            (SELECT SUM(total_amount) FROM orders WHERE user_id = u.id) as total_spent,
            (SELECT MAX(created_at) FROM orders WHERE user_id = u.id) as last_order_date
            FROM users u
            LEFT JOIN roles r ON u.role_id = r.id
            WHERE r.role_name = 'Customer'
            ORDER BY u.id DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Get single customer by ID
 */
function getCustomerById($pdo, $id) {
    $sql = "SELECT u.*, r.role_name
            FROM users u
            LEFT JOIN roles r ON u.role_id = r.id
            WHERE u.id = :id";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * Update customer information (without role)
 */
function updateCustomer($pdo, $id, $data) {
    try {
        $sql = "UPDATE users SET
                username = :username,
                email = :email
                WHERE id = :id";

        $stmt = $pdo->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':username' => $data['username'],
            ':email' => $data['email']
        ]);
    } catch (PDOException $e) {
        error_log("Error updating customer: " . $e->getMessage());
        return false;
    }
}

/**
 * Ban/Suspend a customer
 */
function banCustomer($pdo, $id, $reason = '') {
    try {
        $sql = "UPDATE users SET
                is_active = 0,
                banned_at = NOW(),
                ban_reason = :reason
                WHERE id = :id AND role_id = 3";

        $stmt = $pdo->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':reason' => $reason
        ]);
    } catch (PDOException $e) {
        error_log("Error banning customer: " . $e->getMessage());
        return false;
    }
}

/**
 * Unban a customer
 */
function unbanCustomer($pdo, $id) {
    try {
        $sql = "UPDATE users SET
                is_active = 1,
                banned_at = NULL,
                ban_reason = NULL
                WHERE id = :id AND role_id = 3";

        $stmt = $pdo->prepare($sql);
        return $stmt->execute([':id' => $id]);
    } catch (PDOException $e) {
        error_log("Error unbanning customer: " . $e->getMessage());
        return false;
    }
}

/**
 * Check if customer is banned
 */
function isCustomerBanned($pdo, $id) {
    $sql = "SELECT is_active FROM users WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result ? $result['is_active'] == 0 : false;
}

/**
 * Delete customer and related data
 */
function deleteCustomer($pdo, $id) {
    try {
        // Start transaction
        $pdo->beginTransaction();

        // Delete customer's cart items
        $stmt = $pdo->prepare("DELETE FROM carts WHERE user_id = :id");
        $stmt->execute([':id' => $id]);

        // Delete customer's feedback
        $stmt = $pdo->prepare("DELETE FROM feedbacks WHERE user_id = :id");
        $stmt->execute([':id' => $id]);

        // Delete customer's order status logs (through orders)
        $stmt = $pdo->prepare("DELETE osl FROM order_status_logs osl
                               INNER JOIN orders o ON osl.order_id = o.id
                               WHERE o.user_id = :id");
        $stmt->execute([':id' => $id]);

        // Delete customer's order items (through orders)
        $stmt = $pdo->prepare("DELETE oi FROM order_items oi
                               INNER JOIN orders o ON oi.order_id = o.id
                               WHERE o.user_id = :id");
        $stmt->execute([':id' => $id]);

        // Delete customer's orders
        $stmt = $pdo->prepare("DELETE FROM orders WHERE user_id = :id");
        $stmt->execute([':id' => $id]);

        // Finally delete the customer
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id AND role_id = 3");
        $result = $stmt->execute([':id' => $id]);

        // Commit transaction
        $pdo->commit();
        return $result;

    } catch (PDOException $e) {
        // Rollback transaction on error
        $pdo->rollBack();
        error_log("Error deleting customer: " . $e->getMessage());
        return false;
    }
}

/**
 * Get customer order history
 */
function getCustomerOrders($pdo, $customer_id) {
    $sql = "SELECT o.*, COUNT(oi.id) as item_count
            FROM orders o
            LEFT JOIN order_items oi ON o.id = oi.order_id
            WHERE o.user_id = :user_id
            GROUP BY o.id
            ORDER BY o.created_at DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([':user_id' => $customer_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Reset customer password (sets to default 'password')
 */
function resetCustomerPassword($pdo, $id) {
    // Default password 'password' hashed
    $default_password = hash('sha256', 'password');

    $sql = "UPDATE users SET password = :password WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([
        ':id' => $id,
        ':password' => $default_password
    ]);
}

/**
 * Get banned customers count
 */
function getBannedCustomersCount($pdo) {
    $sql = "SELECT COUNT(*) as count FROM users WHERE role_id = 3 AND is_active = 0";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['count'];
}

/**
 * Get active customers count
 */
function getActiveCustomersCount($pdo) {
    $sql = "SELECT COUNT(*) as count FROM users WHERE role_id = 3 AND is_active = 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['count'];
}
?>
