<?php
// Load environment variables from .env file
function loadEnv($path = null) {
    if ($path === null) {
        $path = __DIR__ . '/../.env';
    }

    if (!file_exists($path)) {
        // Create from example if doesn't exist
        if (file_exists($path . '.example')) {
            copy($path . '.example', $path);
        } else {
            die('Environment file not found. Please create .env file from .env.example');
        }
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Skip comments
        if (strpos(trim($line), '#') === 0) {
            continue;
        }

        // Parse name=value
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);

        // Remove quotes if present
        if (preg_match('/^"(.*)"$/', $value, $matches)) {
            $value = $matches[1];
        } elseif (preg_match('/^\'(.*)\'$/', $value, $matches)) {
            $value = $matches[1];
        }

        // Set environment variable
        putenv("$name=$value");
        $_ENV[$name] = $value;
        $_SERVER[$name] = $value;
    }
}

// Load environment
loadEnv();

// Define configuration constants
define('STRIPE_PUBLIC_KEY', getenv('STRIPE_PUBLIC_KEY') ?: '');
define('STRIPE_SECRET_KEY', getenv('STRIPE_SECRET_KEY') ?: '');
/* define('DB_HOST', getenv('DB_HOST') ?: 'localhost'); */
/* define('DB_NAME', getenv('DB_NAME') ?: 'tigaBelasCafe'); */
/* define('DB_USER', getenv('DB_USER') ?: 'root'); */
/* define('DB_PASSWORD', getenv('DB_PASSWORD') ?: ''); */
define('APP_ENV', getenv('APP_ENV') ?: 'development');
define('DEBUG_MODE', getenv('DEBUG_MODE') === 'true');

// Email configuration
define('SMTP_HOST', getenv('SMTP_HOST') ?: 'smtp.gmail.com');
define('SMTP_PORT', (int) (getenv('SMTP_PORT') ?: 587));
define('SMTP_USERNAME', getenv('SMTP_USERNAME') ?: '');
define('SMTP_PASSWORD', getenv('SMTP_PASSWORD') ?: '');
define('SMTP_FROM_EMAIL', getenv('SMTP_FROM_EMAIL') ?: 'noreply@tigabelascafe.com');
define('SMTP_FROM_NAME', getenv('SMTP_FROM_NAME') ?: 'Kafe Tiga Belas');

// Website URL for verification links
define('BASE_URL', getenv('BASE_URL') ?: 'http://localhost');

// Token expiration time (24 hours)
define('TOKEN_EXPIRY_HOURS', 24);

// Validate required keys in production
if (APP_ENV === 'production') {
    $required = ['STRIPE_PUBLIC_KEY', 'STRIPE_SECRET_KEY'];
    foreach ($required as $key) {
        if (empty(constant($key))) {
            die("Missing required configuration: $key");
        }
    }
}
?>
