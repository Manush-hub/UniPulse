// Publisher Messages Chatbox App
let currentContactId = null;
let currentContactType = null;
let currentContactName = null;

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    console.log('Publisher Messages Chatbox App initialized');
    
    // Load first conversation if exists
    const firstConversation = document.querySelector('.conversation-item.active');
    if (firstConversation) {
        const contactId = firstConversation.dataset.contactId;
        const contactType = firstConversation.dataset.contactType;
        const contactName = firstConversation.dataset.contactName;
        
        currentContactId = contactId;
        currentContactType = contactType;
        currentContactName = contactName;
        
        loadConversation(contactId, contactType, contactName);
    }
    
    // Auto-resize textarea
    const messageInput = document.getElementById('messageInput');
    if (messageInput) {
        messageInput.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 150) + 'px';
            
            // Update character count
            const charCount = document.getElementById('charCount');
            if (charCount) {
                charCount.textContent = this.value.length;
            }
        });
    }
});

// Select a conversation
function selectConversation(element) {
    // Remove active class from all conversations
    document.querySelectorAll('.conversation-item').forEach(item => {
        item.classList.remove('active');
    });
    
    // Add active class to selected conversation
    element.classList.add('active');
    
    // Remove unread badge
    const unreadBadge = element.querySelector('.unread-badge');
    if (unreadBadge) {
        unreadBadge.remove();
    }
    element.classList.remove('has-unread');
    
    // Get conversation details
    const contactId = element.dataset.contactId;
    const contactType = element.dataset.contactType;
    const contactName = element.dataset.contactName;
    
    currentContactId = contactId;
    currentContactType = contactType;
    currentContactName = contactName;
    
    // Update chat header
    document.getElementById('chatContactName').textContent = contactName;
    document.getElementById('chatContactType').textContent = contactType.charAt(0).toUpperCase() + contactType.slice(1);
    document.getElementById('chatAvatar').textContent = contactName.substring(0, 2).toUpperCase();
    
    // Update hidden form fields
    document.getElementById('recipientId').value = contactId;
    document.getElementById('recipientType').value = contactType;
    
    // Load conversation messages
    loadConversation(contactId, contactType, contactName);
}

// Load conversation messages
function loadConversation(contactId, contactType, contactName, silent = false) {
    const chatMessages = document.getElementById('chatMessages');
    
    if (!silent) {
        chatMessages.innerHTML = `
            <div class="loading-messages">
                <i class="fas fa-spinner fa-spin"></i>
                <p>Loading messages...</p>
            </div>
        `;
    }
    
    fetch(`/unipulse/public/publisher/messages/conversation/${contactId}/${contactType}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Conversation data:', data);
            if (data.success) {
                displayMessages(data.messages);
            } else {
                console.error('Failed to load conversation:', data.message);
                chatMessages.innerHTML = `
                    <div class="error-messages">
                        <i class="fas fa-exclamation-circle"></i>
                        <p>Failed to load messages</p>
                        <small>${data.message || 'Unknown error'}</small>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error loading conversation:', error);
            if (!silent) {
                chatMessages.innerHTML = `
                    <div class="error-messages">
                        <i class="fas fa-exclamation-circle"></i>
                        <p>Failed to load messages</p>
                        <small>${error.message}</small>
                    </div>
                `;
            }
        });
}

// Display messages in chat
function displayMessages(messages) {
    const chatMessages = document.getElementById('chatMessages');
    
    if (!messages || messages.length === 0) {
        chatMessages.innerHTML = `
            <div class="no-messages-yet">
                <i class="fas fa-comments fa-3x"></i>
                <p>No messages in this conversation yet</p>
                <small>Start the conversation by sending a message</small>
            </div>
        `;
        return;
    }
    
    let messagesHTML = '';
    let currentDate = null;
    
    messages.forEach(msg => {
        console.log('Message:', msg.message, 'is_mine:', msg.is_mine, 'type:', typeof msg.is_mine);
        
        const messageDate = new Date(msg.created_at).toDateString();
        
        // Add date divider if date changed
        if (messageDate !== currentDate) {
            currentDate = messageDate;
            messagesHTML += `
                <div class="date-divider">
                    <span>${formatDate(msg.created_at)}</span>
                </div>
            `;
        }
        
        // More robust check for is_mine (handles 1, "1", true)
        const isMine = msg.is_mine == 1 || msg.is_mine === true || msg.is_mine === '1';
        const messageClass = isMine ? 'message-mine' : 'message-theirs';
        
        messagesHTML += `
            <div class="message-bubble ${messageClass}">
                <div class="message-content">
                    ${escapeHtml(msg.message)}
                </div>
                <div class="message-time">
                    ${formatTime(msg.created_at)}
                    ${isMine && msg.is_read ? '<i class="fas fa-check-double read-receipt"></i>' : ''}
                </div>
            </div>
        `;
    });
    
    // Add clearfix to properly handle floats
    messagesHTML += '<div style="clear: both;"></div>';
    
    chatMessages.innerHTML = messagesHTML;
    
    // Scroll to bottom
    scrollToBottom();
}

// Send message
function sendMessage(event) {
    event.preventDefault();
    
    const messageInput = document.getElementById('messageInput');
    const sendBtn = document.getElementById('sendBtn');
    const message = messageInput.value.trim();
    
    if (!message) return;
    
    // Disable send button
    sendBtn.disabled = true;
    sendBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    
    const formData = new FormData();
    formData.append('to_user_id', currentContactId);
    formData.append('to_user_type', currentContactType);
    formData.append('subject', 'Message');
    formData.append('message', message);
    
    fetch('/unipulse/public/publisher/messages/send', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Clear input
            messageInput.value = '';
            messageInput.style.height = 'auto';
            document.getElementById('charCount').textContent = '0';
            
            // Reload conversation
            loadConversation(currentContactId, currentContactType, currentContactName, true);
            
            // Show success briefly
            showNotification('Message sent!', 'success');
        } else {
            showNotification(data.message || 'Failed to send message', 'error');
        }
    })
    .catch(error => {
        console.error('Error sending message:', error);
        showNotification('Failed to send message', 'error');
    })
    .finally(() => {
        // Re-enable send button
        sendBtn.disabled = false;
        sendBtn.innerHTML = '<i class="fas fa-paper-plane"></i>';
    });
}

// Refresh chat
function refreshChat() {
    if (currentContactId && currentContactType) {
        loadConversation(currentContactId, currentContactType, currentContactName);
        showNotification('Messages refreshed', 'success');
    }
}

// Utility functions
function scrollToBottom() {
    const chatMessages = document.getElementById('chatMessages');
    chatMessages.scrollTop = chatMessages.scrollHeight;
}

function formatDate(dateString) {
    const date = new Date(dateString);
    const today = new Date();
    const yesterday = new Date(today);
    yesterday.setDate(yesterday.getDate() - 1);
    
    if (date.toDateString() === today.toDateString()) {
        return 'Today';
    } else if (date.toDateString() === yesterday.toDateString()) {
        return 'Yesterday';
    } else {
        return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
    }
}

function formatTime(dateString) {
    const date = new Date(dateString);
    return date.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML.replace(/\n/g, '<br>');
}

function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `chat-notification ${type}`;
    notification.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
        <span>${message}</span>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.classList.add('show');
    }, 100);
    
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 3000);
}
