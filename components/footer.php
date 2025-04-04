    <footer>
        <div class="footer_container">
            <div class="footer_col">
                <div class="footer_logo">
                    <img src="<?php echo getResourcePath('assets/img/log.webp'); ?>" alt="<?php echo SITE_NAME; ?> Logo">
                    <span>food in</span>
                </div>
                <p class="footer_text">Our job is to fill your tummy with delicious food and with fast and free delivery time.</p>
                <ul class="footer_socials">
                    <li><a href="#"><i class="ri-instagram-fill"></i></a></li>
                    <li><a href="#"><i class="ri-facebook-fill"></i></a></li>
                    <li><a href="#"><i class="ri-twitter-fill"></i></a></li>
                </ul>
            </div>

            <div class="footer_col">
                <h4>About</h4>
                <ul class="footer_links">
                    <li><a href="<?php echo getResourcePath('index.php'); ?>#about">About us</a></li>
                    <li><a href="#">Features</a></li>
                    <li><a href="#">News</a></li>
                    <li><a href="#">Menu</a></li>
                </ul>
            </div>

            <div class="footer_col">
                <h4>Company</h4>
                <ul class="footer_links">
                    <li><a href="#">My food in</a></li>
                    <li><a href="#">Partner</a></li>
                    <li><a href="#">FAQ</a></li>
                    <li><a href="#">Blog</a></li>
                </ul>
            </div>

            <div class="footer_col">
                <h4>Support</h4>
                <ul class="footer_links">
                    <li><a href="#">Account</a></li>
                    <li><a href="#">Support Center</a></li>
                    <li><a href="#">Feedback</a></li>
                    <li><a href="<?php echo getResourcePath('pages/contact.php'); ?>">Contact us</a></li>
                    <li><a href="#">Accessibility</a></li>
                </ul>
            </div>
        </div>

        <div class="footer_bar">
            Copyright © <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All rights reserved.
        </div>
    </footer>

    <!-- Scroll to top button -->
    <button class="scroll-top">
        <i class="ri-arrow-up-line"></i>
    </button>

    <!-- JavaScript -->
    <script src="<?php echo $js_main_path; ?>"></script>
    
    <?php if($is_login || $is_register): ?>
    <script src="<?php echo $js_auth_path; ?>"></script>
    <?php endif; ?>
    
    <?php if($is_recipes || $is_home): ?>
    <script src="<?php echo $js_recipes_path; ?>"></script>
    <?php endif; ?>
    
    <script src="<?php echo $js_utils_path; ?>"></script>
</body>
</html>