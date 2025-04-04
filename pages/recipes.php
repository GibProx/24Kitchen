<?php
// Include the config file
require_once '../components/config.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

// Set page title
$page_title = 'Recipes';
$page_class = 'recipes-page';
$meta_description = 'Discover delicious recipes from 24Kitchen.';
$meta_keywords = 'recipes, cooking, food, 24kitchen';

// Initialize variables
$flash_message = '';
$flash_message_type = '';
$is_logged_in = isLoggedIn();
$current_user = getCurrentUser();

// Check if a specific recipe ID is requested
$recipe_id = isset($_GET['id']) ? (int)$_GET['id'] : null;

// Include the header
include_once '../components/header.php';

// If a specific recipe is requested, show the single recipe page
if ($recipe_id) {
    try {
        // Connect to database
        $conn = getDbConnection();
        
        if (!$conn) {
            throw new Exception("Database connection failed");
        }
        
        // Get the recipe details
        $stmt = $conn->prepare("SELECT * FROM Recipes WHERE id = ?");
        if (!$stmt) {
            throw new Exception("Prepare statement failed: " . $conn->error);
        }
        
        $stmt->bind_param("i", $recipe_id);
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }
        
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            // Recipe not found, redirect to recipes page
            $stmt->close();
            $conn->close();
            redirect('recipes.php');
            exit;
        }
        
        $recipe = $result->fetch_assoc();
        $stmt->close();
        
        // Get recipe ratings if Ratings table exists
        $avg_rating = 0;
        $total_ratings = 0;
        $has_ratings_table = false;
        
        $tableCheckResult = $conn->query("SHOW TABLES LIKE 'Ratings'");
        
        if ($tableCheckResult && $tableCheckResult->num_rows > 0) {
            $has_ratings_table = true;
            $ratingStmt = $conn->prepare("SELECT AVG(rating) as avg_rating, COUNT(*) as total_ratings FROM Ratings WHERE recipe_id = ?");
            
            if ($ratingStmt) {
                $ratingStmt->bind_param("i", $recipe_id);
                $ratingStmt->execute();
                $ratingResult = $ratingStmt->get_result();
                
                if ($ratingResult && $ratingResult->num_rows > 0) {
                    $ratingData = $ratingResult->fetch_assoc();
                    $avg_rating = $ratingData['avg_rating'] ?? 0;
                    $total_ratings = $ratingData['total_ratings'] ?? 0;
                }
                
                $ratingStmt->close();
            }
        }
        
        $conn->close();
        ?>

        <section id="single-recipe" class="section">
            <div class="container">
                <?php if (isset($_SESSION['message']) && isset($_SESSION['message_type'])): ?>
                <div class="alert alert-<?php echo htmlspecialchars($_SESSION['message_type']); ?>">
                    <?php 
                    echo htmlspecialchars($_SESSION['message']); 
                    unset($_SESSION['message']);
                    unset($_SESSION['message_type']);
                    ?>
                </div>
                <?php endif; ?>
                
                <div class="single-recipe">
                    <div class="single-recipe-header">
                        <h1><?php echo htmlspecialchars($recipe['name']); ?></h1>
                        <p><?php echo htmlspecialchars($recipe['description']); ?></p>
                    </div>
                    
                    <?php if (!empty($recipe['image']) && file_exists('../' . $recipe['image'])): ?>
                    <img src="<?php echo getResourcePath($recipe['image']); ?>" alt="<?php echo htmlspecialchars($recipe['name']); ?>" class="single-recipe-image">
                    <?php else: ?>
                    <img src="<?php echo getResourcePath('assets/img/pic' . (($recipe_id % 3) + 8) . '.webp'); ?>" alt="<?php echo htmlspecialchars($recipe['name']); ?>" class="single-recipe-image">
                    <?php endif; ?>
                    
                    <div class="single-recipe-meta">
                        <span><i class="ri-fire-line"></i> Cook: <?php echo (int)$recipe['cooking_time']; ?> mins</span>
                        <span><i class="ri-restaurant-line"></i> Type: <?php echo htmlspecialchars($recipe['type']); ?></span>
                        <?php if ($has_ratings_table && $total_ratings > 0): ?>
                        <span><i class="ri-star-fill"></i> Rating: <?php echo number_format($avg_rating, 1); ?> (<?php echo $total_ratings; ?> reviews)</span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="single-recipe-section">
                        <h3>Ingredients</h3>
                        <ul>
                            <?php
                            // Split ingredients by new line
                            $ingredients = explode("\n", $recipe['ingredients']);
                            
                            foreach ($ingredients as $ingredient) {
                                $ingredient = trim($ingredient);
                                if (!empty($ingredient)) {
                                    echo "<li>" . htmlspecialchars($ingredient) . "</li>";
                                }
                            }
                            ?>
                        </ul>
                    </div>
                    
                    <div class="single-recipe-section">
                        <h3>Instructions</h3>
                        <ol>
                            <?php
                            // Split instructions by new line
                            $instructions = explode("\n", $recipe['instructions']);
                            
                            foreach ($instructions as $instruction) {
                                $instruction = trim($instruction);
                                if (!empty($instruction)) {
                                    echo "<li>" . htmlspecialchars($instruction) . "</li>";
                                }
                            }
                            ?>
                        </ol>
                    </div>
                    
                    <?php if ($is_logged_in && $has_ratings_table): ?>
                    <div class="single-recipe-section">
                        <h3>Rate this Recipe</h3>
                        <div class="rating-form">
                            <form id="rating-form-<?php echo $recipe_id; ?>" method="POST" action="rate-recipe.php">
                                <input type="hidden" name="recipe_id" value="<?php echo $recipe_id; ?>">
                                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                
                                <div class="form-group">
                                    <label>Rating:</label>
                                    <div class="rating-stars">
                                        <input type="radio" id="star5" name="rating" value="5" required>
                                        <label for="star5"><i class="ri-star-fill"></i></label>
                                        
                                        <input type="radio" id="star4" name="rating" value="4">
                                        <label for="star4"><i class="ri-star-fill"></i></label>
                                        
                                        <input type="radio" id="star3" name="rating" value="3">
                                        <label for="star3"><i class="ri-star-fill"></i></label>
                                        
                                        <input type="radio" id="star2" name="rating" value="2">
                                        <label for="star2"><i class="ri-star-fill"></i></label>
                                        
                                        <input type="radio" id="star1" name="rating" value="1">
                                        <label for="star1"><i class="ri-star-fill"></i></label>
                                    </div>
                                </div>
                                
                                <button type="submit" class="form-submit">Submit Rating</button>
                            </form>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($is_logged_in && $recipe['user_id'] == $_SESSION['user_id']): ?>
                    <div class="recipe-owner-actions">
                        <a href="edit-recipe.php?id=<?php echo $recipe_id; ?>" class="btn edit-btn">Edit Recipe</a>
                        <a href="delete-recipe.php?id=<?php echo $recipe_id; ?>" class="btn delete-btn" onclick="return confirm('Are you sure you want to delete this recipe?')">Delete Recipe</a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <?php
    } catch (Exception $e) {
        // Log the error
        error_log("Error in recipes.php: " . $e->getMessage());
        
        // Display user-friendly error
        ?>
        <section class="section">
            <div class="container">
                <div class="alert alert-error">
                    <p>Sorry, we encountered an error while loading this recipe. Please try again later.</p>
                </div>
                <a href="recipes.php" class="btn">Back to Recipes</a>
            </div>
        </section>
        <?php
    }
} else {
    // Show the recipes list page
    try {
        ?>

        <section id="recipes" class="section">
            <div class="container">
                <?php if (isset($_SESSION['message']) && isset($_SESSION['message_type'])): ?>
                <div class="alert alert-<?php echo htmlspecialchars($_SESSION['message_type']); ?>">
                    <?php 
                    echo htmlspecialchars($_SESSION['message']); 
                    unset($_SESSION['message']);
                    unset($_SESSION['message_type']);
                    ?>
                </div>
                <?php endif; ?>
                
                <div class="section-title">
                    <h1>Our Special Recipes</h1>
                    <p>Discover our collection of delicious recipes for every occasion.</p>
                </div>
                
                <div class="recipes-filter">
                    <div class="search-container">
                        <input type="text" id="recipe-search" class="form-control" placeholder="Search recipes...">
                        <button id="search-btn" class="btn">Search</button>
                    </div>
                    
                    <div class="filter-options">
                        <select id="type-filter" class="form-control">
                            <option value="">All Types</option>
                            <option value="Breakfast">Breakfast</option>
                            <option value="Lunch">Lunch</option>
                            <option value="Dinner">Dinner</option>
                            <option value="Dessert">Dessert</option>
                            <option value="Snack">Snack</option>
                            <option value="Appetizer">Appetizer</option>
                            <option value="Soup">Soup</option>
                            <option value="Salad">Salad</option>
                            <option value="Drink">Drink</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                </div>
                
                <?php
                // Get all recipes
                $conn = getDbConnection();
                
                if (!$conn) {
                    throw new Exception("Database connection failed");
                }
                
                // Use pagination to improve performance
                $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                $per_page = 12; // Number of recipes per page
                $offset = ($page - 1) * $per_page;
                
                // Get total number of recipes
                $countResult = $conn->query("SELECT COUNT(*) as total FROM Recipes");
                $total_recipes = $countResult->fetch_assoc()['total'] ?? 0;
                $total_pages = ceil($total_recipes / $per_page);
                
                // Get recipes for current page
                $stmt = $conn->prepare("SELECT * FROM Recipes ORDER BY created_at DESC LIMIT ? OFFSET ?");
                
                if (!$stmt) {
                    throw new Exception("Prepare statement failed: " . $conn->error);
                }
                
                $stmt->bind_param("ii", $per_page, $offset);
                
                if (!$stmt->execute()) {
                    throw new Exception("Execute failed: " . $stmt->error);
                }
                
                $result = $stmt->get_result();
                $recipes = $result->fetch_all(MYSQLI_ASSOC);
                $stmt->close();
                $conn->close();
                
                if (empty($recipes)):
                ?>
                <div class="no-recipes">
                    <p>No recipes found. <?php if ($is_logged_in): ?><a href="add-recipe.php">Add a recipe</a> now!<?php endif; ?></p>
                </div>
                <?php else: ?>
                <div id="recipes-container" class="recipes-container">
                    <?php foreach ($recipes as $recipe): ?>
                    <div class="recipe" data-type="<?php echo htmlspecialchars($recipe['type']); ?>">
                        <?php if (!empty($recipe['image']) && file_exists('../' . $recipe['image'])): ?>
                        <img src="<?php echo getResourcePath($recipe['image']); ?>" alt="<?php echo htmlspecialchars($recipe['name']); ?>" class="recipe-image">
                        <?php else: ?>
                        <img src="<?php echo getResourcePath('assets/img/pic' . (($recipe['id'] % 3) + 8) . '.webp'); ?>" alt="<?php echo htmlspecialchars($recipe['name']); ?>" class="recipe-image">
                        <?php endif; ?>
                        
                        <div class="recipe-content">
                            <h2><?php echo htmlspecialchars($recipe['name']); ?></h2>
                            <p><?php echo htmlspecialchars(truncateText($recipe['description'], 100)); ?></p>
                            
                            <div class="recipe-meta">
                                <span><i class="ri-fire-line"></i> Cook: <?php echo (int)$recipe['cooking_time']; ?> mins</span>
                                <span><i class="ri-restaurant-line"></i> Type: <?php echo htmlspecialchars($recipe['type']); ?></span>
                            </div>
                            
                            <a href="recipes.php?id=<?php echo $recipe['id']; ?>" class="recipe-btn">View Recipe</a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <?php if ($total_pages > 1): ?>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                    <a href="?page=<?php echo $page - 1; ?>" class="pagination-link">&laquo; Previous</a>
                    <?php endif; ?>
                    
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?page=<?php echo $i; ?>" class="pagination-link <?php echo ($i == $page) ? 'active' : ''; ?>"><?php echo $i; ?></a>
                    <?php endfor; ?>
                    
                    <?php if ($page < $total_pages): ?>
                    <a href="?page=<?php echo $page + 1; ?>" class="pagination-link">Next &raquo;</a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
                <?php endif; ?>
            </div>
        </section>

        <?php
    } catch (Exception $e) {
        // Log the error
        error_log("Error in recipes.php: " . $e->getMessage());
        
        // Display user-friendly error
        ?>
        <section class="section">
            <div class="container">
                <div class="alert alert-error">
                    <p>Sorry, we encountered an error while loading recipes. Please try again later.</p>
                </div>
            </div>
        </section>
        <?php
    }
}

