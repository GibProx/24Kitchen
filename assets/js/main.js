/**
 * Main JavaScript file for 24Kitchen
 */

document.addEventListener('DOMContentLoaded', function() {
  // Initialize all components
  initNavigation();
  initScrollToTop();
  initSmoothScroll();
  
  // Page specific initializations
  if (document.getElementById('recipes-container')) {
    initRecipes();
  }
  
  if (document.getElementById('login-form')) {
    initAuthForms();
  }
  
  if (document.getElementById('signup-form')) {
    initAuthForms();
  }
  
  if (document.getElementById('contact-form')) {
    initContactForm();
  }
  
  // Rating forms
  const ratingForms = document.querySelectorAll('.rating-form form');
  if (ratingForms.length > 0) {
    initRatingForms();
  }
});

/**
 * Initialize navigation functionality
 */
function initNavigation() {
  const menuBtn = document.getElementById('menu_btn');
  const navItems = document.querySelector('.nav_items');
  const navLinks = document.querySelectorAll('.nav_items a');
  
  // Toggle mobile menu
  if (menuBtn && navItems) {
    menuBtn.addEventListener('click', function() {
      navItems.classList.toggle('active');
      
      // Change icon based on menu state
      const icon = menuBtn.querySelector('i');
      if (icon) {
        if (navItems.classList.contains('active')) {
          icon.classList.remove('ri-menu-line');
          icon.classList.add('ri-close-line');
        } else {
          icon.classList.remove('ri-close-line');
          icon.classList.add('ri-menu-line');
        }
      }
    });
    
    // Close menu when clicking outside
    document.addEventListener('click', function(event) {
      if (!navItems.contains(event.target) && !menuBtn.contains(event.target) && navItems.classList.contains('active')) {
        navItems.classList.remove('active');
        
        const icon = menuBtn.querySelector('i');
        if (icon) {
          icon.classList.remove('ri-close-line');
          icon.classList.add('ri-menu-line');
        }
      }
    });
    
    // Close menu when clicking on a link (for mobile)
    navLinks.forEach(link => {
      link.addEventListener('click', function() {
        if (window.innerWidth <= 992) {
          navItems.classList.remove('active');
          
          const icon = menuBtn.querySelector('i');
          if (icon) {
            icon.classList.remove('ri-close-line');
            icon.classList.add('ri-menu-line');
          }
        }
      });
    });
  }
  
  // Navbar scroll effect
  window.addEventListener('scroll', function() {
    const navbar = document.querySelector('.navbar');
    if (navbar) {
      if (window.scrollY > 50) {
        navbar.classList.add('scrolled');
      } else {
        navbar.classList.remove('scrolled');
      }
    }
  });
}

/**
 * Initialize scroll to top button
 */
function initScrollToTop() {
  const scrollTopBtn = document.querySelector('.scroll-top');
  
  if (scrollTopBtn) {
    // Show/hide button based on scroll position
    window.addEventListener('scroll', function() {
      if (window.scrollY > 300) {
        scrollTopBtn.classList.add('show');
      } else {
        scrollTopBtn.classList.remove('show');
      }
    });
    
    // Scroll to top when button is clicked
    scrollTopBtn.addEventListener('click', function() {
      window.scrollTo({
        top: 0,
        behavior: 'smooth'
      });
    });
  }
}

/**
 * Initialize smooth scrolling for anchor links
 */
function initSmoothScroll() {
  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
      const targetId = this.getAttribute('href');
      
      if (targetId === '#') return;
      
      e.preventDefault();
      
      const targetElement = document.querySelector(targetId);
      
      if (targetElement) {
        const navbarHeight = document.querySelector('.navbar').offsetHeight;
        const targetPosition = targetElement.getBoundingClientRect().top + window.pageYOffset - navbarHeight;
        
        window.scrollTo({
          top: targetPosition,
          behavior: 'smooth'
        });
      }
    });
  });
}

/**
 * Helper function to validate email
 */
function isValidEmail(email) {
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return emailRegex.test(email);
}

/**
 * Helper function to show validation error
 */
function showError(input, message) {
  const formGroup = input.closest('.form-group');
  const errorElement = formGroup.querySelector('.invalid-feedback') || document.createElement('div');
  
  errorElement.className = 'invalid-feedback';
  errorElement.textContent = message;
  
  input.classList.add('is-invalid');
  
  if (!formGroup.querySelector('.invalid-feedback')) {
    formGroup.appendChild(errorElement);
  }
}

/**
 * Helper function to clear validation error
 */
function clearError(input) {
  const formGroup = input.closest('.form-group');
  const errorElement = formGroup.querySelector('.invalid-feedback');
  
  input.classList.remove('is-invalid');
  
  if (errorElement) {
    errorElement.remove();
  }
}

/**
 * Helper function to show success message
 */
function showSuccessMessage(form, message) {
  // Remove any existing message
  const existingMessage = form.querySelector('.success-message');
  if (existingMessage) {
    existingMessage.remove();
  }
  
  // Create success message
  const successMessage = document.createElement('div');
  successMessage.className = 'success-message';
  successMessage.style.color = 'var(--color-success)';
  successMessage.style.marginTop = 'var(--spacing-md)';
  successMessage.style.textAlign = 'center';
  successMessage.textContent = message;
  
  // Add to form
  form.appendChild(successMessage);
  
  // Remove after 5 seconds
  setTimeout(() => {
    successMessage.remove();
  }, 5000);
}

// Add this to the existing main.js file

document.addEventListener('DOMContentLoaded', function() {
  // Initialize flash message close button
  const closeFlashBtn = document.querySelector('.close-flash');
  if (closeFlashBtn) {
    closeFlashBtn.addEventListener('click', function() {
      const flashMessage = this.closest('.flash-message');
      if (flashMessage) {
        flashMessage.style.display = 'none';
      }
    });
    
    // Auto-hide flash message after 5 seconds
    setTimeout(() => {
      const flashMessage = document.querySelector('.flash-message');
      if (flashMessage) {
        flashMessage.style.display = 'none';
      }
    }, 5000);
  }
});