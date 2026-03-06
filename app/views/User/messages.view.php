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
    <?php
    $pageConfig = ['activeNav' => 'messages'];
    include __DIR__ . '/components/header.php';
    ?>

    <div class="main-content chatbox-container">
        <section class="hero">
            <div class="container">
                <div class="hero-content">
                    <h1><i class="fas fa-comments"></i> Messages</h1>
                    <p>Chat with publishers and organizers</p>
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

        <section class="chatbox-section">
            <div class="container">
                <div class="chatbox-wrapper">
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
                                         data-contact-photo="<?= htmlspecialchars($conv->contact_photo ?? '') ?>"
                                         onclick="selectConversation(this)">
                                        <div class="conversation-avatar">
                                            <?php if (!empty($conv->contact_photo)): ?>
                                                <img src="<?= htmlspecialchars($conv->contact_photo) ?>" alt="<?= htmlspecialchars($conv->contact_name) ?>" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                                            <?php else: ?>
                                                <?= strtoupper(substr($conv->contact_name, 0, 2)) ?>
                                            <?php endif; ?>
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

                        <div class="conversations-header" style="margin-top: 1rem; border-top: 1px solid #e5e7eb; padding-top: 1rem;">
                            <h3><i class="fas fa-users"></i> Available Publishers</h3>
                        </div>

                        <?php if (!empty($available_publishers)): ?>
                            <div class="contacts-section" data-type="publisher">
                                <h4 class="contacts-section-title"><i class="fas fa-bullhorn"></i> Publishers</h4>
                                <div class="contacts-list">
                                    <?php foreach (array_slice($available_publishers, 0, 12) as $publisher): ?>
                                        <div class="contact-item"
                                             data-contact-id="<?= $publisher->id ?>"
                                             data-contact-type="publisher"
                                             data-contact-name="<?= htmlspecialchars($publisher->society_name) ?>"
                                             data-contact-photo="<?= htmlspecialchars($publisher->logo_url ?? '') ?>"
                                             onclick="startConversation(<?= $publisher->id ?>, 'publisher', '<?= htmlspecialchars($publisher->society_name) ?>', '<?= htmlspecialchars($publisher->logo_url ?? '') ?>')">
                                            <div class="contact-avatar">
                                                <?php if (!empty($publisher->logo_url)): ?>
                                                    <img src="<?= htmlspecialchars($publisher->logo_url) ?>" alt="<?= htmlspecialchars($publisher->society_name) ?>" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                                                <?php else: ?>
                                                    <?= strtoupper(substr($publisher->society_name, 0, 2)) ?>
                                                <?php endif; ?>
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

                    <div class="chat-panel">
                        <?php if (!empty($conversations)): ?>
                            <div class="chat-header">
                                <div class="chat-contact-info">
                                    <div class="chat-avatar" id="chatAvatar">
                                        <?php if (!empty($conversations[0]->contact_photo)): ?>
                                            <img src="<?= htmlspecialchars($conversations[0]->contact_photo) ?>" alt="<?= htmlspecialchars($conversations[0]->contact_name) ?>" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                                        <?php else: ?>
                                            <?= strtoupper(substr($conversations[0]->contact_name, 0, 2)) ?>
                                        <?php endif; ?>
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

                            <div class="chat-messages" id="chatMessages">
                                <div class="loading-messages">
                                    <i class="fas fa-spinner fa-spin"></i>
                                    <p>Loading messages...</p>
                                </div>
                            </div>

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
                            <div class="chat-empty-state">
                                <i class="fas fa-envelope-open-text fa-4x"></i>
                                <h3>No Messages Yet</h3>
                                <p>You have not started any conversations yet.</p>
                                <p class="help-text">Select a publisher on the left to begin chatting.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <?php include __DIR__ . '/../components/footer.php'; ?>

    <script src="/unipulse/public/assets/js/User/messages-chatbox-app.js?v=<?= time() ?>"></script>
</body>

</html>
