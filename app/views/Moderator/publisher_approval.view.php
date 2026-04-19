<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UniPulse - Publisher Approvals</title>
    <link rel="stylesheet" href="/unipulse/public/assets/css/Moderator/publisher-approval-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <!-- Header -->
    <header class="header">
        <div class="header-container">
            <div class="logo">
                <a href="/unipulse/public/moderator/dashboard">
                    <img src="/unipulse/public/assets/images/logo.png" alt="UniPulse Logo" class="logo-image">
                </a>
            </div>
            <nav class="nav">
                <a href="/unipulse/public/moderator/dashboard">Dashboard</a>
                <a href="/unipulse/public/moderator/publisherapproval" class="active">Publisher Approvals</a>
                <a href="/unipulse/public/moderator/events">Events</a>
                <a href="/unipulse/public/moderator/reports">Reports</a>
            </nav>
            <div class="header-actions">
                <div class="user-menu">
                    <img src="/unipulse/public/assets/images/moderator.png" alt="Moderator" class="moderator-avatar">
                    <div class="user-info">
                        <span class="username"><?= htmlspecialchars($moderator->full_name ?? 'Moderator') ?></span>
                        <span class="user-role">Moderator</span>
                    </div>
                    <div class="user-dropdown" id="userDropdown">
                        <a href="/unipulse/public/moderator/profile"><i class="fas fa-user-cog"></i> Profile</a>
                        <a href="/unipulse/public/logout" class="logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Container -->
    <div class="main-container">
        <!-- Page Header -->
        <section class="page-header">
            <div class="container">
                <div class="header-content">
                    <h1><i class="fas fa-user-check"></i> Publisher Approvals</h1>
                    <p>Review and approve publisher registrations for <?= htmlspecialchars($moderator->university_name ?? $moderator->university) ?></p>
                </div>
                <div class="header-stats">
                    <div class="stat-card">
                        <div class="stat-number"><?= count($pending_publishers) ?></div>
                        <div class="stat-label">Pending Approvals</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Publishers List -->
        <section class="publishers-section">
            <div class="container">
                <?php if (empty($pending_publishers)): ?>
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <h3>All Caught Up!</h3>
                        <p>There are no pending publisher registrations for your university at the moment.</p>
                        <a href="/unipulse/public/moderator/dashboard" class="btn btn-primary">
                            <i class="fas fa-arrow-left"></i> Back to Dashboard
                        </a>
                    </div>
                <?php else: ?>
                    <div class="publishers-grid">
                        <?php foreach ($pending_publishers as $publisher): ?>
                            <div class="publisher-card" data-publisher-id="<?= $publisher->id ?>">
                                <div class="publisher-header">
                                    <div class="publisher-info">
                                        <h3><?= htmlspecialchars($publisher->society_name) ?></h3>
                                        <p class="university-info">
                                            <i class="fas fa-university"></i>
                                            <?= htmlspecialchars($publisher->faculty) ?>
                                        </p>
                                    </div>
                                    <div class="publisher-status">
                                        <span class="status-badge pending">
                                            <i class="fas fa-clock"></i> Pending
                                        </span>
                                    </div>
                                </div>
                                
                                <div class="publisher-details">
                                    <div class="detail-row">
                                        <strong>Email:</strong>
                                        <span><?= htmlspecialchars($publisher->email) ?></span>
                                    </div>
                                    <div class="detail-row">
                                        <strong>Phone:</strong>
                                        <span><?= htmlspecialchars($publisher->country_code . ' ' . $publisher->phone) ?></span>
                                    </div>
                                    <div class="detail-row">
                                        <strong>Registration Date:</strong>
                                        <span><?= date('M j, Y \a\t g:i A', strtotime($publisher->created_at)) ?></span>
                                    </div>
                                    <?php if ($publisher->confirmation_document): ?>
                                        <div class="detail-row">
                                            <strong>Verification Document:</strong>
                                            <a href="/unipulse/public/<?= htmlspecialchars($publisher->confirmation_document) ?>" 
                                               target="_blank" class="document-link">
                                                <i class="fas fa-file-alt"></i> View Document
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="publisher-actions">
                                    <button class="btn btn-success btn-approve" data-publisher-id="<?= $publisher->id ?>">
                                        <i class="fas fa-check"></i> Approve
                                    </button>
                                    <button class="btn btn-danger btn-reject" data-publisher-id="<?= $publisher->id ?>">
                                        <i class="fas fa-times"></i> Reject
                                    </button>
                                    <button class="btn btn-outline btn-view" data-publisher-id="<?= $publisher->id ?>">
                                        <i class="fas fa-eye"></i> View Details
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </div>

    <!-- Rejection Modal -->
    <div id="rejectionModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Reject Publisher Registration</h3>
                <span class="close-button" onclick="closeModal('rejectionModal')">&times;</span>
            </div>
            <div class="modal-body">
                <p>Please provide a reason for rejecting this publisher registration:</p>
                <textarea id="rejectionReason" rows="4" placeholder="Enter rejection reason..."></textarea>
                <div class="modal-actions">
                    <button class="btn btn-secondary" onclick="closeModal('rejectionModal')">Cancel</button>
                    <button class="btn btn-danger" onclick="confirmRejection()">
                        <i class="fas fa-times"></i> Reject Publisher
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div id="confirmationModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="confirmationTitle">Confirm Action</h3>
                <span class="close-button" onclick="closeModal('confirmationModal')">&times;</span>
            </div>
            <div class="modal-body">
                <p id="confirmationMessage">Are you sure you want to perform this action?</p>
                <div class="modal-actions">
                    <button class="btn btn-secondary" onclick="closeModal('confirmationModal')">Cancel</button>
                    <button class="btn btn-primary" id="confirmationButton" onclick="performAction()">
                        Confirm
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="/unipulse/public/assets/js/Moderator/publisher-approval-app.js"></script>

    <link rel="stylesheet" href="/unipulse/public/assets/css/Moderator/publisher-approval-style.css">
</body>

</html>