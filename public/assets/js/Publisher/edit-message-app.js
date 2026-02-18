// Edit Message JavaScript

// Initialize the page when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    initializeEditPage();
    setupEventListeners();
    setupFormValidation();
    setupPreviewUpdates();
});

function initializeEditPage() {
    console.log('Edit Message page loaded successfully!');
    
    // Update character counters on page load
    updateCharacterCounters();
}

function setupEventListeners() {
    const form = document.getElementById('editMessageForm');
    const subjectInput = document.getElementById('subject');
    const messageTextarea = document.getElementById('message');
    
    // Form submission
    if (form) {
        form.addEventListener('submit', handleFormSubmission);
    }
    
    // Character counters
    if (subjectInput) {
        subjectInput.addEventListener('input', updateCharacterCounters);
    }
    
    if (messageTextarea) {
        messageTextarea.addEventListener('input', updateCharacterCounters);
    }
    
    // Keyboard shortcuts
    document.addEventListener('keydown', handleKeyboardShortcuts);
}

function setupFormValidation() {
    const subjectInput = document.getElementById('subject');
    const messageTextarea = document.getElementById('message');
    
    // Real-time validation
    if (subjectInput) {
        subjectInput.addEventListener('blur', validateSubject);
        subjectInput.addEventListener('input', clearValidationError);
    }
    
    if (messageTextarea) {
        messageTextarea.addEventListener('blur', validateMessage);
        messageTextarea.addEventListener('input', clearValidationError);
    }
}

function setupPreviewUpdates() {
    const subjectInput = document.getElementById('subject');
    const messageTextarea = document.getElementById('message');
    
    // Update preview in real-time
    if (subjectInput) {
        subjectInput.addEventListener('input', updatePreview);
    }
    
    if (messageTextarea) {
        messageTextarea.addEventListener('input', updatePreview);
    }
}

function handleFormSubmission(event) {
    event.preventDefault();
    
    const form = event.target;
    const submitButton = form.querySelector('button[type="submit"]');
    const originalButtonText = submitButton.textContent;
    
    // Validate form
    if (!validateForm()) {
        return;
    }
    
    // Show loading state
    showLoadingOverlay();
    submitButton.disabled = true;
    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';
    
    // Prepare form data
    const formData = new FormData(form);
    
    // Submit the form
    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message and redirect
            showSuccessMessageAndRedirect(data.message);
        } else {
            throw new Error(data.message || 'Failed to update message');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        hideLoadingOverlay();
        submitButton.disabled = false;
        submitButton.textContent = originalButtonText;
        showErrorMessage(error.message || 'Failed to update message. Please try again.');
    });
}

function validateForm() {
    const isSubjectValid = validateSubject();
    const isMessageValid = validateMessage();
    
    return isSubjectValid && isMessageValid;
}

function validateSubject() {
    const subjectInput = document.getElementById('subject');
    const value = subjectInput.value.trim();
    
    if (value.length === 0) {
        showFieldError(subjectInput, 'Subject is required');
        return false;
    }
    
    if (value.length > 200) {
        showFieldError(subjectInput, 'Subject must be 200 characters or less');
        return false;
    }
    
    clearFieldError(subjectInput);
    return true;
}

function validateMessage() {
    const messageTextarea = document.getElementById('message');
    const value = messageTextarea.value.trim();
    
    if (value.length === 0) {
        showFieldError(messageTextarea, 'Message is required');
        return false;
    }
    
    if (value.length > 2000) {
        showFieldError(messageTextarea, 'Message must be 2000 characters or less');
        return false;
    }
    
    clearFieldError(messageTextarea);
    return true;
}

function showFieldError(field, message) {
    clearFieldError(field);
    
    field.style.borderColor = '#ef4444';
    
    const errorElement = document.createElement('div');
    errorElement.className = 'field-error';
    errorElement.style.cssText = `
        color: #ef4444;
        font-size: 0.75rem;
        margin-top: 0.25rem;
        display: flex;
        align-items: center;
        gap: 0.25rem;
    `;
    errorElement.innerHTML = `<i class="fas fa-exclamation-circle"></i> ${message}`;
    
    field.parentNode.appendChild(errorElement);
}

function clearFieldError(field) {
    field.style.borderColor = '#d1d5db';
    
    const existingError = field.parentNode.querySelector('.field-error');
    if (existingError) {
        existingError.remove();
    }
}

function clearValidationError(event) {
    clearFieldError(event.target);
}

function updateCharacterCounters() {
    const subjectInput = document.getElementById('subject');
    const messageTextarea = document.getElementById('message');
    const subjectCounter = document.getElementById('subject-count');
    const messageCounter = document.getElementById('message-count');
    
    if (subjectInput && subjectCounter) {
        const count = subjectInput.value.length;
        subjectCounter.textContent = count;
        
        // Color coding for character limit
        if (count > 180) {
            subjectCounter.style.color = '#ef4444';
        } else if (count > 150) {
            subjectCounter.style.color = '#f59e0b';
        } else {
            subjectCounter.style.color = '#6b7280';
        }
    }
    
    if (messageTextarea && messageCounter) {
        const count = messageTextarea.value.length;
        messageCounter.textContent = count;
        
        // Color coding for character limit
        if (count > 1800) {
            messageCounter.style.color = '#ef4444';
        } else if (count > 1500) {
            messageCounter.style.color = '#f59e0b';
        } else {
            messageCounter.style.color = '#6b7280';
        }
    }
}

