<?php
// Demo role
$role = 'admin';
$username = 'demo_admin';

// Dummy data for metrics
$totalOrders = 25;
$pendingOrders = 5;
$totalUsers = 12;

// Dummy orders
$orders = [
    ['id' => 1, 'user_id' => 'cust1', 'status' => 'pending'],
    ['id' => 2, 'user_id' => 'cust2', 'status' => 'completed'],
    ['id' => 3, 'user_id' => 'cust3', 'status' => 'preparing'],
];

// Dummy menu (for admin)
$menuItems = [
    ['name' => 'Coffee', 'price' => 5.00],
    ['name' => 'Sandwich', 'price' => 7.50],
];

// Dummy users
$users = [
    ['id' => 1, 'username' => 'cust1', 'role' => 'customer'],
    ['id' => 2, 'username' => 'cust2', 'role' => 'customer'],
];
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo ucfirst($role); ?> Dashboard (Demo)</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-dark px-4">
    <span class="navbar-brand"><?php echo strtoupper($role); ?> DASHBOARD (Demo)</span>
</nav>

<div class="container mt-4">

    <!-- Metrics -->
    <div class="row mb-4">
        <div class="col-md-4"><div class="card p-3">Total Orders: <?php echo $totalOrders; ?></div></div>
        <div class="col-md-4"><div class="card p-3">Pending Orders: <?php echo $pendingOrders; ?></div></div>
        <div class="col-md-4"><div class="card p-3">Customers: <?php echo $totalUsers; ?></div></div>
    </div>

    <!-- Orders Table -->
    <h4>Orders</h4>
    <table class="table table-bordered">
        <tr><th>ID</th><th>User</th><th>Status</th></tr>
        <?php foreach ($orders as $o): ?>
            <tr>
                <td><?php echo $o['id']; ?></td>
                <td><?php echo $o['user_id']; ?></td>
                <td><?php echo $o['status']; ?></td>
            </tr>
        <?php endforeach; ?>
    </table>

    <!-- Admin-only menu/user management -->
    <?php if ($role === 'admin'): ?>
        <h4>Menu Management</h4>
        <table class="table table-bordered">
            <tr><th>Name</th><th>Price</th></tr>
            <?php foreach ($menuItems as $item): ?>
                <tr>
                    <td><?php echo $item['name']; ?></td>
                    <td><?php echo $item['price']; ?></td>
                </tr>
            <?php endforeach; ?>
        </table>

        <h4>User Management</h4>
        <table class="table table-bordered">
            <tr><th>ID</th><th>Username</th><th>Role</th></tr>
            <?php foreach ($users as $u): ?>
                <tr>
                    <td><?php echo $u['id']; ?></td>
                    <td><?php echo $u['username']; ?></td>
                    <td><?php echo $u['role']; ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>

</div>
</body>
</html>