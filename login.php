<?php
session_start();
if (!empty($_SESSION['flash'])) {
    echo '<div class="flash">'.htmlspecialchars($_SESSION['flash']).'</div>';
    unset($_SESSION['flash']);
}

require 'app/db.php';
require 'app/persmission.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare('SELECT * FROM users WHERE username = :username');
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && hash('sha256', $password) === $user['password']) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role_id'] = $user['role_id'];
        header('Location: index.php');
        exit();
    } else {
        echo " test";
        $_SESSION['flash'] = 'Invalid credentials!';
        header('Location: login.php');
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
<!--Login form starts-->
  <section class="container-fluid">
  <!--row justify-content-center is used for centering the login form-->
    <section class="row justify-content-center">
    <!--Making the form responsive-->
      <section class="col-12 col-sm-6 col-md-4">
        <form class="form-container" method="POST" action="">

        <div class="form-group">
          <h4 class="text-center font-weight-bold"> Login </h4>
          <label for="username">Username</label>
           <input type="text" class="form-control" name="username" placeholder="Enter username" required>
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
