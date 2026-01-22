<?php
session_start();
require 'app/db.php';
require 'app/persmission.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

// Fetch user data
$stmt = $pdo->prepare('SELECT username, email, role_id FROM users WHERE id = ?');
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header('Location: login.php');
    exit();
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update Username
    if (isset($_POST['update_username'])) {
        $new_username = trim($_POST['username']);

        if (empty($new_username)) {
            $error = 'Username cannot be empty';
        } elseif ($new_username === $user['username']) {
            $error = 'New username is the same as current username';
        } else {
            // Check if username already exists
            $check_stmt = $pdo->prepare('SELECT id FROM users WHERE username = ? AND id != ?');
            $check_stmt->execute([$new_username, $user_id]);

            if ($check_stmt->fetch()) {
                $error = 'Username already taken';
            } else {
                $update_stmt = $pdo->prepare('UPDATE users SET username = ? WHERE id = ?');
                if ($update_stmt->execute([$new_username, $user_id])) {
                    $success = 'Username updated successfully';
                    $user['username'] = $new_username; // Update displayed username
                } else {
                    $error = 'Failed to update username';
                }
            }
        }
    }

// Handle Profile Picture Upload
if (isset($_FILES['profile_image'])) {
    $file = $_FILES['profile_image'];
    $allowed_ext = ['jpg', 'jpeg', 'png'];
    $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (in_array($file_ext, $allowed_ext)) {
        if ($file['error'] === 0) {
            if ($file['size'] < 2000000) { // 2MB Limit
                // Create unique name to prevent overwriting
                $new_file_name = "profile_" . $user_id . "_" . time() . "." . $file_ext;
                $file_destination = 'assets/img/profile_picture/' . $new_file_name;

                if (move_uploaded_file($file['tmp_name'], $file_destination)) {
                    // Update Database
                    $update_pic = $pdo->prepare('UPDATE users SET profile_pic = ? WHERE id = ?');
                    if ($update_pic->execute([$new_file_name, $user_id])) {
                        $success = 'Profile picture updated!';
                        // Optional: Delete old photo here if it's not the default
                    }
                } else {
                    $error = 'Failed to move uploaded file.';
                }
            } else {
                $error = 'File is too large (Max 2MB).';
            }
        } else {
            $error = 'There was an error uploading your file.';
        }
    } else {
        $error = 'Invalid file type. Only JPG, JPEG, and PNG allowed.';
    }
}

