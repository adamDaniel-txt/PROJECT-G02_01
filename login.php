<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

require 'app/db.php';
require 'app/flash.php';

echo flash();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Query to allow login with username or email
    $stmt = $pdo->prepare('SELECT * FROM users WHERE username = :identifier OR email = :identifier');
    $stmt->execute(['identifier' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && hash('sha256', $password) === $user['password']) {
        // Email verification
        if (!$user['email_verified']) {
            flash('Please verify your email before logging in. Check your email for the verification link.', 'warning');
            header('Location: login.php');
            exit();
        }

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role_id'] = $user['role_id'];

        flash('Login successful! Redirecting...', 'success');
        header('Location: index.php');
        exit();
    } else {
        flash('Invalid username/email or password!', 'danger');
        header('Location: login.php');
        exit();
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

</head>

<body>
<!--Login form starts-->
  <section class="container-fluid">
  <!--row justify-content-center is used for centering the login form-->
    <section class="row justify-content-center">
    <!--Making the form responsive-->
      <section class="col-12 col-sm-6 col-md-4">
        <form class="form-container" method="POST" action="">

        <div class="form-group">
          <h4 class="text-center font-weight-bold"> Login </h4>
            <label for="username">Username or Email</label>
            <input type="text" class="form-control" name="username" placeholder="Enter username or email" required>
        </div>

        <div class="form-group">
          <label for="password">Password</label>
          <input type="password" class="form-control" name="password" placeholder="Password" required>
        </div>

        <button type="submit" class="btn btn-primary btn-block">Sign in</button>
        <div class="form-footer text-center mt-3">
          <p> Don't have an account? <a href="signup.php">Sign Up</a></p>
        </div>
        </form>
      </section>
    </section>
  </section>
</body>
</html>
