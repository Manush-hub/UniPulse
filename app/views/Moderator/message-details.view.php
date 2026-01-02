<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UniPulse - Message Details</title>
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
                <div class="page-header-content">
                    <div>
                        <h1>Message Details</h1>
                        <p>View message sent to publisher</p>
                    </div>
                    <a href="/unipulse/public/moderator/messages" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Messages
                    </a>
                </div>
            </div>
        </div>

        <!-- Message Details Section -->
        <section class="message-details-section">
            <div class="container">
                <div class="message-details-card">
                    <!-- Message Status -->
                    <div class="message-status-bar">
                        <?php if ($message->is_read): ?>
                            <span class="badge badge-read">
                                <i class="fas fa-check-double"></i> Read by publisher
                            </span>
                            <?php if ($message->read_at): ?>
                                <span class="read-time">
                                    Read on <?= date('M j, Y \a\t g:i A', strtotime($message->read_at)) ?>
                                </span>
                            <?php endif; ?>
                        <?php else: ?>
                            <span class="badge badge-unread">
                                <i class="fas fa-envelope"></i> Not yet read
                            </span>
                        <?php endif; ?>
                    </div>

                    <!-- Message Meta -->
                    <div class="message-meta">
                        <div class="meta-row">
                            <div class="meta-label">
                                <i class="fas fa-building"></i> To:
                            </div>
                            <div class="meta-value">
                                <?= htmlspecialchars($message->recipient_name ?? 'Publisher') ?>
                                <?php if (isset($message->recipient_email)): ?>
                                    <span class="email">(<?= htmlspecialchars($message->recipient_email) ?>)</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="meta-row">
                            <div class="meta-label">
                                <i class="fas fa-user"></i> From:
                            </div>
                            <div class="meta-value">
                                <?= htmlspecialchars($moderator->full_name ?? 'Moderator') ?>
                            </div>
                        </div>
                        <div class="meta-row">
                            <div class="meta-label">
                                <i class="fas fa-clock"></i> Sent:
                            </div>
                            <div class="meta-value">
                                <?= date('l, F j, Y \a\t g:i A', strtotime($message->created_at)) ?>
                            </div>
                        </div>
                    </div>

                    <!-- Message Content -->
                    <div class="message-content">
                        <div class="message-subject-header">
                            <h2><?= htmlspecialchars($message->subject) ?></h2>
                        </div>
                        <div class="message-body-content">
                            <?= nl2br(htmlspecialchars($message->message)) ?>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="message-actions">
                    <a href="/unipulse/public/moderator/messages/compose/<?= $message->to_user_id ?>" class="btn btn-primary">
                        <i class="fas fa-reply"></i> Send Another Message
                    </a>
                </div>
            </div>
        </section>
    </div>

    <script src="/unipulse/public/assets/js/Moderator/header.js"></script>
</body>

</html>
