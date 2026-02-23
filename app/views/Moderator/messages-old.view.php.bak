<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UniPulse - Messages to Publishers</title>
    <link rel="stylesheet" href="/unipulse/public/assets/css/Components/header-style.css">
    <link rel="stylesheet" href="/unipulse/public/assets/css/Moderator/messages-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <!-- Header -->
    <?php
    $pageConfig = ['activeNav' => 'messages'];
    $headerCssLoaded = true;
    include __DIR__ . '/components/header.php';
    ?>

    <!-- Main Container -->
    <div class="main-container">
        <!-- Page Header -->
        <div class="page-header">
            <div class="container">
                <h1>Messages to Publishers</h1>
                <p>View all messages you've sent to publishers</p>
                <a href="/unipulse/public/moderator/messages/compose" class="btn btn-primary">
                    <i class="fas fa-paper-plane"></i> Send New Message
                </a>
            </div>
        </div>

        <!-- Messages Section -->
        <section class="messages-section">
            <div class="container">
                <?php if (empty($messages)): ?>
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <h3>No Messages Yet</h3>
                        <p>You haven't sent any messages to publishers yet.</p>
                        <a href="/unipulse/public/moderator/messages/compose" class="btn btn-primary">
                            <i class="fas fa-paper-plane"></i> Send Your First Message
                        </a>
                    </div>
                <?php else: ?>
                    <div class="messages-list">
                        <?php foreach ($messages as $message): ?>
                            <div class="message-card">
                                <div class="message-header">
                                    <div class="message-to">
                                        <i class="fas fa-building"></i>
                                        <span>To: <?= htmlspecialchars($message->recipient_name ?? 'Publisher') ?></span>
                                    </div>
                                    <div class="message-status">
                                        <?php if ($message->is_read): ?>
                                            <span class="badge badge-read">
                                                <i class="fas fa-check-double"></i> Read
                                            </span>
                                        <?php else: ?>
                                            <span class="badge badge-unread">
                                                <i class="fas fa-envelope"></i> Unread
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="message-body">
                                    <h3 class="message-subject"><?= htmlspecialchars($message->subject) ?></h3>
                                    <p class="message-preview">
                                        <?= htmlspecialchars(substr($message->message, 0, 150)) ?>
                                        <?= strlen($message->message) > 150 ? '...' : '' ?>
                                    </p>
                                </div>
                                <div class="message-footer">
                                    <div class="message-time">
                                        <i class="fas fa-clock"></i>
                                        <?= date('M j, Y \a\t g:i A', strtotime($message->created_at)) ?>
                                    </div>
                                    <a href="/unipulse/public/moderator/messages/details/<?= $message->id ?>" class="btn-view">
                                        View Details <i class="fas fa-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </div>

    <script src="/unipulse/public/assets/js/Moderator/header.js"></script>
</body>

</html>
