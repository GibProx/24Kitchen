<?php
// Include the config file
require_once '../components/config.php';
require_once '../includes/db.php';
require_once '../includes/auth.php';

// Redirect if already logged in
if (isLoggedIn()) {
    redirect('../index.php');
    exit;
}

// Set page title
$page_title = 'Register';
$page_class = 'register-page';
$meta_description = 'Create a 24Kitchen account to save your favorite recipes and more.';
$meta_keywords = 'register, signup, 24kitchen, recipes, cooking';

// Initialize variables
$first_name = '';
$last_name = '';
$email = '';
$flash_message = '';
$flash_message_type = '';
$is_logged_in = isLoggedIn();
$current_user = getCurrentUser();

// Process registration form
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $first_name = isset($_POST['first_name']) ? sanitizeInput($_POST['first_name']) : '';
    $last_name = isset($_POST['last_name']) ? sanitizeInput($_POST['last_name']) : '';
    $email = isset($_POST['email']) ? sanitizeInput($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';
    $terms = isset($_POST['terms']) ? true : false;
    
    // Validate form data
    if (empty($first_name)) {
        $errors['first_name'] = 'First name is required';
    }
    
    if (empty($last_name)) {
        $errors['last_name'] = 'Last name is required';
    }
    
    if (empty($email)) {
        $errors['email'] = 'Email is required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Please enter a valid email address';
    }
    
    if (empty($password)) {
        $errors['password'] = 'Password is required';
    } elseif (strlen($password) < 8) {
        $errors['password'] = 'Password must be at least 8 characters long';
    }
    
    if (empty($confirm_password)) {
        $errors['confirm_password'] = 'Please confirm your password';
    } elseif ($password !== $confirm_password) {
        $errors['confirm_password'] = 'Passwords do not match';
    }
    
    if (!$terms) {
        $errors['terms'] = 'You must agree to the Terms of Service and Privacy Policy';
    }
    
    // If no validation errors, register the user
    if (empty($errors)) {
        // Connect to database
        $conn = getDbConnection();
        
        // Check if email already exists
        $checkStmt = $conn->prepare("SELECT id FROM Users WHERE email = ?");
        $checkStmt->bind_param("s", $email);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();
        
        if ($checkResult->num_rows > 0) {
            $errors['register'] = 'Email address is already registered';
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert user
            $insertStmt = $conn->prepare("INSERT INTO Users (first_name, last_name, email, password, role, created_at) VALUES (?, ?, ?, ?, 'user', NOW())");
            $insertStmt->bind_param("ssss", $first_name, $last_name, $email, $hashed_password);
            $success = $insertStmt->execute();
            
            if ($success) {
                // Set success message
                $_SESSION['message'] = 'Your account has been created successfully. Please log in.';
                $_SESSION['message_type'] = 'success';
                
                // Close database connection
                $insertStmt->close();
                $checkStmt->close();
                $conn->close();
                
                // Redirect to login page
                redirect('/pages/login.php');
                exit;
            } else {
                $errors['register'] = 'Registration failed. Please try again.';
            }
            
            $insertStmt->close();
        }
        
        $checkStmt->close();
        $conn->close();
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

<section id="register">
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <h1>Create an Account</h1>
                <p>Join our community and start exploring delicious recipes!</p>
            </div>
            
            <?php if (isset($errors['register'])): ?>
            <div class="alert alert-error">
                <?php echo $errors['register']; ?>
            </div>
            <?php endif; ?>
            
            <form id="signup-form" class="auth-form" method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                <div class="form-row">
                    <div class="form-group <?php echo isset($errors['first_name']) ? 'has-error' : ''; ?>">
                        <label for="first-name">First Name</label>
                        <div class="input-with-icon">
                            <i class="ri-user-line"></i>
                            <input type="text" id="first-name" name="first_name" class="form-control" placeholder="Enter your first name" value="<?php echo htmlspecialchars($first_name); ?>" required>
                            <?php if (isset($errors['first_name'])): ?>
                            <div class="invalid-feedback"><?php echo $errors['first_name']; ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="form-group <?php echo isset($errors['last_name']) ? 'has-error' : ''; ?>">
                        <label for="last-name">Last Name</label>
                        <div class="input-with-icon">
                            <i class="ri-user-line"></i>
                            <input type="text" id="last-name" name="last_name" class="form-control" placeholder="Enter your last name" value="<?php echo htmlspecialchars($last_name); ?>" required>
                            <?php if (isset($errors['last_name'])): ?>
                            <div class="invalid-feedback"><?php echo $errors['last_name']; ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="form-group <?php echo isset($errors['email']) ? 'has-error' : ''; ?>">
                    <label for="signup-email">Email Address</label>
                    <div class="input-with-icon">
                        <i class="ri-mail-line"></i>
                        <input type="email" id="signup-email" name="email" class="form-control" placeholder="Enter your email" value="<?php echo htmlspecialchars($email); ?>" required>
                        <?php if (isset($errors['email'])): ?>
                        <div class="invalid-feedback"><?php echo $errors['email']; ?></div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="form-group <?php echo isset($errors['password']) ? 'has-error' : ''; ?>">
                    <label for="signup-password">Password</label>
                    <div class="input-with-icon">
                        <i class="ri-lock-line"></i>
                        <input type="password" id="signup-password" name="password" class="form-control" placeholder="Create a password" required minlength="8">
                        <small class="form-text">Password must be at least 8 characters</small>
                        <?php if (isset($errors['password'])): ?>
                        <div class="invalid-feedback"><?php echo $errors['password']; ?></div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="form-group <?php echo isset($errors['confirm_password']) ? 'has-error' : ''; ?>">
                    <label for="confirm-password">Confirm Password</label>
                    <div class="input-with-icon">
                        <i class="ri-lock-line"></i>
                        <input type="password" id="confirm-password" name="confirm_password" class="form-control" placeholder="Confirm your password" required>
                        <?php if (isset($errors['confirm_password'])): ?>
                        <div class="invalid-feedback"><?php echo $errors['confirm_password']; ?></div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="form-group checkbox-group <?php echo isset($errors['terms']) ? 'has-error' : ''; ?>">
                    <input type="checkbox" id="terms" name="terms" <?php echo isset($_POST['terms']) ? 'checked' : ''; ?> required>
                    <label for="terms">I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a></label>
                    <?php if (isset($errors['terms'])): ?>
                    <div class="invalid-feedback"><?php echo $errors['terms']; ?></div>
                    <?php endif; ?>
                </div>
                
                <button type="submit" class="btn-auth">Create Account</button>
            </form>
            
            <div class="auth-footer">
                <p>Already have an account? <a href="login.php">Login</a></p>
            </div>
        </div>
    </div>
</section>

<?php
// Include the footer
include_once '../components/footer.php';
?>