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
    $safeConversations = [];
    if (!empty($conversations) && is_array($conversations)) {
        foreach ($conversations as $conv) {
            $cid = isset($conv->contact_id) ? (int)$conv->contact_id : 0;
            $ctype = trim((string)($conv->contact_type ?? ''));
            if ($cid <= 0 || $ctype === '') {
                continue;
            }
            $safeConversations[] = $conv;
        }
    }

    $initialConversation = !empty($safeConversations) ? $safeConversations[0] : null;
    ?>

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
                        <span class="stat-number"><?= count($safeConversations) ?></span>
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
                            <?php if (!empty($safeConversations)): ?>
                                <?php foreach ($safeConversations as $index => $conv): ?>
                                    <?php
                                    $convContactId = (int)($conv->contact_id ?? 0);
                                    $convContactType = trim((string)($conv->contact_type ?? ''));
                                    $convContactName = trim((string)($conv->contact_name ?? ''));
                                    if ($convContactName === '') {
                                        $convContactName = ucfirst($convContactType) . ' #' . $convContactId;
                                    }
                                    ?>
                                    <div class="conversation-item <?= $conv->unread_count > 0 ? 'has-unread' : '' ?> <?= $index === 0 ? 'active' : '' ?>" 
                                         data-contact-id="<?= $convContactId ?>"
                                         data-contact-type="<?= htmlspecialchars($convContactType) ?>"
                                         data-contact-name="<?= htmlspecialchars($convContactName) ?>"
                                         data-contact-photo="<?= htmlspecialchars($conv->contact_photo ?? '') ?>"
                                         onclick="selectConversation(this)">
                                        <div class="conversation-avatar">
                                            <?php if (!empty($conv->contact_photo)): ?>
                                                <img src="<?= htmlspecialchars($conv->contact_photo ?? '') ?>" alt="<?= htmlspecialchars($convContactName) ?>" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                                            <?php else: ?>
                                                <?= strtoupper(substr($convContactName, 0, 2)) ?>
                                            <?php endif; ?>
                                        </div>
                                        <div class="conversation-info">
                                            <h4 class="conversation-name"><?= htmlspecialchars($convContactName) ?></h4>
                                            <p class="conversation-last-message">
                                                <?= htmlspecialchars(substr((string)($conv->last_message ?? ''), 0, 40)) ?>...
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
                                        <div class="contact-item" 
                                             data-contact-id="<?= $sponsor->id ?>"
                                             data-contact-type="sponsor"
                                             data-contact-name="<?= htmlspecialchars($sponsor->company_name) ?>"
                                             data-contact-photo="<?= htmlspecialchars($sponsor->logo_url ?? '') ?>"
                                            onclick="startConversation(<?= (int)$sponsor->id ?>, <?= htmlspecialchars(json_encode('sponsor'), ENT_QUOTES, 'UTF-8') ?>, <?= htmlspecialchars(json_encode((string)($sponsor->company_name ?? '')), ENT_QUOTES, 'UTF-8') ?>, <?= htmlspecialchars(json_encode((string)($sponsor->logo_url ?? '')), ENT_QUOTES, 'UTF-8') ?>)">
                                            <div class="contact-avatar">
                                                <?php if (!empty($sponsor->logo_url)): ?>
                                                    <img src="<?= htmlspecialchars($sponsor->logo_url) ?>" alt="<?= htmlspecialchars($sponsor->company_name) ?>" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                                                <?php else: ?>
                                                    <?= strtoupper(substr($sponsor->company_name, 0, 2)) ?>
                                                <?php endif; ?>
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
                                        <div class="contact-item" 
                                             data-contact-id="<?= $moderator->id ?>"
                                             data-contact-type="moderator"
                                             data-contact-name="<?= htmlspecialchars($moderator->full_name) ?>"
                                            onclick="startConversation(<?= (int)$moderator->id ?>, <?= htmlspecialchars(json_encode('moderator'), ENT_QUOTES, 'UTF-8') ?>, <?= htmlspecialchars(json_encode((string)($moderator->full_name ?? '')), ENT_QUOTES, 'UTF-8') ?>)">
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
                        <?php if ($initialConversation): ?>
                            <!-- Chat Header -->
                            <div class="chat-header">
                                <div class="chat-contact-info">
                                    <div class="chat-avatar" id="chatAvatar">
                                        <?php if (!empty($initialConversation->contact_photo)): ?>
                                            <img src="<?= htmlspecialchars($initialConversation->contact_photo) ?>" alt="<?= htmlspecialchars($initialConversation->contact_name) ?>" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                                        <?php else: ?>
                                            <?= strtoupper(substr((string)($initialConversation->contact_name ?? ''), 0, 2)) ?>
                                        <?php endif; ?>
                                    </div>
                                    <div class="chat-contact-details">
                                        <h3 id="chatContactName"><?= htmlspecialchars($initialConversation->contact_name ?? '') ?></h3>
                                        <span class="contact-type" id="chatContactType">
                                            <?= ucfirst((string)($initialConversation->contact_type ?? '')) ?>
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
                                    <input type="hidden" id="recipientId" value="<?= (int)($initialConversation->contact_id ?? 0) ?>">
                                    <input type="hidden" id="recipientType" value="<?= htmlspecialchars((string)($initialConversation->contact_type ?? '')) ?>">
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

