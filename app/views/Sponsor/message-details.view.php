<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UniPulse - Message Details</title>
    <link rel="stylesheet" href="/unipulse/public/assets/css/Sponsor/message-details-style.css">
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
        <!-- Breadcrumb Navigation -->
        <section class="breadcrumb-section">
            <div class="container">
                <nav class="breadcrumb-nav">
                    <a href="/unipulse/public/sponsor/messages">Messages</a>
                    <span class="separator">/</span>
                    <span class="current">Message Details</span>
                </nav>
            </div>
        </section>

        <!-- Message Details -->
        <section class="message-details">
            <div class="container">
                <div class="message-container">
                    <!-- Message Header -->
                    <div class="message-header">
                        <div class="sender-section">
                            <div class="sender-avatar">
                                <?= strtoupper(substr($message->sender_name, 0, 2)) ?>
                            </div>
                            <div class="sender-info">
                                <h2 class="sender-name"><?= htmlspecialchars($message->sender_name) ?></h2>
                                <p class="sender-email"><?= htmlspecialchars($message->sender_email) ?></p>
                                <span class="sender-type">Publisher</span>
                            </div>
                        </div>
                        <div class="message-meta">
                            <div class="meta-item">
                                <span class="meta-label">Received:</span>
                                <span class="meta-value"><?= date('F j, Y \a\t g:i A', strtotime($message->created_at)) ?></span>
                            </div>
                            <?php if ($message->read_at): ?>
                                <div class="meta-item">
                                    <span class="meta-label">Read:</span>
                                    <span class="meta-value"><?= date('F j, Y \a\t g:i A', strtotime($message->read_at)) ?></span>
                                </div>
                            <?php endif; ?>
                            <div class="message-status <?= $message->is_read ? 'read' : 'unread' ?>">
                                <?= $message->is_read ? 'Read' : 'Unread' ?>
                            </div>
                        </div>
                    </div>

                    <!-- Message Subject -->
                    <div class="message-subject">
                        <h1><?= htmlspecialchars($message->subject) ?></h1>
                    </div>

                    <!-- Message Content -->
                    <div class="message-content">
                        <div class="message-body">
                            <?= nl2br(htmlspecialchars($message->message)) ?>
                        </div>
                    </div>

                    <!-- Message Actions -->
                    <div class="message-actions">
                        <button class="btn btn-primary" onclick="openReplyModal()">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="m9 10-5 5 5 5"></path>
                                <path d="M20 4v7a4 4 0 0 1-4 4H4"></path>
                            </svg>
                            Reply
                        </button>
                        <button class="btn btn-secondary" onclick="window.history.back()">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="m12 19-7-7 7-7"></path>
                                <path d="M19 12H5"></path>
                            </svg>
                            Back to Messages
                        </button>
                        <button class="btn btn-danger" onclick="deleteMessage(<?= $message->id ?>)">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="3 6 5 6 21 6"></polyline>
                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                            </svg>
                            Delete
                        </button>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Footer -->
    <?php include __DIR__ . '/../components/footer.php'; ?>

    <!-- Reply Modal -->
    <div id="replyModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Reply to <?= htmlspecialchars($message->sender_name) ?></h3>
                <span class="close-button" onclick="closeReplyModal()">&times;</span>
            </div>
            <form id="replyForm" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="original_message_id" value="<?= $message->id ?>">
                    <div class="form-group">
                        <label for="replySubject">Subject</label>
                        <input type="text" id="replySubject" name="subject" 
                               value="Re: <?= htmlspecialchars($message->subject) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="replyMessage">Message</label>
                        <textarea id="replyMessage" name="message" rows="8" placeholder="Type your reply..." required></textarea>
                    </div>
                    <div class="original-message">
                        <h4>Original Message:</h4>
                        <div class="original-content">
                            <strong>From:</strong> <?= htmlspecialchars($message->sender_name) ?> &lt;<?= htmlspecialchars($message->sender_email) ?>&gt;<br>
                            <strong>Date:</strong> <?= date('F j, Y \a\t g:i A', strtotime($message->created_at)) ?><br>
                            <strong>Subject:</strong> <?= htmlspecialchars($message->subject) ?><br><br>
                            <?= nl2br(htmlspecialchars($message->message)) ?>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeReplyModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Send Reply</button>
                </div>
            </form>
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

    <script src="/unipulse/public/assets/js/Sponsor/message-details-app.js"></script>
</body>

</html>