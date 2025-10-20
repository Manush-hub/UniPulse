// Initialize messages page on load
document.addEventListener('DOMContentLoaded', function () {
    initializeMessagesPage();
    setupEventListeners();
});

let allMessages = [];
let deleteMessageId = null;
let currentPopupMessageId = null;

function initializeMessagesPage() {
    // Store all message cards for filtering
    allMessages = Array.from(document.querySelectorAll('.message-card'));
    
    // Set up filter functionality
    setupFilters();
}

function setupEventListeners() {
    // Filter tabs
    const filterTabs = document.querySelectorAll('.filter-tab');
    filterTabs.forEach(tab => {
        tab.addEventListener('click', function() {
            // Update active tab
            filterTabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            
            // Filter messages
            const filter = this.dataset.filter;
            filterMessages(filter);
        });
    });
    
    // Modal close events
    window.addEventListener('click', function(event) {
        const deleteModal = document.getElementById('deleteModal');
        const messagePopupModal = document.getElementById('messagePopupModal');
        
        if (event.target === deleteModal) {
            closeDeleteModal();
        }
        if (event.target === messagePopupModal) {
            closeMessagePopup();
        }
    });
    
    // Escape key to close modals
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeDeleteModal();
            closeMessagePopup();
        }
    });
}

function setupFilters() {
    // Initial filter setup if needed
}

function filterMessages(filter) {
    allMessages.forEach(message => {
        const readStatus = message.dataset.readStatus;
        let shouldShow = true;
        
        switch (filter) {
            case 'unread':
                shouldShow = readStatus === 'unread';
                break;
            case 'read':
                shouldShow = readStatus === 'read';
                break;
            case 'all':
            default:
                shouldShow = true;
                break;
        }
        
        message.style.display = shouldShow ? 'block' : 'none';
    });
    
    // Update empty state
    updateEmptyState(filter);
}

function updateEmptyState(filter) {
    const visibleMessages = allMessages.filter(message => 
        message.style.display !== 'none'
    );
    
    const messagesList = document.getElementById('messagesList');
    const existingEmpty = messagesList.querySelector('.no-messages');
    
    if (visibleMessages.length === 0) {
        if (!existingEmpty) {
            const emptyState = createEmptyState(filter);
            messagesList.appendChild(emptyState);
        }
    } else {
        if (existingEmpty) {
            existingEmpty.remove();
        }
    }
}

function createEmptyState(filter) {
    const emptyDiv = document.createElement('div');
    emptyDiv.className = 'no-messages';
    
    let title, description;
    switch (filter) {
        case 'unread':
            title = 'No Unread Messages';
            description = 'All your messages have been read.';
            break;
        case 'read':
            title = 'No Read Messages';
            description = 'You haven\'t read any messages yet.';
            break;
        default:
            title = 'No Messages Yet';
            description = 'You haven\'t received any messages from publishers yet.';
            break;
    }
    
    emptyDiv.innerHTML = `
        <div class="no-messages-content">
            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                <polyline points="22 6 12 13 2 6"></polyline>
            </svg>
            <h3>${title}</h3>
            <p>${description}</p>
        </div>
    `;
    
    return emptyDiv;
}

function viewMessage(messageId) {
    window.location.href = `/unipulse/public/sponsor/messages/details/${messageId}`;
}

