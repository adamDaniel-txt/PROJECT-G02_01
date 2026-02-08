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
        .receipt-container {
            max-width: 800px;
            margin: 0 auto 50px;
            padding: 40px;
            background: white;
            color: #333;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        .receipt-header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .receipt-details {
            margin-bottom: 30px;
        }
        .receipt-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #ddd;
        }
        .receipt-total {
            font-size: 1.2em;
            font-weight: bold;
            border-top: 2px solid #000;
            padding-top: 15px;
        }
        .print-only {
            display: none;
        }
        @media print {
            .no-print {
                display: none !important;
            }
            .print-only {
                display: block;
            }
            body {
                background: white !important;
                color: black !important;
            }
            .receipt-container {
                box-shadow: none !important;
                margin: 0 !important;
                padding: 20px !important;
            }
        }
    </style>
</head>
<body>
    <main class="main">
        <div class="container d-flex flex-column justify-content-center align-items-center min-vh-25 py-4">
            <a href="index.php" class="text-decoration-none text-dark text-center">
                <div class="d-flex align-items-center flex-wrap justify-content-center">
                    <h1 style="font-size: 4rem; color: var(--accent-color);" class="display-3 fw-bold">Receipt</h1>
                </div>
            </a>
        </div>
        <div class="receipt-container">
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php elseif (isset($order)): ?>
                <!-- Receipt Header -->
                <div class="receipt-header">
                    <h1 class="mb-0">Kafe Tiga Belas</h1>
                    <p class="text-muted mb-0">83, Jalan Lawan Pedang 13/27, Tadisma Business Park</p>
                    <p class="text-muted mb-0">T40100 Shah Alam, Selangor</p>
                    <p class="text-muted mb-0">Phone: 012-234 6861 | Email: tigabelasmedia@gmail.com</p>
                    <div class="mt-3 print-only">
                        <small>Receipt generated on: <?php echo date('F j, Y g:i A'); ?></small>
                    </div>
                </div>

                <!-- Order Information -->
                <div class="receipt-details">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5>Order Information</h5>
                            <p class="mb-1"><strong>Order #:</strong> KTB-<?php echo str_pad($order['id'], 6, '0', STR_PAD_LEFT); ?></p>
                            <p class="mb-1"><strong>Date:</strong> <?php echo date('F j, Y g:i A', strtotime($order['created_at'])); ?></p>
                            <p class="mb-1"><strong>Status:</strong> <span class="badge bg-success">Paid</span></p>
                        </div>
                        <div class="col-md-6">
                            <h5>Customer</h5>
                            <p class="mb-1"><strong>Name:</strong> <?php echo htmlspecialchars($order['username'] ?? 'Guest'); ?></p>
                            <p class="mb-1"><strong>Email:</strong> <?php echo htmlspecialchars($order['email'] ?? 'Not provided'); ?></p>
                            <?php if (isset($stripe_session) && $stripe_session->customer_details->email): ?>
                                <p class="mb-1"><strong>Billing Email:</strong> <?php echo htmlspecialchars($stripe_session->customer_details->email); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Payment Information -->
                    <?php if (isset($stripe_session) || isset($payment_intent)): ?>
                    <div class="mb-4">
                        <h5>Payment Information</h5>
                        <p class="mb-1"><strong>Payment ID:</strong> <?php echo $order['payment_id']; ?></p>
                        <?php if (isset($payment_intent)): ?>
                            <p class="mb-1"><strong>Payment Method:</strong> Card ending in <?php echo substr($payment_intent->charges->data[0]->payment_method_details->card->last4 ?? '****', -4); ?></p>
                        <?php endif; ?>
                        <p class="mb-1"><strong>Paid Amount:</strong> RM <?php echo number_format($order['total_amount'], 2); ?></p>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Order Items -->
                <h5>Order Items</h5>
                <div class="mb-4">
                    <?php foreach ($order_items as $item): ?>
                        <div class="receipt-item">
                            <div>
                                <strong><?php echo htmlspecialchars($item['name']); ?></strong>
                                <br>
                                <small class="text-muted">x<?php echo $item['quantity']; ?></small>
                            </div>
                            <div>RM <?php echo number_format($item['price'] * $item['quantity'], 2); ?></div>
                        </div>
                    <?php endforeach; ?>

                    <!-- Totals -->
                    <div class="receipt-item receipt-total">
                        <div>Total Amount</div>
                        <div>RM <?php echo number_format($order['total_amount'], 2); ?></div>
                    </div>

                    <?php if ($stripe_session->total_details->amount_tax ?? 0 > 0): ?>
                    <div class="receipt-item">
                        <div>Tax</div>
                        <div>RM <?php echo number_format(($stripe_session->total_details->amount_tax ?? 0) / 100, 2); ?></div>
                    </div>
                    <div class="receipt-item receipt-total">
                        <div>Grand Total</div>
                        <div>RM <?php echo number_format($order['total_amount'], 2); ?></div>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Thank You Message -->
                <div class="text-center mt-5">
                    <h5>Thank You for Your Order!</h5>
                    <p class="text-muted">Your order will be ready in approximately 15-20 minutes.</p>
                    <p class="text-muted">Please present this receipt when picking up your order.</p>
                    <div class="mt-4">
                        <small class="text-muted">Order ID: KTB-<?php echo str_pad($order['id'], 6, '0', STR_PAD_LEFT); ?> | Receipt generated: <?php echo date('F j, Y g:i A'); ?></small>
                    </div>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="bi bi-receipt display-1 text-muted"></i>
                    <h3 class="mt-3">Receipt Not Found</h3>
                    <p>Please check your order number and try again.</p>
                    <a href="menu.php" class="btn btn-primary">Back to Menu</a>
                </div>
            <?php endif; ?>

            <!-- Action Buttons -->
            <div class="d-flex justify-content-center gap-3 mt-5 no-print">
                <button onclick="window.print()" class="btn btn-primary">
                    <i class="bi bi-printer me-2"></i>Print Receipt
                </button>
                <button onclick="downloadReceipt()" class="btn btn-success">
                    <i class="bi bi-download me-2"></i>Download PDF
                </button>
                <a href="menu.php" class="btn btn-outline-primary">
                    <i class="bi bi-cup me-2"></i>Order Again
                </a>
            </div>
        </div>
    </main>

    <footer id="footer" class="footer no-print">
        <div class="container copyright text-center mt-4">
            <p>© <span>Copyright</span> <strong class="px-1 sitename">Kafe Tiga Belas</strong> <span>All Rights Reserved</span></p>
        </div>
    </footer>

    <script>
        function downloadReceipt() {
            // Create a PDF version using html2pdf (requires library)
            const element = document.querySelector('.receipt-container');

            // Option 1: Simple print to PDF
            window.print();

            // Option 2: Using html2pdf library (requires adding the library)
            /*
            html2pdf()
                .from(element)
                .set({
                    margin: 10,
                    filename: 'receipt-KTB-<?php echo str_pad($order['id'] ?? '000000', 6, '0', STR_PAD_LEFT); ?>.pdf',
                    image: { type: 'jpeg', quality: 0.98 },
                    html2canvas: { scale: 2 },
                    jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
                })
                .save();
            */
        }
    </script>

    <!-- Include html2pdf library for PDF download (optional) -->
    <!--
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    -->

    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
