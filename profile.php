<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>My Profile | Kafe Tiga Belas</title>

  <!-- Same links as other pages -->
  <link href="assets/img/favicon.png" rel="icon">
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/css/main.css" rel="stylesheet">

  <style>
    /* Profile Page Text Visibility Fixes */
    #profile-pic + div h4, /* Username */
    #display-email,
    .card-body p:not(.text-muted),
    .card-body a {
     color: #ffffff !important; /* Force white for main text */
    }

    .card-body .text-muted {
    color: rgba(255, 255, 255, 0.6) !important; /* Lighter gray for labels */
    }

    #change-password-link {
    color: var(--accent-color) !important; /* Gold for link */
    text-decoration: underline;
    }

    #change-password-link:hover {
    color: #ffffff !important;
    }

    .card-body p:contains("••••") { /* Approximate selector */
    color: rgba(255, 255, 255, 0.8) !important;
    font-size: 1.2rem;
    letter-spacing: 4px;
    }
  </style>
</head>

<body class="index-page">

  <main class="main py-5">
    <div class="container">

      <!-- Page Title -->
      <div class="text-center mb-5" data-aos="fade-up">
        <h2 class="display-4 fw-bold" style="color: var(--accent-color);">My Profile</h2>
      </div>

      <div class="row justify-content-center" data-aos="fade-up" data-aos-delay="100">
        <div class="col-lg-8">

          <div class="card mb-4" style="background-color: var(--surface-color); border: none; border-radius: 15px;">
            <div class="card-body text-center py-5">

              <!-- Profile Picture -->
        <div class="text-center mb-4">
        <div class="position-relative d-inline-block">
        <i class="bi bi-person-circle"
       style="font-size: 120px; color: #ffffff;"></i>

        <!-- Optional: Change picture button (camera icon overlay) -->
        <button id="change-pic-btn"
                class="btn btn-sm position-absolute bottom-0 end-0 translate-middle-x"
                style="background: var(--accent-color); border-radius: 50%; width: 40px; height: 40px; border: none;">
            <i class="bi bi-camera-fill text-white"></i>
        </button>
  </div>
</div>

              <!-- Username (Editable) -->
               <p class="text-muted mb-1">Username</p>
              <h4 id="display-username" class="mb-3"></h4>
              <button id="edit-username-btn" class="btn btn-outline-light btn-sm mb-4">Edit Username</button>

              <!-- Email (Editable) -->
              <p class="text-muted mb-1">Email</p>
              <h5 id="display-email"></h5>
              <button id="edit-email-btn" class="btn btn-outline-light btn-sm mb-4">Edit Email</button>

              <!-- Change Password -->
            <div class="mb-4">
            <p class="text-muted mb-1">Password</p>
            <p class="mb-2" style="color: rgba(255,255,255,0.8); font-size: 1.4rem; letter-spacing: 5px;">••••••••</p>
            <a href="#" id="change-password-link" style="color: var(--accent-color);">Change Password</a>
            </div>

            </div>
          </div>

          <!-- Order History Section -->
          <div class="card mb-4" style="background-color: var(--surface-color); border: none; border-radius: 15px;">
            <div class="card-body">
              <h4 class="mb-4" style="color: var(--accent-color);">Order History</h4>
              <div id="order-history">
                <p class="text-muted text-center py-4">No orders yet. Start shopping!</p>
              </div>
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="text-center">
            <button id="logout-btn" class="btn btn-outline-danger me-3 px-5">Log Out</button>
            <button id="delete-account-btn" class="btn btn-danger px-5">Delete Account</button>
          </div>

        </div>
      </div>
    </div>
  </main>

  <!-- Footer (same as other pages) -->

  <!-- Scripts -->
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/aos/aos.js"></script>
  <script src="assets/js/main.js"></script>

  <script>
    // Check if logged in
    //const loggedInUser = localStorage.getItem('loggedInUser');
    //if (!loggedInUser) {
      //window.location.href = 'login.html';
      //return;
    //}

    //const user = JSON.parse(loggedInUser);

    // Display user info
    document.getElementById('display-username').textContent = user.username || user.email.split('@')[0];
    document.getElementById('display-email').textContent = user.email;

    // Edit Username
    document.getElementById('edit-username-btn').addEventListener('click', () => {
      const newUsername = prompt('Enter new username:', user.username || '');
      if (newUsername && newUsername !== user.username) {
        user.username = newUsername;
        localStorage.setItem('loggedInUser', JSON.stringify(user));
        document.getElementById('display-username').textContent = newUsername;
        alert('Username updated!');
      }
    });

    // Edit Email
    document.getElementById('edit-email-btn').addEventListener('click', () => {
      const newEmail = prompt('Enter new email:', user.email);
      if (newEmail && newEmail !== user.email && newEmail.includes('@')) {
        user.email = newEmail;
        localStorage.setItem('loggedInUser', JSON.stringify(user));
        document.getElementById('display-email').textContent = newEmail;
        alert('Email updated!');
      } else if (newEmail && !newEmail.includes('@')) {
        alert('Please enter a valid email.');
      }
    });

    // Change Password Link (placeholder)
    document.getElementById('change-password-link').addEventListener('click', (e) => {
      e.preventDefault();
      alert('Change password feature coming soon!');
    });

    // Logout
    document.getElementById('logout-btn').addEventListener('click', () => {
      if (confirm('Are you sure you want to log out?')) {
        localStorage.removeItem('loggedInUser');
        window.location.href = 'index.html';
      }
    });

    // Delete Account
    document.getElementById('delete-account-btn').addEventListener('click', () => {
      if (confirm('Are you sure you want to delete your account? This cannot be undone.')) {
        // Remove from users list (if you have one)
        let users = JSON.parse(localStorage.getItem('users') || '[]');
        users = users.filter(u => u.email !== user.email);
        localStorage.setItem('users', JSON.stringify(users));

        localStorage.removeItem('loggedInUser');
        alert('Account deleted successfully.');
        window.location.href = 'index.html';
      }
    });

    // load from localStorage or backend
  </script>
</body>
</html>
