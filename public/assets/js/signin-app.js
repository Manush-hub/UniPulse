
/* Extracted from signin.view.php */

                        function submitAppeal() {
                            const message = document.getElementById('appealMessage').value.trim();
                            
                            if (!message) {
                                alert('Please enter your appeal message');
                                return;
                            }
                            
                            fetch('/unipulse/public/signin/submitAppeal', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                },
                                body: JSON.stringify({
                                    user_id: <?= $user_id ?>,
                                    user_type: '<?= str_replace('_users', '', $user_type) ?>',
                                    appeal_message: message
                                })
                            })
                            .then(response => response.json())
                            .then(data => {
                                const resultDiv = document.getElementById('appealResult');
                                resultDiv.style.display = 'block';
                                
                                if (data.success) {
                                    resultDiv.innerHTML = '<div style="padding: 15px; background: #d4edda; color: #155724; border-radius: 4px;"><i class="fas fa-check-circle"></i> ' + data.message + '</div>';
                                    document.getElementById('appealMessage').value = '';
                                    setTimeout(() => {
                                        window.location.href = '/unipulse/public/signin';
                                    }, 3000);
                                } else {
                                    resultDiv.innerHTML = '<div style="padding: 15px; background: #f8d7da; color: #721c24; border-radius: 4px;"><i class="fas fa-exclamation-circle"></i> ' + data.message + '</div>';
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                alert('An error occurred while submitting your appeal');
                            });
                        }
                    

/* Extracted from signin.view.php */

        // Password toggle functionality
        document.addEventListener('DOMContentLoaded', function() {
            const passwordToggle = document.getElementById('passwordToggle');
            const passwordInput = document.getElementById('password');
            
            if (passwordToggle && passwordInput) {
                passwordToggle.addEventListener('click', function() {
                    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInput.setAttribute('type', type);
                    
                    const icon = this.querySelector('i');
                    if (type === 'password') {
                        icon.classList.remove('fa-eye-slash');
                        icon.classList.add('fa-eye');
                    } else {
                        icon.classList.remove('fa-eye');
                        icon.classList.add('fa-eye-slash');
                    }
                });
            }
            
            // Auto-focus on email field
            const emailInput = document.getElementById('email');
            if (emailInput) {
                emailInput.focus();
            }
        });
    
