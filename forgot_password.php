<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require 'app/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);

    // Check if email exists
    $stmt = $pdo->prepare('SELECT id, username, email FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Generate reset token
        $reset_token = bin2hex(random_bytes(32));
        $token_expires = date('Y-m-d H:i:s', strtotime('+1 hour')); // Token valid for 1 hour

        // Store token in database
        $stmt = $pdo->prepare('UPDATE users SET verification_token = ?, token_expires = ? WHERE id = ?');
        $stmt->execute([$reset_token, $token_expires, $user['id']]);

        // Send reset email
        require_once 'app/mailer.php';

        if (sendPasswordResetEmail($user['email'], $user['username'], $reset_token)) {
            $_SESSION['flash'] = 'Password reset link has been sent to your email!';
            $_SESSION['flash_type'] = 'success';
        } else {
            $_SESSION['flash'] = 'Failed to send reset email. Please try again later.';
            $_SESSION['flash_type'] = 'error';
        }
    } else {
        $_SESSION['flash'] = 'If an account exists with that email, you will receive a reset link.';
        $_SESSION['flash_type'] = 'info';
    }

    header('Location: forgot_password.php');
    exit();
}

// Display flash messages
$flash = '';
$flash_type = '';
if (!empty($_SESSION['flash'])) {
    $flash = $_SESSION['flash'];
    $flash_type = $_SESSION['flash_type'];
    unset($_SESSION['flash']);
    unset($_SESSION['flash_type']);
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/main.css">
    <title>Forgot Password</title>
    <style>
        .flash-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .flash-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .flash-info {
            background-color: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }

        .flash {
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
                <?php if ($flash): ?>
                    <div class="flash flash-<?php echo $flash_type; ?>">
                        <?php echo htmlspecialchars($flash); ?>
                    </div>
                <?php endif; ?>

                <form class="form-container" method="POST" action="">
                    <div class="form-group">
                        <h4 class="text-center font-weight-bold">Reset Password</h4>
                        <p class="text-center">Enter your email address and we'll send you a link to reset your password.</p>

                        <label for="email">Email Address</label>
                        <input type="email" class="form-control" name="email" placeholder="Enter your email" required>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">Send Reset Link</button>
                    <div class="form-footer text-center mt-3">
                        <p><a href="login.php">Back to Login</a></p>
                    </div>
                </form>
            </section>
        </section>
    </section>
</body>
</html>
