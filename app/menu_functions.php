<?php
require_once __DIR__ . '/app/db.php';

// Get all menu items (for customers)
function getAllMenuItems($pdo, $category = null, $available_only = true) {
    try {
        $sql = "SELECT * FROM menu_items";
        $conditions = [];
        $params = [];

        if ($available_only) {
            $conditions[] = "is_available = 1";
        }

        if ($category) {
            $conditions[] = "category = :category";
            $params[':category'] = $category;
        }

        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }

        $sql .= " ORDER BY category, name";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        error_log("Error fetching menu items: " . $e->getMessage());
        return [];
    }
}

// Get menu item by ID (for staff editing)
function getMenuItem($pdo, $id) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM menu_items WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        error_log("Error fetching menu item: " . $e->getMessage());
        return false;
    }
}

// Add new menu item (staff only)
function addMenuItem($pdo, $data) {
    try {
        $stmt = $pdo->prepare("
            INSERT INTO menu_items (name, description, price, category, image_url, is_available)
            VALUES (:name, :description, :price, :category, :image_url, :is_available)
        ");

        return $stmt->execute([
            ':name' => $data['name'],
            ':description' => $data['description'] ?? '',
            ':price' => $data['price'],
            ':category' => $data['category'],
            ':image_url' => $data['image_url'] ?? null,
            ':is_available' => $data['is_available'] ?? 1
        ]);
    } catch(PDOException $e) {
        error_log("Error adding menu item: " . $e->getMessage());
        return false;
    }
}

// Update menu item (staff only)
function updateMenuItem($pdo, $id, $data) {
    try {
        $stmt = $pdo->prepare("
            UPDATE menu_items
            SET name = :name,
                description = :description,
                price = :price,
                category = :category,
                image_url = :image_url,
                is_available = :is_available,
                updated_at = CURRENT_TIMESTAMP
            WHERE id = :id
        ");

        return $stmt->execute([
            ':id' => $id,
            ':name' => $data['name'],
            ':description' => $data['description'] ?? '',
            ':price' => $data['price'],
            ':category' => $data['category'],
            ':image_url' => $data['image_url'] ?? null,
            ':is_available' => $data['is_available'] ?? 1
        ]);
    } catch(PDOException $e) {
        error_log("Error updating menu item: " . $e->getMessage());
        return false;
    }
}

// Delete menu item (staff only)
function deleteMenuItem($pdo, $id) {
    try {
        $stmt = $pdo->prepare("DELETE FROM menu_items WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    } catch(PDOException $e) {
        error_log("Error deleting menu item: " . $e->getMessage());
        return false;
    }
}

// Get all unique categories
function getMenuCategories($pdo) {
    try {
        $stmt = $pdo->query("SELECT DISTINCT category FROM menu_items ORDER BY category");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    } catch(PDOException $e) {
        error_log("Error fetching categories: " . $e->getMessage());
        return [];
    }
}

// Format price
function formatPrice($price) {
    return 'RM' . number_format($price, 2);
}
?>
