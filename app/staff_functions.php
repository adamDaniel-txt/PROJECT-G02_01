<?php
// app/staff_functions.php

/**
 * Get all staff and admin users
 */
function getAllStaff($pdo) {
    $sql = "SELECT u.*, r.role_name
            FROM users u
            LEFT JOIN roles r ON u.role_id = r.id
            WHERE u.role_id = 2
            ORDER BY u.id DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Get single staff/admin by ID
 */
function getStaffById($pdo, $id) {
    $sql = "SELECT u.*, r.role_name
            FROM users u
            LEFT JOIN roles r ON u.role_id = r.id
            WHERE u.id = :id
            AND r.role_name IN ('Admin', 'Staff')";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * Add new staff/admin
 */
function addStaff($pdo, $data) {
    try {
        $sql = "INSERT INTO users 
                (username, email, password, role_id, is_active, created_at)
                VALUES 
                (:username, :email, :password, :role_id, :is_active, NOW())";

        // IMPORTANT: Use proper password hashing
        $hashed_password = hash('sha256', $data['password']);

        $stmt = $pdo->prepare($sql);
        return $stmt->execute([
            ':username'   => $data['username'],
            ':email'      => $data['email'],
            ':password'   => $hashed_password,
            ':role_id'    => $data['role_id'], // 1 = Admin, 2 = Staff
            ':is_active'  => $data['is_active']
        ]);

    } catch (PDOException $e) {
        error_log("Error adding staff: " . $e->getMessage());
        return false;
    }
}

/**
 * Update staff/admin info (excluding password)
 */
function updateStaff($pdo, $id, $data) {
    try {
        $sql = "UPDATE users SET
                username = :username,
                email = :email,
                role_id = :role_id,
                is_active = :is_active
                WHERE id = :id
                AND role_id = 2";

        $stmt = $pdo->prepare($sql);
        return $stmt->execute([
            ':id'         => $id,
            ':username'   => $data['username'],
            ':email'      => $data['email'],
            ':role_id'    => $data['role_id'],
            ':is_active'  => $data['is_active']
        ]);

    } catch (PDOException $e) {
        error_log("Error updating staff: " . $e->getMessage());
        return false;
    }
}

/**
 * Reset staff/admin password (default: 'password')
 */
function resetStaffPassword($pdo, $id) {
    $default_password = hash('sha256', 'password');

    $sql = "UPDATE users 
            SET password = :password
            WHERE id = :id
            AND role_id = 2";

    $stmt = $pdo->prepare($sql);
    return $stmt->execute([
        ':id' => $id,
        ':password' => $default_password
    ]);
}

/**
 * Deactivate (soft delete) staff/admin
 */
function deactivateStaff($pdo, $id) {
    try {
        $sql = "UPDATE users SET
                is_active = 0
                WHERE id = :id
                AND role_id = 2";

        $stmt = $pdo->prepare($sql);
        return $stmt->execute([':id' => $id]);

    } catch (PDOException $e) {
        error_log("Error deactivating staff: " . $e->getMessage());
        return false;
    }
}

/**
 * Activate staff/admin
 */
function activateStaff($pdo, $id) {
    try {
        $sql = "UPDATE users SET
                is_active = 1
                WHERE id = :id
                AND role_id = 2";

        $stmt = $pdo->prepare($sql);
        return $stmt->execute([':id' => $id]);

    } catch (PDOException $e) {
        error_log("Error activating staff: " . $e->getMessage());
        return false;
    }
}

/**
 * Delete staff/admin
 * (Prevents deleting yourself)
 */
function deleteStaff($pdo, $id, $reason) {
    try {

        if ($id == $_SESSION['user_id']) {
            return false;
        }

        // Optional: log delete reason before deleting
        $logSql = "INSERT INTO order_status_logs (order_id, status, notes)
                   VALUES (NULL, 'staff_deleted', :reason)";
        // You can create a proper staff_logs table later

        $sql = "DELETE FROM users
                WHERE id = :id
                AND role_id = 2";

        $stmt = $pdo->prepare($sql);

        return $stmt->execute([':id' => $id]);

    } catch (PDOException $e) {
        error_log("Error deleting staff: " . $e->getMessage());
        return false;
    }
}

/**
 * Get total staff count (excluding customers)
 */
function getStaffCount($pdo) {
    $sql = "SELECT COUNT(*) as count
            FROM users
            WHERE role_id = 2";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['count'];
}

/**
 * Get active staff count
 */
function getActiveStaffCount($pdo) {
    $sql = "SELECT COUNT(*) as count
            FROM users
            WHERE role_id = 2
            AND is_active = 1";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['count'];
}

function getInactiveStaffCount($pdo) {
    $sql = "SELECT COUNT(*) as count
            FROM users
            WHERE role_id = 2
            AND is_active = 0";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['count'];
}
?>