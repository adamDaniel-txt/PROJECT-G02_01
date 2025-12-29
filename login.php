<?php
// Customer login processing (placeholder)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // TEMPORARY (no database yet)
    if ($username == "customer" && $password == "1234") {
        echo "<script>alert('Customer login successful');</script>";
    } else {
        echo "<script>alert('Invalid customer login');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Customer Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>

<body>
<section class="container-fluid">
    <section class="row justify-content-center">
        <section class="col-12 col-sm-6 col-md-4">

            <form method="POST" class="form-container">
                <h4 class="text-center font-weight-bold">Customer Login</h4>

                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>

                <button type="submit" class="btn btn-primary btn-block">Login</button>
            </form>

            <div class="text-center mt-3">
                <a href="signup.php">Create an account</a>
            </div>

        </section>
    </section>
</section>
</body>
</html>