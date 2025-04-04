<?php
// Include the config file
require_once '../components/config.php';
require_once '../includes/db.php';
require_once '../includes/auth.php';

// Remove remember token cookie if exists
if (isset($_COOKIE['remember_token'])) {
    // Delete token from database
    list($user_id, $token) = explode(':', $_COOKIE['remember_token']);
    
    // Connect to database
    $conn = getDbConnection();
    
    // Check if RememberTokens table exists
    $tableCheckResult = $conn->query("SHOW TABLES LIKE 'RememberTokens'");
    
    if ($tableCheckResult->num_rows > 0) {
        // RememberTokens table exists, delete token
        $stmt = $conn->prepare("DELETE FROM RememberTokens WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->close();
    }
    
    $conn->close();
    
    // Delete cookie
    setcookie('remember_token', '', time() - 3600, '/', '', false, true);
}

// Unset all session variables
$_SESSION = [];

// Delete the session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Destroy the session
session_destroy();

// Set success message
$_SESSION['message'] = 'You have been logged out successfully.';
$_SESSION['message_type'] = 'success';

// Redirect to home page
redirect('../index.php');
?>