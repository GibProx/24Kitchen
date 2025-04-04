<?php
/**
 * Configuration file for 24Kitchen
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include required files
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/validation.php';
require_once __DIR__ . '/../includes/auth.php';

// Site settings
define('SITE_NAME', '24Kitchen');
define('SITE_URL', getBaseUrl());
define('SITE_EMAIL', 'info@24kitchen.com');
define('SITE_PHONE', '+44 123 456 7890');
define('SITE_ADDRESS', '123 Kitchen Street, Birmingham, UK');

// Default meta tags
$meta_title = SITE_NAME;
$meta_description = 'Discover delicious recipes and culinary inspiration at 24Kitchen.';
$meta_keywords = 'recipes, cooking, food, kitchen, culinary';

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Time zone
date_default_timezone_set('Europe/London');

// Default page settings
$page_title = SITE_NAME;
$page_class = '';

// Check if user is logged in
$is_logged_in = isLoggedIn();
$current_user = null;

if ($is_logged_in) {
    // Get current user data
    $current_user = getCurrentUser();
}

// Flash messages
$flash_message = null;
$flash_message_type = null;

if (isset($_SESSION['message']) && isset($_SESSION['message_type'])) {
    $flash_message = $_SESSION['message'];
    $flash_message_type = $_SESSION['message_type'];
    
    // Clear the message after retrieving it
    unset($_SESSION['message']);
    unset($_SESSION['message_type']);
}
?>