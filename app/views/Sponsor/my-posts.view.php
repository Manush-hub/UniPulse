<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Sponsor Posts - UniPulse</title>
    <link rel="stylesheet" href="/unipulse/public/assets/css/sponsor/my-posts-style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <!-- Header -->
    <?php
    $pageConfig = ['activeNav' => 'events'];
    include __DIR__ . '/components/header.php';
    ?>

    <div class="container my-posts-container">
        <!-- Page Header -->
        <header class="page-header">
            <div class="header-content">
                <h1><i class="fas fa-list"></i> My Sponsor Posts</h1>
                <p>Manage your promotional posts on event pages</p>
            </div>
            <div class="header-actions">
                <a href="/unipulse/public/sponsor/events?view=sponsor" class="btn btn-primary">
                    <i class="fas fa-plus"></i> New Post
                </a>
            </div>
        </header>

        <!-- Filter Tabs -->
        <div class="filter-tabs">
            <a href="?status=" class="tab <?= empty($status) ? 'active' : '' ?>">
                All Posts
            </a>
            <a href="?status=pending" class="tab <?= $status === 'pending' ? 'active' : '' ?>">
                <i class="fas fa-clock"></i> Pending
            </a>
            <a href="?status=approved" class="tab <?= $status === 'approved' ? 'active' : '' ?>">
                <i class="fas fa-check"></i> Approved
            </a>
            <a href="?status=rejected" class="tab <?= $status === 'rejected' ? 'active' : '' ?>">
                <i class="fas fa-times"></i> Rejected
            </a>
        </div>

        <!-- Posts List -->
        <?php if (empty($posts)): ?>
            <div class="no-posts">
                <div class="no-posts-icon">
                    <i class="fas fa-inbox"></i>
                </div>
                <h3>No Posts Found</h3>
                <p>You haven't created any sponsor posts yet.</p>
                <a href="/unipulse/public/sponsor/events?view=sponsor" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Create Your First Post
                </a>
            </div>
        <?php else: ?>
            <div class="posts-list">
                <?php foreach ($posts as $post): ?>
                    <div class="post-item" data-post-id="<?= $post->id ?>">
                        <div class="post-header">
                            <div class="post-meta">
                                <h3><?= htmlspecialchars($post->title) ?></h3>
                                <p class="event-name">Event: <strong><?= htmlspecialchars($post->event_title) ?></strong></p>
                                <div class="post-info">
                                    <span class="date">
                                        <i class="fas fa-calendar"></i>
                                        <?= date('M j, Y', strtotime($post->created_at)) ?>
                                    </span>
                                    <span class="views">
                                        <i class="fas fa-eye"></i>
                                        <?= $post->views_count ?> views
                                    </span>
                                    <span class="clicks">
                                        <i class="fas fa-mouse"></i>
                                        <?= $post->clicks_count ?> clicks
                                    </span>
                                </div>
                            </div>
                            
                            <div class="post-status">
                                <span class="badge badge-<?= $post->approval_status ?>">
                                    <?php
                                    $statusMap = [
                                        'pending' => '<i class="fas fa-clock"></i> Pending Review',
                                        'approved' => '<i class="fas fa-check-circle"></i> Approved',
                                        'rejected' => '<i class="fas fa-times-circle"></i> Rejected'
                                    ];
                                    echo $statusMap[$post->approval_status] ?? $post->approval_status;
                                    ?>
                                </span>
                            </div>
                        </div>

                        <?php if ($post->approval_status === 'rejected' && $post->rejection_reason): ?>
                            <div class="rejection-notice">
                                <strong>Rejection Reason:</strong>
                                <p><?= htmlspecialchars($post->rejection_reason) ?></p>
                            </div>
                        <?php endif; ?>

                        <div class="post-preview">
                            <p><?= htmlspecialchars(substr($post->content, 0, 200)) ?><?= strlen($post->content) > 200 ? '...' : '' ?></p>
                        </div>

                        <div class="post-actions">
                            <a href="/unipulse/public/sponsor/eventview?id=<?= $post->event_id ?>" class="btn btn-outline btn-sm" title="View event">
                                <i class="fas fa-eye"></i> View Event
                            </a>
                            
                            <?php if ($post->approval_status === 'pending'): ?>
                                <a href="/unipulse/public/sponsor/events/editPost/<?= $post->id ?>" class="btn btn-outline btn-sm" title="Edit post">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <button class="btn btn-outline btn-sm btn-delete" onclick="deletePost(<?= $post->id ?>)" title="Delete post">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            <?php elseif ($post->approval_status === 'approved'): ?>
                                <div class="approved-info">
                                    <i class="fas fa-check-circle"></i> Live on event page
                                </div>
                            <?php elseif ($post->approval_status === 'rejected'): ?>
                                <button class="btn btn-primary btn-sm" onclick="submitAgain(<?= $post->event_id ?>)" title="Create new post">
                                    <i class="fas fa-redo"></i> Try Again
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <?php include __DIR__ . '/../components/footer.php'; ?>

    <script>
        function deletePost(postId) {
            if (!confirm('Are you sure you want to delete this post?')) return;

            fetch(`/unipulse/public/sponsor/events/deletePost/${postId}`, {
                method: 'POST'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.querySelector(`[data-post-id="${postId}"]`).remove();
                    alert('Post deleted successfully');
                } else {
                    alert('Failed to delete post: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while deleting the post');
            });
        }

        function submitAgain(eventId) {
            window.location.href = `/unipulse/public/sponsor/events/createPost/${eventId}`;
        }
    </script>
</body>
</html>
