<?php
session_start();
require 'app/db.php';

$error = '';
$success = '';
$token = $_GET['token'] ?? '';

// Validate token
if ($token) {
    $stmt = $pdo->prepare('SELECT id, token_expires FROM users WHERE verification_token = ?');
    $stmt->execute([$token]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        $error = 'Invalid or expired reset link.';
    } elseif (strtotime($user['token_expires']) < time()) {
        $error = 'Reset link has expired. Please request a new one.';
    }
} else {
    $error = 'No reset token provided.';
}

// Process password reset
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$error) {
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate passwords
    if (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long.';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match.';
    } else {
        // Hash new password
        $hashed_password = hash('sha256', $password);

        // Update password and clear reset token
        $stmt = $pdo->prepare('UPDATE users SET password = ?, verification_token = NULL, token_expires = NULL WHERE id = ?');
        $stmt->execute([$hashed_password, $user['id']]);

        $success = 'Password has been reset successfully! You can now <a href="login.php">login</a> with your new password.';
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/main.css">
    <title>Reset Password</title>
    <style>
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            padding: 12px 20px;
            border-radius: 8px;
            margin: 15px auto;
            max-width: 400px;
            text-align: center;
            font-weight: 600;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            padding: 12px 20px;
            border-radius: 8px;
            margin: 15px auto;
            max-width: 400px;
            text-align: center;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <section class="container-fluid">
        <section class="row justify-content-center">
            <section class="col-12 col-sm-6 col-md-4">
                <?php if ($error): ?>
                    <div class="error">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php elseif ($success): ?>
                    <div class="success">
                        <?php echo $success; ?>
                    </div>
                <?php else: ?>
                    <form class="form-container" method="POST" action="">
                        <div class="form-group">
                            <h4 class="text-center font-weight-bold">Set New Password</h4>
                            <p class="text-center">Please enter your new password below.</p>

                            <label for="password">New Password</label>
                            <input type="password" class="form-control" name="password" placeholder="Enter new password" minlength="6" required>
                            <small class="form-text text-muted">Password must be at least 6 characters long.</small>

                            <label for="confirm_password" class="mt-3">Confirm New Password</label>
                            <input type="password" class="form-control" name="confirm_password" placeholder="Confirm new password" required>
                        </div>

                        <button type="submit" class="btn btn-primary btn-block">Reset Password</button>
                        <div class="form-footer text-center mt-3">
                            <p><a href="login.php">Back to Login</a></p>
                        </div>
                    </form>
                <?php endif; ?>
            </section>
        </section>
    </section>
</body>
</html>
