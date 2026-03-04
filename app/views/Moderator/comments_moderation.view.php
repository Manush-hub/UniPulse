<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UniPulse – Comments Moderation</title>
    <link rel="stylesheet" href="/unipulse/public/assets/css/Components/header-style.css">
    <link rel="stylesheet" href="/unipulse/public/assets/css/Moderator/comments-moderation-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <!-- Header -->
    <?php
    $pageConfig  = ['activeNav' => 'comments'];
    $headerCssLoaded = true;
    include __DIR__ . '/components/header.php';
    ?>

    <div class="main-container">

        <!-- Page Header -->
        <div class="page-header">
            <div class="container">
                <h1><i class="fas fa-comments"></i> Comments Moderation</h1>
                <p>Select a publisher, then an event to review its comments.</p>
            </div>
        </div>

        <!-- Stats Bar -->
        <div class="stats-bar">
            <div class="container">
                <div class="stat-card total">
                    <div class="stat-icon"><i class="fas fa-comments"></i></div>
                    <div class="stat-info">
                        <div class="stat-number" id="totalComments">–</div>
                        <div class="stat-label">Total Comments</div>
                    </div>
                </div>
                <div class="stat-card visible">
                    <div class="stat-icon"><i class="fas fa-eye"></i></div>
                    <div class="stat-info">
                        <div class="stat-number" id="visibleComments">–</div>
                        <div class="stat-label">Visible</div>
                    </div>
                </div>
                <div class="stat-card hidden-card">
                    <div class="stat-icon"><i class="fas fa-eye-slash"></i></div>
                    <div class="stat-info">
                        <div class="stat-number" id="hiddenComments">–</div>
                        <div class="stat-label">Hidden</div>
                    </div>
                </div>
                <div class="stat-card today">
                    <div class="stat-icon"><i class="fas fa-calendar-day"></i></div>
                    <div class="stat-info">
                        <div class="stat-number" id="moderatedToday">–</div>
                        <div class="stat-label">Moderated Today</div>
                    </div>
                </div>
            </div>
        </div>

        <?php if (isset($error)): ?>
        <div class="global-error-banner">
            <i class="fas fa-exclamation-circle"></i>
            <?php echo htmlspecialchars($error); ?>
        </div>
        <?php endif; ?>

        <!-- 3-Panel Chat Layout -->
        <div class="chat-layout">

            <!-- Panel 1 · Publishers -->
            <div class="panel panel-publishers" id="panelPublishers">
                <div class="panel-header">
                    <div class="panel-title">
                        <i class="fas fa-building"></i>
                        <span>Publishers</span>
                    </div>
                    <span class="panel-pill" id="publisherCount">–</span>
                </div>
                <div class="panel-search">
                    <i class="fas fa-search panel-search-icon"></i>
                    <input type="text" id="publisherSearch" placeholder="Search publishers…"
                           oninput="filterPublisherList(this.value)">
                </div>
                <div class="panel-list" id="publisherList">
                    <div class="panel-loading">
                        <i class="fas fa-spinner fa-spin"></i>
                        <p>Loading…</p>
                    </div>
                </div>
            </div>

            <!-- Panel 2 · Events -->
            <div class="panel panel-events" id="panelEvents">
                <div class="panel-header">
                    <div class="panel-title">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Events</span>
                    </div>
                    <span class="panel-pill" id="eventCount">–</span>
                </div>
                <div class="panel-context-bar" id="eventsContextBar">
                    <span id="selectedPublisherLabel">No publisher selected</span>
                </div>
                <div class="panel-search">
                    <i class="fas fa-search panel-search-icon"></i>
                    <input type="text" id="eventSearch" placeholder="Search events…"
                           oninput="filterEventList(this.value)">
                </div>
                <div class="panel-list" id="eventList">
                    <div class="panel-placeholder">
                        <i class="fas fa-arrow-left"></i>
                        <p>Pick a publisher first</p>
                    </div>
                </div>
            </div>

            <!-- Panel 3 · Comments -->
            <div class="panel panel-comments" id="panelComments">
                <div class="panel-header comments-panel-header">
                    <div class="panel-header-left">
                        <div class="panel-title" id="commentsEventTitle">
                            <i class="fas fa-comment-dots"></i>
                            <span>Comments</span>
                        </div>
                        <div class="panel-context-bar panel-context-bar--inline" id="commentsContextBar">
                            <span id="commentsContextLabel">No event selected</span>
                        </div>
                    </div>
                    <div class="comments-toolbar">
                        <span class="panel-pill panel-pill--blue"><span id="commentsCount">–</span> comments</span>
                        <select id="statusFilter" class="toolbar-select">
                            <option value="">All statuses</option>
                            <option value="visible">Visible</option>
                            <option value="hidden">Hidden</option>
                        </select>
                        <div class="toolbar-search">
                            <i class="fas fa-search"></i>
                            <input type="text" id="searchInput" placeholder="Search…"
                                   oninput="renderComments()">
                        </div>
                    </div>
                </div>
                <div class="panel-list comments-list" id="commentsList">
                    <div class="panel-placeholder">
                        <i class="fas fa-calendar-alt"></i>
                        <p>Select an event to view comments</p>
                    </div>
                </div>
            </div>

        </div><!-- /.chat-layout -->

    </div><!-- /.main-container -->

    <!-- Footer -->
    <?php include __DIR__ . '/../components/footer.php'; ?>

    <!-- Hide Comment Modal -->
    <div id="hideModal" class="modal-overlay">
        <div class="modal-box">
            <div class="modal-box-header">
                <h3><i class="fas fa-eye-slash" style="color:#f97316;margin-right:.5rem;"></i>Hide Comment</h3>
                <button class="modal-close-btn" onclick="closeHideModal()">&times;</button>
            </div>
            <div class="modal-box-body">
                <p>Provide a reason for hiding this comment. The user will be notified.</p>
                <div class="form-group">
                    <label for="hideReason">Reason <span style="color:red;">*</span></label>
                    <textarea id="hideReason" class="form-textarea"
                              placeholder="Explain why you are hiding this comment (min. 10 characters)…"></textarea>
                    <div id="hideError" class="form-error"></div>
                </div>
            </div>
            <div class="modal-box-footer">
                <button class="btn btn-secondary" onclick="closeHideModal()">Cancel</button>
                <button id="hideSubmitBtn" class="btn btn-warning" onclick="confirmHideComment()">
                    <i class="fas fa-eye-slash"></i> Hide Comment
                </button>
            </div>
        </div>
    </div>

    <script src="/unipulse/public/assets/js/Moderator/comments-moderation.js"></script>
</body>

</html>
