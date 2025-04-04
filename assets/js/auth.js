/**
 * Authentication related JavaScript for 24Kitchen
 * Simplified for student projects
 */

document.addEventListener('DOMContentLoaded', function() {
  // Get forms
  const loginForm = document.getElementById('login-form');
  const signupForm = document.getElementById('signup-form');
  
  // Password toggle functionality
  setupPasswordToggles();
  
  // Form validation for login
  if (loginForm) {
      loginForm.addEventListener('submit', function(event) {
          if (!validateLoginForm()) {
              event.preventDefault();
          }
      });
  }
  
  // Form validation for signup
  if (signupForm) {
      // Password strength meter
      const passwordInput = document.getElementById('signup-password');
      if (passwordInput) {
          passwordInput.addEventListener('input', updatePasswordStrength);
      }
      
      // Form submission
      signupForm.addEventListener('submit', function(event) {
          if (!validateSignupForm()) {
              event.preventDefault();
          }
      });
  }
  
  // Handle flash messages
  const flashMessage = document.querySelector('.flash-message');
  if (flashMessage) {
      const closeBtn = flashMessage.querySelector('.close-flash');
      
      if (closeBtn) {
          closeBtn.addEventListener('click', function() {
              flashMessage.style.display = 'none';
          });
      }
      
      // Auto-hide flash message after 5 seconds
      setTimeout(function() {
          flashMessage.style.display = 'none';
      }, 5000);
  }
});

/**
* Setup password toggle functionality
*/
function setupPasswordToggles() {
  const passwordFields = document.querySelectorAll('input[type="password"]');
  
  passwordFields.forEach(function(field) {
      // Find the parent input-with-icon div
      const parentDiv = field.closest('.input-with-icon');
      if (!parentDiv) return;
      
      // Create toggle icon
      const toggleIcon = document.createElement('i');
      toggleIcon.className = 'ri-eye-line password-toggle';
      toggleIcon.setAttribute('data-target', field.id);
      
      // Append toggle icon to parent div
      parentDiv.appendChild(toggleIcon);
      
      // Add click event
      toggleIcon.addEventListener('click', function() {
          const targetField = document.getElementById(this.getAttribute('data-target'));
          
          if (targetField.type === 'password') {
              targetField.type = 'text';
              this.className = 'ri-eye-off-line password-toggle';
          } else {
              targetField.type = 'password';
              this.className = 'ri-eye-line password-toggle';
          }
      });
  });
}

/**
* Validate login form
* 
* @return {boolean} True if valid, false otherwise
*/
function validateLoginForm() {
  const email = document.getElementById('email').value.trim();
  const password = document.getElementById('password').value;
  let isValid = true;
  
  // Reset previous error messages
  clearErrors();
  
  // Validate email
  if (!email) {
      displayError('email', 'Email is required');
      isValid = false;
  } else if (!isValidEmail(email)) {
      displayError('email', 'Please enter a valid email address');
      isValid = false;
  }
  
  // Validate password
  if (!password) {
      displayError('password', 'Password is required');
      isValid = false;
  }
  
  return isValid;
}

/**
* Validate signup form
* 
* @return {boolean} True if valid, false otherwise
*/
function validateSignupForm() {
  const firstName = document.getElementById('first-name').value.trim();
  const lastName = document.getElementById('last-name').value.trim();
  const email = document.getElementById('signup-email').value.trim();
  const password = document.getElementById('signup-password').value;
  const confirmPassword = document.getElementById('confirm-password').value;
  const terms = document.getElementById('terms').checked;
  let isValid = true;
  
  // Reset previous error messages
  clearErrors();
  
  // Validate first name
  if (!firstName) {
      displayError('first-name', 'First name is required');
      isValid = false;
  }
  
  // Validate last name
  if (!lastName) {
      displayError('last-name', 'Last name is required');
      isValid = false;
  }
  
  // Validate email
  if (!email) {
      displayError('signup-email', 'Email is required');
      isValid = false;
  } else if (!isValidEmail(email)) {
      displayError('signup-email', 'Please enter a valid email address');
      isValid = false;
  }
  
  // Validate password
  if (!password) {
      displayError('signup-password', 'Password is required');
      isValid = false;
  } else if (password.length < 8) {
      displayError('signup-password', 'Password must be at least 8 characters');
      isValid = false;
  }
  
  // Validate confirm password
  if (password !== confirmPassword) {
      displayError('confirm-password', 'Passwords do not match');
      isValid = false;
  }
  
  // Validate terms
  if (!terms) {
      displayError('terms', 'You must agree to the Terms of Service and Privacy Policy');
      isValid = false;
  }
  
  return isValid;
}

/**
* Update password strength indicator
*/
function updatePasswordStrength() {
  const password = this.value;
  const strengthBar = document.querySelector('.password-strength-bar');
  const strengthText = document.querySelector('.password-strength-text');
  
  if (!strengthBar || !strengthText) return;
  
  const strength = checkPasswordStrength(password);
  
  // Update strength bar
  strengthBar.className = 'password-strength-bar';
  strengthBar.classList.add(`strength-${strength}`);
  
  // Update strength text
  let text = '';
  switch (strength) {
      case 0:
          text = 'Very Weak';
          break;
      case 1:
          text = 'Weak';
          break;
      case 2:
          text = 'Fair';
          break;
      case 3:
          text = 'Good';
          break;
      case 4:
          text = 'Strong';
          break;
  }
  
  strengthText.textContent = text;
}

/**
* Check password strength
* 
* @param {string} password Password to check
* @return {number} Strength level (0-4)
*/
function checkPasswordStrength(password) {
  let strength = 0;
  
  // Return 0 for empty passwords
  if (password.length === 0) {
      return strength;
  }
  
  // Length check
  if (password.length >= 6) {
      strength += 1;
  }
  if (password.length >= 8) {
      strength += 1;
  }
  
  // Character type checks
  if (/[A-Z]/.test(password)) { // Has uppercase
      strength += 1;
  }
  if (/[0-9]/.test(password)) { // Has number
      strength += 1;
  }
  if (/[^A-Za-z0-9]/.test(password)) { // Has special character
      strength += 1;
  }
  
  return Math.min(strength, 4); // Max strength is 4
}

/**
* Check if email is valid
* 
* @param {string} email Email to validate
* @return {boolean} True if valid, false otherwise
*/
function isValidEmail(email) {
  const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
  return re.test(email);
}

/**
* Display error message for form field
* 
* @param {string} fieldId ID of the field with error
* @param {string} message Error message
*/
function displayError(fieldId, message) {
  const field = document.getElementById(fieldId);
  
  if (!field) {
      return;
  }
  
  // Add error class to field
  const formGroup = field.closest('.form-group');
  if (formGroup) {
      formGroup.classList.add('has-error');
  }
  
  // Create or update error message
  let errorElement = document.querySelector(`#${fieldId} + .invalid-feedback`);
  
  if (!errorElement) {
      errorElement = document.createElement('div');
      errorElement.className = 'invalid-feedback';
      field.parentNode.appendChild(errorElement);
  }
  
  errorElement.textContent = message;
}

/**
* Clear all error messages
*/
function clearErrors() {
  // Remove error class from all form groups
  document.querySelectorAll('.form-group.has-error').forEach(function(group) {
      group.classList.remove('has-error');
  });
  
  // Remove all error messages
  document.querySelectorAll('.invalid-feedback').forEach(function(error) {
      error.textContent = '';
  });
}