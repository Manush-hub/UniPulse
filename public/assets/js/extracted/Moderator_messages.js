const moderatorMessagesConfig = window.moderatorMessagesConfig || {};
let currentContactId = Number(moderatorMessagesConfig.currentContactId || 0);
let currentContactType = moderatorMessagesConfig.currentContactType || '';
let messagePollingInterval;

async function loadConversation(contactId, contactType) {
    try {
        const response = await fetch(`/unipulse/public/moderator/messages/conversation/${contactId}/${contactType}`);
        const data = await response.json();

        if (data.success) {
            displayMessages(data.messages);
            updateUnreadCount();
        } else {
            console.error('Failed to load conversation:', data.message);
        }
    } catch (error) {
        console.error('Error loading conversation:', error);
    }
}

function displayMessages(messages) {
    const container = document.getElementById('chatMessages');
    if (!container) return;

    container.innerHTML = '';

    if (!messages || messages.length === 0) {
        container.innerHTML = '<div class="no-messages-yet"><i class="fas fa-comments fa-3x"></i><p>No messages yet</p><small>Start the conversation!</small></div>';
        return;
    }

    messages.forEach(msg => {
        const isOwn = msg.from_user_type === 'moderator';
        const messageDiv = document.createElement('div');
        messageDiv.className = `message-bubble ${isOwn ? 'message-mine' : 'message-theirs'}`;

        const time = new Date(msg.created_at).toLocaleTimeString('en-US', {
            hour: '2-digit',
            minute: '2-digit'
        });

        messageDiv.innerHTML = `
            <div class="message-content">
                ${escapeHtml(msg.message)}
            </div>
            <div class="message-time">${time}</div>
        `;

        container.appendChild(messageDiv);
    });

    container.scrollTop = container.scrollHeight;
}

async function sendMessage(event) {
    event.preventDefault();

    const messageInput = document.getElementById('messageInput');
    const message = messageInput ? messageInput.value.trim() : '';

    if (!message) return;

    try {
        const formData = new FormData();
        formData.append('to_user_id', document.getElementById('recipientId').value);
        formData.append('to_user_type', document.getElementById('recipientType').value);
        formData.append('message', message);

        const response = await fetch('/unipulse/public/moderator/messages/send', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            messageInput.value = '';
            loadConversation(currentContactId, currentContactType);
        } else {
            alert('Failed to send message: ' + data.message);
        }
    } catch (error) {
        console.error('Error sending message:', error);
        alert('Failed to send message. Please try again.');
    }
}

function selectConversation(element) {
    document.querySelectorAll('.conversation-item').forEach(el => el.classList.remove('active'));
    element.classList.add('active');

    currentContactId = element.dataset.contactId;
    currentContactType = element.dataset.contactType;
    const contactName = element.dataset.contactName;

    const nameEl = document.getElementById('chatContactName');
    const typeEl = document.getElementById('chatContactType');
    const avatarEl = document.getElementById('chatAvatar');
    const recipientIdEl = document.getElementById('recipientId');
    const recipientTypeEl = document.getElementById('recipientType');

    if (nameEl) nameEl.textContent = contactName;
    if (typeEl) typeEl.textContent = capitalizeFirst(currentContactType);
    if (avatarEl) avatarEl.textContent = contactName.substring(0, 2).toUpperCase();
    if (recipientIdEl) recipientIdEl.value = currentContactId;
    if (recipientTypeEl) recipientTypeEl.value = currentContactType;

    loadConversation(currentContactId, currentContactType);
}

function startConversation(contactId, contactType, contactName) {
    currentContactId = contactId;
    currentContactType = contactType;

    const chatPanel = document.querySelector('.chat-panel');
    if (!chatPanel) return;

    const emptyState = chatPanel.querySelector('.chat-empty-state');

    if (emptyState) {
        chatPanel.innerHTML = `
            <div class="chat-header">
                <div class="chat-contact-info">
                    <div class="chat-avatar" id="chatAvatar">
                        ${contactName.substring(0, 2).toUpperCase()}
                    </div>
                    <div class="chat-contact-details">
                        <h3 id="chatContactName">${contactName}</h3>
                        <span class="contact-type" id="chatContactType">
                            ${capitalizeFirst(contactType)}
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
                <div class="no-messages-yet">
                    <i class="fas fa-comments fa-3x"></i>
                    <p>No messages yet</p>
                    <small>Start the conversation!</small>
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
        if (messageInput) {
            messageInput.addEventListener('input', function () {
                this.style.height = 'auto';
                this.style.height = Math.min(this.scrollHeight, 120) + 'px';
                const charCountEl = document.getElementById('charCount');
                if (charCountEl) charCountEl.textContent = this.value.length;
            });
        }

        if (messagePollingInterval) {
            clearInterval(messagePollingInterval);
        }
        messagePollingInterval = setInterval(() => {
            loadConversation(currentContactId, currentContactType);
        }, 5000);
    } else {
        document.getElementById('chatContactName').textContent = contactName;
        document.getElementById('chatContactType').textContent = capitalizeFirst(contactType);
        document.getElementById('chatAvatar').textContent = contactName.substring(0, 2).toUpperCase();
        document.getElementById('recipientId').value = contactId;
        document.getElementById('recipientType').value = contactType;

        document.getElementById('chatMessages').innerHTML = '<div class="no-messages-yet"><i class="fas fa-comments fa-3x"></i><p>No messages yet</p><small>Start the conversation!</small></div>';
    }

    loadConversation(currentContactId, currentContactType);
}

async function updateUnreadCount() {
    try {
        const response = await fetch('/unipulse/public/moderator/messages/unreadCount');
        const data = await response.json();
        if (data.success) {
            const statEls = document.querySelectorAll('.stat-number');
            if (statEls.length > 1) statEls[1].textContent = data.count;
        }
    } catch (error) {
        console.error('Error updating unread count:', error);
    }
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function capitalizeFirst(str) {
    return str.charAt(0).toUpperCase() + str.slice(1);
}

const messageInput = document.getElementById('messageInput');
if (messageInput) {
    messageInput.addEventListener('input', function () {
        this.style.height = 'auto';
        this.style.height = Math.min(this.scrollHeight, 120) + 'px';

        const charCountEl = document.getElementById('charCount');
        if (charCountEl) {
            charCountEl.textContent = this.value.length;
        }
    });
}

function refreshChat() {
    if (currentContactId && currentContactType) {
        loadConversation(currentContactId, currentContactType);
    }
}

if (currentContactId && currentContactType) {
    loadConversation(currentContactId, currentContactType);

    messagePollingInterval = setInterval(() => {
        loadConversation(currentContactId, currentContactType);
    }, 5000);
}
