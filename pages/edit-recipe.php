<?php
// Include the config file
require_once '../components/config.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

// Require user to be logged in
requireLogin();

// Set page title
$page_title = 'Edit Recipe';
$page_class = 'edit-recipe-page';
$meta_description = 'Edit your recipe on 24Kitchen.';
$meta_keywords = 'edit recipe, cooking, food, 24kitchen';

// Initialize variables
$flash_message = '';
$flash_message_type = '';
$is_logged_in = isLoggedIn();
$current_user = getCurrentUser();

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
    
    $_SESSION['message'] = 'Recipe not found or you do not have permission to edit it.';
    $_SESSION['message_type'] = 'error';
    redirect('/pages/my-recipes.php');
    exit;
}

$recipe = $result->fetch_assoc();
$stmt->close();

// Process form submission
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $name = isset($_POST['name']) ? sanitizeInput($_POST['name']) : '';
    $description = isset($_POST['description']) ? sanitizeInput($_POST['description']) : '';
    $type = isset($_POST['type']) ? sanitizeInput($_POST['type']) : '';
    $cooking_time = isset($_POST['cooking_time']) ? (int)$_POST['cooking_time'] : 0;
    $ingredients = isset($_POST['ingredients']) ? sanitizeInput($_POST['ingredients']) : '';
    $instructions = isset($_POST['instructions']) ? sanitizeInput($_POST['instructions']) : '';
    
    // Validate form data
    if (empty($name)) {
        $errors['name'] = 'Recipe name is required';
    } elseif (strlen($name) > 100) {
        $errors['name'] = 'Recipe name must be less than 100 characters';
    }
    
    if (empty($description)) {
        $errors['description'] = 'Recipe description is required';
    }
    
    if (empty($type)) {
        $errors['type'] = 'Recipe type is required';
    }
    
    if ($cooking_time <= 0) {
        $errors['cooking_time'] = 'Cooking time must be greater than 0';
    }
    
    if (empty($ingredients)) {
        $errors['ingredients'] = 'Recipe ingredients are required';
    }
    
    if (empty($instructions)) {
        $errors['instructions'] = 'Recipe instructions are required';
    }
    
    // Handle image upload
    $image_path = $recipe['image']; // Keep existing image by default
    
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/webp'];
        $max_size = 5 * 1024 * 1024; // 5MB
        
        $file_type = $_FILES['image']['type'];
        $file_size = $_FILES['image']['size'];
        $file_tmp = $_FILES['image']['tmp_name'];
        
        // Validate file type
        if (!in_array($file_type, $allowed_types)) {
            $errors['image'] = 'Only JPEG, PNG, and WebP images are allowed';
        }
        
        // Validate file size
        if ($file_size > $max_size) {
            $errors['image'] = 'Image size must be less than 5MB';
        }
        
        // If no errors, upload the image
        if (empty($errors['image'])) {
            $upload_dir = '../assets/img/recipes/';
            
            // Create directory if it doesn't exist
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            // Generate unique filename
            $file_ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $file_name = uniqid('recipe_') . '.' . $file_ext;
            $image_path = 'assets/img/recipes/' . $file_name;
            
            // Move uploaded file
            if (move_uploaded_file($file_tmp, $upload_dir . $file_name)) {
                // Delete old image if it exists
                if (!empty($recipe['image']) && file_exists('../' . $recipe['image'])) {
                    unlink('../' . $recipe['image']);
                }
            } else {
                $errors['image'] = 'Failed to upload image';
            }
        }
    }
    
    // If no errors, update recipe
    if (empty($errors)) {
        $updateStmt = $conn->prepare("UPDATE Recipes SET name = ?, description = ?, type = ?, cooking_time = ?, ingredients = ?, instructions = ?, image = ? WHERE id = ? AND user_id = ?");
        $updateStmt->bind_param("sssisssii", $name, $description, $type, $cooking_time, $ingredients, $instructions, $image_path, $recipe_id, $user_id);
        $success = $updateStmt->execute();
        $updateStmt->close();
        
        if ($success) {
            // Set success message
            $_SESSION['message'] = 'Recipe updated successfully!';
            $_SESSION['message_type'] = 'success';
            
            // Redirect to recipe page
            $conn->close();
            redirect('/pages/recipes.php?id=' . $recipe_id);
            exit;
        } else {
            $errors['general'] = 'Failed to update recipe. Please try again.';
        }
    }
}

$conn->close();

// Include the header
include_once '../components/header.php';
?>