// Refresh user data to get the latest profile_pic
$stmt = $pdo->prepare('SELECT username, email, role_id, profile_pic FROM users WHERE id = ?');
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Update Email
    if (isset($_POST['update_email'])) {
        $new_email = trim($_POST['email']);

        if (empty($new_email)) {
            $error = 'Email cannot be empty';
        } elseif (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Please enter a valid email address';
        } elseif ($new_email === $user['email']) {
            $error = 'New email is the same as current email';
        } else {
            // Check if email already exists
            $check_stmt = $pdo->prepare('SELECT id FROM users WHERE email = ? AND id != ?');
            $check_stmt->execute([$new_email, $user_id]);

            if ($check_stmt->fetch()) {
                $error = 'Email already registered';
            } else {
                $update_stmt = $pdo->prepare('UPDATE users SET email = ? WHERE id = ?');
                if ($update_stmt->execute([$new_email, $user_id])) {
                    $success = 'Email updated successfully';
                    $user['email'] = $new_email; // Update displayed email
                } else {
                    $error = 'Failed to update email';
                }
            }
        }
    }

    // Change Password
    elseif (isset($_POST['change_password'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        // Verify current password
        $current_hash = hash('sha256', $current_password);
        $check_stmt = $pdo->prepare('SELECT password FROM users WHERE id = ?');
        $check_stmt->execute([$user_id]);
        $db_password = $check_stmt->fetchColumn();

        if ($current_hash !== $db_password) {
            $error = 'Current password is incorrect';
        } elseif (strlen($new_password) < 8) {
            $error = 'New password must be at least 8 characters';
        } elseif ($new_password !== $confirm_password) {
            $error = 'New passwords do not match';
        } else {
            $new_hash = hash('sha256', $new_password);
            $update_stmt = $pdo->prepare('UPDATE users SET password = ? WHERE id = ?');
            if ($update_stmt->execute([$new_hash, $user_id])) {
                $success = 'Password changed successfully';
            } else {
                $error = 'Failed to change password';
            }
        }
    }

    // Delete Account
    elseif (isset($_POST['delete_account'])) {
        $confirm_password = $_POST['confirm_password'] ?? '';

        if (empty($confirm_password)) {
            $error = 'Please enter your password to confirm account deletion';
        } else {
            // Verify password
            $check_stmt = $pdo->prepare('SELECT password FROM users WHERE id = ?');
            $check_stmt->execute([$user_id]);
            $db_password = $check_stmt->fetchColumn();

            $input_hash = hash('sha256', $confirm_password);

            if ($input_hash === $db_password) {
                // Delete user
                $delete_stmt = $pdo->prepare('DELETE FROM users WHERE id = ?');
                if ($delete_stmt->execute([$user_id])) {
                    session_destroy();
                    header('Location: index.php?deleted=1');
                    exit();
                } else {
                    $error = 'Failed to delete account. Please try again.';
                }
            } else {
                $error = 'Incorrect password. Please try again.';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>My Profile | Kafe Tiga Belas</title>

  <link href="assets/img/favicon.png" rel="icon">
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/css/main.css" rel="stylesheet">

  <style>
    /* Profile Page Text Visibility Fixes */
    #profile-pic + div h4,
    #display-email,
    .card-body p:not(.text-muted),
    .card-body a {
     color: #ffffff !important;
    }

    .card-body .text-muted {
    color: rgba(255, 255, 255, 0.6) !important;
    }

    #change-password-link {
    color: var(--accent-color) !important;
    text-decoration: underline;
    }

    #change-password-link:hover {
    color: #ffffff !important;
    }

    .password-display {
        color: rgba(255, 255, 255, 0.8) !important;
        font-size: 1.2rem;
        letter-spacing: 4px;
    }

    /* Alert styles */
    .alert {
        border-radius: 10px;
        border: none;
        margin-bottom: 20px;
    }

    .alert-success {
        background-color: rgba(40, 167, 69, 0.2);
        color: #28a745;
    }

    .alert-danger {
        background-color: rgba(220, 53, 69, 0.2);
        color: #dc3545;
    }

    /* Form input styles */
    .form-control {
        background-color: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        color: white;
    }

    .form-control:focus {
        background-color: rgba(255, 255, 255, 0.15);
        border-color: var(--accent-color);
        color: white;
        box-shadow: 0 0 0 0.25rem rgba(205, 164, 94, 0.25);
    }

    .form-control::placeholder {
        color: rgba(255, 255, 255, 0.5);
    }

    /* Modal styles */
    .modal-content {
        background-color: var(--surface-color);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .btn-close-white {
        filter: invert(1) grayscale(100%) brightness(200%);
    }
  </style>
</head>

<body class="index-page">

  <main class="main py-5">
    <div class="container">

      <!-- Page Title -->
      <div class="text-center mb-5" data-aos="fade-up">

        <h2 class="display-4 fw-bold" style="color: var(--accent-color);">
            <a href="index.php"><i class="bi bi-arrow-left-circle-fill"></i></a>
                My Profile
        </h2>
      </div>

      <!-- Error and Success Messages -->
      <?php if ($error): ?>
        <div class="alert alert-danger text-center"><?php echo htmlspecialchars($error); ?></div>
      <?php endif; ?>

      <?php if ($success): ?>
        <div class="alert alert-success text-center"><?php echo htmlspecialchars($success); ?></div>
      <?php endif; ?>

      <div class="row justify-content-center" data-aos="fade-up" data-aos-delay="100">
        <div class="col-lg-8">

          <div class="card mb-4" style="background-color: var(--surface-color); border: none; border-radius: 15px;">
            <div class="card-body text-center py-5">

      <!-- Profile Picture -->
   <div class="text-center mb-4">
     <div class="position-relative d-inline-block">
         <?php if (!empty($user['profile_pic'])): ?>
             <img src="assets/img/profile_picture/<?php echo $user['profile_pic']; ?>" 
                  style="width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 3px solid var(--accent-color);">
         <?php else: ?>
             <i class="bi bi-person-circle" style="font-size: 120px; color: #ffffff;"></i>
         <?php endif; ?>

         <form id="profile-pic-form" method="POST" enctype="multipart/form-data">
             <input type="file" id="profile_image_input" name="profile_image" accept="image/*" style="display: none;" onchange="document.getElementById('profile-pic-form').submit();">
             <button type="button" onclick="document.getElementById('profile_image_input').click();" 
                     class="btn btn-sm position-absolute bottom-0 end-0 translate-middle-x"
                     style="background: var(--accent-color); border-radius: 50%; width: 40px; height: 40px; border: none;">
                 <i class="bi bi-camera-fill text-white"></i>
             </button>
         </form>
     </div>
 </div>

            
              <!-- Username (Editable) -->
              <form method="POST" action="" class="mb-4">
                <p class="text-muted mb-1">Username</p>
                <h4 id="display-username" class="mb-2"><?php echo htmlspecialchars($user['username']); ?></h4>

                <div class="row justify-content-center">
                  <div class="col-md-6">
                    <div class="input-group mb-2">
                      <input type="text" class="form-control" name="username"
                             placeholder="New username" value="<?php echo htmlspecialchars($user['username']); ?>">
                    </div>
                    <button type="submit" name="update_username" class="btn btn-outline-light btn-sm w-100">Update Username</button>
                  </div>
                </div>
              </form>

              <!-- Email (Editable) -->
              <form method="POST" action="" class="mb-4">
                <p class="text-muted mb-1">Email</p>
                <h5 id="display-email" class="mb-2"><?php echo htmlspecialchars($user['email']); ?></h5>

                <div class="row justify-content-center">
                  <div class="col-md-6">
                    <div class="input-group mb-2">
                      <input type="email" class="form-control" name="email"
                             placeholder="New email" value="<?php echo htmlspecialchars($user['email']); ?>">
                    </div>
                    <button type="submit" name="update_email" class="btn btn-outline-light btn-sm w-100">Update Email</button>
                  </div>
                </div>
              </form>

              <!-- Change Password Form (Initially Hidden) -->
              <div id="password-form" style="display: none;">
                <form method="POST" action="" class="mb-4">
                  <p class="text-muted mb-1">Change Password</p>

                  <div class="row justify-content-center">
                    <div class="col-md-6">
                      <div class="mb-2">
                        <input type="password" class="form-control" name="current_password"
                               placeholder="Current password" required>
                      </div>
                      <div class="mb-2">
                        <input type="password" class="form-control" name="new_password"
                               placeholder="New password (min 8 characters)" required>
                      </div>
                      <div class="mb-2">
                        <input type="password" class="form-control" name="confirm_password"
                               placeholder="Confirm new password" required>
                      </div>
                      <button type="submit" name="change_password" class="btn btn-outline-light btn-sm w-100">Change Password</button>
                      <button type="button" id="cancel-password-btn" class="btn btn-outline-secondary btn-sm w-100 mt-2">Cancel</button>
                    </div>
                  </div>
                </form>
              </div>

              <!-- Change Password Link (Visible by default) -->
              <div id="password-link" class="mb-4">
                <p class="text-muted mb-1">Password</p>
                <p class="password-display mb-2">••••••••</p>
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
            <a href="logout.php" class="btn btn-outline-danger me-3 px-5">Log Out</a>
            <button id="delete-account-btn" class="btn btn-danger px-5" data-bs-toggle="modal" data-bs-target="#deleteModal">Delete Account</button>
          </div>

        </div>
      </div>
    </div>
  </main>

  <!-- Delete Account Modal -->
  <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content" style="background-color: var(--surface-color);">
        <div class="modal-header border-0">
          <h5 class="modal-title" style="color: var(--accent-color);">Delete Account</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p class="text-white">Are you sure you want to delete your account? This action cannot be undone.</p>
          <form method="POST" action="">
            <div class="mb-3">
              <input type="password" class="form-control" name="confirm_password" placeholder="Enter your password to confirm" required>
            </div>
            <div class="text-center">
              <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Cancel</button>
              <button type="submit" name="delete_account" class="btn btn-danger">Delete Account</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Scripts -->
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/aos/aos.js"></script>
  <script src="assets/js/main.js"></script>

  <script>
    // Toggle password change form
    document.getElementById('change-password-link').addEventListener('click', function(e) {
      e.preventDefault();
      document.getElementById('password-link').style.display = 'none';
      document.getElementById('password-form').style.display = 'block';
    });

    document.getElementById('cancel-password-btn').addEventListener('click', function() {
      document.getElementById('password-form').style.display = 'none';
      document.getElementById('password-link').style.display = 'block';
      // Clear password fields
      document.querySelectorAll('#password-form input').forEach(input => input.value = '');
    });

    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
      const alerts = document.querySelectorAll('.alert');
      alerts.forEach(alert => {
        alert.style.transition = 'opacity 0.5s';
        alert.style.opacity = '0';
        setTimeout(() => alert.remove(), 500);
      });
    }, 5000);

    // Clear error messages when closing modal
    document.getElementById('deleteModal').addEventListener('hidden.bs.modal', function() {
      document.querySelector('#deleteModal input[name="confirm_password"]').value = '';
    });
  </script>
</body>
</html>