// Add JavaScript for filtering and searching
?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Search functionality
    const searchInput = document.getElementById('recipe-search');
    const searchBtn = document.getElementById('search-btn');
    const typeFilter = document.getElementById('type-filter');
    const recipesContainer = document.getElementById('recipes-container');
    
    if (searchBtn && searchInput && recipesContainer) {
        searchBtn.addEventListener('click', filterRecipes);
        searchInput.addEventListener('keyup', function(event) {
            if (event.key === 'Enter') {
                filterRecipes();
            }
        });
    }
    
    if (typeFilter && recipesContainer) {
        typeFilter.addEventListener('change', filterRecipes);
    }
    
    function filterRecipes() {
        const searchTerm = searchInput.value.toLowerCase();
        const selectedType = typeFilter.value;
        const recipes = recipesContainer.querySelectorAll('.recipe');
        
        let visibleCount = 0;
        
        recipes.forEach(function(recipe) {
            const recipeTitle = recipe.querySelector('h2').textContent.toLowerCase();
            const recipeDescription = recipe.querySelector('p').textContent.toLowerCase();
            const recipeType = recipe.getAttribute('data-type');
            
            const matchesSearch = searchTerm === '' || 
                                recipeTitle.includes(searchTerm) || 
                                recipeDescription.includes(searchTerm);
                                
            const matchesType = selectedType === '' || recipeType === selectedType;
            
            if (matchesSearch && matchesType) {
                recipe.style.display = 'flex';
                visibleCount++;
            } else {
                recipe.style.display = 'none';
            }
        });
        
        // Show message if no recipes match
        let noResultsMessage = recipesContainer.querySelector('.no-results-message');
        
        if (visibleCount === 0) {
            if (!noResultsMessage) {
                noResultsMessage = document.createElement('div');
                noResultsMessage.className = 'no-results-message';
                noResultsMessage.innerHTML = '<p>No recipes match your search criteria.</p>';
                recipesContainer.appendChild(noResultsMessage);
            }
        } else if (noResultsMessage) {
            noResultsMessage.remove();
        }
    }
});
</script>

<?php
// Include the footer
include_once '../components/footer.php';
?>