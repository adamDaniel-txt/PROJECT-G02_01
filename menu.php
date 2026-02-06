<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require 'app/db.php';
require 'app/menu_functions.php';
require 'app/cart_functions.php'; // NEW: Include cart functions

// Get all available menu items
$menu_items = getAllMenuItems($pdo);
$categories = getMenuCategories($pdo);

// Filter by category if selected
$selected_category = $_GET['category'] ?? null;
if ($selected_category) {
    $menu_items = getAllMenuItems($pdo, $selected_category);
}

// Handle cart actions via AJAX/Form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $menu_item_id = $_POST['menu_item_id'] ?? 0;
    $quantity = $_POST['quantity'] ?? 1;

    switch ($action) {
    case 'add':
        $success = addToCart($pdo, $menu_item_id, $quantity);
        $response = [
            'success' => $success,
            'cart_count' => getCartCount($pdo),
            'message' => $success ? 'Item added to cart!' : 'Failed to add item.'
        ];
        echo json_encode($response);
        exit;

    case 'update':
        $success = updateCartQuantity($pdo, $menu_item_id, $quantity);
        $response = [
            'success' => $success,
            'cart_count' => getCartCount($pdo),
            'cart_total' => getCartTotal($pdo)
        ];
        echo json_encode($response);
        exit;

    case 'get_cart':
        $cart_items = getCartItems($pdo);
        $response = [
            'cart_items' => $cart_items,
            'cart_count' => getCartCount($pdo),
            'cart_total' => getCartTotal($pdo)
        ];
        echo json_encode($response);
        exit;
    }
}

