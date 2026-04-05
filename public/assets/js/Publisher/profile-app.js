
/* Extracted from Publisher/profile.view.php */

        // Pass publisher data from PHP to JavaScript
        const publisherData = <?= $publisherJson ?? '{}' ?>;
        
        // Add event listeners for preference buttons
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.preference-btn-custom').forEach(function(button) {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    togglePreferenceBtn(this);
                });
            });
        });
        
        // Toggle preference button and auto-save to database
        function togglePreferenceBtn(button) {
            // Toggle active class
            const wasActive = button.classList.contains('active');
            button.classList.toggle('active');
            const isNowActive = button.classList.contains('active');
            
            console.log('Button:', button.getAttribute('data-preference'), 'Was active:', wasActive, 'Now active:', isNowActive);
            
            // Apply inline styles based on new state
            if (isNowActive) {
                button.style.background = 'linear-gradient(135deg, #4A5BCC 0%, #23387f 100%)';
                button.style.borderColor = '#4A5BCC';
                button.style.color = 'white';
                button.style.boxShadow = '0 4px 15px rgba(74, 91, 204, 0.3)';
            } else {
                button.style.background = '#fafafa';
                button.style.borderColor = '#e0e0e0';
                button.style.color = '#666';
                button.style.boxShadow = 'none';
            }
            
            // Get all active preferences after toggle
            const activePreferences = [];
            document.querySelectorAll('.preference-btn-custom.active').forEach(btn => {
                const preference = btn.getAttribute('data-preference');
                if (preference) {
                    activePreferences.push(preference);
                }
            });
            
            console.log('Active preferences now:', activePreferences);
            
            // Auto-save to database via AJAX
            fetch('/unipulse/public/publisher/profile/updatePreferences', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'selected_preferences=' + encodeURIComponent(JSON.stringify(activePreferences))
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log('✓ Saved successfully');
                } else {
                    console.error('✗ Failed:', data.message);
                    // Revert on error
                    button.classList.toggle('active');
                    if (wasActive) {
                        button.style.background = 'linear-gradient(135deg, #4A5BCC 0%, #23387f 100%)';
                        button.style.borderColor = '#4A5BCC';
                        button.style.color = 'white';
                        button.style.boxShadow = '0 4px 15px rgba(74, 91, 204, 0.3)';
                    } else {
                        button.style.background = '#fafafa';
                        button.style.borderColor = '#e0e0e0';
                        button.style.color = '#666';
                        button.style.boxShadow = 'none';
                    }
                }
            })
            .catch(error => {
                console.error('✗ Error:', error);
                // Revert on error
                button.classList.toggle('active');
                if (wasActive) {
                    button.style.background = 'linear-gradient(135deg, #4A5BCC 0%, #23387f 100%)';
                    button.style.borderColor = '#4A5BCC';
                    button.style.color = 'white';
                    button.style.boxShadow = '0 4px 15px rgba(74, 91, 204, 0.3)';
                } else {
                    button.style.background = '#fafafa';
                    button.style.borderColor = '#e0e0e0';
                    button.style.color = '#666';
                    button.style.boxShadow = 'none';
                }
            });
        }
    