function updatePreview() {
    const subjectInput = document.getElementById('subject');
    const messageTextarea = document.getElementById('message');
    const previewSubject = document.getElementById('preview-subject');
    const previewMessage = document.getElementById('preview-message');
    
    if (subjectInput && previewSubject) {
        previewSubject.textContent = subjectInput.value || 'Subject will appear here...';
    }
    
    if (messageTextarea && previewMessage) {
        const messageText = messageTextarea.value || 'Message content will appear here...';
        previewMessage.innerHTML = messageText.replace(/\n/g, '<br>');
    }
}

function handleKeyboardShortcuts(event) {
    // Ctrl/Cmd + S to save
    if ((event.ctrlKey || event.metaKey) && event.key === 's') {
        event.preventDefault();
        const form = document.getElementById('editMessageForm');
        if (form) {
            form.dispatchEvent(new Event('submit'));
        }
    }
    
    // Escape to cancel
    if (event.key === 'Escape') {
        const confirmCancel = confirm('Are you sure you want to cancel? Any unsaved changes will be lost.');
        if (confirmCancel) {
            cancelEdit();
        }
    }
}

function cancelEdit() {
    // Check if there are unsaved changes
    const form = document.getElementById('editMessageForm');
    const formData = new FormData(form);
    
    // For now, just navigate back - in a real app you might want to check for changes
    window.location.href = '/unipulse/public/publisher/messages';
}

function showLoadingOverlay() {
    const overlay = document.getElementById('loadingOverlay');
    if (overlay) {
        overlay.style.display = 'flex';
    }
}

function hideLoadingOverlay() {
    const overlay = document.getElementById('loadingOverlay');
    if (overlay) {
        overlay.style.display = 'none';
    }
}

function showSuccessMessageAndRedirect(message) {
    // Store success message in session storage
    sessionStorage.setItem('successMessage', message);
    
    // Redirect to messages page
    window.location.href = '/unipulse/public/publisher/messages';
}

function showErrorMessage(message) {
    showMessage(message, 'error');
}

function showMessage(message, type) {
    // Remove existing alerts
    const existingAlerts = document.querySelectorAll('.alert');
    existingAlerts.forEach(alert => alert.remove());
    
    const alertClass = type === 'success' ? 'alert-success' : 'alert-error';
    const iconClass = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
    
    const alertElement = document.createElement('div');
    alertElement.className = `alert ${alertClass}`;
    alertElement.style.cssText = `
        position: fixed;
        top: 100px;
        right: 1rem;
        max-width: 400px;
        padding: 1rem;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        z-index: 1000;
        animation: slideInRight 0.3s ease;
    `;
    
    if (type === 'success') {
        alertElement.style.backgroundColor = '#d1fae5';
        alertElement.style.border = '1px solid #a7f3d0';
        alertElement.style.color = '#065f46';
    } else {
        alertElement.style.backgroundColor = '#fee2e2';
        alertElement.style.border = '1px solid #fecaca';
        alertElement.style.color = '#991b1b';
    }
    
    alertElement.innerHTML = `
        <div style="display: flex; align-items: center; gap: 0.5rem;">
            <i class="fas ${iconClass}"></i>
            <span>${message}</span>
            <button onclick="this.parentElement.parentElement.remove()" style="
                background: none;
                border: none;
                cursor: pointer;
                color: inherit;
                margin-left: auto;
                padding: 0.25rem;
            ">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
    
    document.body.appendChild(alertElement);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (alertElement.parentElement) {
            alertElement.remove();
        }
    }, 5000);
}

// Check for success message from session storage (when returning from edit)
document.addEventListener('DOMContentLoaded', function() {
    const successMessage = sessionStorage.getItem('successMessage');
    if (successMessage) {
        showMessage(successMessage, 'success');
        sessionStorage.removeItem('successMessage');
    }
});

// Export functions for global access
window.cancelEdit = cancelEdit;
window.deleteMessage = deleteMessage;
window.closeDeleteModal = closeDeleteModal;
window.confirmDelete = confirmDelete;

// Delete message functionality
let messageToDelete = null;

function deleteMessage(messageId) {
    messageToDelete = messageId;
    const modal = document.getElementById('deleteModal');
    if (modal) {
        modal.style.display = 'flex';
        modal.style.position = 'fixed';
        modal.style.top = '0';
        modal.style.left = '0';
        modal.style.width = '100%';
        modal.style.height = '100%';
        modal.style.backgroundColor = 'rgba(0, 0, 0, 0.5)';
        modal.style.justifyContent = 'center';
        modal.style.alignItems = 'center';
        modal.style.zIndex = '1000';
    }
}

function closeDeleteModal() {
    const modal = document.getElementById('deleteModal');
    if (modal) {
        modal.style.display = 'none';
    }
    messageToDelete = null;
}

function confirmDelete() {
    if (!messageToDelete) {
        showMessage('No message selected for deletion', 'error');
        return;
    }
    
    // Show loading state
    const confirmBtn = document.querySelector('#deleteModal .btn-danger');
    const originalText = confirmBtn.innerHTML;
    confirmBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Deleting...';
    confirmBtn.disabled = true;
    
    fetch(`/unipulse/public/publisher/messages/delete/${messageToDelete}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showMessage(data.message, 'success');
            
            // Close modal
            closeDeleteModal();
            
            // Redirect to messages page after a short delay
            setTimeout(() => {
                window.location.href = '/unipulse/public/publisher/messages';
            }, 1500);
        } else {
            showMessage(data.message, 'error');
            
            // Reset button
            confirmBtn.innerHTML = originalText;
            confirmBtn.disabled = false;
        }
    })
    .catch(error => {
        console.error('Error deleting message:', error);
        showMessage('An error occurred while deleting the message', 'error');
        
        // Reset button
        confirmBtn.innerHTML = originalText;
        confirmBtn.disabled = false;
    });
}