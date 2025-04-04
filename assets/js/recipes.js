/**
 * Recipes related JavaScript
 */

/**
 * Initialize recipes functionality
 */
function initRecipes() {
  // Initialize search and filtering
  initRecipeFilters();
  
  // Initialize rating forms if they exist
  const ratingForms = document.querySelectorAll('.rating-form form');
  if (ratingForms.length > 0) {
    initRatingForms();
  }
}

/**
 * Initialize recipe filters and search
 */
function initRecipeFilters() {
  const searchInput = document.getElementById('recipe-search');
  const searchBtn = document.getElementById('search-btn');
  const typeFilter = document.getElementById('type-filter');
  const recipesContainer = document.getElementById('recipes-container');
  
  if (!recipesContainer) return;
  
  if (searchBtn && searchInput) {
    searchBtn.addEventListener('click', filterRecipes);
    searchInput.addEventListener('keyup', function(event) {
      if (event.key === 'Enter') {
        filterRecipes();
      }
    });
  }
  
  if (typeFilter) {
    typeFilter.addEventListener('change', filterRecipes);
  }
  
  function filterRecipes() {
    const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';
    const selectedType = typeFilter ? typeFilter.value : '';
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
}

/**
 * Initialize rating forms
 */
function initRatingForms() {
  const ratingForms = document.querySelectorAll('.rating-form form');
  
  ratingForms.forEach(form => {
    form.addEventListener('submit', function(e) {
      e.preventDefault();
      
      let isValid = true;
      
      // Get form fields
      const recipeId = form.querySelector('input[name="recipe_id"]').value;
      const rating = form.querySelector('input[name="rating"]:checked');
      const csrfToken = form.querySelector('input[name="csrf_token"]');
      
      // Validate rating
      if (!rating) {
        // Find the rating container
        const ratingContainer = form.querySelector('.rating-stars');
        
        // Show error message
        const errorElement = document.createElement('div');
        errorElement.className = 'invalid-feedback';
        errorElement.textContent = 'Please select a rating';
        
        // Remove any existing error message
        const existingError = ratingContainer.querySelector('.invalid-feedback');
        if (existingError) {
          existingError.remove();
        }
        
        ratingContainer.appendChild(errorElement);
        isValid = false;
      }
      
      // If form is valid, submit
      if (isValid) {
        const formData = new FormData(form);
        
        // Submit the form data
        fetch('rate-recipe.php', {
          method: 'POST',
          body: formData
        })
        .then(response => {
          if (!response.ok) {
            throw new Error('Network response was not ok');
          }
          return response.json();
        })
        .then(data => {
          if (data.status === 'success') {
            // Show success message
            showMessage(form, 'Rating submitted successfully!', 'success');
            
            // Reset form
            form.reset();
            
            // Reload page after a short delay to show updated rating
            setTimeout(() => {
              window.location.reload();
            }, 1500);
          } else {
            // Show error message
            throw new Error(data.message || 'Failed to submit rating');
          }
        })
        .catch(error => {
          console.error('Error submitting rating:', error);
          
          // Show error message
          showMessage(form, 'Error submitting rating. Please try again.', 'error');
        });
      }
    });
  });
}

/**
 * Show a message in a form
 * @param {HTMLElement} form - The form element
 * @param {string} message - The message to display
 * @param {string} type - The message type (success or error)
 */
function showMessage(form, message, type = 'success') {
  // Create message element
  const messageElement = document.createElement('div');
  messageElement.className = `message ${type}-message`;
  messageElement.textContent = message;
  
  // Style the message
  messageElement.style.padding = '10px';
  messageElement.style.marginTop = '15px';
  messageElement.style.borderRadius = '4px';
  messageElement.style.textAlign = 'center';
  
  if (type === 'success') {
    messageElement.style.backgroundColor = '#d4edda';
    messageElement.style.color = '#155724';
    messageElement.style.border = '1px solid #c3e6cb';
  } else {
    messageElement.style.backgroundColor = '#f8d7da';
    messageElement.style.color = '#721c24';
    messageElement.style.border = '1px solid #f5c6cb';
  }
  
  // Remove any existing message
  const existingMessage = form.querySelector('.message');
  if (existingMessage) {
    existingMessage.remove();
  }
  
  // Add message to form
  form.appendChild(messageElement);
  
  // Remove message after 5 seconds
  setTimeout(() => {
    messageElement.remove();
  }, 5000);
}

// Initialize recipes when the DOM is loaded
document.addEventListener('DOMContentLoaded', initRecipes);