// Get current cart items for initial display
$cart_items = getCartItems($pdo);
$cart_count = getCartCount($pdo);
$cart_total = getCartTotal($pdo);
?>
<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="utf-8">
        <meta content="width=device-width, initial-scale=1.0" name="viewport">
        <title>Kafe Tiga Belas Menu</title>
        <meta name="description" content="">
        <meta name="keywords" content="">

        <!-- Favicons -->
        <link href="assets/img/favicon.png" rel="icon">
        <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com" rel="preconnect">
        <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;0,800;0,900;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

        <!-- Vendor CSS Files -->
        <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
        <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
        <link href="assets/vendor/aos/aos.css" rel="stylesheet">
        <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
        <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

        <!-- Main CSS File -->
        <link href="assets/css/main.css" rel="stylesheet">

    </head>

    <body class="index-page">

        <!-- ======= Header ======= -->
        <header id="header" class="header fixed-top">
            <div class="topbar d-flex align-items-center" style="background-color: var(--background-color);">
                <div class="container d-flex justify-content-center justify-content-md-between">
                    <div class="contact-info d-flex align-items-center">
                        <i class="bi bi-envelope d-flex align-items-center"><a href="mailto:contact@example.com">tigabelasmedia@gmail.com</a></i>
                        <i class="bi bi-phone d-flex align-items-center ms-4"><span>012-234 6861</span></i>
                    </div>
                    <div class="languages d-none d-md-flex align-items-center">
                        <ul>
                            <li>En</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="branding d-flex align-items-center">
                <div class="container position-relative d-flex align-items-center justify-content-between">
                    <a href="index.php">
                        <i class="bi bi-arrow-left-circle-fill fs-3"></i>
                    </a>
                    <a href="#menu" class="logo d-flex align-items-center me-auto me-xl-0">
                        <h1 class="sitename">Kafe Tiga Belas</h1>
                        <h1 style="font-size: 3em; transform: rotate(4deg); color: var(--accent-color);"
                            class="sitename">&nbsp;&nbsp;Menu</h1>
                    </a>

                    <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>

                    <!-- CART ICON with live count -->
                    <div class="icon-cart" style="cursor: pointer; position: relative;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-cart" viewBox="0 0 20 20">
                            <path d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .491.592l-1.5 8A.5.5 0 0 1 13 12H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5M3.102 4l1.313 7h8.17l1.313-7zM5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4m7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4m-7 1a1 1 0 1 1 0 2 1 1 0 0 1 0-2m7 0a1 1 0 1 1 0 2 1 1 0 0 1 0-2"/>
                        </svg>
                        <span class="cart-count" id="cart-count"><?php echo $cart_count; ?></span>
                    </div>
                </div>
            </div>
        </header>
        <!-- End Header -->

        <main class="main">
            <!-- Menu Section -->
            <section id="menu" class="menu section container py-5">
                <br><br><br>

                <!-- Menu Grid -->
                <div class="container" data-aos="fade-up" data-aos-delay="100">
                    <div id="menu-grid" class="row gy-4 justify-content-center">

                        <?php if (empty($menu_items)): ?>
                        <div class="text-center py-5">
                            <div class="mb-3">
                                <i class="bi bi-cup display-1 text-muted opacity-25"></i>
                            </div>
                            <h3 class="text-muted">No items found</h3>
                            <p class="text-muted">Please check back later or try another category.</p>
                        </div>
                        <?php else: ?>
                        <div class="row g-4">
                            <?php foreach ($menu_items as $item): ?>
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card menu-card shadow-sm h-100">
                                    <!-- Item Image -->
                                    <?php if ($item['image_url']): ?>
                                    <img src="<?php echo htmlspecialchars($item['image_url']); ?>"
                                         alt="<?php echo htmlspecialchars($item['name']); ?>"
                                         class="card-img-top menu-img">
                                         <?php else: ?>
                                         <div class="default-menu-img">
                                             <i class="bi bi-cup-hot display-1"></i>
                                         </div>
                                         <?php endif; ?>

                                         <div class="card-body d-flex flex-column">
                                             <!-- Item Name -->
                                             <h3 class="card-title h5 text-light mb-2"><?php echo htmlspecialchars($item['name']); ?></h3>

                                             <!-- Description -->
                                             <?php if ($item['description']): ?>
                                             <p class="card-text text-light flex-grow-1 mb-3"><?php echo htmlspecialchars($item['description']); ?></p>
                                             <?php endif; ?>

                                             <!-- Price and Action -->
                                             <div class="d-flex justify-content-between align-items-center mt-auto">
                                                 <div class="price-tag"><?php echo formatPrice($item['price']); ?></div>
                                                 <button class="btn btn-add-to-cart"
                                                         data-id="<?php echo $item['id']; ?>"
                                                         data-name="<?php echo htmlspecialchars($item['name']); ?>"
                                                         data-price="<?php echo $item['price']; ?>">
                                                         <i class="bi bi-cart-plus me-1"></i>Add to Cart
                                                 </button>
                                             </div>
                                         </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>

                    </div>
                </div>
            </section>
        </main>

        <!-- Cart Sidebar -->
        <div id="cart-sidebar" class="cart-sidebar">
            <div class="cart-header">
                <h3>Your Cart</h3>
                <button id="close-cart" class="close-btn">&times;</button>
            </div>
            <div id="cart-items" class="cart-items">
                <!-- Cart items will be loaded dynamically via AJAX -->
                <div class="text-center py-5">
                    <i class="bi bi-cart display-1 text-muted opacity-25"></i>
                    <p class="text-muted mt-3">Your cart is empty</p>
                </div>
            </div>
            <div class="cart-footer">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">Total:</h5>
                    <h5 class="mb-0" id="cart-total">RM <?php echo number_format($cart_total, 2); ?></h5>
                </div>
                <button class="btn checkout-btn" onclick="proceedToCheckout()">
                    <i class="bi bi-credit-card me-2"></i>Proceed to Checkout
                </button>
            </div>
        </div>

        <div id="cart-overlay" class="cart-overlay"></div>

        <!-- Footer -->
        <footer id="footer" class="footer">
            <!-- Your existing footer code -->
            <div class="container footer-top">
                <div class="row gy-4">
                    <div class="col-lg-4 col-md-6 footer-about">
                        <a href="index.html" class="logo d-flex align-items-center">
                            <span class="sitename">Kafe Tiga Belas</span>
                        </a>
                        <div class="footer-contact pt-3">
                            <p>83, Jalan Lawan Pedang 13/27,</p>
                            <p>Tadisma Business Park,</p>
                            <p>T40100 Shah Alam, Selangor</p>
                            <p class="mt-3"><strong>Phone:</strong> <span>012-234 6861</span></p>
                            <p><strong="Email:</strong> <span>tigabelasmedia@gmail.com</span></p>
                        </div>
                        <div class="social-links d-flex mt-4">
                            <a href=""><i class="bi bi-facebook"></i></a>
                            <a href=""><i class="bi bi-instagram"></i></a>
                        </div>
                    </div>

                    <div class="col-lg-2 col-md-3 footer-links">
                        <h4>Useful Links</h4>
                        <ul>
                            <li><a href="#">Home</a></li>
                            <li><a href="#">About us</a></li>
                            <li><a href="#">Services</a></li>
                            <li><a href="#">Terms of service</a></li>
                            <li><a href="#">Privacy policy</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="container copyright text-center mt-4">
                <p>© <span>Copyright</span> <strong class="px-1 sitename">Restaurantly</strong> <span>All Rights Reserved</span></p>
                <div class="credits">
                            Designed by <a href="https://bootstrapmade.com/">BootstrapMade</a> Distributed by <a href="https://themewagon.com" target="_blank">ThemeWagon</a>
                </div>
            </div>
        </footer>

        <!-- Scroll Top -->
        <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

        <!-- Preloader -->
        <div id="preloader"></div>

        <!-- Vendor JS Files -->
        <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
        <script src="assets/vendor/php-email-form/validate.js"></script>
        <script src="assets/vendor/aos/aos.js"></script>
        <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
        <script src="assets/vendor/imagesloaded/imagesloaded.pkgd.min.js"></script>
        <script src="assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>
        <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>

        <!-- Main JS File -->
        <script src="assets/js/main.js"></script>

        <!-- Cart JavaScript -->
        <script src="assets/js/cart.js"></script>

    </body>
</html>