function markAsRead(messageId) {
    fetch(`/unipulse/public/sponsor/messages/markRead/${messageId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Update UI
            const messageCard = document.querySelector(`[data-message-id="${messageId}"]`);
            if (messageCard) {
                messageCard.classList.remove('unread');
                messageCard.classList.add('read');
                messageCard.dataset.readStatus = 'read';
                
                // Remove unread indicator
                const unreadIndicator = messageCard.querySelector('.unread-indicator');
                if (unreadIndicator) {
                    unreadIndicator.remove();
                }
                
                // Remove mark as read button
                const markReadBtn = messageCard.querySelector('button[onclick*="markAsRead"]');
                if (markReadBtn) {
                    markReadBtn.remove();
                }
                
                // Update button data attribute for popup
                const readBtn = messageCard.querySelector('button[onclick*="showMessagePopup"]');
                if (readBtn) {
                    readBtn.dataset.isRead = 'true';
                }
            }
            
            // Update unread count
            updateUnreadCount();
            
            // Don't show success message for auto-mark (to avoid spam)
            if (data.message !== 'Message already marked as read') {
                showSuccessMessage(data.message || 'Message marked as read');
            }
        } else {
            // Only show error if it's not already read
            if (data.message !== 'Message already marked as read') {
                showErrorMessage(data.message || 'Failed to mark message as read');
            }
        }
    })
    .catch(error => {
        console.error('Mark read error:', error);
        showErrorMessage('Failed to mark message as read: ' + error.message);
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
    if (!deleteMessageId) {
        console.error('No message ID to delete');
        showErrorMessage('No message selected for deletion');
        return;
    }
    
    fetch(`/unipulse/public/sponsor/messages/delete/${deleteMessageId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Remove message card from UI
            const messageCard = document.querySelector(`[data-message-id="${deleteMessageId}"]`);
            if (messageCard) {
                messageCard.style.animation = 'fadeOut 0.3s ease-out';
                setTimeout(() => {
                    messageCard.remove();
                    
                    // Update allMessages array
                    allMessages = allMessages.filter(msg => msg !== messageCard);
                    
                    // Check if we need to show empty state
                    const currentFilter = document.querySelector('.filter-tab.active');
                    if (currentFilter) {
                        updateEmptyState(currentFilter.dataset.filter);
                    }
                    
                    // Update unread count
                    updateUnreadCount();
                }, 300);
            }
            
            closeDeleteModal();
            showSuccessMessage(data.message || 'Message deleted successfully');
        } else {
            showErrorMessage(data.message || 'Failed to delete message');
        }
    })
    .catch(error => {
        console.error('Delete error:', error);
        showErrorMessage('Failed to delete message: ' + error.message);
    })
    .finally(() => {
        closeDeleteModal();
    });
}

