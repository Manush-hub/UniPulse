<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UniPulse - Send Message to Publisher</title>
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
                        <h1>Send Message to Publisher</h1>
                        <p>Send a message to a publisher from your university</p>
                    </div>
                    <a href="/unipulse/public/moderator/messages" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Messages
                    </a>
                </div>
            </div>
        </div>

        <!-- Message Form Section -->
        <section class="form-section">
            <div class="container">
                <div class="form-container">
                    <form id="messageForm" method="POST">
                        <!-- Publisher Selection -->
                        <div class="form-group">
                            <label for="publisher_id">
                                <i class="fas fa-building"></i> Select Publisher *
                            </label>
                            <select id="publisher_id" name="publisher_id" required <?= empty($publishers) ? 'disabled' : '' ?>>
                                <option value="">-- Choose a publisher --</option>
                                <?php if (!empty($publishers)): ?>
                                    <?php foreach ($publishers as $publisher): ?>
                                        <option value="<?= $publisher->id ?>" 
                                            <?= (isset($selected_publisher) && $selected_publisher->id == $publisher->id) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($publisher->society_name) ?> 
                                            (<?= htmlspecialchars($publisher->email) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <option value="">No approved publishers available from your university</option>
                                <?php endif; ?>
                            </select>
                            <small class="form-hint">
                                <?php if (!empty($publishers)): ?>
                                    Select the publisher from <?= htmlspecialchars($moderator->university_name ?? $moderator->university) ?> you want to send a message to
                                <?php else: ?>
                                    Currently no approved publishers available from <?= htmlspecialchars($moderator->university_name ?? $moderator->university) ?>
                                <?php endif; ?>
                            </small>
                        </div>

                        <!-- Subject -->
                        <div class="form-group">
                            <label for="subject">
                                <i class="fas fa-tag"></i> Subject *
                            </label>
                            <input 
                                type="text" 
                                id="subject" 
                                name="subject" 
                                placeholder="Enter message subject"
                                maxlength="200"
                                required
                                <?= empty($publishers) ? 'disabled' : '' ?>
                            >
                            <div class="char-counter">
                                <span id="subjectCounter">0</span> / 200 characters
                            </div>
                        </div>

                        <!-- Message -->
                        <div class="form-group">
                            <label for="message">
                                <i class="fas fa-comment-alt"></i> Message *
                            </label>
                            <textarea 
                                id="message" 
                                name="message" 
                                rows="10"
                                placeholder="Type your message here..."
                                maxlength="2000"
                                required
                                <?= empty($publishers) ? 'disabled' : '' ?>
                            ></textarea>
                            <div class="char-counter">
                                <span id="messageCounter">0</span> / 2000 characters
                            </div>
                        </div>

                        <!-- Preview Section -->
                        <div class="message-preview-section" id="previewSection" style="display: none;">
                            <h3><i class="fas fa-eye"></i> Message Preview</h3>
                            <div class="preview-card">
                                <div class="preview-header">
                                    <strong>Subject:</strong> <span id="previewSubject">-</span>
                                </div>
                                <div class="preview-body" id="previewMessage">
                                    -
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="form-actions">
                            <button type="button" class="btn btn-secondary" onclick="togglePreview()" <?= empty($publishers) ? 'disabled' : '' ?>>
                                <i class="fas fa-eye"></i> Preview
                            </button>
                            <button type="submit" class="btn btn-primary" id="submitBtn" <?= empty($publishers) ? 'disabled' : '' ?>>
                                <i class="fas fa-paper-plane"></i> Send Message
                            </button>
                        </div>

                        <!-- Error/Success Messages -->
                        <div id="messageAlert" class="alert" style="display: none;"></div>
                    </form>
                </div>

                <!-- Info Box -->
                <div class="info-box">
                    <h3><i class="fas fa-info-circle"></i> Information</h3>
                    <ul>
                        <li><strong>University Restriction:</strong> You can only send messages to approved publishers from <?= htmlspecialchars($moderator->university_name ?? $moderator->university) ?></li>
                        <li>Publishers will receive your message and can view it in their dashboard</li>
                        <li>Subject line is limited to 200 characters</li>
                        <li>Message body is limited to 2000 characters</li>
                        <li>Once sent, messages cannot be edited</li>
                        <?php if (empty($publishers)): ?>
                        <li class="no-publishers-warning"><i class="fas fa-exclamation-triangle"></i> <strong>Note:</strong> There are currently no approved publishers from your university available to message.</li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </section>
    </div>

    <script src="/unipulse/public/assets/js/Moderator/messages-app.js"></script>
</body>

</html>
