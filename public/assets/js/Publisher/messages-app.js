// Publisher Messages JavaScript

// Initialize the page when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    initializeMessagesPage();
    setupEventListeners();
    handleURLMessages();
});

function initializeMessagesPage() {
    console.log('Publisher Messages page loaded successfully!');
    
    // Auto-hide alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            if (alert.parentElement) {
                alert.remove();
            }
        }, 5000);
    });
}

function setupEventListeners() {
    // Close alert buttons
    const closeButtons = document.querySelectorAll('.alert-close');
    closeButtons.forEach(button => {
        button.addEventListener('click', function() {
            this.closest('.alert').remove();
        });
    });
}

function handleURLMessages() {
    // Handle success/error messages from URL parameters
    const urlParams = new URLSearchParams(window.location.search);
    const success = urlParams.get('success');
    const error = urlParams.get('error');
    
    if (success) {
        showSuccessMessage(success);
        // Clean URL
        const url = new URL(window.location);
        url.searchParams.delete('success');
        window.history.replaceState({}, document.title, url);
    }
    
    if (error) {
        showErrorMessage(error);
        // Clean URL
        const url = new URL(window.location);
        url.searchParams.delete('error');
        window.history.replaceState({}, document.title, url);
    }
}

// Tab switching functionality
function showTab(tabName) {
    // Remove active class from all tabs and content
    document.querySelectorAll('.nav-tab').forEach(tab => {
        tab.classList.remove('active');
    });
    
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.remove('active');
    });
    
    // Add active class to selected tab and content
    const activeTab = document.querySelector(`.nav-tab[onclick="showTab('${tabName}')"]`);
    const activeContent = document.getElementById(`${tabName}-tab`);
    
    if (activeTab && activeContent) {
        activeTab.classList.add('active');
        activeContent.classList.add('active');
    }
}

// Message actions
function viewMessage(messageId) {
    if (!messageId) {
        showErrorMessage('Invalid message ID');
        return;
    }
    
    // Navigate to message details page
    window.location.href = `/unipulse/public/publisher/messages/details/${messageId}`;
}

function editMessage(messageId) {
    if (!messageId) {
        showErrorMessage('Invalid message ID');
        return;
    }
    
    // First check if the message can still be edited
    fetch(`/unipulse/public/publisher/messages/canEdit/${messageId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.canEdit) {
                // Navigate to edit page
                window.location.href = `/unipulse/public/publisher/messages/edit/${messageId}`;
            } else {
                showErrorMessage('This message cannot be edited because it has already been read by the recipient.');
            }
        })
        .catch(error => {
            console.error('Error checking edit permission:', error);
            showErrorMessage('Unable to check message edit permission. Please try again.');
        });
}

// Message utility functions
function showSuccessMessage(message) {
    showMessage(message, 'success');
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
    alertElement.innerHTML = `
        <div class="alert-content">
            <i class="fas ${iconClass}"></i>
            <span>${message}</span>
            <button class="alert-close" onclick="this.parentElement.parentElement.remove()">
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

// Search and filter functionality (for future enhancement)
function searchMessages(query) {
    const messageCards = document.querySelectorAll('.message-card');
    const searchLower = query.toLowerCase();
    
    messageCards.forEach(card => {
        const subject = card.querySelector('.message-subject')?.textContent.toLowerCase() || '';
        const preview = card.querySelector('.message-preview')?.textContent.toLowerCase() || '';
        const sender = card.querySelector('.sender-name, .recipient-name')?.textContent.toLowerCase() || '';
        
        const matches = subject.includes(searchLower) || 
                       preview.includes(searchLower) || 
                       sender.includes(searchLower);
        
        card.style.display = matches ? 'block' : 'none';
    });
}

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Ctrl/Cmd + F for search (future enhancement)
    if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
        e.preventDefault();
        // Focus search input when implemented
        console.log('Search shortcut triggered');
    }
    
    // Escape to close alerts
    if (e.key === 'Escape') {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => alert.remove());
    }
});

