<?php
// Include the config file
require_once '../components/config.php';

// Set page title
$page_title = 'Contact Us';
$page_class = 'contact-page';
$meta_description = 'Get in touch with 24Kitchen. We\'d love to hear from you!';

// Include the header
include_once '../components/header.php';
?>

<section id="contact" class="section">
    <div class="container">
        <div class="section-title">
            <h1>Contact Us</h1>
            <p>We'd love to hear from you! Fill out the form below to get in touch.</p>
        </div>
        
        <div class="contact-container">
            <div class="contact-form-container">
                <form id="contact-form" class="form-container">
                    <div class="form-group">
                        <label for="contact-method">Preferred Contact Method:</label>
                        <select id="contact-method" class="form-control">
                            <option value="email">Email</option>
                            <option value="phone">Phone</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email (Aston University only):</label>
                        <div class="input-with-icon">
                            <i class="ri-mail-line"></i>
                            <input type="email" id="email" class="form-control" required>
                            <small class="form-text">Please use your Aston University email (@aston.ac.uk)</small>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="date">Choose a date for a private lesson:</label>
                        <div class="input-with-icon">
                            <i class="ri-calendar-line"></i>
                            <input type="date" id="date" class="form-control" required>
                            <small class="form-text">Please select a future date</small>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="message">Your Message:</label>
                        <textarea id="message" class="form-control" rows="4" placeholder="Tell us what you're looking for..."></textarea>
                    </div>
                    
                    <button type="submit" class="form-submit">Submit</button>
                </form>
            </div>
            
            <div class="contact-info">
                <h3>Other Ways to Reach Us</h3>
                <p><i class="ri-map-pin-line"></i> <?php echo SITE_ADDRESS; ?></p>
                <p><i class="ri-phone-line"></i> <?php echo SITE_PHONE; ?></p>
                <p><i class="ri-mail-line"></i> <?php echo SITE_EMAIL; ?></p>
                <div class="social-links">
                    <a href="#"><i class="ri-facebook-fill"></i></a>
                    <a href="#"><i class="ri-instagram-line"></i></a>
                    <a href="#"><i class="ri-twitter-x-line"></i></a>
                </div>
                
                <div class="map-container">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2430.2112254740776!2d-1.8904345!3d52.4862039!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x4870bc9ae4f2e4b3%3A0x9a670ba18e08a084!2sAston%20University!5e0!3m2!1sen!2suk!4v1616682792422!5m2!1sen!2suk" width="100%" height="300" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
// Include the footer
include_once '../components/footer.php';
?>