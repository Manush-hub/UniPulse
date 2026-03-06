// User Messages Chatbox App
let currentContactId = null;
let currentContactType = null;
let currentContactName = null;

function normalizeContactType(contactType) {
    return String(contactType || '').trim().toLowerCase();
}

function ensureConversationItem(contactId, contactType, contactName) {
    const normalizedType = normalizeContactType(contactType);
    const safeName = String(contactName || 'Contact').trim() || 'Contact';
    const conversationsList = document.getElementById('conversationsList');

    if (!conversationsList) {
        return null;
    }

    let conversationItem = conversationsList.querySelector(
        `.conversation-item[data-contact-id="${contactId}"][data-contact-type="${normalizedType}"]`
    );

    if (!conversationItem) {
        const emptyState = conversationsList.querySelector('.no-conversations');
        if (emptyState) {
            emptyState.remove();
        }

        const initialText = safeName.substring(0, 2).toUpperCase();
        conversationItem = document.createElement('div');
        conversationItem.className = 'conversation-item';
        conversationItem.dataset.contactId = String(contactId);
        conversationItem.dataset.contactType = normalizedType;
        conversationItem.dataset.contactName = safeName;
        conversationItem.dataset.contactPhoto = '';
        conversationItem.onclick = function() {
            selectConversation(conversationItem);
        };

        conversationItem.innerHTML = `
            <div class="conversation-avatar">${initialText}</div>
            <div class="conversation-info">
                <h4 class="conversation-name">${escapeHtml(safeName)}</h4>
                <p class="conversation-last-message"></p>
            </div>
            <div class="conversation-meta">
                <span class="conversation-time"></span>
            </div>
        `;

        conversationsList.insertBefore(conversationItem, conversationsList.firstChild);
    }

    return conversationItem;
}

function updateConversationPreview(contactId, contactType, contactName, lastMessage) {
    const conversationItem = ensureConversationItem(contactId, contactType, contactName);

    if (!conversationItem) {
        return;
    }

    const safeName = String(contactName || 'Contact').trim() || 'Contact';
    const safeMessage = String(lastMessage || '').trim();
    const previewText = safeMessage.length > 40 ? `${safeMessage.substring(0, 40)}...` : safeMessage;

    conversationItem.dataset.contactName = safeName;

    const nameNode = conversationItem.querySelector('.conversation-name');
    if (nameNode) {
        nameNode.textContent = safeName;
    }

    const previewNode = conversationItem.querySelector('.conversation-last-message');
    if (previewNode) {
        previewNode.textContent = previewText;
    }

    const timeNode = conversationItem.querySelector('.conversation-time');
    if (timeNode) {
        timeNode.textContent = 'Now';
    }

    const unreadBadge = conversationItem.querySelector('.unread-badge');
    if (unreadBadge) {
        unreadBadge.remove();
    }
    conversationItem.classList.remove('has-unread');

    const conversationsList = document.getElementById('conversationsList');
    if (conversationsList && conversationsList.firstChild !== conversationItem) {
        conversationsList.insertBefore(conversationItem, conversationsList.firstChild);
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const recipientId = urlParams.get('recipient_id');
    const recipientType = normalizeContactType(urlParams.get('recipient_type'));
    const recipientName = urlParams.get('recipient_name');

    if (recipientId && recipientType && recipientName) {
        const existing = document.querySelector(
            `.conversation-item[data-contact-id="${recipientId}"][data-contact-type="${recipientType}"]`
        );

        if (existing) {
            selectConversation(existing);
        } else {
            const contactItem = document.querySelector(
                `.contact-item[data-contact-id="${recipientId}"][data-contact-type="${recipientType}"]`
            );

            if (contactItem) {
                document.querySelectorAll('.contact-item').forEach(item => {
                    item.classList.remove('selected');
                });
                contactItem.classList.add('selected');
                contactItem.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }

            const contactPhoto = contactItem ? contactItem.dataset.contactPhoto : '';
            startConversation(recipientId, recipientType, recipientName, contactPhoto);
        }

        return;
    }

    const firstConversation = document.querySelector('.conversation-item.active');
    if (firstConversation) {
        const contactId = firstConversation.dataset.contactId;
        const contactType = normalizeContactType(firstConversation.dataset.contactType);
        const contactName = firstConversation.dataset.contactName;

        currentContactId = contactId;
        currentContactType = contactType;
        currentContactName = contactName;

        loadConversation(contactId, contactType, contactName);
    }

    const messageInput = document.getElementById('messageInput');
    if (messageInput) {
        messageInput.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 150) + 'px';

            const charCount = document.getElementById('charCount');
            if (charCount) {
                charCount.textContent = this.value.length;
            }
        });
    }
});

function selectConversation(element) {
    document.querySelectorAll('.conversation-item').forEach(item => {
        item.classList.remove('active');
    });

    document.querySelectorAll('.contact-item').forEach(item => {
        item.classList.remove('selected');
    });

    element.classList.add('active');

    const unreadBadge = element.querySelector('.unread-badge');
    if (unreadBadge) {
        unreadBadge.remove();
    }
    element.classList.remove('has-unread');

    const contactId = element.dataset.contactId;
    const contactType = normalizeContactType(element.dataset.contactType);
    const contactName = element.dataset.contactName;

    currentContactId = contactId;
    currentContactType = contactType;
    currentContactName = contactName;

    const newUrl = `${window.location.pathname}?recipient_id=${contactId}&recipient_type=${contactType}&recipient_name=${encodeURIComponent(contactName)}`;
    history.replaceState(null, '', newUrl);

    const correspondingContact = document.querySelector(
        `.contact-item[data-contact-id="${contactId}"][data-contact-type="${contactType}"]`
    );
    if (correspondingContact) {
        correspondingContact.classList.add('selected');
    }

    document.getElementById('chatContactName').textContent = contactName;
    document.getElementById('chatContactType').textContent = contactType.charAt(0).toUpperCase() + contactType.slice(1);

    const contactPhoto = element.dataset.contactPhoto;
    const chatAvatar = document.getElementById('chatAvatar');
    if (contactPhoto) {
        chatAvatar.innerHTML = `<img src="${contactPhoto}" alt="${contactName}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">`;
    } else {
        chatAvatar.textContent = contactName.substring(0, 2).toUpperCase();
    }

    document.getElementById('recipientId').value = contactId;
    document.getElementById('recipientType').value = contactType;

    loadConversation(contactId, contactType, contactName);
}

