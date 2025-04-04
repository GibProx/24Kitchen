/**
 * Utility functions for 24Kitchen
 */

/**
 * Initialize contact form
 */
function initContactForm() {
  const contactForm = document.getElementById('contact-form');
  
  if (contactForm) {
    contactForm.addEventListener('submit', function(e) {
      e.preventDefault();
      
      let isValid = true;
      
      // Get form fields
      const email = document.getElementById('email');
      const date = document.getElementById('date');
      const message = document.getElementById('message');
      
      // Validate email
      if (!email.value.trim()) {
        showError(email, 'Email is required');
        isValid = false;
      } else if (!isValidEmail(email.value.trim())) {
        showError(email, 'Please enter a valid email address');
        isValid = false;
      } else if (!email.value.trim().endsWith('@aston.ac.uk')) {
        showError(email, 'Please enter a valid Aston University email');
        isValid = false;
      } else {
        clearError(email);
      }
      
      // Validate date
      if (!date.value) {
        showError(date, 'Please select a date');
        isValid = false;
      } else {
        const selectedDate = new Date(date.value);
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        
        if (selectedDate <= today) {
          showError(date, 'Please select a future date');
          isValid = false;
        } else {
          clearError(date);
        }
      }
      
      // Validate message if it exists
      if (message && !message.value.trim()) {
        showError(message, 'Please enter your message');
        isValid = false;
      } else if (message) {
        clearError(message);
      }
      
      // If form is valid, submit
      if (isValid) {
        // In a real application, you would submit the form to the server
        // For demo purposes, we'll just show a success message
        showSuccessMessage(contactForm, 'Form submitted successfully!');
        
        // Reset form
        contactForm.reset();
      }
    });
  }
}

/**
 * Format date to a readable string
 */
function formatDate(dateString) {
  const options = { year: 'numeric', month: 'long', day: 'numeric' };
  return new Date(dateString).toLocaleDateString(undefined, options);
}

/**
 * Format time (minutes) to hours and minutes
 */
function formatTime(minutes) {
  if (minutes < 60) {
    return `${minutes} min`;
  }
  
  const hours = Math.floor(minutes / 60);
  const mins = minutes % 60;
  
  if (mins === 0) {
    return `${hours} hr`;
  }
  
  return `${hours} hr ${mins} min`;
}

/**
 * Truncate text to a specified length
 */
function truncateText(text, maxLength) {
  if (text.length <= maxLength) {
    return text;
  }
  
  return text.substring(0, maxLength) + '...';
}

/**
 * Generate random ID
 */
function generateId() {
  return Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15);
}