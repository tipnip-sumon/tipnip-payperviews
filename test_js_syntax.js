// Extract just the JavaScript from the login form to validate syntax
document.querySelector('.stunning-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const form = this;
    const button = this.querySelector('.stunning-submit');
    const btnText = button.querySelector('.btn-text');
    const btnLoader = button.querySelector('.btn-loader');
    const btnIcon = button.querySelector('.btn-icon');
    
    // Get form values
    const username = document.getElementById('username').value.trim();
    const password = document.getElementById('signin-password').value;
    
    // Clear previous error styling
    document.querySelectorAll('.stunning-input').forEach(input => {
        input.parentElement.style.borderColor = '';
        input.parentElement.style.background = '';
        input.classList.remove('is-invalid');
    });
    
    // Clear previous error messages
    document.querySelectorAll('.beautiful-error').forEach(error => {
        error.style.display = 'none';
    });
    
    let hasValidationErrors = false;
    let errorMessages = [];
    
    // Client-side validation
    if (!username) {
        showFieldError('username', 'Username or email is required');
        errorMessages.push('Username or email is required');
        hasValidationErrors = true;
    } else if (username.length < 3) {
        showFieldError('username', 'Username or email must be at least 3 characters');
        errorMessages.push('Username or email must be at least 3 characters');
        hasValidationErrors = true;
    }
    
    if (!password) {
        showFieldError('signin-password', 'Password is required');
        errorMessages.push('Password is required');
        hasValidationErrors = true;
    } else if (password.length < 8) {
        showFieldError('signin-password', 'Password must be at least 8 characters');
        errorMessages.push('Password must be at least 8 characters');
        hasValidationErrors = true;
    }
    
    // If validation fails, show SweetAlert and stop
    if (hasValidationErrors) {
        const errorHtml = errorMessages.map(error => `• ${error}`).join('<br>');
        
        Swal.fire({
            title: 'Validation Error!',
            html: `Please correct the following:<br><br>${errorHtml}`,
            icon: 'error',
            confirmButtonText: 'Fix Errors',
            width: '500px'
        });
        return false;
    }
    
    // Show loading state
    btnText.style.opacity = '0';
    btnIcon.style.opacity = '0';
    btnLoader.style.display = 'block';
    button.disabled = true;
    
    // Simple form submission - let Laravel handle CSRF properly
    // Use setTimeout to allow UI to update before submission
    setTimeout(() => {
        try {
            console.log('Submitting login form normally (no AJAX)');
            // Submit the form normally - no AJAX to avoid 419 issues
            form.submit();
        } catch (error) {
            console.error('Form submission error:', error);
            resetSubmitButton();
            
            Swal.fire({
                title: 'Submission Error!',
                text: 'There was an error submitting the form. Please try again.',
                icon: 'error',
                confirmButtonText: 'Try Again'
            });
        }
    }, 100);
    
    function resetSubmitButton() {
        btnText.style.opacity = '1';
        btnIcon.style.opacity = '1';
        btnLoader.style.display = 'none';
        button.disabled = false;
    }
    
    return false; // Prevent default form submission
});
