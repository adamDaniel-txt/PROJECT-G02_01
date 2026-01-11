<?php
// app/flash.php
function flash($message = null, $type = 'danger') {
    if ($message) {
        // Set flash message
        $_SESSION['flash'] = $message;
        $_SESSION['flash_type'] = $type;
    } else {
        // Get and display flash message
        if (!empty($_SESSION['flash'])) {
            $type = $_SESSION['flash_type'] ?? 'danger';
            $message = $_SESSION['flash'];

            $html = '
            <!-- Fixed position alert that doesn\'t push content -->
            <div class="alert alert-' . htmlspecialchars($type) . ' alert-dismissible fade show"
                 style="position: fixed; top: 20px; left: 50%; transform: translateX(-50%); z-index: 1050; min-width: 300px; max-width: 500px;">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">' . htmlspecialchars($message) . '</div>
                </div>
            </div>

            <!-- Auto-dismiss script -->
            <script>
                setTimeout(function() {
                    var alert = document.querySelector(\'.alert[style*="position: fixed"]\');
                    if (alert) {
                        var bsAlert = new bootstrap.Alert(alert);
                        bsAlert.close();
                    }
                }, 5000); // Auto close after 5 seconds
            </script>';

            unset($_SESSION['flash'], $_SESSION['flash_type']);
            return $html;
        }
        return '';
    }
}
