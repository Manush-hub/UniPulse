// Moderator Messages App - Client-side functionality for sending messages to publishers

document.addEventListener('DOMContentLoaded', function() {
    console.log('Moderator Messages App initialized');
    initializeMessageForm();
});

function initializeMessageForm() {
    const form = document.getElementById('messageForm');
    if (!form) return;

    const subjectInput = document.getElementById('subject');
    const messageInput = document.getElementById('message');
    const subjectCounter = document.getElementById('subjectCounter');
    const messageCounter = document.getElementById('messageCounter');
    const submitBtn = document.getElementById('submitBtn');

    // Character counters
    if (subjectInput && subjectCounter) {
        subjectInput.addEventListener('input', function() {
            updateCharCounter(this, subjectCounter, 200);
        });
    }

    if (messageInput && messageCounter) {
        messageInput.addEventListener('input', function() {
            updateCharCounter(this, messageCounter, 2000);
        });
    }

    // Form submission
    form.addEventListener('submit', async function(e) {
        e.preventDefault();

        // Validate form
        if (!validateForm()) {
            return;
        }

        // Disable submit button
        submitBtn.disabled = true;
        submitBtn.classList.add('loading');
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';

        // Prepare form data
        const formData = new FormData(form);

        try {
            const response = await fetch('/unipulse/public/moderator/messages/send', {
                method: 'POST',
                body: formData
            });

            // Log response status for debugging
            console.log('Response status:', response.status);
            
            // Check if response is ok
            if (!response.ok) {
                console.error('Response not OK:', response.status, response.statusText);
            }

            // Try to parse JSON
            let result;
            try {
                const text = await response.text();
                console.log('Response text:', text);
                result = JSON.parse(text);
            } catch (parseError) {
                console.error('JSON parse error:', parseError);
                throw new Error('Invalid server response. Please check the server logs.');
            }

            if (result.success) {
                showAlert('success', result.message);
                
                // Reset form
                form.reset();
                updateCharCounter(subjectInput, subjectCounter, 200);
                updateCharCounter(messageInput, messageCounter, 2000);
                
                // Hide preview
                const previewSection = document.getElementById('previewSection');
                if (previewSection) {
                    previewSection.style.display = 'none';
                }

                // Redirect after 2 seconds
                setTimeout(() => {
                    window.location.href = '/unipulse/public/moderator/messages';
                }, 2000);
            } else {
                showAlert('error', result.message || 'Failed to send message');
                
                // Re-enable submit button
                submitBtn.disabled = false;
                submitBtn.classList.remove('loading');
                submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Send Message';
            }
        } catch (error) {
            console.error('Error sending message:', error);
            showAlert('error', error.message || 'An error occurred while sending the message. Please try again.');
            
            // Re-enable submit button
            submitBtn.disabled = false;
            submitBtn.classList.remove('loading');
            submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Send Message';
        }
    });
}

function updateCharCounter(input, counter, maxLength) {
    const length = input.value.length;
    counter.textContent = length;
    
    const parent = counter.parentElement;
    parent.classList.remove('warning', 'danger');
    
    const percentage = (length / maxLength) * 100;
    if (percentage >= 90) {
        parent.classList.add('danger');
    } else if (percentage >= 75) {
        parent.classList.add('warning');
    }
}

function validateForm() {
    const publisherId = document.getElementById('publisher_id').value;
    const subject = document.getElementById('subject').value.trim();
    const message = document.getElementById('message').value.trim();
    const errors = [];

    if (!publisherId) {
        errors.push('Please select a publisher');
    }

    if (!subject) {
        errors.push('Subject is required');
    } else if (subject.length > 200) {
        errors.push('Subject must not exceed 200 characters');
    }

    if (!message) {
        errors.push('Message is required');
    } else if (message.length > 2000) {
        errors.push('Message must not exceed 2000 characters');
    }

    if (errors.length > 0) {
        showAlert('error', errors.join('<br>'));
        return false;
    }

    return true;
}

function togglePreview() {
    const previewSection = document.getElementById('previewSection');
    const subject = document.getElementById('subject').value.trim();
    const message = document.getElementById('message').value.trim();
    const previewSubject = document.getElementById('previewSubject');
    const previewMessage = document.getElementById('previewMessage');

    if (!subject && !message) {
        showAlert('error', 'Please enter a subject and message to preview');
        return;
    }

    // Update preview content
    if (previewSubject) {
        previewSubject.textContent = subject || '-';
    }
    if (previewMessage) {
        previewMessage.textContent = message || '-';
    }

    // Toggle preview visibility
    if (previewSection) {
        if (previewSection.style.display === 'none' || !previewSection.style.display) {
            previewSection.style.display = 'block';
            previewSection.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        } else {
            previewSection.style.display = 'none';
        }
    }
}

function showAlert(type, message) {
    const alertDiv = document.getElementById('messageAlert');
    if (!alertDiv) return;

    alertDiv.className = `alert alert-${type}`;
    alertDiv.innerHTML = message;
    alertDiv.style.display = 'block';

    // Scroll to alert
    alertDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });

    // Auto-hide success messages after 5 seconds
    if (type === 'success') {
        setTimeout(() => {
            alertDiv.style.display = 'none';
        }, 5000);
    }
}

// Make togglePreview available globally
window.togglePreview = togglePreview;
