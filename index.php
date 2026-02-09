<?php
session_start();
require 'app/db.php';
require 'app/persmission.php';
require 'app/feedback.php';

// Check if user is logged in and get their role
$isLoggedIn = isset($_SESSION['role_id']);
$userRole = $_SESSION['role'] ?? null; // Assuming you store role in session

// Get all feedbacks for display
$feedbacks = getAllFeedbacks($pdo);
?>

<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="utf-8">
        <meta content="width=device-width, initial-scale=1.0" name="viewport">
        <title>Kafe Tiga Belas Online Ordering System</title>
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

        <!-- =======================================================
            * Template Name: Restaurantly
            * Template URL: https://bootstrapmade.com/restaurantly-restaurant-template/
            * Updated: Aug 07 2024 with Bootstrap v5.3.3
            * Author: BootstrapMade.com
            * License: https://bootstrapmade.com/license/
            ======================================================== -->
    </head>

    <body class="index-page">

        <header id="header" class="header fixed-top">

            <div class="topbar d-flex align-items-center">
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
            </div><!-- End Top Bar -->

            <div class="branding d-flex align-items-left">

                <div class="container position-relative d-flex align-items-center justify-content-between">
                    <a href="index.php" class="logo d-flex align-items-center me-auto">
                        <!-- Uncomment the line below if you also wish to use an image logo -->
                        <!-- <img src="assets/img/logo.png" alt=""> -->
                        <h1 class="sitename">Kafe Tiga Belas</h1>
                    </a>

                    <nav id="navmenu" class="navmenu navbar navbar-expand-lg me-3">
                        <ul>
                            <li><a href="#hero" class="active">Home<br></a></li>
                            <li><a href="#about">About</a></li>
                            <li><a href="#testimonials">Reviews</a></li>
                            <li><a href="#contact">Contact</a></li>
                            <li><a href="menu.php">Menu</a></li>
                        </ul>
                        <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
                    </nav>

                    <div class="dropdown ms-3">
                        <a href="#" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle fs-3"></i>
                        </a>
                        <div class="dropdown-menu">
                            <?php if (!$isLoggedIn): ?>
                            <a class="dropdown-item" href="login.php"><i class="bi bi-box-arrow-in-right"></i>&nbsp&nbspLogin/Register</a>
                            <?php else: ?>
                            <a class="dropdown-item" href="profile.php"><i class="bi bi-person"></i>&nbsp&nbspProfile</a>
                                <?php if (hasPermission('view_dashboard')): ?>
                                <a class="dropdown-item" href="dashboard.php"><i class="bi bi-speedometer2"></i>&nbsp&nbspDashboard</a>
                                <?php elseif (hasPermission('view_order_status/history')): ?>
                                <a class="dropdown-item" href="order_history.php"><i class="bi bi-clock-history"></i>&nbsp&nbspMy Orders</a>
                                <a class="dropdown-item" href="order_track.php"><i class="bi bi-truck"></i>&nbsp&nbspTrack Order</a>
                                <?php endif; ?>
                            <a class="dropdown-item" href="app/logout.php"><i class="bi bi-box-arrow-left"></i>&nbsp&nbspLog Out</a>
                            <?php endif; ?>
                        </div>
                    </div>

                </div>
            </div>

        </header>

        <main class="main">

            <!-- Hero Section -->
            <section id="hero" class="hero section dark-background">

                <img src="assets/img/hero-bg.jpg" alt="" data-aos="fade-in">

                <div class="container">
                    <div class="row">
                        <div class="col-lg-8 d-flex flex-column align-items-center align-items-lg-start">
                            <h2 data-aos="fade-up" data-aos-delay="100">Welcome to <span>Kafe Tiga Belas</span></h2>
                            <p data-aos="fade-up" data-aos-delay="200">Delivering great food for more than 20 years!</p>
                            <div class="d-flex mt-4" data-aos="fade-up" data-aos-delay="300">
                                <a href="menu.php" class="cta-btn">Order Now!</a>
                                <!--<a href="#give-feedback" class="cta-btn">Book a Table</a>-->
                            </div>
                        </div>
                        <div class="col-lg-4 d-flex align-items-center justify-content-center mt-5 mt-lg-0">
                            <!--<a href="https://www.youtube.com/watch?v=Y7f98aduVJ8" class="glightbox pulsating-play-btn"></a>-->
                        </div>
                    </div>
                </div>

            </section><!-- /Hero Section -->

            <!-- About Section -->
            <section id="about" class="about section">

                <div class="container" data-aos="fade-up" data-aos-delay="100">

                    <div class="row gy-4">
                        <div class="col-lg-6 order-1 order-lg-2">
                            <img src="assets/img/about.jpg" class="img-fluid about-img" alt="">
                        </div>
                        <div class="col-lg-6 order-2 order-lg-1 content">
                            <h2>Kafe Tiga Belas</h2>
                            <p class="fst-italic">
                            Tiga Belas Cafe is a charming family-owned business that offers a delightful combination of aromatic coffee and delectable cakes.
                            With a passionate team of five siblings at the helm, this cozy cafe creates an inviting ambiance for coffee enthusiasts and dessert lovers alike.
                            </p>
                            <ul>
                                <li><i class="bi bi-check2-all"></i> <span>The finest coffee beans are sourced for a rich and flavorful experience.</span></li>
                                <li><i class="bi bi-check2-all"></i> <span>Classic espresso-based beverages to handcrafted specialty brews available.</span></li>
                                <li><i class="bi bi-check2-all"></i> <span>Delicious cakes baked with love, catering to various tastes.</span></li>
                            </ul>
                            <p>
                            Tiga Belas Cafe is your perfect destination for a cozy indulgence in quality coffee and delightful desserts.
                            </p>
                        </div>
                    </div>

                </div>

            </section><!-- /About Section -->

            <!-- Why Us Section -->
            <section id="why-us" class="why-us section">

                <!-- Section Title -->
                <div class="container section-title" data-aos="fade-up">
                    <h2>WHY US</h2>
                    <p>Why Choose Our Restaurant</p>
                </div><!-- End Section Title -->

                <div class="container">

                    <div class="row gy-4">

                        <div class="col-lg-4" data-aos="fade-up" data-aos-delay="100">
                            <div class="card-item">
                                <span>01</span>
                                <h4><a href="" class="stretched-link">Affordable price</a></h4>
                                <p>We believe good food should be affordable, so we keep our prices friendly while maintaining the quality you expect.</p>
                            </div>
                        </div><!-- Card Item -->

                        <div class="col-lg-4" data-aos="fade-up" data-aos-delay="200">
                            <div class="card-item">
                                <span>02</span>
                                <h4><a href="" class="stretched-link">Good services</a></h4>
                                <p>From greeting you at the door to serving your meal with care, we’re committed to making every dining experience smooth and enjoyable.</p>
                            </div>
                        </div><!-- Card Item -->

                        <div class="col-lg-4" data-aos="fade-up" data-aos-delay="300">
                            <div class="card-item">
                                <span>03</span>
                                <h4><a href="" class="stretched-link">High quality of food</a></h4>
                                <p>Quality matters to us, so every meal is prepared with fresh ingredients and cooked to perfection for a taste you can trust.</p>
                            </div>
                        </div><!-- Card Item -->

                    </div>

                </div>

            </section><!-- /Why Us Section -->

            <!-- Reviews Section -->
            <section id="testimonials" class="testimonials section">

                <!-- Section Title -->
                <div class="container section-title" data-aos="fade-up">
                    <h2>Reviews</h2>
                    <p>What they're saying about us</p>
                </div><!-- End Section Title -->

                <div class="container" data-aos="fade-up" data-aos-delay="100">

                    <div class="swiper init-swiper" data-speed="600" data-delay="5000" data-breakpoints="{ &quot;320&quot;: { &quot;slidesPerView&quot;: 1, &quot;spaceBetween&quot;: 40 }, &quot;1200&quot;: { &quot;slidesPerView&quot;: 3, &quot;spaceBetween&quot;: 40 } }">
                        <script type="application/json" class="swiper-config">
                            {
    "loop": true,
        "speed": 600,
        "autoplay": {
        "delay": 5000
},
    "slidesPerView": "auto",
    "pagination": {
    "el": ".swiper-pagination",
        "type": "bullets",
        "clickable": true
},
    "breakpoints": {
    "320": {
    "slidesPerView": 1,
        "spaceBetween": 40
},
    "1200": {
    "slidesPerView": 3,
        "spaceBetween": 20
}
}
}
                        </script>
                        <!-- View Feedback Section with Swiper -->
                        <div id="view-feedback">
                            <?php if (empty($feedbacks)): ?>
                            <div class="text-center py-5">
                                <div class="mb-3">
                                    <i class="bi bi-chat-square-text display-1 text-muted opacity-25"></i>
                                </div>
                                <h4 class="h5 text-muted mb-2">No feedback yet</h4>
                                <p class="text-muted mb-0">Be the first to share your experience!</p>
                            </div>
                            <?php else: ?>
                            <!-- Swiper Container -->
                            <div class="swiper-container">
                                <div class="swiper reviewsSwiper">
                                    <div class="swiper-wrapper">
                                        <?php foreach ($feedbacks as $feedback): ?>
                                        <div class="swiper-slide">
                                            <div class="card feedback-card h-100">
                                                <div class="card-body d-flex flex-column">
                                                    <!-- User Info -->
                                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                                        <div class="flex-grow-1">
                                                            <h5 class="card-title mb-1">
                                                                <i class="bi bi-person-circle me-2"></i>
                                                                <?php echo htmlspecialchars($feedback['user_name']); ?>
                                                            </h5>
                                                            <?php if (!empty($feedback['user_email'])): ?>
                                                            <small class="text-light">
                                                                <i class="bi bi-envelope me-1"></i>
                                                                <?php echo htmlspecialchars($feedback['user_email']); ?>
                                                            </small>
                                                            <?php endif; ?>
                                                        </div>
                                                        <span class="badge ms-2">
                                                            <i class="bi bi-calendar3 me-1"></i>
                                                            <?php echo formatFeedbackDate($feedback['created_at']); ?>
                                                        </span>
                                                    </div>

                                                    <!-- Rating Stars -->
                                                    <div class="star-display mb-3">
                                                        <?php echo getStarDisplay($feedback['rating']); ?>
                                                    </div>

                                                    <!-- Feedback Text -->
                                                    <div class="feedback-text text-light mb-3 flex-grow-1">
                                                        <div class="p-3 rounded h-100">
                                                            <i class="bi bi-quote me-1"></i>
                                                            <span class="feedback-content-text">
                                                                <?php echo nl2br(htmlspecialchars($feedback['feedback_text'])); ?>
                                                            </span>
                                                        </div>
                                                    </div>

                                                    <!-- Time -->
                                                    <div class="mt-auto text-end">
                                                        <small class="text-light">
                                                            <i class="bi bi-clock-history me-1"></i>
                                                            Posted <?php echo date('g:i A', strtotime($feedback['created_at'])); ?>
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>

                                    <!-- Navigation buttons -->
                                    <div class="swiper-button-next"></div>
                                    <div class="swiper-button-prev"></div>

                                    <!-- Pagination dots -->
                                    <br>
                                    <div class="swiper-pagination"></div>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Section Title -->
                <div class="container section-title" data-aos="fade-up">
                    <h2>Feedback</h2>
                </div><!-- End Section Title -->

                <div class="container col-10" data-aos="fade-up" data-aos-delay="100">

                    <?php if (!hasPermission('create_feedback')): ?>
                    <div class="alert alert-primary text-center" role="alert">
                      To give a feedback, you need to be a <a href="login.php" class="alert-link">Customer</a> first.
                    </div>
                    <?php endif; ?>

                    <!-- Success/Error Messages -->
                    <?php if (!empty($message)): ?>
                    <div class="alert alert-<?php echo $message_type === 'success' ? 'success' : 'danger'; ?> alert-custom alert-dismissible fade show" role="alert">
                        <i class="bi <?php echo $message_type === 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill'; ?> me-2"></i>
                        <?php echo htmlspecialchars($message); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php endif; ?>


                    <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>#testimonials" novalidate>
                        <div class="col-12">
                            <div class="star-rating star-label mb-2">
                                <!-- Stars in reverse order for CSS sibling selector -->
                                <input type="radio" id="star5" name="rating" value="5" <?php echo (($_POST['rating'] ?? 3) == 5) ? 'checked' : ''; ?>>
                                <label for="star5" title="Excellent - Perfect experience!">
                                    <i class="bi bi-star-fill"></i>
                                </label>

                                <input type="radio" id="star4" name="rating" value="4" <?php echo (($_POST['rating'] ?? 3) == 4) ? 'checked' : ''; ?>>
                                <label for="star4" title="Very Good - Great experience">
                                    <i class="bi bi-star-fill"></i>
                                </label>

                                <input type="radio" id="star3" name="rating" value="3" <?php echo (($_POST['rating'] ?? 3) == 3) ? 'checked' : ''; ?>>
                                <label for="star3" title="Good - Satisfied">
                                    <i class="bi bi-star-fill"></i>
                                </label>

                                <input type="radio" id="star2" name="rating" value="2" <?php echo (($_POST['rating'] ?? 3) == 2) ? 'checked' : ''; ?>>
                                <label for="star2" title="Fair - Could be better">
                                    <i class="bi bi-star-fill"></i>
                                </label>

                                <input type="radio" id="star1" name="rating" value="1" <?php echo (($_POST['rating'] ?? 3) == 1) ? 'checked' : ''; ?>>
                                <label for="star1" title="Poor - Needs improvement">
                                    <i class="bi bi-star-fill"></i>
                                </label>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="form-floating">
                                <textarea class="form-control"
                                          id="feedback_text"
                                          name="feedback"
                                          placeholder="Give Your feedback..."
                                          style="height: 120px"
                                          required><?php echo htmlspecialchars($_POST['feedback'] ?? ''); ?></textarea> <br>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="d-grid d-md-flex justify-content-md-end">
                                <?php if (hasPermission('create_feedback')): ?>
                                <button type="reset" class="btn btn-outline-secondary me-md-2">
                                    <i class="bi bi-arrow-clockwise me-2"></i>Clear
                                </button>
                                <button type="submit" name="submit_feedback" class="btn px-4" style="color: var(--accent-color); border-color: var(--accent-color);">
                                    <i class="bi bi-send-check me-2"></i>Submit Feedback
                                </button>
                                <?php else: ?>
                                <button hidden type="reset" class="btn btn-outline-secondary me-md-2">
                                    <i class="bi bi-arrow-clockwise me-2"></i>Clear
                                </button>
                                <button hidden type="submit" name="submit_feedback" class="btn px-4" style="color: var(--accent-color); border-color: var(--accent-color);">
                                    <i class="bi bi-send-check me-2"></i>Submit Feedback
                                </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </form>
                </div>
            </section>
            <!-- /reviews section -->

            <!-- contact section -->
            <section id="contact" class="contact section">

                <!-- section title -->
                <div class="container section-title" data-aos="fade-up">
                    <h2>Contact</h2>
                    <p>Keep in touch with us</p>
                </div><!-- end section title -->

                <div class="container" data-aos="fade-up" data-aos-delay="100">

                    <!-- map + info side-by-side -->
                    <div class="row gy-5 align-items-stretch">

                        <!-- google map (left side) -->
                        <div class="col-lg-8">
                            <iframe
                                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3984.0151918481743!2d101.54185571050638!3d3.0906143534967794!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31cc4d34ec93230b%3a0xaf9872bdbb787390!2skafe%20tiga%20belas!5e0!3m2!1sen!2smy!4v1766971925335!5m2!1sen!2smy"
                                    width="100%"
                                    height="500"
                                    style="border:0;"
                                    allowfullscreen=""
                                    loading="lazy"
                                    referrerpolicy="no-referrer-when-downgrade">
                            </iframe>
                        </div>

                        <!-- Contact Info (right side) -->
                        <div class="col-lg-4">
                            <div class="info-item d-flex" data-aos="fade-up" data-aos-delay="300">
                                <i class="bi bi-geo-alt flex-shrink-0"></i>
                                <div>
                                    <h3>Location</h3>
                                    <p>83, Jalan Lawan Pedang 13/27, Tadisma Business Park,<br>40100 Shah Alam, Selangor</p>
                                </div>
                            </div><!-- End Info Item -->

                            <div class="info-item d-flex" data-aos="fade-up" data-aos-delay="400">
                                <i class="bi bi-clock flex-shrink-0"></i> <!-- Better icon for hours -->
                                <div>
                                    <h3>Open Hours</h3>
                                    <p>Monday-Sunday:<br>04:00 PM - 12:00 AM</p>
                                </div>
                            </div><!-- End Info Item -->

                            <div class="info-item d-flex" data-aos="fade-up" data-aos-delay="500">
                                <i class="bi bi-telephone flex-shrink-0"></i>
                                <div>
                                    <h3>Call Us</h3>
                                    <p>012-234 6861</p>
                                </div>
                            </div><!-- End Info Item -->

                            <div class="info-item d-flex" data-aos="fade-up" data-aos-delay="600">
                                <i class="bi bi-envelope flex-shrink-0"></i>
                                <div>
                                    <h3>Email Us</h3>
                                    <p>tigabelasmedia@gmail.com</p>
                                </div>
                            </div><!-- End Info Item -->
                        </div>

                    </div>

                </div>

            </section>
            <!-- /Contact Section -->

        </main>

        <footer id="footer" class="footer">

            <div class="container footer-top">
                <div class="row gy-4">
                    <div class="col-lg-4 col-md-6 footer-about">
                        <a href="index.php" class="logo d-flex align-items-center">
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
                            <a href="https://www.facebook.com/p/TIGA-BELAS-CAFE-61558395301486/"><i class="bi bi-facebook"></i></a>
                            <a href="https://www.instagram.com/co.tigabelas/"><i class="bi bi-instagram"></i></a>
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
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Set default rating if not already set by PHP
                const ratingInputs = document.querySelectorAll('input[name="rating"]');
                const hasChecked = Array.from(ratingInputs).some(input => input.checked);

                if (!hasChecked) {
                    document.getElementById('star3').checked = true;
                }

                // Clear form button functionality
                document.querySelector('button[type="reset"]').addEventListener('click', function() {
                    setTimeout(() => {
                        document.getElementById('star3').checked = true;
                    }, 10);
                });

                // Initialize Swiper only if reviews exist
                const reviewsSwiper = document.querySelector('.reviewsSwiper');
                if (reviewsSwiper && reviewsSwiper.querySelectorAll('.swiper-slide').length > 0) {
                    const swiper = new Swiper('.reviewsSwiper', {
                        // Card settings
                        slidesPerView: 'auto', // Auto-adjust based on card width
                        centeredSlides: false,
                        spaceBetween: 25,
                        loop: false,

                        // Fixed card width
                        breakpoints: {
                            320: {
                                slidesPerView: 1,
                                spaceBetween: 20
                            },
                            576: {
                                slidesPerView: 1.2,
                                spaceBetween: 25
                            },
                            768: {
                                slidesPerView: 1.5,
                                spaceBetween: 30
                            },
                            992: {
                                slidesPerView: 2,
                                spaceBetween: 30
                            },
                            1200: {
                                slidesPerView: 2.5,
                                spaceBetween: 30
                            },
                            1400: {
                                slidesPerView: 3,
                                spaceBetween: 30
                            }
                        },

                        // Navigation arrows
                        navigation: {
                            nextEl: '.swiper-button-next',
                            prevEl: '.swiper-button-prev',
                        },

                        // Pagination dots
                        pagination: {
                            el: '.swiper-pagination',
                            clickable: true,
                            dynamicBullets: true,
                        },

                        // Accessibility
                        a11y: {
                            prevSlideMessage: 'Previous review',
                            nextSlideMessage: 'Next review',
                            firstSlideMessage: 'This is the first review',
                            lastSlideMessage: 'This is the last review',
                            paginationBulletMessage: 'Go to review {{index}}',
                        },

                        // Keyboard control
                        keyboard: {
                            enabled: true,
                            onlyInViewport: true,
                        },

                        // Touch events
                        grabCursor: true,

                        // Speed
                        speed: 300,

                        // Effect
                        effect: 'slide',

                        // Mousewheel (optional)
                        mousewheel: {
                            forceToAxis: true,
                            sensitivity: 0.5,
                        },
                    });

                    // Update button states initially
                    swiper.update();

                    // Handle window resize
                    let resizeTimeout;
                    window.addEventListener('resize', function() {
                        clearTimeout(resizeTimeout);
                        resizeTimeout = setTimeout(() => {
                            swiper.update();
                        }, 100);
                    });

                    // Add "Read More" functionality for long feedback
                    document.querySelectorAll('.feedback-content-text').forEach(textElement => {
                        const parentCard = textElement.closest('.feedback-card');
                        const cardBody = parentCard.querySelector('.card-body');

                        // Check if text is too long
                        if (textElement.scrollHeight > 120) {
                            textElement.classList.add('expandable');

                            // Create "Read More" button
                            const readMoreBtn = document.createElement('button');
                            readMoreBtn.className = 'read-more-btn mt-2';
                            readMoreBtn.innerHTML = '<i class="bi bi-chevron-down me-1"></i>Read More';

                            readMoreBtn.addEventListener('click', function(e) {
                                e.preventDefault();
                                textElement.classList.toggle('expandable');
                                this.innerHTML = textElement.classList.contains('expandable')
                                    ? '<i class="bi bi-chevron-down me-1"></i>Read More'
                                    : '<i class="bi bi-chevron-up me-1"></i>Read Less';
                            });

                            // Insert button after the text container
                            textElement.parentNode.parentNode.appendChild(readMoreBtn);
                        }
                    });
                }
            });
        </script>
    </body>
</html>
