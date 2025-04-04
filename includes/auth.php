<?php
/**
 * Authentication functions for 24Kitchen
 * Simplified version for student projects
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include required files
require_once 'db.php';
require_once 'functions.php'; // Include functions.php to use existing functions

/**
 * Check if user is logged in
 * 
 * @return bool True if logged in, false otherwise
 */
function isLoggedIn() {
    // Check session
    if (isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in'] === true) {
        return true;
    }
    
    // Check remember token
    if (isset($_COOKIE['remember_token'])) {
        list($user_id, $token) = explode(':', $_COOKIE['remember_token']);
        
        $conn = getDbConnection();
        
        // Check if RememberTokens table exists
        $tableCheckResult = $conn->query("SHOW TABLES LIKE 'RememberTokens'");
        
        if ($tableCheckResult->num_rows > 0) {
            // RememberTokens table exists, check token
            $stmt = $conn->prepare("SELECT * FROM RememberTokens WHERE user_id = ? AND expires_at > NOW()");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $token_row = $result->fetch_assoc();
                
                // Verify token
                if (password_verify($token, $token_row['token'])) {
                    // Get user data
                    $stmt = $conn->prepare("SELECT id, first_name, last_name, email, role FROM Users WHERE id = ?");
                    $stmt->bind_param("i", $user_id);
                    $stmt->execute();
                    $user_result = $stmt->get_result();
                    
                    if ($user_result->num_rows > 0) {
                        $user = $user_result->fetch_assoc();
                        
                        // Create user session data
                        $userData = [
                            'id' => $user['id'],
                            'name' => $user['first_name'] . ' ' . $user['last_name'],
                            'email' => $user['email'],
                            'role' => $user['role']
                        ];
                        
                        // Set session variables
                        $_SESSION['user'] = $userData;
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['is_logged_in'] = true;
                        
                        $stmt->close();
                        $conn->close();
                        
                        return true;
                    }
                }
            }
            
            $stmt->close();
        }
        
        $conn->close();
    }
    
    return false;
}

/**
 * Get current user
 * 
 * @return array|null User data or null if not logged in
 */
function getCurrentUser() {
    if (isLoggedIn() && isset($_SESSION['user'])) {
        return $_SESSION['user'];
    }
    
    return null;
}

/**
 * Check if user is admin
 * 
 * @return bool True if admin, false otherwise
 */
function isAdmin() {
    $user = getCurrentUser();
    
    return $user && $user['role'] === 'admin';
}

/**
 * Require user to be logged in
 * Redirects to login page if not logged in
 * 
 * @param string $redirect_url URL to redirect to after login
 * @return void
 */
function requireLogin($redirect_url = '') {
    if (!isLoggedIn()) {
        // Set redirect URL in session if provided
        if (!empty($redirect_url)) {
            $_SESSION['redirect_after_login'] = $redirect_url;
        } else {
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
        }
        
        // Set message
        $_SESSION['message'] = 'Please log in to access this page';
        $_SESSION['message_type'] = 'error';
        
        // Redirect to login page
        header('Location: ' . getResourcePath('pages/login.php'));
        exit;
    }
}

/**
 * Require user to be admin
 * Redirects to home page if not admin
 * 
 * @return void
 */
function requireAdmin() {
    requireLogin();
    
    if (!isAdmin()) {
        // Set message
        $_SESSION['message'] = 'You do not have permission to access this page';
        $_SESSION['message_type'] = 'error';
        
        // Redirect to home page
        header('Location: ' . getResourcePath('index.php'));
        exit;
    }
}

// Note: The following functions have been removed to avoid conflicts with functions.php:
// - generateRandomString()
// - sanitizeInput()
// - validateEmail()
// These functions should be used from functions.php instead.
?>