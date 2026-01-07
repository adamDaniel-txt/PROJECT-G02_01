<?php
session_start();
if (!empty($_SESSION['flash'])) {
    echo '<div class="flash">'.htmlspecialchars($_SESSION['flash']).'</div>';
    unset($_SESSION['flash']);
}

require 'app/db.php';
require 'app/persmission.php';

// Simple helper to redirect after successful registration
function redirect_to_login() {
    header('Location: login.php');
    exit();
}

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
        header('Location: signup.php'); // or the form page
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

    // Hash password with SHA-256 to match the style in your login code.
    // Note: using password_hash()/password_verify() is recommended for new apps.
    $password_hash = hash('sha256', $password);

    // Default role_id (adjust as needed)
    $default_role_id = 3; // 3 for customer

    // Insert new user
    $insert = $pdo->prepare('INSERT INTO users (username, email, password, role_id) VALUES (:username, :email, :password, :role_id)');
    $success = $insert->execute([
        'username' => $username,
        'email' => $email,
        'password' => $password_hash,
        'role_id'  => $default_role_id
    ]);

    if ($success) {
        // After successful registration, redirect to login page
        redirect_to_login();
    } else {
        $_SESSION['flash'] = 'Registration failed. Please try again.';
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
