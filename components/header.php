<?php
// Get the current page path
$current_page = basename($_SERVER['PHP_SELF']);
$is_home = ($current_page == 'index.php');
$is_recipes = ($current_page == 'recipes.php');
$is_contact = ($current_page == 'contact.php');
$is_login = ($current_page == 'login.php');
$is_register = ($current_page == 'register.php');
$is_add_recipe = ($current_page == 'add-recipe.php');
$is_my_recipes = ($current_page == 'my-recipes.php');

// Set header ID based on page
$header_id = $is_home ? 'home' : 'main-header';

// Set resource paths
$css_path = getResourcePath('assets/css/main.css');
$js_main_path = getResourcePath('assets/js/main.js');
$js_auth_path = getResourcePath('assets/js/auth.js');
$js_recipes_path = getResourcePath('assets/js/recipes.js');
$js_utils_path = getResourcePath('assets/js/utils.js');
$logo_path = getResourcePath('assets/img/log.webp');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - <?php echo SITE_NAME; ?></title>
    <meta name="description" content="<?php echo $meta_description; ?>">
    <meta name="keywords" content="<?php echo $meta_keywords; ?>">
    <link rel="stylesheet" href="<?php echo $css_path; ?>">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet">
</head>
<body class="<?php echo $page_class; ?>">
    <?php if ($flash_message): ?>
    <div class="flash-message flash-<?php echo $flash_message_type; ?>">
        <div class="container">
            <?php echo $flash_message; ?>
            <button class="close-flash"><i class="ri-close-line"></i></button>
        </div>
    </div>
    <?php endif; ?>

    <header id="<?php echo $header_id; ?>">
        <nav class="navbar">
            <div class="logo">
                <img src="<?php echo $logo_path; ?>" alt="<?php echo SITE_NAME; ?> Logo">
                <span>food in</span>
            </div>

            <ul class="nav_items">
                <div class="item">
                    <li><a href="<?php echo getResourcePath('index.php'); ?>" class="<?php echo $is_home ? 'active' : ''; ?>">HOME</a></li>
                    <li><a href="<?php echo getResourcePath('pages/recipes.php'); ?>" class="<?php echo $is_recipes ? 'active' : ''; ?>">RECIPES</a></li>
                    <li><a href="<?php echo getResourcePath('index.php'); ?>#services">SERVICES</a></li>
                    <li><a href="<?php echo getResourcePath('index.php'); ?>#about">ABOUT</a></li>
                    <li><a href="<?php echo getResourcePath('pages/contact.php'); ?>" class="<?php echo $is_contact ? 'active' : ''; ?>">CONTACT</a></li>
                    
                    <?php if ($is_logged_in): ?>
                    <li><a href="<?php echo getResourcePath('pages/add-recipe.php'); ?>" class="<?php echo $is_add_recipe ? 'active' : ''; ?>">ADD RECIPE</a></li>
                    <li><a href="<?php echo getResourcePath('pages/my-recipes.php'); ?>" class="<?php echo $is_my_recipes ? 'active' : ''; ?>">MY RECIPES</a></li>
                    <?php endif; ?>
                </div>

                <div class="auth-buttons">
                    <?php if ($is_logged_in): ?>
                    <li>
                        <div class="user-menu">
                            <span class="user-name">Hello, <?php echo $current_user['name']; ?></span>
                            <a href="<?php echo getResourcePath('pages/logout.php'); ?>" class="logout-btn">LOGOUT</a>
                        </div>
                    </li>
                    <?php else: ?>
                    <li><a href="<?php echo getResourcePath('pages/login.php'); ?>" class="login-link <?php echo $is_login ? 'active' : ''; ?>">LOGIN</a></li>
                    <li><a href="<?php echo getResourcePath('pages/register.php'); ?>" class="signup-btn <?php echo $is_register ? 'active' : ''; ?>">SIGN UP</a></li>
                    <?php endif; ?>
                </div>
            </ul>

            <div class="nav_menu" id="menu_btn">
                <i class="ri-menu-line"></i>
            </div>
        </nav>

        <?php if($is_home): ?>
        <section class="main">
            <h2 class="heading_1">Welcome to 24Kitchen</h2>
            <h1 class="heading">Enjoy our <br> delicious meal</h1>
            <div class="main_btn">
                <button class="btn btn1">Explore more</button>
                <button class="btn btn2"><i class="ri-play-circle-line"></i>Watch video</button>
            </div>
        </section>
        <?php endif; ?>
    </header>