<?php
// Email configuration
define('SMTP_HOST', 'smtp.gmail.com'); // Change to your SMTP server
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'your-email@gmail.com'); // Your email address
define('SMTP_PASSWORD', 'your-app-password'); // App password, not regular password
define('SMTP_FROM_EMAIL', 'your-email@gmail.com');
define('SMTP_FROM_NAME', 'TigaBelas Cafe');

// Website URL for verification links
define('BASE_URL', 'http://localhost/tigaBelasCafe'); // Change to your actual URL

// Token expiration time (24 hours)
define('TOKEN_EXPIRY_HOURS', 24);
