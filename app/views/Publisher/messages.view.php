<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?> - UniPulse</title>
    <link rel="stylesheet" href="/unipulse/public/assets/css/Publisher/messages-style.css?v=<?= time() ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <!-- Navigation -->
    <?php
    $pageConfig = ['activeNav' => 'messages'];
    include __DIR__ . '/components/header.php';
    ?>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Hero Section -->
        <section class="hero">
            <div class="container">
                <div class="hero-content">
                    <h1><i class="fas fa-envelope"></i> My Messages</h1>
                    <p>Manage your communications with sponsors</p>
                </div>
            </div>
        </section>

        <!-- Messages Section -->
        <section class="messages-section">
            <div class="container">
                <!-- Messages Navigation -->
                <div class="messages-nav">
                    <button class="nav-tab active" onclick="showTab('sent')">
                        <i class="fas fa-paper-plane"></i>
                        Sent Messages
                        <span class="message-count"><?= count($sent_messages) ?></span>
                    </button>
                    <button class="nav-tab" onclick="showTab('received')">
                        <i class="fas fa-inbox"></i>
                        Received Messages
                        <span class="message-count"><?= count($received_messages) ?></span>
                        <?php if ($unread_count > 0): ?>
                            <span class="unread-badge"><?= $unread_count ?></span>
                        <?php endif; ?>
                    </button>
                </div>

                <!-- Sent Messages Tab -->
                <div id="sent-tab" class="tab-content active">
                    <div class="messages-header">
                        <h2>Sent Messages</h2>
                        <p>Messages you have sent to sponsors</p>
                    </div>

                    <?php if (!empty($sent_messages)): ?>
                        <div class="messages-grid">
                            <?php foreach ($sent_messages as $message): ?>
                                <div class="message-card sent-message" data-message-id="<?= $message->id ?>">
                                    <div class="message-header">
                                        <div class="recipient-info">
                                            <div class="recipient-avatar">
                                                <?= strtoupper(substr($message->recipient_name, 0, 2)) ?>
                                            </div>
                                            <div class="recipient-details">
                                                <h3 class="recipient-name"><?= htmlspecialchars($message->recipient_name) ?></h3>
                                                <p class="recipient-type">Sponsor</p>
                                            </div>
                                        </div>
                                        <div class="message-status">
                                            <?php if ($message->is_read): ?>
                                                <span class="status-badge read">
                                                    <i class="fas fa-check-double"></i> Read
                                                </span>
                                            <?php else: ?>
                                                <span class="status-badge unread">
                                                    <i class="fas fa-clock"></i> Unread
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="message-content">
                                        <h4 class="message-subject"><?= htmlspecialchars($message->subject) ?></h4>
                                        <p class="message-preview">
                                            <?= htmlspecialchars(substr($message->message, 0, 150)) ?>
                                            <?php if (strlen($message->message) > 150): ?>
                                                <span class="read-more">...</span>
                                            <?php endif; ?>
                                        </p>
                                        <div class="message-meta">
                                            <span class="message-date">
                                                <i class="fas fa-calendar"></i>
                                                <?= date('M j, Y \a\t g:i A', strtotime($message->created_at)) ?>
                                            </span>
                                            <?php if ($message->updated_at): ?>
                                                <span class="message-updated">
                                                    <i class="fas fa-edit"></i>
                                                    Updated: <?= date('M j, Y \a\t g:i A', strtotime($message->updated_at)) ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="message-actions">
                                        <button class="btn btn-primary btn-sm" onclick="viewMessage(<?= $message->id ?>)">
                                            <i class="fas fa-eye"></i> View
                                        </button>
                                        <?php if (!$message->is_read): ?>
                                            <button class="btn btn-secondary btn-sm" onclick="editMessage(<?= $message->id ?>)">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                            <button class="btn btn-danger btn-sm" onclick="deleteMessageFromList(<?= $message->id ?>)">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="empty-state">
                            <div class="empty-content">
                                <i class="fas fa-paper-plane"></i>
                                <h3>No Sent Messages</h3>
                                <p>You haven't sent any messages to sponsors yet.</p>
                                <a href="/unipulse/public/publisher/sponsors" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Browse Sponsors
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Received Messages Tab -->
                <div id="received-tab" class="tab-content">
                    <div class="messages-header">
                        <h2>Received Messages</h2>
                        <p>Messages you have received from sponsors</p>
                    </div>

                    <?php if (!empty($received_messages)): ?>
                        <div class="messages-grid">
                            <?php foreach ($received_messages as $message): ?>
                                <div class="message-card received-message <?= !$message->is_read ? 'unread' : '' ?>">
                                    <div class="message-header">
                                        <div class="sender-info">
                                            <div class="sender-avatar">
                                                <?= strtoupper(substr($message->sender_name, 0, 2)) ?>
                                            </div>
                                            <div class="sender-details">
                                                <h3 class="sender-name"><?= htmlspecialchars($message->sender_name) ?></h3>
                                                <p class="sender-type">Sponsor</p>
                                            </div>
                                        </div>
                                        <?php if (!$message->is_read): ?>
                                            <div class="unread-indicator">
                                                <span class="unread-dot"></span>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <div class="message-content">
                                        <h4 class="message-subject"><?= htmlspecialchars($message->subject) ?></h4>
                                        <p class="message-preview">
                                            <?= htmlspecialchars(substr($message->message, 0, 150)) ?>
                                            <?php if (strlen($message->message) > 150): ?>
                                                <span class="read-more">...</span>
                                            <?php endif; ?>
                                        </p>
                                        <div class="message-meta">
                                            <span class="message-date">
                                                <i class="fas fa-calendar"></i>
                                                <?= date('M j, Y \a\t g:i A', strtotime($message->created_at)) ?>
                                            </span>
                                        </div>
                                    </div>

                                    <div class="message-actions">
                                        <button class="btn btn-primary btn-sm" onclick="viewMessage(<?= $message->id ?>)">
                                            <i class="fas fa-eye"></i> View
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="empty-state">
                            <div class="empty-content">
                                <i class="fas fa-inbox"></i>
                                <h3>No Received Messages</h3>
                                <p>You haven't received any messages from sponsors yet.</p>
                                <p class="help-text">Sponsors can contact you through the platform when they're interested in your events.</p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    </div>

    <!-- Footer -->
    <?php include __DIR__ . '/../components/footer.php'; ?>

    <!-- Message View Modal -->
    <div id="messageModal" class="modal-overlay">
        <div class="modal-container">
            <div class="modal-header">
                <h2 id="modalTitle">Message Details</h2>
                <button class="modal-close" onclick="closeMessageModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-content">
                <div id="modalLoading" class="loading-state">
                    <i class="fas fa-spinner fa-spin"></i>
                    <span>Loading message...</span>
                </div>
                <div id="modalMessageContent" class="message-details" style="display: none;">
                    <div class="message-info">
                        <div class="participant-info">
                            <div class="participant-avatar" id="modalAvatar"></div>
                            <div class="participant-details">
                                <h3 id="modalParticipantName"></h3>
                                <p id="modalParticipantType"></p>
                            </div>
                        </div>
                        <div class="message-status" id="modalStatus"></div>
                    </div>
                    
                    <div class="message-subject-section">
                        <h4>Subject</h4>
                        <p id="modalSubject"></p>
                    </div>
                    
                    <div class="message-body-section">
                        <h4>Message</h4>
                        <div id="modalMessage" class="message-body"></div>
                    </div>
                    
                    <div class="message-metadata">
                        <div class="metadata-item">
                            <i class="fas fa-calendar"></i>
                            <span>Sent: <span id="modalCreatedAt"></span></span>
                        </div>
                        <div class="metadata-item" id="modalUpdatedContainer" style="display: none;">
                            <i class="fas fa-edit"></i>
                            <span>Updated: <span id="modalUpdatedAt"></span></span>
                        </div>
                    </div>
                    
                    <div class="modal-actions" id="modalActions">
                        <!-- Actions will be populated based on message type and status -->
                    </div>
                </div>
                <div id="modalError" class="error-state" style="display: none;">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span>Failed to load message details</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Error/Success Messages -->
    <?php if (isset($_GET['error'])): ?>
        <div id="messageAlert" class="alert alert-error">
            <div class="alert-content">
                <i class="fas fa-exclamation-circle"></i>
                <span><?= htmlspecialchars($_GET['error']) ?></span>
                <button class="alert-close" onclick="this.parentElement.parentElement.remove()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['success'])): ?>
        <div id="messageAlert" class="alert alert-success">
            <div class="alert-content">
                <i class="fas fa-check-circle"></i>
                <span><?= htmlspecialchars($_GET['success']) ?></span>
                <button class="alert-close" onclick="this.parentElement.parentElement.remove()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    <?php endif; ?>

    <script src="/unipulse/public/assets/js/Publisher/messages-app.js?v=<?= time() ?>"></script>
</body>

</html>