<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
if (!empty($_SESSION['flash'])) {
    echo '<div class="flash">'.htmlspecialchars($_SESSION['flash']).'</div>';
    unset($_SESSION['flash']);
}

require 'app/db.php';
require 'app/config.php';
require 'app/mailer.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Basic sanitization/trim
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $email    = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $confirm  = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';

    // Minimal validation (reasonable defaults)
    if ($username === '' || $email === '' || $password === '') {
        $_SESSION['flash'] = 'Username, email and password are required.';
        header('Location: signup.php');
        exit();
    }

    // Add email validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['flash'] = 'Please enter a valid email address.';
        header('Location: signup.php');
        exit();
    }

    if ($password !== $confirm) {
        $_SESSION['flash'] = 'Passwords do not match.';
        header('Location: signup.php');
        exit();
    }

    if (strlen($password) < 8) {
        $_SESSION['flash'] = 'Password must be at least 8 characters.';
        header('Location: signup.php');
        exit();
    }

    // Check if username already exists
    $stmt = $pdo->prepare('SELECT id FROM users WHERE username = :username');
    $stmt->execute(['username' => $username]);
    if ($stmt->fetch(PDO::FETCH_ASSOC)) {
        $_SESSION['flash'] = 'Username already taken.';
        header('Location: signup.php');
        exit();
    }

    // Check if email already exists
    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = :email');
    $stmt->execute(['email' => $email]);
    if ($stmt->fetch(PDO::FETCH_ASSOC)) {
        $_SESSION['flash'] = 'Email already registered.';
        header('Location: signup.php');
        exit();
    }

    // Hash password with SHA-256
    $password_hash = hash('sha256', $password);

    // Generate verification token and expiration time
    $verificationToken = bin2hex(random_bytes(32)); // 64 characters
    $tokenExpires = date('Y-m-d H:i:s', strtotime('+' . TOKEN_EXPIRY_HOURS . ' hours'));

    // Default role_id (3 for customer)
    $default_role_id = 3;

    // Insert new user with verification data
    $insert = $pdo->prepare('INSERT INTO users (username, email, password, role_id, email_verified, verification_token, token_expires)
                             VALUES (:username, :email, :password, :role_id, :email_verified, :verification_token, :token_expires)');

    $success = $insert->execute([
        'username' => $username,
        'email' => $email,
        'password' => $password_hash,
        'role_id'  => $default_role_id,
        'email_verified' => 0,
        'verification_token' => $verificationToken,
        'token_expires' => $tokenExpires
    ]);

    if ($success) {
        // Send verification email
        $emailSent = sendVerificationEmail($email, $username, $verificationToken);

        if ($emailSent) {
            $_SESSION['flash'] = 'Registration successful! Please check your email to verify your account.';
            $_SESSION['flash_type'] = 'success';
            header('Location: login.php');
        } else {
            $_SESSION['flash'] = 'Registration successful but verification email could not be sent. Please contact support.';
            $_SESSION['flash_type'] = 'warning';
            header('Location: login.php');
        }
    } else {
        $_SESSION['flash'] = 'Registration failed. Please try again.';
        $_SESSION['flash_type'] = 'error';
        header('Location: signup.php');
    }
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/main.css">

    <style>
    @keyframes fadeIn {
    from { opacity: 0; transform: translateY(-20px); }
    to { opacity: 1; transform: translateY(0); }
    }

    .flash {
        color: #cda45e;
        animation: fadeIn 0.5s ease-out;
        text-align: center;
    }
    </style>
</head>

<body>
<section class="container-fluid">
    <section class="row justify-content-center">
        <section class="col-12 col-sm-6 col-md-4">
            <form class="form-container" method="post" action="">
                <h4 class="text-center font-weight-bold">Sign Up</h4>

                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" class="form-control" name="username" placeholder="Enter username" required>
                </div>

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" class="form-control" name="email" placeholder="Enter email" required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" class="form-control" name="password" placeholder="Password" required>
                </div>

                <div class="form-group">
                    <label for="confirmPassword">Confirm Password</label>
                    <input type="password" class="form-control" name="confirm_password" placeholder="Confirm Password" required>
                </div>

                <button type="submit" class="btn btn-primary btn-block">Sign Up</button>

                <div class="form-footer text-center mt-3">
                    <p>Already have an account? <a href="login.php">Login</a></p>
                </div>
            </form>
        </section>
    </section>
</section>
</body>
</html>
