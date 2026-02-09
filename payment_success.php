<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require 'app/db.php';
require 'app/cart_functions.php';
require 'app/order_functions.php';
require __DIR__ . '/assets/vendor/stripe/stripe-php/init.php';

require 'app/config.php';
\Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

$session_id = $_GET['session_id'] ?? '';

if (!$session_id) {
    header('Location: menu.php');
    exit();
}

try {
    // Retrieve the session from Stripe
    $stripe_session = \Stripe\Checkout\Session::retrieve($session_id);

    // Get cart items before clearing
    $cart_items = getCartItems($pdo);
    $cart_total = getCartTotal($pdo);
    $user_id = $_SESSION['user_id'] ?? null;

    // Create order record
    $order_id = createOrder($pdo, $user_id, $cart_items, $cart_total, $session_id);

    // Clear the cart
    clearCart($pdo, $user_id);

    // Store order details for display
    $_SESSION['last_order'] = [
        'order_id' => $order_id,
        'total' => $cart_total,
        'payment_id' => $session_id
    ];

} catch (Exception $e) {
    $error = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful - Kafe Tiga Belas</title>
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/css/main.css" rel="stylesheet">
</head>
<body class="index-page">
    <main class="main">
        <div class="success-container">
            <div class="success-icon">
                <i class="bi bi-check-circle-fill"></i>
            </div>

            <h1 class="mb-3">Payment Successful!</h1>
            <p class="lead mb-4">Thank you for your order. We'll prepare your drinks right away.</p>

            <?php if (isset($error)): ?>
                <div class="alert alert-warning">Note: <?php echo htmlspecialchars($error); ?></div>
            <?php elseif (isset($order_id)): ?>
                <div class="order-details">
                    <h5>Order Details</h5>
                    <p class="mb-2"><strong>Order ID:</strong> #<?php echo str_pad($order_id, 6, '0', STR_PAD_LEFT); ?></p>
                    <p class="mb-2"><strong>Total Paid:</strong> RM <?php echo number_format($cart_total, 2); ?></p>
                    <p class="mb-2"><strong>Payment ID:</strong> <?php echo substr($session_id, 0, 20) . '...'; ?></p>
                    <p class="mb-0"><strong>Status:</strong> <span class="text-success">Paid</span></p>
                </div>
            <?php endif; ?>

            <div class="d-flex justify-content-center gap-3 mt-4">
                <a href="menu.php" class="btn btn-outline-primary">
                    <i class="bi bi-cup me-2"></i>Order More
                </a>
                <a href="index.php" class="btn btn-primary">
                    <i class="bi bi-house me-2"></i>Home
                </a>
            </div>

            <div class="mt-4">
                <small>
                    A confirmation email has been sent (if you provided an email).
                    Your order will be ready in approximately 15-20 minutes.
                </small>
            </div>

            <div class="d-flex justify-content-center gap-3 mt-4">
                <a href="receipt.php?order_id=<?php echo $order_id; ?>" class="btn btn-outline-success btn-sm">
                    <i class="bi bi-receipt me-1"></i>View Receipt
                </a>
                <a class="btn btn-outline-success btn-sm" href="order_track.php">
                    <i class="bi bi-truck me-1"></i>Track Order
                </a>
            </div>
        </div>
    </main>

    <footer id="footer" class="footer">
        <div class="container copyright text-center mt-4">
            <p>© <span>Copyright</span> <strong class="px-1 sitename">Kafe Tiga Belas</strong> <span>All Rights Reserved</span></p>
        </div>
    </footer>
</body>
</html>
