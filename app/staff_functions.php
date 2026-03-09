<?php
// app/staff_functions.php

/**
 * Get all staff members
 */
function getAllStaff($pdo) {
    $sql = "SELECT u.*, r.role_name
    FROM users u
    LEFT JOIN roles r ON u.role_id = r.id
    WHERE r.role_name = 'Staff'
    ORDER BY u.id DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Get single staff by ID
 */
function getStaffById($pdo, $id) {
    $sql = "SELECT u.*, r.role_name
    FROM users u
    LEFT JOIN roles r ON u.role_id = r.id
    WHERE u.id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * Add new staff member
 */
function addStaff($pdo, $data) {
    try {
        $sql = "INSERT INTO users (
            username,
            email,
            password,
            role_id,
            email_verified,
            is_active,
            profile_picture
        ) VALUES (
            :username,
            :email,
            :password,
            :role_id,
            :email_verified,
            :is_active,
            :profile_picture
        )";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([
            ':username' => $data['username'],
            ':email' => $data['email'],
            ':password' => $data['password'],
            ':role_id' => 2, // Staff role_id
            ':email_verified' => $data['email_verified'] ?? 1,
            ':is_active' => $data['is_active'] ?? 1,
            ':profile_picture' => !empty($data['profile_picture']) ? $data['profile_picture'] : NULL
        ]);
    } catch (PDOException $e) {
        error_log("Error adding staff: " . $e->getMessage());
        return false;
    }
}

/**
 * Update staff information
 */
function updateStaff($pdo, $id, $data) {
    try {
        $sql = "UPDATE users SET
            username = :username,
            email = :email
        WHERE id = :id AND role_id = 2";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':username' => $data['username'],
            ':email' => $data['email']
        ]);
    } catch (PDOException $e) {
        error_log("Error updating staff: " . $e->getMessage());
        return false;
    }
}

/**
 * Ban/Suspend a staff member
 */
function banStaff($pdo, $id, $reason = '') {
    try {
        $sql = "UPDATE users SET
            is_active = 0,
            banned_at = NOW(),
            ban_reason = :reason
        WHERE id = :id AND role_id = 2";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':reason' => $reason
        ]);
    } catch (PDOException $e) {
        error_log("Error banning staff: " . $e->getMessage());
        return false;
    }
}

/**
 * Unban a staff member
 */
function unbanStaff($pdo, $id) {
    try {
        $sql = "UPDATE users SET
            is_active = 1,
            banned_at = NULL,
            ban_reason = NULL
        WHERE id = :id AND role_id = 2";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([':id' => $id]);
    } catch (PDOException $e) {
        error_log("Error unbanning staff: " . $e->getMessage());
        return false;
    }
}

/**
 * Delete staff and related data
 */
function deleteStaff($pdo, $id) {
    try {
        $pdo->beginTransaction();

        // Delete staff's cart items
        $stmt = $pdo->prepare("DELETE FROM carts WHERE user_id = :id");
        $stmt->execute([':id' => $id]);

        // Delete staff's feedback
        $stmt = $pdo->prepare("DELETE FROM feedbacks WHERE user_id = :id");
        $stmt->execute([':id' => $id]);

        // Delete staff's order status logs (through orders)
        $stmt = $pdo->prepare("DELETE osl FROM order_status_logs osl
            INNER JOIN orders o ON osl.order_id = o.id
            WHERE o.user_id = :id");
        $stmt->execute([':id' => $id]);

        // Delete staff's order items (through orders)
        $stmt = $pdo->prepare("DELETE oi FROM order_items oi
            INNER JOIN orders o ON oi.order_id = o.id
            WHERE o.user_id = :id");
        $stmt->execute([':id' => $id]);

        // Delete staff's orders
        $stmt = $pdo->prepare("DELETE FROM orders WHERE user_id = :id");
        $stmt->execute([':id' => $id]);

        // Finally delete the staff member
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id AND role_id = 2");
        $result = $stmt->execute([':id' => $id]);

        $pdo->commit();
        return $result;
    } catch (PDOException $e) {
        $pdo->rollBack();
        error_log("Error deleting staff: " . $e->getMessage());
        return false;
    }
}

/**
 * Reset staff password (sets to default 'password')
 */
function resetStaffPassword($pdo, $id) {
    $default_password = hash('sha256', 'password');
    $sql = "UPDATE users SET password = :password WHERE id = :id AND role_id = 2";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([
        ':id' => $id,
        ':password' => $default_password
    ]);
}

/**
 * Get banned staff count
 */
function getBannedStaffCount($pdo) {
    $sql = "SELECT COUNT(*) as count FROM users WHERE role_id = 2 AND is_active = 0";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['count'];
}

/**
 * Get active staff count
 */
function getActiveStaffCount($pdo) {
    $sql = "SELECT COUNT(*) as count FROM users WHERE role_id = 2 AND is_active = 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['count'];
}

/**
 * Check if email already exists
 */
function emailExists($pdo, $email, $exclude_id = null) {
    $sql = "SELECT COUNT(*) as count FROM users WHERE email = :email";
    if ($exclude_id) {
        $sql .= " AND id != :exclude_id";
    }
    $stmt = $pdo->prepare($sql);
    $params = [':email' => $email];
    if ($exclude_id) {
        $params[':exclude_id'] = $exclude_id;
    }
    $stmt->execute($params);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['count'] > 0;
}

/**
 * Check if username already exists
 */
function usernameExists($pdo, $username, $exclude_id = null) {
    $sql = "SELECT COUNT(*) as count FROM users WHERE username = :username";
    if ($exclude_id) {
        $sql .= " AND id != :exclude_id";
    }
    $stmt = $pdo->prepare($sql);
    $params = [':username' => $username];
    if ($exclude_id) {
        $params[':exclude_id'] = $exclude_id;
    }
    $stmt->execute($params);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['count'] > 0;
}
?>
