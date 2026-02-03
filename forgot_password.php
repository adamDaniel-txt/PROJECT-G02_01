<?php
session_start();
require 'app/db.php';

// Import PHPMailer classes into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

// Adjust this path based on where your vendor folder is
require 'assets/vendor/autoload.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Capture the email from the form
    $email = $_POST['email']; 

    // 2. Check if the user exists in the database
    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        // 3. Generate a secure token
        $token = bin2hex(random_bytes(32));

        $resetLink = BASE_URL . '/reset_password.php?token=' . $token;

        // 4. Update the database (token only, no expiry)
        $stmt = $pdo->prepare('UPDATE users SET reset_token = ? WHERE email = ?');
        $stmt->execute([$token, $email]);

        // 5. Send the Email using PHPMailer
        $mail = new PHPMailer(true);

        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host       = SMTP_HOST;
            $mail->SMTPAuth   = true;
            $mail->Username   = SMTP_USERNAME;
            $mail->Password   = SMTP_PASSWORD;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            // Recipients
            $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
            $mail->addAddress($email, 'User');

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Request';
            $mail->Body    = "Hello,<br><br>Click the link below to reset your password:<br><br><a href='$resetLink'>$resetLink</a>";

            $mail->send();
            $_SESSION['flash'] = "Check your email for the reset link!";
        } catch (Exception $e) {
            $_SESSION['flash'] = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        // Privacy: don't reveal if email exists or not
        $_SESSION['flash'] = "If that email is registered, a reset link has been sent.";
    }
    
    header('Location: login.php');
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
        .form-container {
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px 0px #000;
            margin-top: 10vh;
        }
    </style>
</head>
<body>
    <section class="container-fluid">
        <section class="row justify-content-center">
            <section class="col-12 col-sm-6 col-md-4">
                <form class="form-container" method="POST" action="">
                    <h4 class="text-center font-weight-bold">Forgot Password</h4>
                    <p class="text-center text-muted">Enter your email to receive a reset link.</p>
                    
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" class="form-control" name="email" placeholder="Enter email" required>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">Send Reset Link</button>
                    
                    <div class="form-footer text-center mt-3">
                        <a href="login.php">Back to Login</a>
                    </div>
                </form>
            </section>
        </section>
    </section>
</body>
</html>