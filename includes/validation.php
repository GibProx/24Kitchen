<?php
/**
 * Validation functions for 24Kitchen
 */

/**
 * Validate email address
 * 
 * @param string $email Email address
 * @return bool True if valid, false otherwise
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validate password strength
 * 
 * @param string $password Password
 * @param int $min_length Minimum length
 * @return bool True if valid, false otherwise
 */
function validatePassword($password, $min_length = 8) {
    // Check minimum length
    if (strlen($password) < $min_length) {
        return false;
    }
    
    return true;
}

/**
 * Validate number
 * 
 * @param mixed $number Number to validate
 * @param int $min Minimum value
 * @param int $max Maximum value
 * @return bool True if valid, false otherwise
 */
function validateNumber($number, $min = null, $max = null) {
    // Check if it's a number
    if (!is_numeric($number)) {
        return false;
    }
    
    // Check minimum value
    if ($min !== null && $number < $min) {
        return false;
    }
    
    // Check maximum value
    if ($max !== null && $number > $max) {
        return false;
    }
    
    return true;
}

/**
 * Validate text length
 * 
 * @param string $text Text to validate
 * @param int $min_length Minimum length
 * @param int $max_length Maximum length
 * @return bool True if valid, false otherwise
 */
function validateTextLength($text, $min_length = 1, $max_length = null) {
    $length = strlen($text);
    
    // Check minimum length
    if ($length < $min_length) {
        return false;
    }
    
    // Check maximum length
    if ($max_length !== null && $length > $max_length) {
        return false;
    }
    
    return true;
}

/**
 * Validate file upload
 * 
 * @param array $file File from $_FILES
 * @param array $allowed_types Allowed MIME types
 * @param int $max_size Maximum file size in bytes
 * @return bool|string True if valid, error message otherwise
 */
function validateFileUpload($file, $allowed_types = [], $max_size = 5242880) {
    // Check if file was uploaded
    if ($file['error'] !== UPLOAD_ERR_OK) {
        switch ($file['error']) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                return 'File is too large';
            case UPLOAD_ERR_PARTIAL:
                return 'File was only partially uploaded';
            case UPLOAD_ERR_NO_FILE:
                return 'No file was uploaded';
            default:
                return 'Unknown upload error';
        }
    }
    
    // Check file size
    if ($file['size'] > $max_size) {
        return 'File is too large (maximum ' . formatFileSize($max_size) . ')';
    }
    
    // Check file type
    if (!empty($allowed_types) && !in_array($file['type'], $allowed_types)) {
        return 'File type not allowed';
    }
    
    return true;
}

/**
 * Format file size
 * 
 * @param int $bytes File size in bytes
 * @return string Formatted file size
 */
function formatFileSize($bytes) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $i = 0;
    
    while ($bytes >= 1024 && $i < count($units) - 1) {
        $bytes /= 1024;
        $i++;
    }
    
    return round($bytes, 2) . ' ' . $units[$i];
}
?>