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
              <li><a href="menu.html">Menu</a></li>
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
                    <?php endif; ?>
                    <a class="dropdown-item" href="logout.php"><i class="bi bi-box-arrow-left"></i>&nbsp&nbspLog Out</a>
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
              <a href="#menu" class="cta-btn">Order Now!</a>
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
            <h3>Kafe Tiga Belas</h3>
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
    <!--<section id="Reviews" class="Reviews section">-->

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
          <div class="swiper-wrapper">

            <div class="swiper-slide">
              <div class="testimonial-item" ="">
            <p>
              <i class=" bi bi-quote quote-icon-left"></i>
                <span>Proin iaculis purus consequat sem cure digni ssim donec porttitora entum suscipit rhoncus. Accusantium quam, ultricies eget id, aliquam eget nibh et. Maecen aliquam, risus at semper.</span>
                <i class="bi bi-quote quote-icon-right"></i>
                </p>
                <img src="assets/img/testimonials/testimonials-1.jpg" class="testimonial-img" alt="">
                <h3>Saul Goodman</h3>
                <h4>Ceo &amp; Founder</h4>
              </div>
            </div><!-- End testimonial item -->

            <div class="swiper-slide">
              <div class="testimonial-item">
                <p>
                  <i class="bi bi-quote quote-icon-left"></i>
                  <span>Export tempor illum tamen malis malis eram quae irure esse labore quem cillum quid malis quorum velit fore eram velit sunt aliqua noster fugiat irure amet legam anim culpa.</span>
                  <i class="bi bi-quote quote-icon-right"></i>
                </p>
                <img src="assets/img/testimonials/testimonials-2.jpg" class="testimonial-img" alt="">
                <h3>Sara Wilsson</h3>
                <h4>Designer</h4>
              </div>
            </div><!-- End testimonial item -->

            <div class="swiper-slide">
              <div class="testimonial-item">
                <p>
                  <i class="bi bi-quote quote-icon-left"></i>
                  <span>Enim nisi quem export duis labore cillum quae magna enim sint quorum nulla quem veniam duis minim tempor labore quem eram duis noster aute amet eram fore quis sint minim.</span>
                  <i class="bi bi-quote quote-icon-right"></i>
                </p>
                <img src="assets/img/testimonials/testimonials-3.jpg" class="testimonial-img" alt="">
                <h3>Jena Karlis</h3>
                <h4>Store Owner</h4>
              </div>
            </div><!-- End testimonial item -->

            <div class="swiper-slide">
              <div class="testimonial-item">
                <p>
                  <i class="bi bi-quote quote-icon-left"></i>
                  <span>Fugiat enim eram quae cillum dolore dolor amet nulla culpa multos export minim fugiat dolor enim duis veniam ipsum anim magna sunt elit fore quem dolore labore illum veniam.</span>
                  <i class="bi bi-quote quote-icon-right"></i>
                </p>
                <img src="assets/img/testimonials/testimonials-4.jpg" class="testimonial-img" alt="">
                <h3>Matt Brandon</h3>
                <h4>Freelancer</h4>
              </div>
            </div><!-- End testimonial item -->

            <div class="swiper-slide">
              <div class="testimonial-item">
                <p>
                  <i class="bi bi-quote quote-icon-left"></i>
                  <span>Quis quorum aliqua sint quem legam fore sunt eram irure aliqua veniam tempor noster veniam sunt culpa nulla illum cillum fugiat legam esse veniam culpa fore nisi cillum quid.</span>
                  <i class="bi bi-quote quote-icon-right"></i>
                </p>
                <img src="assets/img/testimonials/testimonials-5.jpg" class="testimonial-img" alt="">
                <h3>John Larson</h3>
                <h4>Entrepreneur</h4>
              </div>
            </div><!-- End testimonial item -->

          </div>
          <div class="swiper-pagination"></div>
        </div>

      </div>

    </section><!-- /Testimonials Section -->

    <!-- Give Feedback Section -->
    <section id="give-feedback" class="give-feedback section">

      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <h2>Feedback</h2>
        <p>Leave a message for us to improve our food and services!</p>
      </div><!-- End Section Title -->

      <div class="container col-12" data-aos="fade-up" data-aos-delay="100">

        <!-- Success/Error Messages -->
        <?php if (!empty($message)): ?>
            <div class="alert alert-<?php echo $message_type === 'success' ? 'success' : 'danger'; ?> alert-custom alert-dismissible fade show" role="alert">
                <i class="bi <?php echo $message_type === 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill'; ?> me-2"></i>
                <?php echo htmlspecialchars($message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Give Feedback Form -->
        <form method="POST" action="" novalidate>
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
                    <textarea class="form-control border-coffee"
                              id="feedback_text"
                              name="feedback"
                              placeholder="Share your thoughts..."
                              style="height: 120px"
                              required><?php echo htmlspecialchars($_POST['feedback'] ?? ''); ?></textarea>
                    <label for="feedback_text" class="text-muted">
                        <i class="bi bi-chat-text me-1"></i>Your Feedback
                    </label>
                    <br>
                </div>
            </div>

            <div class="col-12">
                <div class="d-grid d-md-flex justify-content-md-end">
                    <button type="reset" class="btn btn-outline-secondary me-md-2 border-coffee">
                        <i class="bi bi-arrow-clockwise me-2"></i>Clear
                    </button>
                    <button type="submit" name="submit_feedback" class="btn coffee-primary px-4" style="color: white; border-color: #cda45e;">
                        <i class="bi bi-send-check me-2"></i>Submit Feedback
                    </button>
                </div>
            </div>
        </form>
      </div>

    </section><!-- /feedback section -->

  <!-- /reviews section -->

    <!-- contact section -->
    <section id="contact" class="contact section">

      <!-- section title -->
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

</body>

</html>
