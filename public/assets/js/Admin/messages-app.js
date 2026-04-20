const adminMessagesConfig = window.adminMessagesConfig || {};
let currentContactId = Number(adminMessagesConfig.currentContactId || 0);
let currentContactType = adminMessagesConfig.currentContactType || '';

async function loadConversation(contactId, contactType) {
    try {
        const res = await fetch(`/unipulse/public/admin/messages/conversation/${contactId}/${contactType}`);
        const data = await res.json();

        if (data.success) {
            displayMessages(data.messages);
            refreshUnreadCount();
        } else {
            showChatError('Failed to load messages: ' + data.message);
        }
    } catch (err) {
        console.error('loadConversation error:', err);
    }
}

async function sendMessage(event) {
    event.preventDefault();

    const input = document.getElementById('messageInput');
    const message = input ? input.value.trim() : '';
    if (!message) return;

    const sendBtn = document.getElementById('sendBtn');
    if (sendBtn) sendBtn.disabled = true;

    try {
        const fd = new FormData();
        fd.append('to_user_id', document.getElementById('recipientId').value);
        fd.append('to_user_type', document.getElementById('recipientType').value);
        fd.append('message', message);

        const res = await fetch('/unipulse/public/admin/messages/send', { method: 'POST', body: fd });
        const data = await res.json();

        if (data.success) {
            input.value = '';
            input.style.height = 'auto';
            const charCount = document.getElementById('charCount');
            if (charCount) charCount.textContent = '0';
            await loadConversation(currentContactId, currentContactType);
            showToast('Message sent', 'success');
        } else {
            showToast('Failed to send: ' + data.message, 'error');
        }
    } catch (err) {
        console.error('sendMessage error:', err);
        showToast('Network error - please try again', 'error');
    } finally {
        if (sendBtn) sendBtn.disabled = false;
    }
}

async function refreshUnreadCount() {
    try {
        const res = await fetch('/unipulse/public/admin/messages/unreadCount');
        const data = await res.json();
        if (data.success) {
            const el = document.getElementById('unreadStatCount');
            if (el) el.textContent = data.count;
        }
    } catch (_) { }
}

function displayMessages(messages) {
    const container = document.getElementById('chatMessages');
    if (!container) return;

    container.innerHTML = '';

    if (!messages || messages.length === 0) {
        container.innerHTML = `
            <div class="no-messages-yet">
                <i class="fas fa-comments fa-3x"></i>
                <p>No messages yet</p>
                <small>Send the first message!</small>
            </div>`;
        return;
    }

    let lastDate = '';

    messages.forEach(msg => {
        const msgDate = new Date(msg.created_at).toLocaleDateString('en-US', {
            month: 'long', day: 'numeric', year: 'numeric'
        });

        if (msgDate !== lastDate) {
            const div = document.createElement('div');
            div.className = 'date-divider';
            div.innerHTML = `<span>${msgDate}</span>`;
            container.appendChild(div);
            lastDate = msgDate;
        }

        const isOwn = parseInt(msg.is_mine, 10) === 1;
        const bubble = document.createElement('div');
        bubble.className = `message-bubble ${isOwn ? 'message-mine' : 'message-theirs'}`;

        const time = new Date(msg.created_at).toLocaleTimeString('en-US', {
            hour: '2-digit', minute: '2-digit'
        });

        bubble.innerHTML = `
            <div class="message-content">${escapeHtml(msg.message)}</div>
            <div class="message-time">
                ${time}
                ${isOwn && msg.is_read ? '<i class="fas fa-check-double read-receipt"></i>' : ''}
            </div>`;

        container.appendChild(bubble);
    });

    container.scrollTop = container.scrollHeight;
}

function showChatError(msg) {
    const container = document.getElementById('chatMessages');
    if (!container) return;

    container.innerHTML = `
        <div class="error-messages">
            <i class="fas fa-exclamation-triangle"></i>
            <p>Could not load messages</p>
            <small>${escapeHtml(msg)}</small>
        </div>`;
}

function selectConversation(el) {
    document.querySelectorAll('.conversation-item').forEach(e => e.classList.remove('active'));
    el.classList.add('active');

    currentContactId = el.dataset.contactId;
    currentContactType = el.dataset.contactType;
    const name = el.dataset.contactName;

    updateChatHeader(name, currentContactType);
    updateRecipientFields(currentContactId, currentContactType);
    loadConversation(currentContactId, currentContactType);
}

function startConversation(contactId, contactType, contactName) {
    currentContactId = contactId;
    currentContactType = contactType;

    const panel = document.getElementById('chatPanel');

    if (panel && panel.querySelector('.chat-empty-state')) {
        panel.innerHTML = buildChatUI(contactId, contactType, contactName);
        attachInputListeners();
    } else {
        updateChatHeader(contactName, contactType);
        updateRecipientFields(contactId, contactType);
        const messagesEl = document.getElementById('chatMessages');
        if (messagesEl) {
            messagesEl.innerHTML = `
                <div class="no-messages-yet">
                    <i class="fas fa-comments fa-3x"></i>
                    <p>No messages yet</p>
                    <small>Send the first message!</small>
                </div>`;
        }
    }

    loadConversation(currentContactId, currentContactType);
}

