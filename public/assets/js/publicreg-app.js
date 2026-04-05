
/* Extracted from publicreg.view.php */

        // Terms validation with improved feedback
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            const termsCheckbox = document.getElementById('terms');
            
            // Add event listener to form submission
            form.addEventListener('submit', function(e) {
                if (!termsCheckbox.checked) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    // Add visual feedback
                    termsCheckbox.style.border = '2px solid #dc3545';
                    
                    // Show alert
                    alert('Please agree to the Terms & Conditions and Privacy Policy to continue.');
                    
                    // Focus on checkbox
                    termsCheckbox.focus();
                    
                    // Remove visual feedback after 3 seconds
                    setTimeout(() => {
                        termsCheckbox.style.border = '2px solid #ccc';
                    }, 3000);
                    
                    return false;
                }
            });
            
            // Remove error styling when checkbox is checked
            termsCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    this.style.border = '2px solid #ccc';
                }
            });
        });
    
