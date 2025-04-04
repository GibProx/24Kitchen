<?php
// Include the config file
require_once '../components/config.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

// Require user to be logged in
requireLogin();

// Set page title
$page_title = 'My Recipes';
$page_class = 'my-recipes-page';
$meta_description = 'Manage your recipes on 24Kitchen.';
$meta_keywords = 'my recipes, cooking, food, 24kitchen';

// Initialize variables
$flash_message = '';
$flash_message_type = '';
$is_logged_in = isLoggedIn();
$current_user = getCurrentUser();

// Get user's recipes
$user_id = $_SESSION['user_id'];

// Connect to database
$conn = getDbConnection();
$stmt = $conn->prepare("SELECT * FROM Recipes WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$recipes = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$conn->close();

// Include the header
include_once '../components/header.php';
?>

<section id="my-recipes" class="section">
    <div class="container">
        <div class="section-title">
            <h1>My Recipes</h1>
            <p>Manage your culinary creations</p>
        </div>
        
        <div class="my-recipes-actions">
            <a href="add-recipe.php" class="btn">Add New Recipe</a>
        </div>
        
        <?php if (empty($recipes)): ?>
        <div class="no-recipes">
            <p>You haven't added any recipes yet. <a href="add-recipe.php">Add your first recipe</a> now!</p>
        </div>
        <?php else: ?>
        <div class="recipes-container">
            <?php foreach ($recipes as $recipe): ?>
            <div class="recipe">
                <?php if (!empty($recipe['image'])): ?>
                <img src="<?php echo getResourcePath($recipe['image']); ?>" alt="<?php echo htmlspecialchars($recipe['name']); ?>" class="recipe-image">
                <?php else: ?>
                <img src="<?php echo getResourcePath('assets/img/pic' . (($recipe['id'] % 3) + 8) . '.webp'); ?>" alt="<?php echo htmlspecialchars($recipe['name']); ?>" class="recipe-image">
                <?php endif; ?>
                
                <div class="recipe-content">
                    <h2><?php echo htmlspecialchars($recipe['name']); ?></h2>
                    <p><?php echo htmlspecialchars(truncateText($recipe['description'], 100)); ?></p>
                    
                    <div class="recipe-meta">
                        <span><i class="ri-fire-line"></i> Cook: <?php echo $recipe['cooking_time']; ?> mins</span>
                        <span><i class="ri-restaurant-line"></i> Type: <?php echo htmlspecialchars($recipe['type']); ?></span>
                    </div>
                    
                    <div class="recipe-actions">
                        <a href="recipes.php?id=<?php echo $recipe['id']; ?>" class="recipe-btn">View</a>
                        <a href="edit-recipe.php?id=<?php echo $recipe['id']; ?>" class="recipe-btn edit-btn">Edit</a>
                        <a href="delete-recipe.php?id=<?php echo $recipe['id']; ?>" class="recipe-btn delete-btn" onclick="return confirm('Are you sure you want to delete this recipe?')">Delete</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php
// Include the footer
include_once '../components/footer.php';
?>