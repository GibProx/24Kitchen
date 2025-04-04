<?php
// Include the config file
require_once '../components/config.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

// Require user to be logged in
requireLogin();

// Get recipe ID
$recipe_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Connect to database
$conn = getDbConnection();

// Get recipe details
$stmt = $conn->prepare("SELECT * FROM Recipes WHERE id = ? AND user_id = ?");
$user_id = $_SESSION['user_id'];
$stmt->bind_param("ii", $recipe_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

// If recipe not found or doesn't belong to user, redirect
if ($result->num_rows === 0) {
    $stmt->close();
    $conn->close();
    
    $_SESSION['message'] = 'Recipe not found or you do not have permission to delete it.';
    $_SESSION['message_type'] = 'error';
    redirect('/pages/my-recipes.php');
    exit;
}

$recipe = $result->fetch_assoc();
$stmt->close();

// Delete recipe
$deleteStmt = $conn->prepare("DELETE FROM Recipes WHERE id = ? AND user_id = ?");
$deleteStmt->bind_param("ii", $recipe_id, $user_id);
$success = $deleteStmt->execute();
$deleteStmt->close();
$conn->close();

if ($success) {
    // Delete recipe image if it exists
    if (!empty($recipe['image']) && file_exists('../' . $recipe['image'])) {
        unlink('../' . $recipe['image']);
    }
    
    // Set success message
    $_SESSION['message'] = 'Recipe deleted successfully!';
    $_SESSION['message_type'] = 'success';
} else {
    // Set error message
    $_SESSION['message'] = 'Failed to delete recipe. Please try again.';
    $_SESSION['message_type'] = 'error';
}

// Redirect to my recipes page
redirect('/pages/my-recipes.php');
?>