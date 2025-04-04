<?php
// Include the config file
require_once 'components/config.php';

// Set page title
$page_title = 'Home';
$page_class = 'home-page';

// Include the header
include_once 'components/header.php';
?>

<!-- Services Section -->
<section class="section" id="services">
    <div class="container">
        <div class="section-title">
            <h3 class="heading_1">Our Services</h3>
            <h1 class="heading">Best <span>Quality Food</span> <br>Order Now</h1>
        </div>

        <div class="boxes">
            <div class="box">
                <img src="assets/img/pic1.webp" alt="Easy to Order">
                <h4>Easy to Order</h4>
                <p>You only need a few steps in ordering food</p>
            </div>

            <div class="box">
                <img src="assets/img/pic2.webp" alt="Fastest Delivery">
                <h4>Fastest Delivery</h4>
                <p>Delivery that is always on time even faster</p>
            </div>

            <div class="box">
                <img src="assets/img/pic3.webp" alt="Good Quality">
                <h4>Good Quality</h4>
                <p>Not only fast for us, quality is also number one</p>
            </div>
        </div>

        <div class="section-title mt-4">
            <h3 class="heading_1">Featured Recipes</h3>
            <h2 class="heading">Try Our <span>Popular Dishes</span></h2>
        </div>

        <!-- Container for displaying recipes dynamically -->
        <div id="recipes-container" class="recipes-container">
            <!-- Recipes will be loaded here dynamically -->
        </div>
        
        <div class="text-center mt-4">
            <a href="<?php echo getResourcePath('pages/recipes.php'); ?>" class="btn">View All Recipes</a>
        </div>
    </div>
</section>

<!-- About Section -->
<section class="section bg-alt" id="about">
    <div class="container">
        <div class="about_heading">
            <h3 class="heading_1">About us</h3>
            <h1 class="heading">What <span>our customers</span> <br> say about us</h1>
        </div>
        <div class="about para">
            <p>
                At 24Kitchen, we're passionate about bringing delicious, homemade meals to your table. 
                Our team of expert chefs and food enthusiasts work tirelessly to create recipes that 
                are not only mouth-watering but also easy to prepare in your own kitchen.
            </p>
        </div>

        <div class="about_box">
            <div class="box1">
                <img src="assets/img/pic4.webp" alt="Customer Testimonial">
                <div class="box1_text">
                    <p class="para1">"The recipes from 24Kitchen have transformed my cooking! Everything is so delicious and easy to follow."</p>
                    <button class="btn btn1">Read More</button>
                </div>
            </div>

            <div class="box1">
                <img src="assets/img/pic5.webp" alt="Customer Testimonial">
                <div class="box1_text">
                    <p class="para1">"I've tried many cooking websites, but 24Kitchen stands out with their quality recipes and excellent service."</p>
                    <button class="btn btn1">Read More</button>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section class="section" id="download">
    <div class="container">
        <div class="contact_box">
            <div class="left_box">
                <h2 class="heading_1">Download app</h2>
                <h1 class="heading">Get started with food in today!</h1>
                <p class="para1">Download our mobile app to access recipes on the go, save your favorites, and get personalized recommendations.</p>
                <button class="btn btn1">Get the app</button>
            </div>
            <div class="right_box">
                <img src="assets/img/right.webp" alt="Mobile App">
            </div>
        </div>
    </div>
</section>

<?php
// Include the footer
include_once 'components/footer.php';
?>