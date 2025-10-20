<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?> - UniPulse</title>
    <link rel="stylesheet" href="/unipulse/public/assets/css/Publisher/edit-message-style.css?v=<?= time() ?>">
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
        <!-- Header Section -->
        <section class="edit-header">
            <div class="container">
                <div class="header-content">
                    <div class="breadcrumb">
                        <a href="/unipulse/public/publisher/messages">
                            <i class="fas fa-envelope"></i> Messages
                        </a>
                        <span class="separator">/</span>
                        <span class="current">Edit Message</span>
                    </div>
                    <h1><i class="fas fa-edit"></i> Edit Message</h1>
                    <p>Update your message to <?= htmlspecialchars($message->recipient_name) ?></p>
                </div>
            </div>
        </section>

        <!-- Edit Form Section -->
        <section class="edit-form-section">
            <div class="container">
                <div class="edit-form-container">
                    <!-- Message Info -->
                    <div class="message-info">
                        <div class="info-header">
                            <div class="recipient-avatar">
                                <?= strtoupper(substr($message->recipient_name, 0, 2)) ?>
                            </div>
                            <div class="recipient-details">
                                <h3><?= htmlspecialchars($message->recipient_name) ?></h3>
                                <p class="recipient-type">Sponsor</p>
                                <p class="original-date">
                                    <i class="fas fa-calendar"></i>
                                    Originally sent: <?= date('M j, Y \a\t g:i A', strtotime($message->created_at)) ?>
                                </p>
                            </div>
                        </div>
                        
                        <div class="edit-notice">
                            <i class="fas fa-info-circle"></i>
                            <div class="notice-content">
                                <strong>Note:</strong> You can only edit messages that haven't been read yet. 
                                Once the recipient reads your message, it cannot be edited.
                            </div>
                        </div>
                    </div>

                    <!-- Edit Form -->
                    <form id="editMessageForm" action="/unipulse/public/publisher/messages/edit/<?= $message->id ?>" method="POST">
                        <input type="hidden" name="message_id" value="<?= $message->id ?>">
                        
                        <div class="form-group">
                            <label for="subject">
                                <i class="fas fa-tag"></i> Subject
                            </label>
                            <input type="text" 
                                   id="subject" 
                                   name="subject" 
                                   value="<?= htmlspecialchars($message->subject) ?>" 
                                   placeholder="Enter message subject" 
                                   required 
                                   maxlength="200">
                            <div class="char-counter">
                                <span id="subject-count"><?= strlen($message->subject) ?></span>/200
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="message">
                                <i class="fas fa-comment"></i> Message
                            </label>
                            <textarea id="message" 
                                      name="message" 
                                      rows="8" 
                                      placeholder="Enter your message..." 
                                      required 
                                      maxlength="2000"><?= htmlspecialchars($message->message) ?></textarea>
                            <div class="char-counter">
                                <span id="message-count"><?= strlen($message->message) ?></span>/2000
                            </div>
                        </div>

                        <div class="form-actions">
                            <div class="action-buttons">
                                <button type="button" class="btn btn-secondary" onclick="cancelEdit()">
                                    <i class="fas fa-times"></i> Cancel
                                </button>
                                
                                <!-- Delete button for unread messages -->
                                <?php if (!$message->is_read): ?>
                                <button type="button" class="btn btn-danger" onclick="deleteMessage(<?= $message->id ?>)">
                                    <i class="fas fa-trash"></i> Delete Message
                                </button>
                                <?php endif; ?>
                                
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Update Message
                                </button>
                            </div>
                            
                            <div class="action-info">
                                <p class="last-modified">
                                    <?php if ($message->updated_at): ?>
                                        <i class="fas fa-clock"></i>
                                        Last modified: <?= date('M j, Y \a\t g:i A', strtotime($message->updated_at)) ?>
                                    <?php else: ?>
                                        <i class="fas fa-info-circle"></i>
                                        This message hasn't been modified yet
                                    <?php endif; ?>
                                </p>
                                
                                <?php if (!$message->is_read): ?>
                                <p class="delete-info">
                                    <i class="fas fa-info-circle text-warning"></i>
                                    You can delete this message since it hasn't been read yet
                                </p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Original Message Preview -->
                <div class="original-preview">
                    <div class="preview-header">
                        <h3><i class="fas fa-eye"></i> Original Message Preview</h3>
                        <p>This is how your message currently appears</p>
                    </div>
                    
                    <div class="preview-content">
                        <div class="preview-message">
                            <div class="preview-header-info">
                                <div class="preview-sender">
                                    <div class="sender-avatar">
                                        <?= strtoupper(substr($user['society_name'] ?? $user['name'] ?? 'Society', 0, 2)) ?>
                                    </div>
                                    <div class="sender-details">
                                        <h4><?= htmlspecialchars($user['society_name'] ?? $user['name'] ?? 'Society Name') ?></h4>
                                        <p>Publisher</p>
                                    </div>
                                </div>
                                <div class="preview-date">
                                    <?= date('M j, Y \a\t g:i A', strtotime($message->created_at)) ?>
                                </div>
                            </div>
                            
                            <div class="preview-subject">
                                <strong id="preview-subject"><?= htmlspecialchars($message->subject) ?></strong>
                            </div>
                            
                            <div class="preview-body">
                                <div id="preview-message"><?= nl2br(htmlspecialchars($message->message)) ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Footer -->
    <?php include __DIR__ . '/../components/footer.php'; ?>

    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="loading-overlay" style="display: none;">
        <div class="loading-content">
            <div class="loading-spinner"></div>
            <p>Updating message...</p>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-exclamation-triangle text-danger"></i> Confirm Deletion</h3>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this message?</p>
                <p class="text-warning"><strong>This action cannot be undone.</strong></p>
                <div class="message-preview">
                    <strong>Subject:</strong> <?= htmlspecialchars($message->subject) ?><br>
                    <strong>To:</strong> <?= htmlspecialchars($message->recipient_name) ?>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeDeleteModal()">
                    <i class="fas fa-times"></i> Cancel
                </button>
                <button type="button" class="btn btn-danger" onclick="confirmDelete()">
                    <i class="fas fa-trash"></i> Delete Message
                </button>
            </div>
        </div>
    </div>

    <script src="/unipulse/public/assets/js/Publisher/edit-message-app.js?v=<?= time() ?>"></script>
</body>

</html>