<?php
/**
 * API endpoint to submit a recipe rating
 */

// Include the config file
require_once '../components/config.php';

// Set content type to JSON
header('Content-Type: application/json');

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request method'
    ]);
    exit;
}

// Get the form data
$user_id = isset($_POST['user_id']) ? (int)$_POST['user_id'] : null;
$recipe_id = isset($_POST['recipe_id']) ? (int)$_POST['recipe_id'] : null;
$rating = isset($_POST['rating']) ? (int)$_POST['rating'] : null;
$likes = isset($_POST['likes']) ? 1 : 0;

// Validate the form data
$errors = validateForm(
    [
        'user_id' => $user_id,
        'recipe_id' => $recipe_id,
        'rating' => $rating
    ],
    [
        'user_id' => [
            'required' => true,
            'integer' => true
        ],
        'recipe_id' => [
            'required' => true,
            'integer' => true
        ],
        'rating' => [
            'required' => true,
            'integer' => true,
            'min_value' => 1,
            'max_value' => 5
        ]
    ]
);

// If there are validation errors, return them
if (!empty($errors)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Validation failed',
        'errors' => $errors
    ]);
    exit;
}

// Check if the user and recipe exist
$user = getRecord("SELECT id FROM Users WHERE id = ?", [$user_id], 'i');
$recipe = getRecord("SELECT id FROM Recipes WHERE id = ?", [$recipe_id], 'i');

if (!$user) {
    echo json_encode([
        'status' => 'error',
        'message' => 'User not found'
    ]);
    exit;
}

if (!$recipe) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Recipe not found'
    ]);
    exit;
}

// Check if the user has already rated this recipe
$existing_rating = getRecord(
    "SELECT id FROM Ratings WHERE user_id = ? AND recipe_id = ?",
    [$user_id, $recipe_id],
    'ii'
);

if ($existing_rating) {
    // Update the existing rating
    $result = updateRecord(
        'Ratings',
        [
            'rating' => $rating,
            'likes' => $likes,
            'updated_at' => date('Y-m-d H:i:s')
        ],
        'id = ?',
        [$existing_rating['id']],
        'i'
    );
    
    if ($result) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Rating updated successfully'
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to update rating'
        ]);
    }
} else {
    // Insert a new rating
    $result = insertRecord(
        'Ratings',
        [
            'user_id' => $user_id,
            'recipe_id' => $recipe_id,
            'rating' => $rating,
            'likes' => $likes,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]
    );
    
    if ($result) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Rating submitted successfully'
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to submit rating'
        ]);
    }
}
?>