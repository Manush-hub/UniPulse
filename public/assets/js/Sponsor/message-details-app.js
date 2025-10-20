// Initialize message details page on load
document.addEventListener('DOMContentLoaded', function () {
    setupEventListeners();
});

let deleteMessageId = null;

function setupEventListeners() {
    // Modal close events
    window.addEventListener('click', function(event) {
        const replyModal = document.getElementById('replyModal');
        const deleteModal = document.getElementById('deleteModal');
        
        if (event.target === replyModal) {
            closeReplyModal();
        }
        if (event.target === deleteModal) {
            closeDeleteModal();
        }
    });
    
    // Escape key to close modals
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeReplyModal();
            closeDeleteModal();
        }
    });
    
    // Reply form submission
    const replyForm = document.getElementById('replyForm');
    if (replyForm) {
        replyForm.addEventListener('submit', handleReplySubmission);
    }
}

function openReplyModal() {
    const modal = document.getElementById('replyModal');
    if (modal) {
        modal.style.display = 'block';
        document.body.style.overflow = 'hidden';
        
        // Focus on message textarea
        setTimeout(() => {
            const messageTextarea = document.getElementById('replyMessage');
            if (messageTextarea) {
                messageTextarea.focus();
            }
        }, 300);
    }
}

function closeReplyModal() {
    const modal = document.getElementById('replyModal');
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
        
        // Clear form
        const form = document.getElementById('replyForm');
        if (form) {
            const messageTextarea = document.getElementById('replyMessage');
            if (messageTextarea) {
                messageTextarea.value = '';
            }
        }
    }
}

function handleReplySubmission(event) {
    event.preventDefault();
    
    const form = event.target;
    const formData = new FormData(form);
    
    // Show loading state
    const submitButton = form.querySelector('button[type="submit"]');
    const originalText = submitButton.textContent;
    submitButton.textContent = 'Sending...';
    submitButton.disabled = true;
    
    // Submit reply
    fetch('/unipulse/public/sponsor/messages/reply', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeReplyModal();
            showSuccessMessage(data.message || 'Reply sent successfully!');
        } else {
            throw new Error(data.message || 'Failed to send reply');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showErrorMessage(error.message || 'Failed to send reply. Please try again.');
    })
    .finally(() => {
        submitButton.textContent = originalText;
        submitButton.disabled = false;
    });
}

function deleteMessage(messageId) {
    deleteMessageId = messageId;
    openDeleteModal();
}

function openDeleteModal() {
    const modal = document.getElementById('deleteModal');
    if (modal) {
        modal.style.display = 'block';
        document.body.style.overflow = 'hidden';
    }
}

function closeDeleteModal() {
    const modal = document.getElementById('deleteModal');
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
        deleteMessageId = null;
    }
}

function confirmDelete() {
    if (!deleteMessageId) return;
    
    fetch(`/unipulse/public/sponsor/messages/delete/${deleteMessageId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeDeleteModal();
            showSuccessMessage(data.message || 'Message deleted successfully');
            
            // Redirect back to messages list after a short delay
            setTimeout(() => {
                window.location.href = '/unipulse/public/sponsor/messages';
            }, 1500);
        } else {
            showErrorMessage(data.message || 'Failed to delete message');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showErrorMessage('Failed to delete message');
    })
    .finally(() => {
        closeDeleteModal();
    });
}

function showSuccessMessage(message) {
    showMessage(message, 'success');
}

function showErrorMessage(message) {
    showMessage(message, 'error');
}

function showMessage(message, type) {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.innerHTML = `
        <div class="notification-content">
            <span class="notification-message">${message}</span>
            <button class="notification-close" onclick="this.parentElement.parentElement.remove()">&times;</button>
        </div>
    `;
    
    // Add to page
    document.body.appendChild(notification);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (notification.parentElement) {
            notification.remove();
        }
    }, 5000);
}