<section id="edit-recipe" class="section">
    <div class="container">
        <div class="section-title">
            <h1>Edit Recipe</h1>
            <p>Update your culinary creation</p>
        </div>
        
        <?php if (isset($errors['general'])): ?>
        <div class="alert alert-error">
            <?php echo $errors['general']; ?>
        </div>
        <?php endif; ?>
        
        <div class="recipe-form-container">
            <form id="edit-recipe-form" class="form-container" method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'] . '?id=' . $recipe_id); ?>" enctype="multipart/form-data">
                <div class="form-group <?php echo isset($errors['name']) ? 'has-error' : ''; ?>">
                    <label for="name">Recipe Name</label>
                    <input type="text" id="name" name="name" class="form-control" placeholder="Enter recipe name" value="<?php echo htmlspecialchars($recipe['name']); ?>" required>
                    <?php if (isset($errors['name'])): ?>
                    <div class="invalid-feedback"><?php echo $errors['name']; ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="form-group <?php echo isset($errors['description']) ? 'has-error' : ''; ?>">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" class="form-control" rows="3" placeholder="Briefly describe your recipe"><?php echo htmlspecialchars($recipe['description']); ?></textarea>
                    <?php if (isset($errors['description'])): ?>
                    <div class="invalid-feedback"><?php echo $errors['description']; ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="form-row">
                    <div class="form-group <?php echo isset($errors['type']) ? 'has-error' : ''; ?>">
                        <label for="type">Recipe Type</label>
                        <select id="type" name="type" class="form-control" required>
                            <option value="">Select type</option>
                            <option value="Breakfast" <?php echo ($recipe['type'] === 'Breakfast') ? 'selected' : ''; ?>>Breakfast</option>
                            <option value="Lunch" <?php echo ($recipe['type'] === 'Lunch') ? 'selected' : ''; ?>>Lunch</option>
                            <option value="Dinner" <?php echo ($recipe['type'] === 'Dinner') ? 'selected' : ''; ?>>Dinner</option>
                            <option value="Dessert" <?php echo ($recipe['type'] === 'Dessert') ? 'selected' : ''; ?>>Dessert</option>
                            <option value="Snack" <?php echo ($recipe['type'] === 'Snack') ? 'selected' : ''; ?>>Snack</option>
                            <option value="Appetizer" <?php echo ($recipe['type'] === 'Appetizer') ? 'selected' : ''; ?>>Appetizer</option>
                            <option value="Soup" <?php echo ($recipe['type'] === 'Soup') ? 'selected' : ''; ?>>Soup</option>
                            <option value="Salad" <?php echo ($recipe['type'] === 'Salad') ? 'selected' : ''; ?>>Salad</option>
                            <option value="Drink" <?php echo ($recipe['type'] === 'Drink') ? 'selected' : ''; ?>>Drink</option>
                            <option value="Other" <?php echo ($recipe['type'] === 'Other') ? 'selected' : ''; ?>>Other</option>
                        </select>
                        <?php if (isset($errors['type'])): ?>
                        <div class="invalid-feedback"><?php echo $errors['type']; ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group <?php echo isset($errors['cooking_time']) ? 'has-error' : ''; ?>">
                        <label for="cooking_time">Cooking Time (minutes)</label>
                        <input type="number" id="cooking_time" name="cooking_time" class="form-control" min="1" value="<?php echo (int)$recipe['cooking_time']; ?>" required>
                        <?php if (isset($errors['cooking_time'])): ?>
                        <div class="invalid-feedback"><?php echo $errors['cooking_time']; ?></div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="form-group <?php echo isset($errors['ingredients']) ? 'has-error' : ''; ?>">
                    <label for="ingredients">Ingredients</label>
                    <textarea id="ingredients" name="ingredients" class="form-control" rows="6" placeholder="Enter ingredients, one per line"><?php echo htmlspecialchars($recipe['ingredients']); ?></textarea>
                    <small class="form-text">Enter each ingredient on a new line, including quantity and unit (e.g., 2 cups flour)</small>
                    <?php if (isset($errors['ingredients'])): ?>
                    <div class="invalid-feedback"><?php echo $errors['ingredients']; ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="form-group <?php echo isset($errors['instructions']) ? 'has-error' : ''; ?>">
                    <label for="instructions">Instructions</label>
                    <textarea id="instructions" name="instructions" class="form-control" rows="8" placeholder="Enter step-by-step instructions"><?php echo htmlspecialchars($recipe['instructions']); ?></textarea>
                    <small class="form-text">Enter each step on a new line</small>
                    <?php if (isset($errors['instructions'])): ?>
                    <div class="invalid-feedback"><?php echo $errors['instructions']; ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="form-group <?php echo isset($errors['image']) ? 'has-error' : ''; ?>">
                    <label for="image">Recipe Image</label>
                    <?php if (!empty($recipe['image'])): ?>
                    <div class="current-image">
                        <img src="<?php echo getResourcePath($recipe['image']); ?>" alt="Current recipe image" style="max-width: 200px; margin-bottom: 10px;">
                        <p>Current image</p>
                    </div>
                    <?php endif; ?>
                    <input type="file" id="image" name="image" class="form-control" accept="image/jpeg, image/png, image/webp">
                    <small class="form-text">Upload a new image to replace the current one (JPEG, PNG, or WebP, max 5MB)</small>
                    <?php if (isset($errors['image'])): ?>
                    <div class="invalid-feedback"><?php echo $errors['image']; ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="form-submit">Update Recipe</button>
                    <a href="/pages/my-recipes.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</section>

<?php
// Include the footer
include_once '../components/footer.php';
?>