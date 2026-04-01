// Publisher Messages Chatbox App
let currentContactId = null;
let currentContactType = null;
let currentContactName = null;

function hasValidContact(contactId, contactType) {
    const normalizedId = String(contactId || '').trim();
    const normalizedType = String(contactType || '').trim();
    return normalizedId !== '' && normalizedId !== '0' && normalizedType !== '';
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    console.log('Publisher Messages Chatbox App initialized');

    // Check URL params to auto-open a specific conversation (e.g. after a report redirect or from sponsor card)
    const urlParams = new URLSearchParams(window.location.search);
    const recipientId = urlParams.get('recipient_id');
    const recipientType = urlParams.get('recipient_type');
    const recipientName = urlParams.get('recipient_name');
    const openContactId = urlParams.get('open_contact');
    const openContactType = urlParams.get('contact_type');

    // Check for recipient params first (from sponsor card "Contact" button)
    if (recipientId && recipientType && recipientName) {
        // Try to click an existing conversation item first
        const existing = document.querySelector(
            `.conversation-item[data-contact-id="${recipientId}"][data-contact-type="${recipientType}"]`
        );
        if (existing) {
            selectConversation(existing);
        } else {
            // Check if this contact exists in the available contacts list
            const contactItem = document.querySelector(
                `.contact-item[data-contact-id="${recipientId}"][data-contact-type="${recipientType}"]`
            );
            
            if (contactItem) {
                // Highlight the contact in Available Contacts section
                document.querySelectorAll('.contact-item').forEach(item => {
                    item.classList.remove('selected');
                });
                contactItem.classList.add('selected');
                
                // Scroll the contact into view
                contactItem.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
            
            const contactPhoto = contactItem ? contactItem.dataset.contactPhoto : '';
            
            // Start new conversation with this contact
            startConversation(recipientId, recipientType, recipientName, contactPhoto);
        }

        // Don't clean URL - keep params so selection persists on refresh
        return;
    }
    
    // Check for open_contact params (legacy support)
    if (openContactId && openContactType) {
        // Try to click an existing conversation item first
        const existing = document.querySelector(
            `.conversation-item[data-contact-id="${openContactId}"][data-contact-type="${openContactType}"]`
        );
        if (existing) {
            selectConversation(existing);
        } else {
            // No existing conversation yet — start a new one
            // Try to derive a name from the sidebar list or fall back to a label
            const nameEl = document.querySelector(
                `[data-moderator-id="${openContactId}"] .contact-name, [data-contact-id="${openContactId}"] .conversation-name`
            );
            const contactName = nameEl ? nameEl.textContent.trim() : 'Moderator';
            startConversation(openContactId, openContactType, contactName);
        }

        // Clean URL without reloading
        const cleanUrl = window.location.pathname;
        history.replaceState(null, '', cleanUrl);
        return;
    }

    // Load first conversation if exists
    const firstConversation = document.querySelector('.conversation-item.active');
    if (firstConversation) {
        const contactId = firstConversation.dataset.contactId;
        const contactType = firstConversation.dataset.contactType;
        const contactName = firstConversation.dataset.contactName || 'Unknown Contact';

        if (hasValidContact(contactId, contactType)) {
            currentContactId = contactId;
            currentContactType = contactType;
            currentContactName = contactName;
            loadConversation(contactId, contactType, contactName);
        }
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
    
    // Remove selected class from all contact items
    document.querySelectorAll('.contact-item').forEach(item => {
        item.classList.remove('selected');
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
    const contactName = element.dataset.contactName || 'Unknown Contact';

    if (!hasValidContact(contactId, contactType)) {
        console.error('Invalid conversation metadata', { contactId, contactType, element });
        showNotification('This conversation has invalid contact details.', 'error');
        return;
    }
    
    currentContactId = contactId;
    currentContactType = contactType;
    currentContactName = contactName;
    
    // Update URL to reflect current selection (without reloading)
    const newUrl = `${window.location.pathname}?recipient_id=${contactId}&recipient_type=${contactType}&recipient_name=${encodeURIComponent(contactName)}`;
    history.replaceState(null, '', newUrl);
    
    // Highlight corresponding contact in Available Contacts if it exists
    const correspondingContact = document.querySelector(
        `.contact-item[data-contact-id="${contactId}"][data-contact-type="${contactType}"]`
    );
    if (correspondingContact) {
        correspondingContact.classList.add('selected');
    }
    
    // Update chat header
    document.getElementById('chatContactName').textContent = contactName;
    document.getElementById('chatContactType').textContent = contactType.charAt(0).toUpperCase() + contactType.slice(1);
    
    const contactPhoto = element.dataset.contactPhoto;
    const chatAvatar = document.getElementById('chatAvatar');
    if (contactPhoto) {
        chatAvatar.innerHTML = `<img src="${contactPhoto}" alt="${contactName}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">`;
    } else {
        chatAvatar.textContent = contactName.substring(0, 2).toUpperCase();
    }
    
    // Update hidden form fields
    document.getElementById('recipientId').value = contactId;
    document.getElementById('recipientType').value = contactType;
    
    // Load conversation messages
    loadConversation(contactId, contactType, contactName);
}

// Start a new conversation with a contact
function startConversation(contactId, contactType, contactName, contactPhoto = '') {
    if (!hasValidContact(contactId, contactType)) {
        console.error('Cannot start conversation with invalid contact details', { contactId, contactType });
        showNotification('Unable to open chat: invalid contact details.', 'error');
        return;
    }

    contactName = contactName || 'Unknown Contact';

    // Remove active class from all conversations
    document.querySelectorAll('.conversation-item').forEach(item => {
        item.classList.remove('active');
    });
    
    // Highlight the selected contact in Available Contacts section
    document.querySelectorAll('.contact-item').forEach(item => {
        item.classList.remove('selected');
    });
    
    const selectedContact = document.querySelector(
        `.contact-item[data-contact-id="${contactId}"][data-contact-type="${contactType}"]`
    );
    if (selectedContact) {
        selectedContact.classList.add('selected');
        selectedContact.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }
    
    // Set current contact info
    currentContactId = contactId;
    currentContactType = contactType;
    currentContactName = contactName;
    
    // Update URL to reflect current selection (without reloading)
    const newUrl = `${window.location.pathname}?recipient_id=${contactId}&recipient_type=${contactType}&recipient_name=${encodeURIComponent(contactName)}`;
    history.replaceState(null, '', newUrl);
    
    // Check if chat interface exists, if not create it
    if (!document.getElementById('chatContactName')) {
        const chatPanel = document.querySelector('.chat-panel');
        if (chatPanel) {
            chatPanel.innerHTML = `
                <!-- Chat Header -->
                <div class="chat-header">
                    <div class="chat-contact-info">
                        <div class="chat-avatar" id="chatAvatar">
                            ${contactName.substring(0, 2).toUpperCase()}
                        </div>
                        <div class="chat-contact-details">
                            <h3 id="chatContactName">${contactName}</h3>
                            <span class="contact-type" id="chatContactType">
                                ${contactType.charAt(0).toUpperCase() + contactType.slice(1)}
                            </span>
                        </div>
                    </div>
                    <div class="chat-actions">
                        <button class="chat-action-btn" onclick="refreshChat()" title="Refresh">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </div>
                </div>

                <!-- Chat Messages -->
                <div class="chat-messages" id="chatMessages">
                    <div class="loading-messages">
                        <i class="fas fa-spinner fa-spin"></i>
                        <p>Loading messages...</p>
                    </div>
                </div>

                <!-- Chat Input -->
                <div class="chat-input-container">
                    <form id="chatForm" onsubmit="sendMessage(event)">
                        <input type="hidden" id="recipientId" value="${contactId}">
                        <input type="hidden" id="recipientType" value="${contactType}">
                        <div class="chat-input-wrapper">
                            <textarea 
                                id="messageInput" 
                                placeholder="Type your message..." 
                                rows="1"
                                maxlength="2000"
                                required
                            ></textarea>
                            <button type="submit" class="send-btn" id="sendBtn">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </div>
                        <div class="char-counter">
                            <span id="charCount">0</span> / 2000
                        </div>
                    </form>
                </div>
            `;
            
            // Re-initialize character counter for the new textarea
            const messageInput = document.getElementById('messageInput');
            const charCount = document.getElementById('charCount');
            if (messageInput && charCount) {
                messageInput.addEventListener('input', function() {
                    charCount.textContent = this.value.length;
                });
            }
        }
    } else {
        // Update chat header if interface already exists
        document.getElementById('chatContactName').textContent = contactName;
        document.getElementById('chatContactType').textContent = contactType.charAt(0).toUpperCase() + contactType.slice(1);
        
        const chatAvatar = document.getElementById('chatAvatar');
        if (contactPhoto) {
            chatAvatar.innerHTML = `<img src="${contactPhoto}" alt="${contactName}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">`;
        } else {
            chatAvatar.textContent = contactName.substring(0, 2).toUpperCase();
        }
        
        // Update hidden form fields
        document.getElementById('recipientId').value = contactId;
        document.getElementById('recipientType').value = contactType;
    }
    
    // Load conversation (will show empty if no messages exist)
    loadConversation(contactId, contactType, contactName);
}

// Load conversation messages
function loadConversation(contactId, contactType, contactName, silent = false) {
    if (!hasValidContact(contactId, contactType)) {
        console.error('Cannot load conversation. Missing contact details.', { contactId, contactType });
        const chatMessages = document.getElementById('chatMessages');
        if (chatMessages) {
            chatMessages.innerHTML = `
                <div class="error-messages">
                    <i class="fas fa-exclamation-circle"></i>
                    <p>Failed to load messages</p>
                    <small>Contact ID and type are required</small>
                </div>
            `;
        }
        return;
    }

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
    if (!hasValidContact(currentContactId, currentContactType)) {
        showNotification('Cannot send message: invalid recipient details.', 'error');
        return;
    }
    
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
            
        } else {
            console.error('Failed to send message:', data.message);
            showNotification(`Failed to send message: ${data.message || 'Unknown error'}`, 'error');
        }
    })
    .catch(error => {
        console.error('Error sending message:', error);
        showNotification('Failed to send message: Network error', 'error');
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
