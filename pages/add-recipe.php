<?php
// Include the config file
require_once '../components/config.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

// Require user to be logged in
requireLogin();

// Set page title
$page_title = 'Add Recipe';
$page_class = 'add-recipe-page';
$meta_description = 'Add a new recipe to 24Kitchen.';
$meta_keywords = 'add recipe, cooking, 24kitchen';

// Initialize variables
$flash_message = '';
$flash_message_type = '';
$is_logged_in = isLoggedIn();
$current_user = getCurrentUser();

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
    $image_path = '';
    
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
                // Image uploaded successfully
            } else {
                $errors['image'] = 'Failed to upload image';
            }
        }
    }
    
    // If no errors, insert recipe
    if (empty($errors)) {
        // Connect to database
        $conn = getDbConnection();
        
        // Check if the Recipes table exists with the simplified structure
        $tableCheckResult = $conn->query("SHOW TABLES LIKE 'Recipes'");
        
        if ($tableCheckResult->num_rows === 0) {
            // Create simplified Recipes table if it doesn't exist
            $createTableSQL = "CREATE TABLE Recipes (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                name VARCHAR(100) NOT NULL,
                description TEXT NOT NULL,
                type VARCHAR(50) NOT NULL,
                cooking_time INT NOT NULL,
                ingredients TEXT NOT NULL,
                instructions TEXT NOT NULL,
                image VARCHAR(255),
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES Users(id) ON DELETE CASCADE
            )";
            
            $conn->query($createTableSQL);
        }
        
        // Prepare statement
        $stmt = $conn->prepare("INSERT INTO Recipes (user_id, name, description, type, cooking_time, ingredients, instructions, image, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
        
        // Bind parameters
        $user_id = $_SESSION['user_id'];
        $stmt->bind_param("isssisss", $user_id, $name, $description, $type, $cooking_time, $ingredients, $instructions, $image_path);
        
        // Execute statement
        $success = $stmt->execute();
        $recipe_id = $success ? $conn->insert_id : 0;
        
        // Close statement and connection
        $stmt->close();
        $conn->close();
        
        if ($recipe_id) {
            // Set success message
            $_SESSION['message'] = 'Recipe added successfully!';
            $_SESSION['message_type'] = 'success';
            
            // Redirect to recipe page
            redirect('/pages/recipes.php?id=' . $recipe_id);
            exit;
        } else {
            $errors['general'] = 'Failed to add recipe. Please try again.';
        }
    }
}

// Get flash message if any
if (isset($_SESSION['message'])) {
    $flash_message = $_SESSION['message'];
    $flash_message_type = $_SESSION['message_type'] ?? 'info';
    
    // Clear message
    unset($_SESSION['message']);
    unset($_SESSION['message_type']);
}

// Include the header
include_once '../components/header.php';
?>

