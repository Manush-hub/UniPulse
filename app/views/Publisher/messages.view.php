<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?> - UniPulse</title>
    <link rel="stylesheet" href="/unipulse/public/assets/css/components/header-style.css">
    <link rel="stylesheet" href="/unipulse/public/assets/css/Publisher/messages-chatbox-style.css?v=<?= time() ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <!-- Navigation -->
    <?php
    $pageConfig = ['activeNav' => 'messages'];
    include __DIR__ . '/components/header.php';
    ?>

    <!-- Main Content -->
    <div class="main-content chatbox-container">
        <!-- Hero Section -->
        <section class="hero">
            <div class="container">
                <div class="hero-content">
                    <h1><i class="fas fa-comments"></i> Messages</h1>
                    <p>Chat with sponsors and event organizers</p>
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
                                    <small>Start by contacting a sponsor or moderator</small>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Available Contacts Section -->
                        <div class="conversations-header" style="margin-top: 1rem; border-top: 1px solid #e5e7eb; padding-top: 1rem;">
                            <h3><i class="fas fa-users"></i> Available Contacts</h3>
                        </div>
                        
                        <!-- Sponsors -->
                        <?php if (!empty($available_sponsors)): ?>
                            <div class="contacts-section" data-type="sponsor">
                                <h4 class="contacts-section-title"><i class="fas fa-handshake"></i> Sponsors</h4>
                                <div class="contacts-list">
                                    <?php foreach (array_slice($available_sponsors, 0, 5) as $sponsor): ?>
                                        <div class="contact-item" onclick="startConversation(<?= $sponsor->id ?>, 'sponsor', '<?= htmlspecialchars($sponsor->company_name) ?>')">
                                            <div class="contact-avatar">
                                                <?= strtoupper(substr($sponsor->company_name, 0, 2)) ?>
                                            </div>
                                            <div class="contact-info">
                                                <h5 class="contact-name"><?= htmlspecialchars($sponsor->company_name) ?></h5>
                                                <p class="contact-type">Sponsor</p>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Moderators -->
                        <?php if (!empty($available_moderators)): ?>
                            <div class="contacts-section" data-type="moderator">
                                <h4 class="contacts-section-title"><i class="fas fa-user-shield"></i> Moderators (Your University)</h4>
                                <div class="contacts-list">
                                    <?php foreach ($available_moderators as $moderator): ?>
                                        <div class="contact-item" onclick="startConversation(<?= $moderator->id ?>, 'moderator', '<?= htmlspecialchars($moderator->full_name) ?>')">
                                            <div class="contact-avatar">
                                                <?= strtoupper(substr($moderator->full_name, 0, 2)) ?>
                                            </div>
                                            <div class="contact-info">
                                                <h5 class="contact-name"><?= htmlspecialchars($moderator->full_name) ?></h5>
                                                <p class="contact-type">Moderator</p>
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
                                <p class="help-text">Start a conversation with sponsors through the platform.</p>
                                <a href="/unipulse/public/publisher/sponsors" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Browse Sponsors
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Footer -->
    <?php include __DIR__ . '/../components/footer.php'; ?>

    <script src="/unipulse/public/assets/js/Publisher/messages-chatbox-app.js?v=<?= time() ?>"></script>
</body>

