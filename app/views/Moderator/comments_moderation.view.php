<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UniPulse – Comments Moderation</title>
    <link rel="stylesheet" href="/unipulse/public/assets/css/Components/header-style.css">
    <link rel="stylesheet" href="/unipulse/public/assets/css/Moderator/comments-moderation-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Status badges */
        .status-badge { display:inline-flex; align-items:center; gap:.3rem; font-size:.75rem; font-weight:600; padding:.15rem .55rem; border-radius:20px; vertical-align:middle; }
        .visible-badge { background:#dcfce7; color:#15803d; }
        .hidden-badge  { background:#fef3c7; color:#b45309; }
    </style>
</head>

<body>
    <!-- Header -->
    <?php
    $pageConfig  = ['activeNav' => 'comments'];
    $headerCssLoaded = true;
    include __DIR__ . '/components/header.php';
    ?>

    <!-- Main Container -->
    <div class="main-container">

        <!-- Page Header -->
        <div class="page-header">
            <div class="container">
                <h1><i class="fas fa-comments" style="margin-right:.5rem;"></i>Comments Moderation</h1>
                <p>Review, hide, and restore user comments on events in your university.</p>
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
                <div class="stat-card hidden">
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

        <!-- Filter Section -->
        <div class="filter-section">
            <div class="container">
                <div class="filter-controls">
                    <div class="search-box">
                        <input type="text" id="searchInput" class="search-input"
                               placeholder="Search comments, users, events…">
                    </div>
                    <div class="filter-group">
                        <select id="statusFilter" class="filter-select">
                            <option value="">All Statuses</option>
                            <option value="visible">Visible</option>
                            <option value="hidden">Hidden</option>
                        </select>
                        <select id="eventFilter" class="filter-select">
                            <option value="">All Events</option>
                            <!-- populated by JS -->
                        </select>
                        <select id="dateFilter" class="filter-select">
                            <option value="">All Time</option>
                            <option value="today">Today</option>
                            <option value="week">Last 7 Days</option>
                            <option value="month">Last 30 Days</option>
                        </select>
                        <button class="filter-btn" onclick="clearFilters()">
                            <i class="fas fa-times"></i> Clear
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Comments List -->
        <div class="content-section">
            <div class="container">

                <?php if (isset($error)): ?>
                <div class="error-banner" style="margin-bottom:1.5rem;">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
                <?php endif; ?>

                <div class="section-header">
                    <h2>University Comments</h2>
                    <span class="comments-count-badge">
                        <span id="commentsCount">–</span> comments
                    </span>
                </div>

                <div id="commentsList">
                    <div class="loading-spinner">
                        <i class="fas fa-spinner fa-spin"></i>
                        <p>Loading comments…</p>
                    </div>
                </div>

            </div>
        </div>

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
