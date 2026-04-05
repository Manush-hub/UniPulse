// Moderator Messages App - Client-side functionality for sending messages to publishers

document.addEventListener('DOMContentLoaded', function() {
    console.log('Moderator Messages App initialized');
    initializeMessageForm();
});

function initializeMessageForm() {
    const form = document.getElementById('messageForm');
    if (!form) return;

    const subjectInput = document.getElementById('subject');
    const messageInput = document.getElementById('message');
    const subjectCounter = document.getElementById('subjectCounter');
    const messageCounter = document.getElementById('messageCounter');
    const submitBtn = document.getElementById('submitBtn');

    // Character counters
    if (subjectInput && subjectCounter) {
        subjectInput.addEventListener('input', function() {
            updateCharCounter(this, subjectCounter, 200);
        });
    }

    if (messageInput && messageCounter) {
        messageInput.addEventListener('input', function() {
            updateCharCounter(this, messageCounter, 2000);
        });
    }

    // Form submission
    form.addEventListener('submit', async function(e) {
        e.preventDefault();

        // Validate form
        if (!validateForm()) {
            return;
        }

        // Disable submit button
        submitBtn.disabled = true;
        submitBtn.classList.add('loading');
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';

        // Prepare form data
        const formData = new FormData(form);

        try {
            const response = await fetch('/unipulse/public/moderator/messages/send', {
                method: 'POST',
                body: formData
            });

            // Log response status for debugging
            console.log('Response status:', response.status);
            
            // Check if response is ok
            if (!response.ok) {
                console.error('Response not OK:', response.status, response.statusText);
            }

            // Try to parse JSON
            let result;
            try {
                const text = await response.text();
                console.log('Response text:', text);
                result = JSON.parse(text);
            } catch (parseError) {
                console.error('JSON parse error:', parseError);
                throw new Error('Invalid server response. Please check the server logs.');
            }

            if (result.success) {
                showAlert('success', result.message);
                
                // Reset form
                form.reset();
                updateCharCounter(subjectInput, subjectCounter, 200);
                updateCharCounter(messageInput, messageCounter, 2000);
                
                // Hide preview
                const previewSection = document.getElementById('previewSection');
                if (previewSection) {
                    previewSection.style.display = 'none';
                }

                // Redirect after 2 seconds
                setTimeout(() => {
                    window.location.href = '/unipulse/public/moderator/messages';
                }, 2000);
            } else {
                showAlert('error', result.message || 'Failed to send message');
                
                // Re-enable submit button
                submitBtn.disabled = false;
                submitBtn.classList.remove('loading');
                submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Send Message';
            }
        } catch (error) {
            console.error('Error sending message:', error);
            showAlert('error', error.message || 'An error occurred while sending the message. Please try again.');
            
            // Re-enable submit button
            submitBtn.disabled = false;
            submitBtn.classList.remove('loading');
            submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Send Message';
        }
    });
}

function updateCharCounter(input, counter, maxLength) {
    const length = input.value.length;
    counter.textContent = length;
    
    const parent = counter.parentElement;
    parent.classList.remove('warning', 'danger');
    
    const percentage = (length / maxLength) * 100;
    if (percentage >= 90) {
        parent.classList.add('danger');
    } else if (percentage >= 75) {
        parent.classList.add('warning');
    }
}

function validateForm() {
    const publisherId = document.getElementById('publisher_id').value;
    const subject = document.getElementById('subject').value.trim();
    const message = document.getElementById('message').value.trim();
    const errors = [];

    if (!publisherId) {
        errors.push('Please select a publisher');
    }

    if (!subject) {
        errors.push('Subject is required');
    } else if (subject.length > 200) {
        errors.push('Subject must not exceed 200 characters');
    }

    if (!message) {
        errors.push('Message is required');
    } else if (message.length > 2000) {
        errors.push('Message must not exceed 2000 characters');
    }

    if (errors.length > 0) {
        showAlert('error', errors.join('<br>'));
        return false;
    }

    return true;
}

function togglePreview() {
    const previewSection = document.getElementById('previewSection');
    const subject = document.getElementById('subject').value.trim();
    const message = document.getElementById('message').value.trim();
    const previewSubject = document.getElementById('previewSubject');
    const previewMessage = document.getElementById('previewMessage');

    if (!subject && !message) {
        showAlert('error', 'Please enter a subject and message to preview');
        return;
    }

    // Update preview content
    if (previewSubject) {
        previewSubject.textContent = subject || '-';
    }
    if (previewMessage) {
        previewMessage.textContent = message || '-';
    }

    // Toggle preview visibility
    if (previewSection) {
        if (previewSection.style.display === 'none' || !previewSection.style.display) {
            previewSection.style.display = 'block';
            previewSection.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        } else {
            previewSection.style.display = 'none';
        }
    }
}

function showAlert(type, message) {
    const alertDiv = document.getElementById('messageAlert');
    if (!alertDiv) return;

    alertDiv.className = `alert alert-${type}`;
    alertDiv.innerHTML = message;
    alertDiv.style.display = 'block';

    // Scroll to alert
    alertDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });

    // Auto-hide success messages after 5 seconds
    if (type === 'success') {
        setTimeout(() => {
            alertDiv.style.display = 'none';
        }, 5000);
    }
}

// Make togglePreview available globally
window.togglePreview = togglePreview;

/* Extracted from Moderator/messages.view.php */

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
    
