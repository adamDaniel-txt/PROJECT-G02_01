<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require 'app/db.php';
require __DIR__ . '/assets/vendor/stripe/stripe-php/init.php';

require 'app/config.php';
\Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

$order_id = $_GET['order_id'] ?? 0;
$session_id = $_GET['session_id'] ?? '';

if (!$order_id && !$session_id) {
    header('Location: menu.php');
    exit();
}

try {
    // Fetch order details from database
    $stmt = $pdo->prepare("
        SELECT o.*, u.username, u.email
        FROM orders o
        LEFT JOIN users u ON o.user_id = u.id
        WHERE o.id = ? OR o.payment_id = ?
    ");
    $stmt->execute([$order_id, $session_id]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        throw new Exception("Order not found");
    }

    // Fetch order items
    $stmt = $pdo->prepare("
        SELECT oi.*, mi.name, mi.description
        FROM order_items oi
        JOIN menu_items mi ON oi.menu_item_id = mi.id
        WHERE oi.order_id = ?
    ");
    $stmt->execute([$order['id']]);
    $order_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get Stripe payment details
    if (!empty($order['payment_id'])) {
        try {
            $stripe_session = \Stripe\Checkout\Session::retrieve($order['payment_id']);
            $payment_intent = \Stripe\PaymentIntent::retrieve($stripe_session->payment_intent);
            $charge = \Stripe\Charge::retrieve($payment_intent->latest_charge);
        } catch (Exception $e) {
            // If Stripe retrieval fails, use basic info
            $stripe_session = null;
        }
    }

} catch (Exception $e) {
    $error = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt - Order #<?php echo str_pad($order['id'] ?? 0, 6, '0', STR_PAD_LEFT); ?> - Kafe Tiga Belas</title>
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/css/main.css" rel="stylesheet">
   <style>
    body{
      background-image:url('https://cdn.thespaces.com/wp-content/uploads/2019/09/Korea-Hero.jpg');
      background-size: cover;
      background-position: center;
      background-repeat: no-repeat;     /* Prevents tiling/repeating */
      background-attachment: fixed;     /* Keeps background fixed when scrolling (parallax effect) */

      /*darken the image*/
      background-color: rgba(0, 0, 0, 0.5);
      background-blend-mode: multiply;
    }
    
    .receipt-container {
        max-width: 400px;                   /* Narrow like a real receipt */
        margin: 60px auto 50px;
        padding: 20px 15px;
        background: #f8f8f8;                /* Light paper color */
        color: #000;
        font-family: 'Courier New', Courier, monospace;
        font-size: 14px;
        line-height: 1.4;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        position: relative;
    }

    .receipt-header {
        text-align: center;
        margin-bottom: 20px;
    }

    .receipt-header h1 {
        font-size: 1.6rem;
        margin: 0 0 6px 0;
        letter-spacing: 1px;
        text-transform: uppercase;
    }

    .receipt-shop-info {
        font-size: 0.9rem;
        margin-bottom: 18px;
    }

    .receipt-title {
        text-align: center;
        font-size: 1.3rem;
        font-weight: bold;
        text-transform: uppercase;
        margin: 12px 0 8px;
        border-bottom: 1px dashed #000;
        padding-bottom: 6px;
    }

    .receipt-order-info {
        font-size: 0.95rem;
        margin-bottom: 12px;
    }

    .receipt-order-info p {
        margin: 3px 0;
    }

    .receipt-items {
        margin: 15px 0;
    }

    .receipt-item {
        display: flex;
        justify-content: space-between;
        margin: 6px 0;
        font-size: 0.95rem;
    }

    .receipt-item-left {
        flex: 1;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        padding-right: 8px;
    }

    .receipt-item-right {
        text-align: right;
        min-width: 80px;
    }

    .item-name {
        font-weight: bold;
    }

    .item-qty {
        color: #444;
        font-size: 0.85rem;
    }

    .dashed-line {
        border-bottom: 1px dashed #000;
        margin: 10px 0;
    }

    .receipt-total {
        font-weight: bold;
        font-size: 1.1rem;
        display: flex;
        justify-content: space-between;
        margin-top: 8px;
    }

    .thank-you {
        text-align: center;
        margin-top: 25px;
        font-size: 0.95rem;
    }

    .barcode {
        text-align: center;
        margin-top: 15px;
        font-size: 1.4rem;
        letter-spacing: -2px;
    }

    .footer-info {
        text-align: center;
        font-size: 0.8rem;
        margin-top: 12px;
        color: #555;
    }

    @media print {
        .no-print { display: none !important; }
        .receipt-container {
            box-shadow: none;
            border: none;
            margin: 0;
            padding: 10px;
            background: white;
        }
    }

    .receipt-footer{
        max-width: 400px;
        margin: 40px auto 60px;
        padding: 30px;
    }
</style>

<div class="receipt-container">
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php elseif (isset($order)): ?>

        <!-- Header -->
        <div class="receipt-header">
            <p style="font-size:30px; font-weight: bold">KAFE TIGA BELAS</p>
            <div class="receipt-shop-info">
                83, Jalan Lawan Pedang 13/27<br>
                Tadisma Business Park<br>
                40100 Shah Alam, Selangor<br>
                Phone: 012-234 6861
            </div>
        </div>

        <div class="receipt-title">RECEIPT</div>

        <!-- Order info -->
        <div class="receipt-order-info">
            <p>ORDER #KTB-<?php echo str_pad($order['id'], 6, '0', STR_PAD_LEFT); ?></p>
            <p>DATE: <?php echo strtoupper(date('l, F j, Y', strtotime($order['created_at']))); ?></p>
            <p>FOR: <?php echo htmlspecialchars(strtoupper($order['username'] ?? 'GUEST')); ?></p>
            <?php if (!empty($order['payment_id'])): ?>
                <p>PAYMENT ID: <?php echo substr($order['payment_id'], 0, 10) . '...'; ?></p>
            <?php endif; ?>
        </div>

        <div class="dashed-line"></div>

        <!-- Items -->
        <div class="receipt-items">
            <?php foreach ($order_items as $index => $item): ?>
                <div class="receipt-item">
                    <div class="receipt-item-left">
                        <span class="item-name">
                            <?php printf("%02d", $index + 1); ?> 
                            <?php echo htmlspecialchars(strtoupper($item['name'])); ?>
                        </span><br>
                        <span class="item-qty">x<?php echo $item['quantity']; ?></span>
                    </div>
                    <div class="receipt-item-right">
                        RM <?php echo number_format($item['price'] * $item['quantity'], 2); ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="dashed-line"></div>

        <!-- Totals -->
        <div class="receipt-total">
            <div>ITEM COUNT:</div>
            <div><?php echo count($order_items); ?></div>
        </div>
        <div class="receipt-total">
            <div>TOTAL:</div>
            <div>RM <?php echo number_format($order['total_amount'], 2); ?></div>
        </div>

        <?php if (!empty($order['payment_id']) && isset($stripe_session) && $stripe_session->total_details->amount_tax ?? 0 > 0): ?>
            <div class="receipt-total">
                <div>TAX:</div>
                <div>RM <?php echo number_format(($stripe_session->total_details->amount_tax ?? 0) / 100, 2); ?></div>
            </div>
        <?php endif; ?>

        <div class="thank-you">
            THANK YOU FOR YOUR ORDER!<br>
            ORDER READY IN APPROX. 15-20 MINUTES
        </div>
</div>

<div class="receipt-footer">
    <?php else: ?>
        <!-- Not found message remains the same -->
        <div class="text-center py-5">
            <i class="bi bi-receipt display-1 text-muted"></i>
            <h3 class="mt-3">Receipt Not Found</h3>
            <p>Please check your order number and try again.</p>
            <a href="menu.php" class="btn btn-primary">Back to Menu</a>
        </div>
    <?php endif; ?>

    <!-- Action buttons (keep as is) -->
    <div class="d-flex justify-content-center gap-3 mt-5 no-print">
        <button onclick="window.print()" class="btn btn-primary">
            <i class="bi bi-printer me-2"></i>Print Receipt
        </button>
        <a href="menu.php" class="btn btn-outline-primary">
            <i class="bi bi-cup me-2"></i>Order Again
        </a>
    </div>
</div>
</body>
</html>
