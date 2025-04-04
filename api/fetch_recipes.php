<?php
/**
 * API endpoint to fetch recipes
 */

// Include the config file
require_once '../components/config.php';

// Set content type to JSON
header('Content-Type: application/json');

// Connect to the database
$conn = connectDB();

// Get query parameters
$category = isset($_GET['category']) ? sanitizeInput($_GET['category']) : null;
$search = isset($_GET['search']) ? sanitizeInput($_GET['search']) : null;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : null;

// Build the SQL query
$sql = "SELECT * FROM Recipes";
$params = [];
$types = '';

// Add WHERE clause if category or search is provided
if ($category || $search) {
    $sql .= " WHERE ";
    
    if ($category) {
        $sql .= "category = ?";
        $params[] = $category;
        $types .= 's';
    }
    
    if ($category && $search) {
        $sql .= " AND ";
    }
    
    if ($search) {
        $sql .= "(title LIKE ? OR description LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
        $types .= 'ss';
    }
}

// Add ORDER BY clause
$sql .= " ORDER BY id DESC";

// Add LIMIT clause if limit is provided
if ($limit) {
    $sql .= " LIMIT ?";
    $params[] = $limit;
    $types .= 'i';
}

// Execute the query
$result = executeQuery($sql, $params, $types);

// Check if the query returned results
if (is_array($result) && count($result) > 0) {
    // Return the recipes as JSON
    echo json_encode([
        'status' => 'success',
        'data' => $result
    ]);
} else {
    // Return an empty array if no recipes found
    echo json_encode([
        'status' => 'success',
        'data' => []
    ]);
}
?>