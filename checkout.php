<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

session_start();
require 'app/db.php';
require 'app/cart_functions.php';
require __DIR__ . '/assets/vendor/stripe/stripe-php/init.php';

// Use Stripe's test keys for sandbox
require 'app/config.php';
\Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

// URL encode the folder name
$folder_name = 'MASTER PROJECT - KAFE TIGA BELAS ONLINE ORDERING SYSTEM';
$encoded_folder = rawurlencode($folder_name);

// Get cart items
$cart_items = getCartItems($pdo);
$cart_total = getCartTotal($pdo);

if (empty($cart_items)) {
    header('Location: menu.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Prepare line items for Stripe
        $line_items = [];
        foreach ($cart_items as $item) {
            $product_data = [
                'name' => $item['name'],
            ];

            // Only add description if it exists and is not empty
            $description = trim($item['description'] ?? '');
            if (!empty($description)) {
                $product_data['description'] = substr($description, 0, 200);
            }

            $line_items[] = [
                'price_data' => [
                    'currency' => 'myr',
                    'product_data' => $product_data,
                    'unit_amount' => round($item['price'] * 100), // Convert to cents
                ],
                'quantity' => $item['quantity'],
            ];
        }

        // Create Stripe checkout session
        $checkout_session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items' => $line_items,
            'mode' => 'payment',
            'success_url' => 'http://' . $_SERVER['HTTP_HOST'] . '/' . $encoded_folder . '/payment_success.php?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => 'http://' . $_SERVER['HTTP_HOST'] . '/' . $encoded_folder . '/checkout.php',
            /* 'success_url' => 'http://' . $_SERVER['HTTP_HOST'] . '/payment_success.php?session_id={CHECKOUT_SESSION_ID}', */
            /* 'cancel_url' => 'http://' . $_SERVER['HTTP_HOST'] . '/checkout.php', */
            'metadata' => [
                'user_id' => $_SESSION['user_id'] ?? 0,
                'cart_count' => count($cart_items),
            ],
        ]);

        // Store session ID temporarily
        $_SESSION['stripe_session_id'] = $checkout_session->id;

        // Redirect to Stripe Checkout
        header("HTTP/1.1 303 See Other");
        header("Location: " . $checkout_session->url);
        exit();

    } catch (Exception $e) {
        $error = $e->getMessage();
        // Log the error for debugging
        error_log("Stripe Error: " . $error);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Kafe Tiga Belas</title>
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/css/main.css" rel="stylesheet">
    <style>
        .checkout-container {
            max-width: 800px;
            margin: 25px auto 50px;
            padding: 30px;
            background: var(--background-color);
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .order-summary {
            background: rgba(255,255,255,0.05);
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
        }
        .summary-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .summary-total {
            font-size: 1.2em;
            font-weight: bold;
            color: var(--accent-color);
        }
        .stripe-logo {
            height: 30px;
            margin-right: 10px;
        }
    </style>
</head>
<body class="index-page">
    <main class="main">
        <div class="checkout-container">
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <h1 class="sitename mb-4">Checkout</h1>

            <div class="order-summary">
                <h2 class="mb-4">Order Summary</h2>
                <?php foreach ($cart_items as $item): ?>
                    <div class="summary-item">
                        <div>
                            <h6 class="mb-1"><?php echo htmlspecialchars($item['name']); ?></h6>
                            <small>x<?php echo $item['quantity']; ?></small>
                        </div>
                        <div>RM <?php echo number_format($item['price'] * $item['quantity'], 2); ?></div>
                    </div>
                <?php endforeach; ?>

                <div class="summary-item summary-total">
                    <div>Total Amount:</div>
                    <div>RM <?php echo number_format($cart_total, 2); ?></div>
                </div>
            </div>

            <div class="payment-method mb-4">
                <h5 class="mb-3">Payment Method</h5>
                <div class="d-flex align-items-center mb-3">
                    <img src="https://stripe.com/img/v3/home/twitter.png" alt="Stripe" class="stripe-logo">
                    <span>Secure payment powered by Stripe</span>
                </div>
                <small>Test card: 4242 4242 4242 4242 | Exp: 12/34 | CVC: 123</small>
            </div>

            <form method="POST">
                <button type="submit" class="btn btn-primary btn-lg w-100">
                    <i class="bi bi-credit-card me-2"></i>Pay RM <?php echo number_format($cart_total, 2); ?>
                </button>
            </form>

            <div class="text-center mt-3">
                <a href="menu.php">Return to menu</a>
            </div>
        </div>
    </main>

    <!-- Footer (similar to menu.php) -->
    <footer id="footer" class="footer">
        <div class="container copyright text-center mt-4">
            <p>© <span>Copyright</span> <strong class="px-1 sitename">Kafe Tiga Belas</strong> <span>All Rights Reserved</span></p>
        </div>
    </footer>

    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
