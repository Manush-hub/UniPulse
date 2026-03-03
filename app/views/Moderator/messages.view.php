<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?> - UniPulse</title>
    <link rel="stylesheet" href="/unipulse/public/assets/css/components/header-style.css">
    <link rel="stylesheet" href="/unipulse/public/assets/css/Moderator/messages-chatbox-style.css?v=<?= time() ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <!-- Navigation -->
    <?php
    $pageConfig = ['activeNav' => 'messages'];
    $headerCssLoaded = true;
    include __DIR__ . '/components/header.php';
    ?>

    <!-- Main Content -->
    <div class="main-content chatbox-container">
        <!-- Hero Section -->
        <section class="hero">
            <div class="container">
                <div class="hero-content">
                    <h1><i class="fas fa-comments"></i> Messages</h1>
                    <p>Chat with publishers in your university</p>
                </div>
                <div class="hero-stats">
                    <div class="stat-item">
                        <span class="stat-number"><?= count($conversations) ?></span>
                        <span class="stat-label">Conversations</span>
                    </div>
                    <div class="stat-item unread">
                        <span class="stat-number"><?= $unread_count ?></span>
                        <span class="stat-label">Unread</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- Chatbox Section -->
        <section class="chatbox-section">
            <div class="container">
                <div class="chatbox-wrapper">
                    <!-- Conversations List (Left Panel) -->
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
                                    <small>Start by contacting a publisher</small>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Available Contacts Section -->
                        <div class="conversations-header" style="margin-top: 1rem; border-top: 1px solid #e5e7eb; padding-top: 1rem;">
                            <h3><i class="fas fa-users"></i> Available Contacts</h3>
                        </div>
                        
                        <!-- Publishers -->
                        <?php if (!empty($available_publishers)): ?>
                            <div class="contacts-section" data-type="publisher">
                                <h4 class="contacts-section-title"><i class="fas fa-building"></i> Publishers</h4>
                                <div class="contacts-list">
                                    <?php foreach ($available_publishers as $publisher): ?>
                                        <div class="contact-item" onclick="startConversation(<?= $publisher->id ?>, 'publisher', '<?= htmlspecialchars($publisher->society_name) ?>')">
                                            <div class="contact-avatar">
                                                <?= strtoupper(substr($publisher->society_name, 0, 2)) ?>
                                            </div>
                                            <div class="contact-info">
                                                <h5 class="contact-name"><?= htmlspecialchars($publisher->society_name) ?></h5>
                                                <p class="contact-type">Publisher</p>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Chat Panel (Right Panel) -->
                    <div class="chat-panel">
                        <?php if (!empty($conversations)): ?>
                            <!-- Chat Header -->
                            <div class="chat-header">
                                <div class="chat-contact-info">
                                    <div class="chat-avatar" id="chatAvatar">
                                        <?= strtoupper(substr($conversations[0]->contact_name, 0, 2)) ?>
                                    </div>
                                    <div class="chat-contact-details">
                                        <h3 id="chatContactName"><?= htmlspecialchars($conversations[0]->contact_name) ?></h3>
                                        <span class="contact-type" id="chatContactType">
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
                                    <input type="hidden" id="recipientId" value="<?= $conversations[0]->contact_id ?>">
                                    <input type="hidden" id="recipientType" value="<?= $conversations[0]->contact_type ?>">
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
                        <?php else: ?>
                            <!-- Empty State -->
                            <div class="chat-empty-state">
                                <i class="fas fa-envelope-open-text fa-4x"></i>
                                <h3>No Messages Yet</h3>
                                <p>You haven't received any messages yet.</p>
                                <p class="help-text">Start a conversation with publishers from the available contacts.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <script>
        // Current conversation
        let currentContactId = <?= !empty($conversations) ? $conversations[0]->contact_id : 0 ?>;
        let currentContactType = '<?= !empty($conversations) ? $conversations[0]->contact_type : '' ?>';
        let messagePollingInterval;

        // Load conversation messages
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

        // Display messages
        function displayMessages(messages) {
            const container = document.getElementById('chatMessages');
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

        // Send message
        async function sendMessage(event) {
            event.preventDefault();
            
            const messageInput = document.getElementById('messageInput');
            const message = messageInput.value.trim();
            
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

        // Select conversation
        function selectConversation(element) {
            document.querySelectorAll('.conversation-item').forEach(el => el.classList.remove('active'));
            element.classList.add('active');
            
            currentContactId = element.dataset.contactId;
            currentContactType = element.dataset.contactType;
            const contactName = element.dataset.contactName;
            
            // Update UI if elements exist
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

        // Start new conversation
        function startConversation(contactId, contactType, contactName) {
            currentContactId = contactId;
            currentContactType = contactType;
            
            // Check if chat interface exists, if not create it
            const chatPanel = document.querySelector('.chat-panel');
            const emptyState = chatPanel.querySelector('.chat-empty-state');
            
            if (emptyState) {
                // Replace empty state with chat interface
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

                    <!-- Chat Messages -->
                    <div class="chat-messages" id="chatMessages">
                        <div class="no-messages-yet">
                            <i class="fas fa-comments fa-3x"></i>
                            <p>No messages yet</p>
                            <small>Start the conversation!</small>
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
                
                // Re-attach event listeners
                const messageInput = document.getElementById('messageInput');
                if (messageInput) {
                    messageInput.addEventListener('input', function() {
                        this.style.height = 'auto';
                        this.style.height = Math.min(this.scrollHeight, 120) + 'px';
                        
                        // Update character counter
                        const charCount = this.value.length;
                        document.getElementById('charCount').textContent = charCount;
                    });
                }
                
                // Start polling for messages
                if (messagePollingInterval) {
                    clearInterval(messagePollingInterval);
                }
                messagePollingInterval = setInterval(() => {
                    loadConversation(currentContactId, currentContactType);
                }, 5000);
            } else {
                // Update existing chat interface
                document.getElementById('chatContactName').textContent = contactName;
                document.getElementById('chatContactType').textContent = capitalizeFirst(contactType);
                document.getElementById('chatAvatar').textContent = contactName.substring(0, 2).toUpperCase();
                document.getElementById('recipientId').value = contactId;
                document.getElementById('recipientType').value = contactType;
                
                document.getElementById('chatMessages').innerHTML = '<div class="no-messages-yet"><i class="fas fa-comments fa-3x"></i><p>No messages yet</p><small>Start the conversation!</small></div>';
            }
            
            // Load conversation (will be empty for new conversation)
            loadConversation(currentContactId, currentContactType);
        }

        // Update unread count
        async function updateUnreadCount() {
            try {
                const response = await fetch('/unipulse/public/moderator/messages/unreadCount');
                const data = await response.json();
                if (data.success) {
                    document.querySelectorAll('.stat-number')[1].textContent = data.count;
                }
            } catch (error) {
                console.error('Error updating unread count:', error);
            }
        }

        // Utility functions
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function capitalizeFirst(str) {
            return str.charAt(0).toUpperCase() + str.slice(1);
        }

        // Auto-expand textarea - initial setup
        const messageInput = document.getElementById('messageInput');
        if (messageInput) {
            messageInput.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = Math.min(this.scrollHeight, 120) + 'px';
                
                // Update character counter
                const charCount = this.value.length;
                const charCountEl = document.getElementById('charCount');
                if (charCountEl) {
                    charCountEl.textContent = charCount;
                }
            });
        }
        
        // Refresh chat function
        function refreshChat() {
            if (currentContactId && currentContactType) {
                loadConversation(currentContactId, currentContactType);
            }
        }

        // Load initial conversation
        if (currentContactId && currentContactType) {
            loadConversation(currentContactId, currentContactType);
            
            // Poll for new messages every 5 seconds
            messagePollingInterval = setInterval(() => {
                loadConversation(currentContactId, currentContactType);
            }, 5000);
        }
    </script>
    
</body>

</html>
