<?php
session_start();
require 'app/db.php';

$message = '';
$message_type = 'danger'; // Bootstrap alert type

if (isset($_GET['token']) && !empty($_GET['token'])) {
    $token = $_GET['token'];

    // Check if token exists and is not expired
    $stmt = $pdo->prepare('SELECT id, email, token_expires FROM users WHERE verification_token = :token AND email_verified = 0');
    $stmt->execute(['token' => $token]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Check if token is expired
        $currentTime = date('Y-m-d H:i:s');
        if ($currentTime > $user['token_expires']) {
            // Token expired - generate new one
            $newToken = bin2hex(random_bytes(32));
            $newExpires = date('Y-m-d H:i:s', strtotime('+24 hours'));

            $updateStmt = $pdo->prepare('UPDATE users SET verification_token = :new_token, token_expires = :new_expires WHERE id = :id');
            $updateStmt->execute([
                'new_token' => $newToken,
                'new_expires' => $newExpires,
                'id' => $user['id']
            ]);

            $message = 'Verification link has expired. A new verification email has been sent.';
            $message_type = 'warning';
        } else {
            // Token is valid - verify user
            $verifyStmt = $pdo->prepare('UPDATE users SET email_verified = 1, verification_token = NULL, token_expires = NULL WHERE id = :id');
            $verifyStmt->execute(['id' => $user['id']]);

            if ($verifyStmt->rowCount() > 0) {
                $message = 'Email verified successfully! You can now login to your account.';
                $message_type = 'success';
            } else {
                $message = 'Verification failed. Please try again.';
                $message_type = 'danger';
            }
        }
    } else {
        $message = 'Invalid or already used verification token.';
        $message_type = 'danger';
    }
} else {
    $message = 'No verification token provided.';
    $message_type = 'danger';
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
    <title>Email Verification - TigaBelas Cafe</title>
</head>
<body>
    <section class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-center">Email Verification</h3>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-<?php echo $message_type; ?> text-center">
                            <?php echo htmlspecialchars($message); ?>
                        </div>
                        <div class="text-center mt-3">
                            <a href="login.php" class="btn btn-primary">Go to Login</a>
                            <a href="index.php" class="btn btn-secondary">Back to Home</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</body>
</html>
