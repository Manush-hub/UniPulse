<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UniPulse - Messages</title>
    <link rel="stylesheet" href="/unipulse/public/assets/css/components/header-style.css">
    <link rel="stylesheet" href="/unipulse/public/assets/css/Sponsor/messages-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <!-- Header -->
    <?php
    $pageConfig = ['activeNav' => 'dashboard'];
    include __DIR__ . '/components/header.php';
    ?>

    <!-- Main Container -->
    <div class="main-container">
        <!-- Page Header -->
        <section class="page-header">
            <div class="container">
                <div class="header-content">
                    <div class="header-text">
                        <h1>Messages</h1>
                        <p>Communications from publishers and event organizers</p>
                    </div>
                    <div class="header-stats">
                        <div class="stat-item">
                            <span class="stat-number"><?= count($messages) ?></span>
                            <span class="stat-label">Total Messages</span>
                        </div>
                        <div class="stat-item unread">
                            <span class="stat-number"><?= $unread_count ?></span>
                            <span class="stat-label">Unread</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Messages Section -->
        <section class="messages-section">
            <div class="container">
                <div class="messages-header">
                    <div class="filter-tabs">
                        <button class="filter-tab active" data-filter="all">All Messages</button>
                        <button class="filter-tab" data-filter="unread">Unread (<?= $unread_count ?>)</button>
                        <button class="filter-tab" data-filter="read">Read</button>
                    </div>
                    <div class="message-actions">
                        <button class="btn btn-secondary" onclick="markAllAsRead()">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                            Mark All Read
                        </button>
                        <button class="btn btn-secondary" onclick="refreshMessages()">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="1 4 1 10 7 10"></polyline>
                                <path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"></path>
                            </svg>
                            Refresh
                        </button>
                    </div>
                </div>

                <div class="messages-list" id="messagesList">
                    <?php if (!empty($messages)): ?>
                        <?php foreach ($messages as $message): ?>
                            <div class="message-card <?= $message->is_read ? 'read' : 'unread' ?>" 
                                 data-message-id="<?= $message->id ?>"
                                 data-read-status="<?= $message->is_read ? 'read' : 'unread' ?>">
                                <div class="message-header">
                                    <div class="sender-info">
                                        <div class="sender-avatar">
                                            <?= strtoupper(substr($message->sender_name, 0, 2)) ?>
                                        </div>
                                        <div class="sender-details">
                                            <h3 class="sender-name"><?= htmlspecialchars($message->sender_name) ?></h3>
                                            <p class="sender-email"><?= htmlspecialchars($message->sender_email) ?></p>
                                        </div>
                                    </div>
                                    <div class="message-meta">
                                        <span class="message-date"><?= date('M j, Y', strtotime($message->created_at)) ?></span>
                                        <span class="message-time"><?= date('g:i A', strtotime($message->created_at)) ?></span>
                                        <?php if (!$message->is_read): ?>
                                            <span class="unread-indicator"></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="message-content">
                                    <h4 class="message-subject"><?= htmlspecialchars($message->subject) ?></h4>
                                    <p class="message-preview"><?= htmlspecialchars(substr($message->message, 0, 150)) ?>...</p>
                                </div>
                                <div class="message-actions">
                                    <button class="btn btn-primary btn-sm" 
                                            onclick="showMessagePopup(<?= $message->id ?>)"
                                            data-sender-name="<?= htmlspecialchars($message->sender_name) ?>"
                                            data-sender-email="<?= htmlspecialchars($message->sender_email) ?>"
                                            data-subject="<?= htmlspecialchars($message->subject) ?>"
                                            data-message="<?= htmlspecialchars($message->message) ?>"
                                            data-created-at="<?= $message->created_at ?>"
                                            data-is-read="<?= $message->is_read ? 'true' : 'false' ?>">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                            <circle cx="12" cy="12" r="3"></circle>
                                        </svg>
                                        Read Message
                                    </button>
                                    <?php if (!$message->is_read): ?>
                                        <button class="btn btn-secondary btn-sm" onclick="markAsRead(<?= $message->id ?>)">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <polyline points="20 6 9 17 4 12"></polyline>
                                            </svg>
                                            Mark Read
                                        </button>
                                    <?php endif; ?>
                                    <button class="btn btn-danger btn-sm" onclick="deleteMessage(<?= $message->id ?>)">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <polyline points="3 6 5 6 21 6"></polyline>
                                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                        </svg>
                                        Delete
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="no-messages">
                            <div class="no-messages-content">
                                <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
                                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                    <polyline points="22 6 12 13 2 6"></polyline>
                                </svg>
                                <h3>No Messages Yet</h3>
                                <p>You haven't received any messages from publishers yet.</p>
                                <p class="help-text">Publishers can contact you directly through the sponsorship system.</p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    </div>

    <!-- Footer -->
    <?php include __DIR__ . '/../components/footer.php'; ?>

    <!-- Message Popup Modal -->
    <div id="messagePopupModal" class="modal">
        <div class="modal-content message-popup">
            <div class="modal-header">
                <h3 id="popupMessageSubject">Message Subject</h3>
                <span class="close-button" onclick="closeMessagePopup()">&times;</span>
            </div>
            <div class="modal-body">
                <div class="popup-sender-info">
                    <div class="sender-avatar" id="popupSenderAvatar">AB</div>
                    <div class="sender-details">
                        <h4 id="popupSenderName">Sender Name</h4>
                        <p id="popupSenderEmail">sender@email.com</p>
                        <span class="sender-type">Publisher</span>
                    </div>
                    <div class="message-date">
                        <span id="popupMessageDate">Jan 1, 2025</span>
                        <span id="popupMessageTime">12:00 PM</span>
                    </div>
                </div>
                <div class="popup-message-content">
                    <div id="popupMessageBody">Message content will appear here...</div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="replyToPopupMessage()">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="m9 10-5 5 5 5"></path>
                        <path d="M20 4v7a4 4 0 0 1-4 4H4"></path>
                    </svg>
                    Reply
                </button>
                <button type="button" class="btn btn-secondary" onclick="markPopupAsRead()" id="markReadPopupBtn" style="display: none;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                    Mark as Read
                </button>
                <button type="button" class="btn btn-danger" onclick="deletePopupMessage()">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="3 6 5 6 21 6"></polyline>
                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2 2h4a2 2 0 0 1 2 2v2"></path>
                    </svg>
                    Delete
                </button>
                <button type="button" class="btn btn-secondary" onclick="closeMessagePopup()">Close</button>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Delete Message</h3>
                <span class="close-button" onclick="closeDeleteModal()">&times;</span>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this message? This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeDeleteModal()">Cancel</button>
                <button type="button" class="btn btn-danger" onclick="confirmDelete()">Delete Message</button>
            </div>
        </div>
    </div>

    <script src="/unipulse/public/assets/js/Sponsor/messages-app.js"></script>
</body>

</html>