function markAllAsRead() {
    const unreadMessages = document.querySelectorAll('.message-card.unread');
    if (unreadMessages.length === 0) {
        showSuccessMessage('No unread messages to mark');
        return;
    }
    
    const promises = Array.from(unreadMessages).map(messageCard => {
        const messageId = messageCard.dataset.messageId;
        return fetch(`/unipulse/public/sponsor/messages/markRead/${messageId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            }
        });
    });
    
    Promise.all(promises)
        .then(responses => Promise.all(responses.map(r => r.json())))
        .then(results => {
            // Update UI for all messages
            unreadMessages.forEach(messageCard => {
                messageCard.classList.remove('unread');
                messageCard.classList.add('read');
                messageCard.dataset.readStatus = 'read';
                
                // Remove unread indicator
                const unreadIndicator = messageCard.querySelector('.unread-indicator');
                if (unreadIndicator) {
                    unreadIndicator.remove();
                }
                
                // Remove mark as read button
                const markReadBtn = messageCard.querySelector('button[onclick*="markAsRead"]');
                if (markReadBtn) {
                    markReadBtn.remove();
                }
            });
            
            // Update unread count
            updateUnreadCount();
            
            showSuccessMessage(`Marked ${unreadMessages.length} messages as read`);
        })
        .catch(error => {
            console.error('Error:', error);
            showErrorMessage('Failed to mark all messages as read');
        });
}

function refreshMessages() {
    window.location.reload();
}

function updateUnreadCount() {
    const unreadMessages = document.querySelectorAll('.message-card.unread');
    const unreadCount = unreadMessages.length;
    
    // Update header stats
    const unreadStat = document.querySelector('.stat-item.unread .stat-number');
    if (unreadStat) {
        unreadStat.textContent = unreadCount;
    }
    
    // Update filter tab
    const unreadTab = document.querySelector('.filter-tab[data-filter="unread"]');
    if (unreadTab) {
        unreadTab.textContent = `Unread (${unreadCount})`;
    }
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

// Message Popup Functions
function showMessagePopup(messageId, buttonElement = null) {
    try {
        currentPopupMessageId = messageId;
        
        // If no button element provided, find it
        if (!buttonElement) {
            buttonElement = event.target.closest('button');
        }
        
        // Get data from button attributes
        const senderName = buttonElement.dataset.senderName || 'Unknown Sender';
        const senderEmail = buttonElement.dataset.senderEmail || '';
        const subject = buttonElement.dataset.subject || 'No Subject';
        const message = buttonElement.dataset.message || '';
        const createdAt = buttonElement.dataset.createdAt || new Date().toISOString();
        const isRead = buttonElement.dataset.isRead === 'true';
        
        // Populate popup with message data
        document.getElementById('popupMessageSubject').textContent = subject;
        document.getElementById('popupSenderName').textContent = senderName;
        document.getElementById('popupSenderEmail').textContent = senderEmail;
        document.getElementById('popupMessageBody').textContent = message;
        
        // Set sender avatar
        const avatar = document.getElementById('popupSenderAvatar');
        avatar.textContent = senderName.substring(0, 2).toUpperCase();
        
        // Format and set date/time
        const messageDate = new Date(createdAt);
        document.getElementById('popupMessageDate').textContent = messageDate.toLocaleDateString('en-US', {
            month: 'short',
            day: 'numeric',
            year: 'numeric'
        });
        document.getElementById('popupMessageTime').textContent = messageDate.toLocaleTimeString('en-US', {
            hour: 'numeric',
            minute: '2-digit',
            hour12: true
        });
        
        // Show/hide mark as read button
        const markReadBtn = document.getElementById('markReadPopupBtn');
        if (!isRead) {
            markReadBtn.style.display = 'inline-block';
        } else {
            markReadBtn.style.display = 'none';
        }
        
        // Show the modal
        const modal = document.getElementById('messagePopupModal');
        modal.style.display = 'block';
        document.body.style.overflow = 'hidden';
        
        // Auto-mark as read if unread
        if (!isRead) {
            setTimeout(() => {
                markAsRead(messageId);
            }, 1000); // Mark as read after 1 second of viewing
        }
    } catch (error) {
        console.error('Error showing message popup:', error);
        showErrorMessage('Failed to display message');
    }
}

function closeMessagePopup() {
    const modal = document.getElementById('messagePopupModal');
    modal.style.display = 'none';
    document.body.style.overflow = 'auto';
    currentPopupMessageId = null;
}

function markPopupAsRead() {
    if (currentPopupMessageId) {
        markAsRead(currentPopupMessageId);
        // Hide the mark as read button
        document.getElementById('markReadPopupBtn').style.display = 'none';
    }
}

function deletePopupMessage() {
    if (currentPopupMessageId) {
        closeMessagePopup();
        deleteMessage(currentPopupMessageId);
    }
}

function replyToPopupMessage() {
    if (currentPopupMessageId) {
        // Navigate to message details page for reply functionality
        window.location.href = `/unipulse/public/sponsor/messages/details/${currentPopupMessageId}`;
    }
}

// CSS for fade out animation and popup styles
const style = document.createElement('style');
style.textContent = `
    @keyframes fadeOut {
        from { opacity: 1; transform: translateX(0); }
        to { opacity: 0; transform: translateX(-100%); }
    }
    
    .message-popup {
        max-width: 700px;
        width: 90vw;
    }
    
    .popup-sender-info {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem 0;
        border-bottom: 1px solid #e5e7eb;
        margin-bottom: 1.5rem;
    }
    
    .popup-sender-info .sender-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: linear-gradient(135deg, #1E3A8A, #3B82F6);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 1.1rem;
    }
    
    .popup-sender-info .sender-details {
        flex: 1;
    }
    
    .popup-sender-info .sender-details h4 {
        margin: 0 0 0.25rem 0;
        font-size: 1.1rem;
        color: #1f2937;
    }
    
    .popup-sender-info .sender-details p {
        margin: 0 0 0.25rem 0;
        color: #6b7280;
        font-size: 0.9rem;
    }
    
    .popup-sender-info .sender-type {
        background: #e0f2fe;
        color: #0369a1;
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    
    .popup-sender-info .message-date {
        text-align: right;
        color: #6b7280;
        font-size: 0.875rem;
    }
    
    .popup-message-content {
        max-height: 400px;
        overflow-y: auto;
        padding: 1rem;
        background: #f9fafb;
        border-radius: 8px;
        margin-bottom: 1rem;
    }
    
    .popup-message-content #popupMessageBody {
        line-height: 1.6;
        color: #374151;
        white-space: pre-wrap;
        word-wrap: break-word;
    }
`;
document.head.appendChild(style);