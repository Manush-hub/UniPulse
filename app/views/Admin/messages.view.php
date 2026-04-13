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
                    <div class="stat-item">
                        <span class="stat-number"><?= count($contact_reaches ?? []) ?></span>
                        <span class="stat-label">Contact Reaches</span>
                    </div>
                </div>
            </div>
        </section>

        <section class="contact-reaches-section">
            <div class="container">
                <div class="contact-reaches-card">
                    <div class="contact-reaches-header">
                        <h3><i class="fas fa-life-ring"></i> Contact Us Reaches</h3>
                        <span class="contact-reaches-count"><?= count($contact_reaches ?? []) ?> recent</span>
                    </div>

                    <?php if (!empty($contact_reaches)): ?>
                        <div class="contact-reaches-list <?= count($contact_reaches) > 2 ? 'has-scroll' : '' ?>">
                            <?php foreach ($contact_reaches as $reach): ?>
                                <article class="contact-reach-item">
                                    <div class="contact-reach-top">
                                        <div>
                                            <h4><?= htmlspecialchars($reach->subject ?? 'No Subject') ?></h4>
                                            <p class="contact-reach-meta">
                                                From <?= htmlspecialchars($reach->full_name ?? 'Unknown User') ?>
                                                (<?= htmlspecialchars($reach->email ?? 'No Email') ?>)
                                            </p>
                                        </div>
                                        <span class="contact-reach-time">
                                            <?= !empty($reach->created_at) ? date('M j, Y g:i A', strtotime($reach->created_at)) : '-' ?>
                                        </span>
                                    </div>
                                    <p class="contact-reach-message">
                                        <?= nl2br(htmlspecialchars($reach->message ?? '')) ?>
                                    </p>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="contact-reaches-empty">
                            <i class="fas fa-inbox"></i>
                            <p>No contact submissions yet.</p>
                        </div>
                    <?php endif; ?>
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
                                    <input type="hidden" id="recipientId" value="<?= $conversations[0]->contact_id ?>">
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
        window.adminMessagesConfig = {
            currentContactId: <?= !empty($conversations) ? $conversations[0]->contact_id : 0 ?>,
            currentContactType: '<?= !empty($conversations) ? $conversations[0]->contact_type : '' ?>'
        };
    </script>
    <script src="<?php echo ROOT ?>/assets/js/extracted/Admin_messages.js"></script>

</body>

</html>