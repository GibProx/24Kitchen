<?php
/**
 * Helper functions for 24Kitchen
 */

/**
 * Get base URL of the website
 * 
 * @return string Base URL
 */
function getBaseUrl() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $script_name = dirname($_SERVER['SCRIPT_NAME']);
    
    // Remove "/pages" from script name if present
    $base_path = str_replace('/pages', '', $script_name);
    
    return $protocol . '://' . $host . $base_path;
}

/**
 * Get resource path
 * 
 * @param string $path Resource path
 * @return string Full resource path
 */
function getResourcePath($path) {
    return getBaseUrl() . '/' . ltrim($path, '/');
}

/**
 * Redirect to a URL
 * 
 * @param string $url URL to redirect to
 * @return void
 */
function redirect($url) {
    header('Location: ' . getResourcePath($url));
    exit;
}

/**
 * Sanitize user input
 * 
 * @param string $input User input
 * @return string Sanitized input
 */
function sanitizeInput($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Truncate text to a specified length
 * 
 * @param string $text Text to truncate
 * @param int $length Maximum length
 * @param string $append String to append if truncated
 * @return string Truncated text
 */
function truncateText($text, $length = 100, $append = '...') {
    if (strlen($text) <= $length) {
        return $text;
    }
    
    $text = substr($text, 0, $length);
    $text = substr($text, 0, strrpos($text, ' '));
    
    return $text . $append;
}

/**
 * Generate a random string
 * 
 * @param int $length Length of the string
 * @return string Random string
 */
function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $string = '';
    
    for ($i = 0; $i < $length; $i++) {
        $string .= $characters[rand(0, strlen($characters) - 1)];
    }
    
    return $string;
}

/**
 * Format date
 * 
 * @param string $date Date string
 * @param string $format Date format
 * @return string Formatted date
 */
function formatDate($date, $format = 'd M Y') {
    return date($format, strtotime($date));
}
?>