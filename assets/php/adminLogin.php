<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($_POST['adminUser'] == "admin" && $_POST['password'] == "admin123") {
        echo "<script>alert('Admin login successful');</script>";
    } else {
        echo "<script>alert('Invalid admin credentials');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Admin Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>

<body>
<section class="container-fluid">
    <section class="row justify-content-center">
        <section class="col-12 col-sm-6 col-md-4">

            <form method="POST" class="form-container">
                <h4 class="text-center font-weight-bold">Admin Login</h4>

                <div class="form-group">
                    <label>Admin Username</label>
                    <input type="text" name="adminUser" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>

                <button type="submit" class="btn btn-danger btn-block">Login</button>
            </form>

        </section>
    </section>
</section>
</body>
</html>