function buildChatUI(contactId, contactType, contactName) {
    return `
        <div class="chat-header">
            <div class="chat-contact-info">
                <div class="chat-avatar" id="chatAvatar">${contactName.substring(0, 2).toUpperCase()}</div>
                <div class="chat-contact-details">
                    <h3 id="chatContactName">${escapeHtml(contactName)}</h3>
                    <span class="contact-type-badge" id="chatContactType">${capitalizeFirst(contactType)}</span>
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
                <small>Send the first message!</small>
            </div>
        </div>
        <div class="chat-input-container">
            <form id="chatForm" onsubmit="sendMessage(event)">
                <input type="hidden" id="recipientId" value="${contactId}">
                <input type="hidden" id="recipientType" value="${contactType}">
                <div class="chat-input-wrapper">
                    <textarea id="messageInput" placeholder="Type your message..." rows="1" maxlength="2000" required></textarea>
                    <button type="submit" class="send-btn" id="sendBtn">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
                <div class="char-counter"><span id="charCount">0</span> / 2000</div>
            </form>
        </div>`;
}

function updateChatHeader(name, type) {
    const nameEl = document.getElementById('chatContactName');
    const typeEl = document.getElementById('chatContactType');
    const avatarEl = document.getElementById('chatAvatar');
    if (nameEl) nameEl.textContent = name;
    if (typeEl) typeEl.textContent = capitalizeFirst(type);
    if (avatarEl) avatarEl.textContent = name.substring(0, 2).toUpperCase();
}

function updateRecipientFields(id, type) {
    const idEl = document.getElementById('recipientId');
    const typeEl = document.getElementById('recipientType');
    if (idEl) idEl.value = id;
    if (typeEl) typeEl.value = type;
}


function refreshChat() {
    if (currentContactId && currentContactType) {
        loadConversation(currentContactId, currentContactType);
    }
}

function escapeHtml(text) {
    const d = document.createElement('div');
    d.textContent = text;
    return d.innerHTML;
}

function capitalizeFirst(str) {
    return str.charAt(0).toUpperCase() + str.slice(1);
}

function showToast(message, type = 'success') {
    const el = document.getElementById('chatNotification');
    const iconEl = document.getElementById('notifIcon');
    const msgEl = document.getElementById('notifMessage');
    if (!el || !iconEl || !msgEl) return;

    el.className = `chat-notification ${type}`;
    iconEl.className = type === 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-circle';
    msgEl.textContent = message;
    el.classList.add('show');
    setTimeout(() => el.classList.remove('show'), 3000);
}

function attachInputListeners() {
    const input = document.getElementById('messageInput');
    if (!input) return;

    input.addEventListener('input', function () {
        this.style.height = 'auto';
        this.style.height = Math.min(this.scrollHeight, 120) + 'px';
        const cc = document.getElementById('charCount');
        if (cc) cc.textContent = this.value.length;
    });
}

async function refreshAdminMessageBadge() {
    const badge = document.getElementById('adminMsgBadge');
    if (!badge) return;

    try {
        const [chatRes, notifRes] = await Promise.all([
            fetch('/unipulse/public/admin/messages/unreadCount').then(r => r.json()).catch(() => ({ success: false, count: 0 })),
            fetch('/unipulse/public/admin/dashboard/getNotifications').then(r => r.json()).catch(() => ({ success: false, notifications: [] }))
        ]);

        const chatUnread = (chatRes && chatRes.success) ? (parseInt(chatRes.count, 10) || 0) : 0;
        const supportUnread = (notifRes && notifRes.success && Array.isArray(notifRes.notifications))
            ? notifRes.notifications.filter(n => n && n.type === 'support_message' && n.unread).length
            : 0;

        const totalUnread = chatUnread + supportUnread;
        if (totalUnread > 0) {
            badge.textContent = String(totalUnread);
            badge.style.display = 'inline-block';
        } else {
            badge.textContent = '';
            badge.style.display = 'none';
        }
    } catch (_) {
        // No-op. Badge will be refreshed by polling script eventually.
    }
}

function markContactReachRead(el) {
    if (!el) return;

    const supportId = Number(el.dataset.supportId || 0);
    if (supportId <= 0) return;

    if (!el.classList.contains('unread')) {
        return;
    }

    el.classList.remove('unread');
    const newBadge = el.querySelector('.reach-new-badge');
    if (newBadge) {
        newBadge.remove();
    }

    fetch('/unipulse/public/admin/dashboard/markNotificationRead', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ notificationId: `support:${supportId}` }),
        keepalive: true
    })
        .then(r => r.json())
        .then(data => {
            if (!data.success) {
                el.classList.add('unread');
            }
            refreshAdminMessageBadge();
        })
        .catch(() => {
            el.classList.add('unread');
            refreshAdminMessageBadge();
        });
}

function markAllContactReachesRead(btn) {
    const button = btn || null;
    if (button) {
        button.disabled = true;
    }

    fetch('/unipulse/public/admin/dashboard/markAllNotificationsRead', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' }
    })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                document.querySelectorAll('.contact-reach-item.unread').forEach(item => {
                    item.classList.remove('unread');
                    const newBadge = item.querySelector('.reach-new-badge');
                    if (newBadge) {
                        newBadge.remove();
                    }
                });
                refreshAdminMessageBadge();
                showToast('All contact reaches marked as read', 'success');
            } else {
                showToast('Failed to mark all contact reaches as read', 'error');
            }
        })
        .catch(() => {
            showToast('Failed to mark all contact reaches as read', 'error');
        })
        .finally(() => {
            if (button) {
                button.disabled = false;
            }
        });
}

attachInputListeners();

if (currentContactId && currentContactType) {
    loadConversation(currentContactId, currentContactType);
}