// Utility function to format dates (for future enhancements)
function formatDate(dateString) {
    const date = new Date(dateString);
    const now = new Date();
    const diffInHours = (now - date) / (1000 * 60 * 60);
    
    if (diffInHours < 1) {
        return 'Just now';
    } else if (diffInHours < 24) {
        return `${Math.floor(diffInHours)} hours ago`;
    } else if (diffInHours < 48) {
        return 'Yesterday';
    } else {
        return date.toLocaleDateString('en-US', {
            month: 'short',
            day: 'numeric',
            year: 'numeric'
        });
    }
}

// Export functions for global access
window.showTab = showTab;
window.viewMessage = viewMessage;
window.editMessage = editMessage;
window.deleteMessageFromList = deleteMessageFromList;

// Delete message from messages list
function deleteMessageFromList(messageId) {
    if (!confirm('Are you sure you want to delete this message? This action cannot be undone.')) {
        return;
    }
    
    // Find the message card
    const messageCard = document.querySelector(`[data-message-id="${messageId}"]`);
    if (messageCard) {
        // Add loading state
        const deleteBtn = messageCard.querySelector('.btn-danger');
        if (deleteBtn) {
            deleteBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            deleteBtn.disabled = true;
        }
        
        // Disable all buttons in the card
        const allButtons = messageCard.querySelectorAll('button');
        allButtons.forEach(btn => btn.disabled = true);
    }
    
    fetch(`/unipulse/public/publisher/messages/delete/${messageId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showSuccessMessage(data.message);
            
            // Remove the message card with animation
            if (messageCard) {
                messageCard.style.transition = 'all 0.3s ease';
                messageCard.style.opacity = '0';
                messageCard.style.transform = 'scale(0.95)';
                
                setTimeout(() => {
                    messageCard.remove();
                    
                    // Update message counts
                    updateMessageCounts();
                    
                    // Check if there are no more messages
                    checkEmptyState();
                }, 300);
            }
        } else {
            showErrorMessage(data.message);
            
            // Restore buttons
            if (messageCard) {
                const allButtons = messageCard.querySelectorAll('button');
                allButtons.forEach(btn => btn.disabled = false);
                
                const deleteBtn = messageCard.querySelector('.btn-danger');
                if (deleteBtn) {
                    deleteBtn.innerHTML = '<i class="fas fa-trash"></i> Delete';
                }
            }
        }
    })
    .catch(error => {
        console.error('Error deleting message:', error);
        showErrorMessage('An error occurred while deleting the message');
        
        // Restore buttons
        if (messageCard) {
            const allButtons = messageCard.querySelectorAll('button');
            allButtons.forEach(btn => btn.disabled = false);
            
            const deleteBtn = messageCard.querySelector('.btn-danger');
            if (deleteBtn) {
                deleteBtn.innerHTML = '<i class="fas fa-trash"></i> Delete';
            }
        }
    });
}

// Update message counts after deletion
function updateMessageCounts() {
    const sentCount = document.querySelectorAll('#sent-tab .message-card').length;
    const receivedCount = document.querySelectorAll('#received-tab .message-card').length;
    
    // Update count badges
    const sentBadge = document.querySelector('button[onclick="showTab(\'sent\')"] .message-count');
    const receivedBadge = document.querySelector('button[onclick="showTab(\'received\')"] .message-count');
    
    if (sentBadge) sentBadge.textContent = sentCount;
    if (receivedBadge) receivedBadge.textContent = receivedCount;
}

// Check if we need to show empty state
function checkEmptyState() {
    const activeTab = document.querySelector('.tab-content.active');
    const messageCards = activeTab.querySelectorAll('.message-card');
    
    if (messageCards.length === 0) {
        const messagesGrid = activeTab.querySelector('.messages-grid');
        if (messagesGrid) {
            // Create empty state
            const emptyState = document.createElement('div');
            emptyState.className = 'empty-state';
            emptyState.innerHTML = `
                <div class="empty-content">
                    <i class="fas fa-paper-plane"></i>
                    <h3>No Messages</h3>
                    <p>All messages have been deleted.</p>
                    <a href="/unipulse/public/publisher/sponsors" class="btn btn-primary">
                        <i class="fas fa-search"></i> Browse Sponsors
                    </a>
                </div>
            `;
            
            // Replace messages grid with empty state
            messagesGrid.parentNode.replaceChild(emptyState, messagesGrid);
        }
    }
}