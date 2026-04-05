// Help tooltip functionality
        function toggleHelp() {
            const tooltip = document.getElementById('helpTooltip');
            tooltip.classList.toggle('active');
        }

        // Close tooltip when clicking outside
        document.addEventListener('click', function(e) {
            const helpIcon = document.querySelector('.help-icon');
            const tooltip = document.getElementById('helpTooltip');
            
            if (!helpIcon.contains(e.target) && !tooltip.contains(e.target)) {
                tooltip.classList.remove('active');
            }
        });

        // Single file upload functionality
        document.getElementById('confirmation-file').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const fileInput = document.querySelector('.file-upload-input');
            
            if (file) {
                fileInput.value = file.name;
                fileInput.classList.add('has-files');
            } else {
                fileInput.value = 'Upload club verification document';
                fileInput.classList.remove('has-files');
            }
        });

        // File validation for single file
        document.getElementById('confirmation-file').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;
            
            const maxSize = 5 * 1024 * 1024; // 5MB
            const allowedTypes = ['.pdf', '.jpg', '.jpeg', '.png', '.doc', '.docx'];
            
            // Check file size
            if (file.size > maxSize) {
                alert(`File "${file.name}" is too large. Maximum size is 5MB.`);
                e.target.value = '';
                document.querySelector('.file-upload-input').value = 'Upload club verification document';
                document.querySelector('.file-upload-input').classList.remove('has-files');
                return;
            }
            
            // Check file type
            const fileExtension = '.' + file.name.split('.').pop().toLowerCase();
            if (!allowedTypes.includes(fileExtension)) {
                alert(`File "${file.name}" has an unsupported format. Please use: PDF, JPG, PNG, DOC, or DOCX.`);
                e.target.value = '';
                document.querySelector('.file-upload-input').value = 'Upload club verification document';
                document.querySelector('.file-upload-input').classList.remove('has-files');
                return;
            }
        });

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