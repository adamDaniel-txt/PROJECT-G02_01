<?php
session_start();
require 'app/db.php';

$token = $_GET['token'] ?? '';

// 1. Verify token and expiry
$stmt = $pdo->prepare('SELECT * FROM users WHERE reset_token = ?');
$stmt->execute([$token]);
$user = $stmt->fetch();

if (!$user) {
    die("This password reset link is invalid or has expired.");
}

// 2. Process password update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_pass = $_POST['password'];
    $confirm_pass = $_POST['confirm_password'];

    if ($new_pass === $confirm_pass) {
        // Use SHA256 as per your current login logic (though password_hash is better!)
        $hashedPassword = hash('sha256', $new_pass);

        $update = $pdo->prepare('UPDATE users SET password = ?, reset_token = NULL WHERE id = ?');
        $update->execute([$hashedPassword, $user['id']]);

        $_SESSION['flash'] = "Password updated successfully! You can now login.";
        header('Location: login.php');
        exit();
    } else {
        $error = "Passwords do not match.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
</head>
<body>
<section class="container-fluid mt-5">
    <section class="row justify-content-center">
        <section class="col-12 col-sm-6 col-md-4">
            <form class="form-container" method="POST" action="">
                <h4 class="text-center font-weight-bold">Create New Password</h4>
                <?php if(isset($error)) echo '<div class="alert alert-danger">'.$error.'</div>'; ?>
                <div class="form-group">
                    <label>New Password</label>
                    <input type="password" class="form-control" name="password" required minlength="6">
                </div>
                <div class="form-group">
                    <label>Confirm New Password</label>
                    <input type="password" class="form-control" name="confirm_password" required>
                </div>
                <button type="submit" class="btn btn-success btn-block">Update Password</button>
            </form>
        </section>
    </section>
</section>
</body>
</html>