function startConversation(contactId, contactType, contactName, contactPhoto = '') {
    contactType = normalizeContactType(contactType);

    document.querySelectorAll('.conversation-item').forEach(item => {
        item.classList.remove('active');
    });

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

    currentContactId = contactId;
    currentContactType = contactType;
    currentContactName = contactName;

    const newUrl = `${window.location.pathname}?recipient_id=${contactId}&recipient_type=${contactType}&recipient_name=${encodeURIComponent(contactName)}`;
    history.replaceState(null, '', newUrl);

    if (!document.getElementById('chatContactName')) {
        const chatPanel = document.querySelector('.chat-panel');
        if (chatPanel) {
            chatPanel.innerHTML = `
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

                <div class="chat-messages" id="chatMessages">
                    <div class="loading-messages">
                        <i class="fas fa-spinner fa-spin"></i>
                        <p>Loading messages...</p>
                    </div>
                </div>

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

            const messageInput = document.getElementById('messageInput');
            const charCount = document.getElementById('charCount');
            if (messageInput && charCount) {
                messageInput.addEventListener('input', function() {
                    charCount.textContent = this.value.length;
                });
            }
        }
    } else {
        document.getElementById('chatContactName').textContent = contactName;
        document.getElementById('chatContactType').textContent = contactType.charAt(0).toUpperCase() + contactType.slice(1);

        const chatAvatar = document.getElementById('chatAvatar');
        if (contactPhoto) {
            chatAvatar.innerHTML = `<img src="${contactPhoto}" alt="${contactName}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">`;
        } else {
            chatAvatar.textContent = contactName.substring(0, 2).toUpperCase();
        }

        document.getElementById('recipientId').value = contactId;
        document.getElementById('recipientType').value = contactType;
    }

    loadConversation(contactId, contactType, contactName);
}

function loadConversation(contactId, contactType, contactName, silent = false) {
    const chatMessages = document.getElementById('chatMessages');

    if (!silent && chatMessages) {
        chatMessages.innerHTML = `
            <div class="loading-messages">
                <i class="fas fa-spinner fa-spin"></i>
                <p>Loading messages...</p>
            </div>
        `;
    }

    fetch(`/unipulse/public/user/messages/conversation/${contactId}/${contactType}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                displayMessages(data.messages);
            } else if (chatMessages) {
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
            if (!silent && chatMessages) {
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

function displayMessages(messages) {
    const chatMessages = document.getElementById('chatMessages');
    if (!chatMessages) {
        return;
    }

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
        const messageDate = new Date(msg.created_at).toDateString();

        if (messageDate !== currentDate) {
            currentDate = messageDate;
            messagesHTML += `
                <div class="date-divider">
                    <span>${formatDate(msg.created_at)}</span>
                </div>
            `;
        }

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

    messagesHTML += '<div style="clear: both;"></div>';
    chatMessages.innerHTML = messagesHTML;
    scrollToBottom();
}

function sendMessage(event) {
    event.preventDefault();

    const messageInput = document.getElementById('messageInput');
    const sendBtn = document.getElementById('sendBtn');
    if (!messageInput || !sendBtn) {
        return;
    }

    const message = messageInput.value.trim();
    if (!message) {
        return;
    }

    sendBtn.disabled = true;
    sendBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

    const formData = new FormData();
    formData.append('to_user_id', currentContactId);
    formData.append('to_user_type', currentContactType);
    formData.append('subject', 'Message');
    formData.append('message', message);

    fetch('/unipulse/public/user/messages/send', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                messageInput.value = '';
                messageInput.style.height = 'auto';
                const charCount = document.getElementById('charCount');
                if (charCount) {
                    charCount.textContent = '0';
                }

                updateConversationPreview(currentContactId, currentContactType, currentContactName, message);
                loadConversation(currentContactId, currentContactType, currentContactName, false);
            } else {
                showNotification(`Failed to send message: ${data.message || 'Unknown error'}`, 'error');
            }
        })
        .catch(() => {
            showNotification('Failed to send message: Network error', 'error');
        })
        .finally(() => {
            sendBtn.disabled = false;
            sendBtn.innerHTML = '<i class="fas fa-paper-plane"></i>';
        });
}

function refreshChat() {
    if (currentContactId && currentContactType) {
        loadConversation(currentContactId, currentContactType, currentContactName);
    }
}

function scrollToBottom() {
    const chatMessages = document.getElementById('chatMessages');
    if (chatMessages) {
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }
}

function formatDate(dateString) {
    const date = new Date(dateString);
    const today = new Date();
    const yesterday = new Date(today);
    yesterday.setDate(yesterday.getDate() - 1);

    if (date.toDateString() === today.toDateString()) {
        return 'Today';
    }

    if (date.toDateString() === yesterday.toDateString()) {
        return 'Yesterday';
    }

    return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
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
