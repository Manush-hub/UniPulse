
/* Extracted from Publisher/editevent.view.php */

    // Dropdown scroll functionality - show 5 items when opened
    document.addEventListener('DOMContentLoaded', function() {
        // Cover image preview functionality
        const coverFileInput = document.getElementById('coverFileInput');
        if (coverFileInput) {
            coverFileInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const newImagePreview = document.getElementById('newImagePreview');
                        const newImageContainer = document.getElementById('newImagePreviewContainer');
                        
                        newImagePreview.src = e.target.result;
                        newImageContainer.style.display = 'block';
                    };
                    reader.readAsDataURL(file);
                }
            });
        }
    });
    
    // Function to clear new image selection
    function clearNewImage() {
        const coverFileInput = document.getElementById('coverFileInput');
        const newImageContainer = document.getElementById('newImagePreviewContainer');
        
        coverFileInput.value = '';
        newImageContainer.style.display = 'none';
    }
    
    // Function to show success message
    function showSuccessMessage(message) {
        // Remove any existing success messages
        const existingMessage = document.querySelector('.success-message');
        if (existingMessage) {
            existingMessage.remove();
        }
        
        // Create success message element
        const successDiv = document.createElement('div');
        successDiv.className = 'success-message';
        successDiv.style.cssText = `
            background: #4CAF50;
            color: white;
            padding: 15px 20px;
            border-radius: 5px;
            margin: 20px 0;
            font-size: 16px;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            animation: slideDown 0.3s ease-out;
        `;
        successDiv.innerHTML = `
            <strong>✓ ${message}</strong>
            <button onclick="this.parentElement.remove()" style="
                background: none; 
                border: none; 
                color: white; 
                float: right; 
                cursor: pointer; 
                font-size: 18px;
                margin-top: -2px;
            ">×</button>
        `;
        
        // Insert at the top of the form
        const form = document.getElementById('edit-event');
        form.insertBefore(successDiv, form.firstChild);
        
        // Auto-remove after 5 seconds
        setTimeout(() => {
            if (successDiv.parentElement) {
                successDiv.remove();
            }
        }, 5000);
    }
    
    // Handle form submission
    document.getElementById('edit-event').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const submitBtn = document.querySelector('.publish-btn');
        const originalText = submitBtn.innerHTML;
        
        // Show loading state
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating Event...';
        submitBtn.disabled = true;
        
        // Get form data
        const formData = new FormData(this);
        
        // Ensure AJAX detection
        formData.set('ajax', '1');
        
        // Submit form
        fetch(this.action, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => {
            console.log('Response status:', response.status);
            console.log('Response headers:', response.headers.get('content-type'));
            
            if (!response.ok && response.status !== 200) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            // Check if response is JSON
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                return response.text().then(text => {
                    console.error('Non-JSON response received:', text);
                    throw new Error('Server returned non-JSON response: ' + text.substring(0, 200));
                });
            }
            
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Show success message with better styling
                showSuccessMessage('Event updated successfully! Redirecting to event view...');
                
                // Redirect to event view after a short delay
                setTimeout(function() {
                    window.location.href = '/unipulse/public/publisher/eventview?id=<?= $data['event_id'] ?>';
                }, 1500);
            } else {
                // Show error messages
                let errorMessage = 'Please fix the following errors:\n';
                if (data.errors) {
                    for (const [field, message] of Object.entries(data.errors)) {
                        errorMessage += `- ${message}\n`;
                    }
                } else {
                    errorMessage += data.message || 'Unknown error occurred';
                }
                alert(errorMessage);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Network or server error: ' + error.message + '. Please check the console for more details.');
        })
        .finally(() => {
            // Reset button
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        });
    });
    
