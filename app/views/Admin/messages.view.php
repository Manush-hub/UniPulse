<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?> - UniPulse Admin</title>
    <link rel="stylesheet" href="/unipulse/public/assets/css/components/header-style.css">
    <link rel="stylesheet" href="/unipulse/public/assets/css/Admin/messages-style.css?v=<?= time() ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <?php
    $pageConfig = ['activeNav' => 'messages'];
    $headerCssLoaded = true;
    include __DIR__ . '/components/header.php';
    ?>

    <div class="main-content chatbox-container">

        <!-- Hero -->
        <section class="hero">
            <div class="container">
                <div class="hero-content">
                    <h1><i class="fas fa-comments"></i> Messages</h1>
                    <p>Chat directly with moderators across all universities</p>
                </div>
                <div class="hero-stats">
                    <div class="stat-item">
                        <span class="stat-number"><?= count($conversations) ?></span>
                        <span class="stat-label">Conversations</span>
                    </div>
                    <div class="stat-item unread">
                        <span class="stat-number" id="unreadStatCount"><?= $unread_count ?></span>
                        <span class="stat-label">Unread</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- Chatbox -->
        <section class="chatbox-section">
            <div class="container">
                <div class="chatbox-wrapper">

                    <!-- Left Panel: Conversations + Available Moderators -->
                    <div class="conversations-panel">
                        <div class="conversations-header">
                            <h3><i class="fas fa-inbox"></i> Conversations</h3>
                        </div>

                        <div class="conversations-list" id="conversationsList">
                            <?php if (!empty($conversations)): ?>
                                <?php foreach ($conversations as $index => $conv): ?>
                                    <div class="conversation-item <?= $conv->unread_count > 0 ? 'has-unread' : '' ?> <?= $index === 0 ? 'active' : '' ?>"
                                         data-contact-id="<?= $conv->contact_id ?>"
                                         data-contact-type="<?= $conv->contact_type ?>"
                                         data-contact-name="<?= htmlspecialchars($conv->contact_name) ?>"
                                         onclick="selectConversation(this)">
                                        <div class="conversation-avatar">
                                            <?= strtoupper(substr($conv->contact_name, 0, 2)) ?>
                                        </div>
                                        <div class="conversation-info">
                                            <h4 class="conversation-name"><?= htmlspecialchars($conv->contact_name) ?></h4>
                                            <p class="conversation-last-message">
                                                <?= htmlspecialchars(substr($conv->last_message, 0, 40)) ?>...
                                            </p>
                                        </div>
                                        <div class="conversation-meta">
                                            <span class="conversation-time">
                                                <?php
                                                $time = strtotime($conv->last_message_time);
                                                $diff = time() - $time;
                                                if ($diff < 3600) {
                                                    echo round($diff / 60) . 'm';
                                                } elseif ($diff < 86400) {
                                                    echo round($diff / 3600) . 'h';
                                                } else {
                                                    echo date('M j', $time);
                                                }
                                                ?>
                                            </span>
                                            <?php if ($conv->unread_count > 0): ?>
                                                <span class="unread-badge"><?= $conv->unread_count ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="no-conversations">
                                    <i class="fas fa-comments fa-3x"></i>
                                    <p>No conversations yet</p>
                                    <small>Select a moderator below to start chatting</small>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Available Moderators -->
                        <div class="conversations-header" style="margin-top:1rem; border-top:1px solid #e5e7eb; padding-top:1rem;">
                            <h3><i class="fas fa-user-shield"></i> Moderators</h3>
                        </div>

                        <?php if (!empty($available_moderators)): ?>
                            <div class="contacts-section" data-type="moderator">
                                <div class="contacts-list">
                                    <?php foreach ($available_moderators as $mod): ?>
                                        <div class="contact-item"
                                             onclick="startConversation(<?= $mod->id ?>, 'moderator', '<?= htmlspecialchars($mod->full_name) ?>')">
                                            <div class="contact-avatar">
                                                <?= strtoupper(substr($mod->full_name, 0, 2)) ?>
                                            </div>
                                            <div class="contact-info">
                                                <h5 class="contact-name"><?= htmlspecialchars($mod->full_name) ?></h5>
                                                <p class="contact-sub">
                                                    <?= htmlspecialchars($mod->university_name ?? $mod->university ?? 'Moderator') ?>
                                                </p>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php else: ?>
                            <div style="padding:1rem 1.5rem; color:#64748b; font-size:0.875rem;">
                                No active moderators found.
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Right Panel: Chat -->
                    <div class="chat-panel" id="chatPanel">
                        <?php if (!empty($conversations)): ?>
                            <!-- Chat Header -->
                            <div class="chat-header">
                                <div class="chat-contact-info">
                                    <div class="chat-avatar" id="chatAvatar">
                                        <?= strtoupper(substr($conversations[0]->contact_name, 0, 2)) ?>
                                    </div>
                                    <div class="chat-contact-details">
                                        <h3 id="chatContactName"><?= htmlspecialchars($conversations[0]->contact_name) ?></h3>
                                        <span class="contact-type-badge" id="chatContactType">
                                            <?= ucfirst($conversations[0]->contact_type) ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="chat-actions">
                                    <button class="chat-action-btn" onclick="refreshChat()" title="Refresh">
                                        <i class="fas fa-sync-alt"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Messages -->
                            <div class="chat-messages" id="chatMessages">
                                <div class="loading-messages">
                                    <i class="fas fa-spinner fa-spin"></i>
                                    <p>Loading messages...</p>
                                </div>
                            </div>

                            <!-- Input -->
                            <div class="chat-input-container">
                                <form id="chatForm" onsubmit="sendMessage(event)">
                                    <input type="hidden" id="recipientId"   value="<?= $conversations[0]->contact_id ?>">
                                    <input type="hidden" id="recipientType" value="<?= $conversations[0]->contact_type ?>">
                                    <div class="chat-input-wrapper">
                                        <textarea
                                            id="messageInput"
                                            placeholder="Type your message..."
                                            rows="1"
                                            maxlength="2000"
                                            required></textarea>
                                        <button type="submit" class="send-btn" id="sendBtn">
                                            <i class="fas fa-paper-plane"></i>
                                        </button>
                                    </div>
                                    <div class="char-counter"><span id="charCount">0</span> / 2000</div>
                                </form>
                            </div>

                        <?php else: ?>
                            <!-- Empty State: no conversations yet -->
                            <div class="chat-empty-state">
                                <i class="fas fa-envelope-open-text fa-4x"></i>
                                <h3>No Messages Yet</h3>
                                <p>You haven&apos;t started any conversations.</p>
                                <p style="font-size:.875rem;color:#64748b;margin-top:.5rem;">
                                    Select a moderator from the left panel to begin chatting.
                                </p>
                            </div>
                        <?php endif; ?>
                    </div>

                </div><!-- /.chatbox-wrapper -->
            </div>
        </section>
    </div>

    <!-- Toast notification -->
    <div class="chat-notification" id="chatNotification">
        <i class="fas fa-check-circle" id="notifIcon"></i>
        <span id="notifMessage"></span>
    </div>

    <script>
        let currentContactId   = <?= !empty($conversations) ? $conversations[0]->contact_id   : 0 ?>;
        let currentContactType = '<?= !empty($conversations) ? $conversations[0]->contact_type : '' ?>';
        let pollingInterval;

        /* ─────────────────── API helpers ─────────────────── */

        async function loadConversation(contactId, contactType) {
            try {
                const res  = await fetch(`/unipulse/public/admin/messages/conversation/${contactId}/${contactType}`);
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

            const input   = document.getElementById('messageInput');
            const message = input.value.trim();
            if (!message) return;

            const sendBtn = document.getElementById('sendBtn');
            sendBtn.disabled = true;

            try {
                const fd = new FormData();
                fd.append('to_user_id',   document.getElementById('recipientId').value);
                fd.append('to_user_type', document.getElementById('recipientType').value);
                fd.append('message', message);

                const res  = await fetch('/unipulse/public/admin/messages/send', { method: 'POST', body: fd });
                const data = await res.json();

                if (data.success) {
                    input.value = '';
                    input.style.height = 'auto';
                    document.getElementById('charCount').textContent = '0';
                    await loadConversation(currentContactId, currentContactType);
                    showToast('Message sent', 'success');
                } else {
                    showToast('Failed to send: ' + data.message, 'error');
                }
            } catch (err) {
                console.error('sendMessage error:', err);
                showToast('Network error – please try again', 'error');
            } finally {
                sendBtn.disabled = false;
            }
        }

        async function refreshUnreadCount() {
            try {
                const res  = await fetch('/unipulse/public/admin/messages/unreadCount');
                const data = await res.json();
                if (data.success) {
                    const el = document.getElementById('unreadStatCount');
                    if (el) el.textContent = data.count;
                }
            } catch (_) {}
        }

        /* ─────────────────── Display ─────────────────── */

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
                // Date divider
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

                const isOwn = parseInt(msg.is_mine) === 1;
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
            if (container) {
                container.innerHTML = `
                    <div class="error-messages">
                        <i class="fas fa-exclamation-triangle"></i>
                        <p>Could not load messages</p>
                        <small>${escapeHtml(msg)}</small>
                    </div>`;
            }
        }

        /* ─────────────────── Conversation selection ─────────────────── */

        function selectConversation(el) {
            document.querySelectorAll('.conversation-item').forEach(e => e.classList.remove('active'));
            el.classList.add('active');

            currentContactId   = el.dataset.contactId;
            currentContactType = el.dataset.contactType;
            const name = el.dataset.contactName;

            updateChatHeader(name, currentContactType);
            updateRecipientFields(currentContactId, currentContactType);
            resetPolling();
            loadConversation(currentContactId, currentContactType);
        }

        function startConversation(contactId, contactType, contactName) {
            currentContactId   = contactId;
            currentContactType = contactType;

            const panel = document.getElementById('chatPanel');

            // If we only have the empty state, replace with full chat UI
            if (panel.querySelector('.chat-empty-state')) {
                panel.innerHTML = buildChatUI(contactId, contactType, contactName);
                attachInputListeners();
            } else {
                updateChatHeader(contactName, contactType);
                updateRecipientFields(contactId, contactType);
                if (document.getElementById('chatMessages')) {
                    document.getElementById('chatMessages').innerHTML = `
                        <div class="no-messages-yet">
                            <i class="fas fa-comments fa-3x"></i>
                            <p>No messages yet</p>
                            <small>Send the first message!</small>
                        </div>`;
                }
            }

            resetPolling();
            loadConversation(currentContactId, currentContactType);
        }

        function buildChatUI(contactId, contactType, contactName) {
            return `
                <div class="chat-header">
                    <div class="chat-contact-info">
                        <div class="chat-avatar" id="chatAvatar">${contactName.substring(0,2).toUpperCase()}</div>
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
                        <input type="hidden" id="recipientId"   value="${contactId}">
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
            const nameEl   = document.getElementById('chatContactName');
            const typeEl   = document.getElementById('chatContactType');
            const avatarEl = document.getElementById('chatAvatar');
            if (nameEl)   nameEl.textContent   = name;
            if (typeEl)   typeEl.textContent   = capitalizeFirst(type);
            if (avatarEl) avatarEl.textContent = name.substring(0, 2).toUpperCase();
        }

        function updateRecipientFields(id, type) {
            const idEl   = document.getElementById('recipientId');
            const typeEl = document.getElementById('recipientType');
            if (idEl)   idEl.value   = id;
            if (typeEl) typeEl.value = type;
        }

        /* ─────────────────── Polling ─────────────────── */

        function resetPolling() {
            if (pollingInterval) clearInterval(pollingInterval);
            pollingInterval = setInterval(() => {
                if (currentContactId && currentContactType) {
                    loadConversation(currentContactId, currentContactType);
                }
            }, 5000);
        }

        function refreshChat() {
            if (currentContactId && currentContactType) {
                loadConversation(currentContactId, currentContactType);
            }
        }

        /* ─────────────────── Utilities ─────────────────── */

        function escapeHtml(text) {
            const d = document.createElement('div');
            d.textContent = text;
            return d.innerHTML;
        }

        function capitalizeFirst(str) {
            return str.charAt(0).toUpperCase() + str.slice(1);
        }

        function showToast(message, type = 'success') {
            const el     = document.getElementById('chatNotification');
            const iconEl = document.getElementById('notifIcon');
            const msgEl  = document.getElementById('notifMessage');
            el.className = `chat-notification ${type}`;
            iconEl.className = type === 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-circle';
            msgEl.textContent = message;
            el.classList.add('show');
            setTimeout(() => el.classList.remove('show'), 3000);
        }

        function attachInputListeners() {
            const input = document.getElementById('messageInput');
            if (input) {
                input.addEventListener('input', function () {
                    this.style.height = 'auto';
                    this.style.height = Math.min(this.scrollHeight, 120) + 'px';
                    const cc = document.getElementById('charCount');
                    if (cc) cc.textContent = this.value.length;
                });
            }
        }

        /* ─────────────────── Init ─────────────────── */

        attachInputListeners();

        if (currentContactId && currentContactType) {
            loadConversation(currentContactId, currentContactType);
            resetPolling();
        }
    </script>

</body>
</html>
