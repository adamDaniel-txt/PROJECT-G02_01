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
    <!-- Paste your full header code from index.html here -->
    <!-- Including the topbar, logo, navmenu, and the .icon-cart div -->
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
                <h1 style="
                           font-size: 3em;
                           transform: rotate(4deg);
                           color: var(--accent-color);"
                    class="sitename">&nbsp;&nbsp;Menu
                </h1>
            </a>

            <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
            </nav>

            <!-- CART ICON - CRITICAL -->
            <div class="icon-cart">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-cart" viewBox="0 0 20 20">
                    <path d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .491.592l-1.5 8A.5.5 0 0 1 13 12H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5M3.102 4l1.313 7h8.17l1.313-7zM5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4m7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4m-7 1a1 1 0 1 1 0 2 1 1 0 0 1 0-2m7 0a1 1 0 1 1 0 2 1 1 0 0 1 0-2"/>
                </svg>
                <span>0</span>
            </div>
        </div>
    </div>
  </header>
  <!-- End Header -->

  <main class="main">
      <!-- Menu Section -->
<section id="menu" class="menu section">

  <!-- Menu Grid -->
  <div class="container" data-aos="fade-up" data-aos-delay="100">
    <div id="menu-grid" class="row gy-4 justify-content-center">

      <!-- Menu Item 1 -->
      <div class="menu-item-card">
        <div class="card">
          <img src="assets/img/menu/americano2.png" alt="Americano">
          <div class="card-body">
            <h4>Americano</h4>
            <p class="price">RM7</p>
            <button class="btn-add-to-cart" data-name="Espresso" data-price="5.00">Add to Cart</button>
          </div>
        </div>
      </div>

      <!-- Menu Item 2 -->
      <div class="menu-item-card">
        <div class="card">
          <img src="assets/img/menu/hotdrink.jpg" alt="Cappuccino">
          <div class="card-body">
            <h4>Cappuccino</h4>
            <p class="price">RM11</p>
            <button class="btn-add-to-cart" data-name="Cappuccino" data-price="7.50">Add to Cart</button>
          </div>
        </div>
      </div>

      <div class="menu-item-card">
        <div class="card">
          <img src="assets/img/menu/latte.jpg" alt="Latte">
          <div class="card-body">
            <h4>Latte</h4>
            <p class="price">RM11</p>
            <button class="btn-add-to-cart" data-name="Cappuccino" data-price="7.50">Add to Cart</button>
          </div>
        </div>
      </div>

      <div class="menu-item-card">
        <div class="card">
          <img src="assets/img/menu/latte.jpg" alt="Hazelnut Latte">
          <div class="card-body">
            <h4>Hazelnut Latte</h4>
            <p class="price">RM13</p>
            <button class="btn-add-to-cart" data-name="Cappuccino" data-price="7.50">Add to Cart</button>
          </div>
        </div>
      </div>

      <div class="menu-item-card">
        <div class="card">
          <img src="assets/img/menu/latte.jpg" alt="Spanish Latte">
          <div class="card-body">
            <h4>Spanish Latte</h4>
            <p class="price">RM13</p>
            <button class="btn-add-to-cart" data-name="Cappuccino" data-price="7.50">Add to Cart</button>
          </div>
        </div>
      </div>

      <div class="menu-item-card">
        <div class="card">
          <img src="assets/img/menu/creme.jpg" alt="Creme brulee latte">
          <div class="card-body">
            <h4>Creme Brulee Latte</h4>
            <p class="price">RM14</p>
            <button class="btn-add-to-cart" data-name="Cappuccino" data-price="7.50">Add to Cart</button>
          </div>
        </div>
      </div>

      <div class="menu-item-card">
        <div class="card">
          <img src="assets/img/menu/butterscotch.jpg" alt="Creamy Butterscotch Latte">
          <div class="card-body">
            <h4>Creamy Butterscotch Latte</h4>
            <p class="price">RM14</p>
            <button class="btn-add-to-cart" data-name="Cappuccino" data-price="7.50">Add to Cart</button>
          </div>
        </div>
      </div>

      <div class="menu-item-card">
        <div class="card">
          <img src="assets/img/menu/macchiato.png" alt="Macchiato">
          <div class="card-body">
            <h4>Caramel Macchiato</h4>
            <p class="price">RM13</p>
            <button class="btn-add-to-cart" data-name="Cappuccino" data-price="7.50">Add to Cart</button>
          </div>
        </div>
      </div>

      <div class="menu-item-card">
        <div class="card">
          <img src="assets/img/menu/chocolate.png" alt="Chocolate">
          <div class="card-body">
            <h4>Chocolate</h4>
            <p class="price">RM13</p>
            <button class="btn-add-to-cart" data-name="Cappuccino" data-price="7.50">Add to Cart</button>
          </div>
        </div>
      </div>

      <div class="menu-item-card">
        <div class="card">
          <img src="assets/img/menu/matcha.png" alt="Matcha">
          <div class="card-body">
            <h4>Matcha</h4>
            <p class="price">RM13</p>
            <button class="btn-add-to-cart" data-name="Cappuccino" data-price="7.50">Add to Cart</button>
          </div>
        </div>
      </div>

      <div class="menu-item-card">
        <div class="card">
          <img src="assets/img/menu/strawberrylatte.jpg" alt="Strawberry Latte">
          <div class="card-body">
            <h4>Strawberry Latte</h4>
            <p class="price">RM12</p>
            <button class="btn-add-to-cart" data-name="Cappuccino" data-price="7.50">Add to Cart</button>
          </div>
        </div>
      </div>

      <div class="menu-item-card">
        <div class="card">
          <img src="assets/img/menu/grape.png" alt="Sakura grape">
          <div class="card-body">
            <h4>Sakura Grape</h4>
            <p class="price">RM12</p>
            <button class="btn-add-to-cart" data-name="Cappuccino" data-price="7.50">Add to Cart</button>
          </div>
        </div>
      </div>

      <div class="menu-item-card">
        <div class="card">
          <img src="assets/img/menu/peach.png" alt="Peach">
          <div class="card-body">
            <h4>Peach</h4>
            <p class="price">RM12</p>
            <button class="btn-add-to-cart" data-name="Cappuccino" data-price="7.50">Add to Cart</button>
          </div>
        </div>
      </div>

      <div class="menu-item-card">
        <div class="card">
          <img src="assets/img/menu/mojito.jpg" alt="Mojito">
          <div class="card-body">
            <h4>Mojito</h4>
            <p class="price">RM12</p>
            <button class="btn-add-to-cart" data-name="Cappuccino" data-price="7.50">Add to Cart</button>
          </div>
        </div>
      </div>

      <div class="menu-item-card">
        <div class="card">
          <img src="assets/img/menu/hotdrink.jpg" alt="Green tea">
          <div class="card-body">
            <h4>Green tea</h4>
            <p class="price">RM7</p>
            <button class="btn-add-to-cart" data-name="Cappuccino" data-price="7.50">Add to Cart</button>
          </div>
        </div>
      </div>

      <div class="menu-item-card">
        <div class="card">
          <img src="assets/img/menu/hotdrink.jpg" alt="Earl Grey Tea">
          <div class="card-body">
            <h4>Earl Grey Tea</h4>
            <p class="price">RM7</p>
            <button class="btn-add-to-cart" data-name="Cappuccino" data-price="7.50">Add to Cart</button>
          </div>
        </div>
      </div>


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
       <!-- Items will appear here dynamically -->
      <p class="empty-cart">Your cart is empty.</p>
    </div>
    <div class="cart-footer">
      <p>Total: <span id="cart-total">$0.00</span></p>
      <button class="btn checkout-btn">Checkout</button>
    </div>
  </div>
  <div id="cart-overlay" class="cart-overlay"></div>

  <!-- Footer -->
  <footer id="footer" class="footer">

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
            <p><strong>Email:</strong> <span>tigabelasmedia@gmail.com</span></p>
          </div>
          <div class="social-links d-flex mt-4">
            <a href=""><i class="bi bi-twitter-x"></i></a>
            <a href=""><i class="bi bi-facebook"></i></a>
            <a href=""><i class="bi bi-instagram"></i></a>
            <a href=""><i class="bi bi-linkedin"></i></a>
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
        <!-- All the links in the footer should remain intact. -->
        <!-- You can delete the links only if you've purchased the pro version. -->
        <!-- Licensing information: https://bootstrapmade.com/license/ -->
        <!-- Purchase the pro version with working PHP/AJAX contact form: [buy-url] -->
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
  <script src="assets/js/addToCart.js"></script>

</body>
</html>
