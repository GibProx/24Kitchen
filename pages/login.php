<?php
// Include the config file
require_once '../components/config.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

// Redirect if already logged in
if (isLoggedIn()) {
    redirect('../index.php');
    exit;
}

// Set page title
$page_title = 'Login';
$page_class = 'login-page';
$meta_description = 'Login to your 24Kitchen account to access your saved recipes and more.';
$meta_keywords = 'login, 24kitchen, recipes, cooking';

// Initialize variables
$email = '';
$flash_message = '';
$flash_message_type = '';
$is_logged_in = isLoggedIn();
$current_user = getCurrentUser();

// Process login form
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $email = isset($_POST['email']) ? sanitizeInput($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $remember = isset($_POST['remember']) ? true : false;
    
    // Validate form data
    if (empty($email)) {
        $errors['email'] = 'Email is required';
    } elseif (!validateEmail($email)) {
        $errors['email'] = 'Please enter a valid email address';
    }
    
    if (empty($password)) {
        $errors['password'] = 'Password is required';
    }
    
    // If no validation errors, attempt to authenticate
    if (empty($errors)) {
        // Connect to database
        $conn = getDbConnection();
        
        // Prepare statement to get user
        $stmt = $conn->prepare("SELECT id, first_name, last_name, email, password, role FROM Users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            // Verify password
            if (password_verify($password, $user['password'])) {
                // Create user session data
                $userData = [
                    'id' => $user['id'],
                    'name' => $user['first_name'] . ' ' . $user['last_name'],
                    'email' => $user['email'],
                    'role' => $user['role']
                ];
                
                // Log in the user
                $_SESSION['user'] = $userData;
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['is_logged_in'] = true;
                
                // Set remember me cookie if requested
                if ($remember) {
                    $token = generateRandomString(32); // Use the existing function
                    $expiry = time() + (30 * 24 * 60 * 60); // 30 days
                    
                    // Hash token for storage
                    $hashedToken = password_hash($token, PASSWORD_DEFAULT);
                    
                    // Store token in database - only if RememberTokens table exists
                    $expiryDate = date('Y-m-d H:i:s', $expiry);
                    
                    // Check if RememberTokens table exists
                    $tableCheckResult = $conn->query("SHOW TABLES LIKE 'RememberTokens'");
                    
                    if ($tableCheckResult->num_rows > 0) {
                        // RememberTokens table exists, use it
                        $tokenStmt = $conn->prepare("INSERT INTO RememberTokens (user_id, token, expires_at) VALUES (?, ?, ?)");
                        $tokenStmt->bind_param("iss", $user['id'], $hashedToken, $expiryDate);
                        $tokenStmt->execute();
                        $tokenStmt->close();
                    }
                    
                    // Set cookie
                    setcookie('remember_token', $user['id'] . ':' . $token, $expiry, '/', '', false, true);
                }
                
                // No need to update last_login for simplified version
                
                // Set success message
                $_SESSION['message'] = 'You have been logged in successfully.';
                $_SESSION['message_type'] = 'success';
                
                // Close database connection
                $stmt->close();
                $conn->close();
                
                // Redirect to home page
                redirect('../index.php');
                exit;
            } else {
                $errors['login'] = 'Invalid email or password';
            }
        } else {
            $errors['login'] = 'Invalid email or password';
        }
        
        // Close database connection
        $stmt->close();
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

<section id="login">
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <h1>Login to Your Account</h1>
                <p>Welcome back! Please enter your details to access your account.</p>
            </div>
            
            <?php if (isset($errors['login'])): ?>
            <div class="alert alert-error">
                <?php echo $errors['login']; ?>
            </div>
            <?php endif; ?>
            
            <form id="login-form" class="auth-form" method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                <div class="form-group <?php echo isset($errors['email']) ? 'has-error' : ''; ?>">
                    <label for="email">Email Address</label>
                    <div class="input-with-icon">
                        <i class="ri-mail-line"></i>
                        <input type="email" id="email" name="email" class="form-control" placeholder="Enter your email" value="<?php echo htmlspecialchars($email); ?>" required>
                        <?php if (isset($errors['email'])): ?>
                        <div class="invalid-feedback"><?php echo $errors['email']; ?></div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="form-group <?php echo isset($errors['password']) ? 'has-error' : ''; ?>">
                    <label for="password">Password</label>
                    <div class="input-with-icon">
                        <i class="ri-lock-line"></i>
                        <input type="password" id="password" name="password" class="form-control" placeholder="Enter your password" required>
                        <?php if (isset($errors['password'])): ?>
                        <div class="invalid-feedback"><?php echo $errors['password']; ?></div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="form-options">
                    <div class="remember-me">
                        <input type="checkbox" id="remember" name="remember">
                        <label for="remember">Remember me</label>
                    </div>
                    <a href="#" class="forgot-password">Forgot password?</a>
                </div>
                
                <button type="submit" class="btn-auth">Login</button>
            </form>
            
            <div class="auth-footer">
                <p>Don't have an account? <a href="register.php">Sign up</a></p>
            </div>
        </div>
    </div>
</section>

<?php
// Include the footer
include_once '../components/footer.php';
?>