<section id="add-recipe" class="section">
    <div class="container">
        <div class="section-title">
            <h1>Add a New Recipe</h1>
            <p>Share your culinary creations with the 24Kitchen community!</p>
        </div>
        
        <?php if (isset($errors['general'])): ?>
        <div class="alert alert-error">
            <?php echo $errors['general']; ?>
        </div>
        <?php endif; ?>
        
        <div class="recipe-form-container">
            <form id="add-recipe-form" class="form-container" method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" enctype="multipart/form-data">
                <div class="form-group <?php echo isset($errors['name']) ? 'has-error' : ''; ?>">
                    <label for="name">Recipe Name</label>
                    <input type="text" id="name" name="name" class="form-control" placeholder="Enter recipe name" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" required>
                    <?php if (isset($errors['name'])): ?>
                    <div class="invalid-feedback"><?php echo $errors['name']; ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="form-group <?php echo isset($errors['description']) ? 'has-error' : ''; ?>">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" class="form-control" rows="3" placeholder="Briefly describe your recipe"><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
                    <?php if (isset($errors['description'])): ?>
                    <div class="invalid-feedback"><?php echo $errors['description']; ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="form-row">
                    <div class="form-group <?php echo isset($errors['type']) ? 'has-error' : ''; ?>">
                        <label for="type">Recipe Type</label>
                        <select id="type" name="type" class="form-control" required>
                            <option value="">Select type</option>
                            <option value="Breakfast" <?php echo (isset($_POST['type']) && $_POST['type'] === 'Breakfast') ? 'selected' : ''; ?>>Breakfast</option>
                            <option value="Lunch" <?php echo (isset($_POST['type']) && $_POST['type'] === 'Lunch') ? 'selected' : ''; ?>>Lunch</option>
                            <option value="Dinner" <?php echo (isset($_POST['type']) && $_POST['type'] === 'Dinner') ? 'selected' : ''; ?>>Dinner</option>
                            <option value="Dessert" <?php echo (isset($_POST['type']) && $_POST['type'] === 'Dessert') ? 'selected' : ''; ?>>Dessert</option>
                            <option value="Snack" <?php echo (isset($_POST['type']) && $_POST['type'] === 'Snack') ? 'selected' : ''; ?>>Snack</option>
                            <option value="Appetizer" <?php echo (isset($_POST['type']) && $_POST['type'] === 'Appetizer') ? 'selected' : ''; ?>>Appetizer</option>
                            <option value="Soup" <?php echo (isset($_POST['type']) && $_POST['type'] === 'Soup') ? 'selected' : ''; ?>>Soup</option>
                            <option value="Salad" <?php echo (isset($_POST['type']) && $_POST['type'] === 'Salad') ? 'selected' : ''; ?>>Salad</option>
                            <option value="Drink" <?php echo (isset($_POST['type']) && $_POST['type'] === 'Drink') ? 'selected' : ''; ?>>Drink</option>
                            <option value="Other" <?php echo (isset($_POST['type']) && $_POST['type'] === 'Other') ? 'selected' : ''; ?>>Other</option>
                        </select>
                        <?php if (isset($errors['type'])): ?>
                        <div class="invalid-feedback"><?php echo $errors['type']; ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group <?php echo isset($errors['cooking_time']) ? 'has-error' : ''; ?>">
                        <label for="cooking_time">Cooking Time (minutes)</label>
                        <input type="number" id="cooking_time" name="cooking_time" class="form-control" min="1" value="<?php echo isset($_POST['cooking_time']) ? (int)$_POST['cooking_time'] : ''; ?>" required>
                        <?php if (isset($errors['cooking_time'])): ?>
                        <div class="invalid-feedback"><?php echo $errors['cooking_time']; ?></div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="form-group <?php echo isset($errors['ingredients']) ? 'has-error' : ''; ?>">
                    <label for="ingredients">Ingredients</label>
                    <textarea id="ingredients" name="ingredients" class="form-control" rows="6" placeholder="Enter ingredients, one per line"><?php echo isset($_POST['ingredients']) ? htmlspecialchars($_POST['ingredients']) : ''; ?></textarea>
                    <small class="form-text">Enter each ingredient on a new line, including quantity and unit (e.g., 2 cups flour)</small>
                    <?php if (isset($errors['ingredients'])): ?>
                    <div class="invalid-feedback"><?php echo $errors['ingredients']; ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="form-group <?php echo isset($errors['instructions']) ? 'has-error' : ''; ?>">
                    <label for="instructions">Instructions</label>
                    <textarea id="instructions" name="instructions" class="form-control" rows="8" placeholder="Enter step-by-step instructions"><?php echo isset($_POST['instructions']) ? htmlspecialchars($_POST['instructions']) : ''; ?></textarea>
                    <small class="form-text">Enter each step on a new line</small>
                    <?php if (isset($errors['instructions'])): ?>
                    <div class="invalid-feedback"><?php echo $errors['instructions']; ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="form-group <?php echo isset($errors['image']) ? 'has-error' : ''; ?>">
                    <label for="image">Recipe Image</label>
                    <input type="file" id="image" name="image" class="form-control" accept="image/jpeg, image/png, image/webp">
                    <small class="form-text">Upload an image of your recipe (JPEG, PNG, or WebP, max 5MB)</small>
                    <?php if (isset($errors['image'])): ?>
                    <div class="invalid-feedback"><?php echo $errors['image']; ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="form-submit">Add Recipe</button>
                    <a href="/pages/recipes.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</section>

<?php
// Include the footer
include_once '../components/footer.php';
?>