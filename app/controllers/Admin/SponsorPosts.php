<?php

class AdminSponsorPosts extends Controller {
    
    private $sponsorPostModel;
    
    public function __construct() {
        parent::__construct();
        $this->sponsorPostModel = new SponsorPost();
    }
    
    /**
     * View pending sponsor posts for moderation
     */
    public function index($a = '', $b = '', $c = '') {
        // Require admin authentication
        $currentUser = AuthService::getCurrentUser();
        if (!$currentUser || $currentUser['type'] !== 'admin') {
            header('Location: /unipulse/public/signin');
            exit();
        }
        
        $status = $_GET['status'] ?? 'pending';
        
        if ($status === 'pending') {
            $posts = $this->sponsorPostModel->getPendingPosts();
        } else {
            // Get all posts by status (approved, rejected)
            $query = "SELECT sp.*, s.company_name, e.title as event_title
                      FROM sponsor_posts sp
                      LEFT JOIN sponsors s ON sp.sponsor_id = s.id
                      LEFT JOIN events e ON sp.event_id = e.id
                      WHERE sp.approval_status = :status
                      ORDER BY sp.created_at DESC";
            
            $posts = $this->sponsorPostModel->query($query, ['status' => $status]);
        }
        
        $data = [
            'user' => $currentUser,
            'posts' => $posts,
            'status' => $status,
            'page_title' => ucfirst($status) . ' Sponsor Posts'
        ];
        
        $this->view('Admin/sponsor-posts', $data);
    }
    
    /**
     * View single post details
     */
    public function view($postId = '') {
        // Require admin authentication
        $currentUser = AuthService::getCurrentUser();
        if (!$currentUser || $currentUser['type'] !== 'admin') {
            header('Location: /unipulse/public/signin');
            exit();
        }
        
        if (!$postId || !is_numeric($postId)) {
            header('Location: /unipulse/public/admin/sponsorposts');
            exit();
        }
        
        $post = $this->sponsorPostModel->getPostById($postId);
        
        if (!$post) {
            header('Location: /unipulse/public/admin/sponsorposts');
            exit();
        }
        
        $data = [
            'user' => $currentUser,
            'post' => $post,
            'page_title' => 'Review Sponsor Post'
        ];
        
        $this->view('Admin/sponsor-post-details', $data);
    }
    
    /**
     * Approve a sponsor post
     */
    public function approve($postId = '') {
        header('Content-Type: application/json');
        
        // Require admin authentication
        $currentUser = AuthService::getCurrentUser();
        if (!$currentUser || $currentUser['type'] !== 'admin') {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }
        
        if (!$postId || !is_numeric($postId)) {
            echo json_encode(['success' => false, 'message' => 'Invalid post ID']);
            exit;
        }
        
        // Check post exists
        $post = $this->sponsorPostModel->getPostById($postId);
        if (!$post) {
            echo json_encode(['success' => false, 'message' => 'Post not found']);
            exit;
        }
        
        if ($post->approval_status !== 'pending') {
            echo json_encode(['success' => false, 'message' => 'Post has already been reviewed']);
            exit;
        }
        
        // Approve the post
        $result = $this->sponsorPostModel->approvePost($postId, $currentUser['id']);
        
        if ($result) {
            // Send notification to sponsor
            $this->notifySponsor($post, 'approved');
            
            echo json_encode([
                'success' => true,
                'message' => 'Post approved successfully'
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to approve post']);
        }
        exit;
    }
    
    /**
     * Reject a sponsor post
     */
    public function reject($postId = '') {
        header('Content-Type: application/json');
        
        // Require admin authentication
        $currentUser = AuthService::getCurrentUser();
        if (!$currentUser || $currentUser['type'] !== 'admin') {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }
        
        if (!$postId || !is_numeric($postId)) {
            echo json_encode(['success' => false, 'message' => 'Invalid post ID']);
            exit;
        }
        
        // Get rejection reason from POST
        $reason = trim($_POST['reason'] ?? '');
        if (empty($reason)) {
            echo json_encode(['success' => false, 'message' => 'Rejection reason is required']);
            exit;
        }
        
        // Check post exists
        $post = $this->sponsorPostModel->getPostById($postId);
        if (!$post) {
            echo json_encode(['success' => false, 'message' => 'Post not found']);
            exit;
        }
        
        if ($post->approval_status !== 'pending') {
            echo json_encode(['success' => false, 'message' => 'Post has already been reviewed']);
            exit;
        }
        
        // Reject the post
        $result = $this->sponsorPostModel->rejectPost($postId, $currentUser['id'], $reason);
        
        if ($result) {
            // Send notification to sponsor
            $this->notifySponsor($post, 'rejected', $reason);
            
            echo json_encode([
                'success' => true,
                'message' => 'Post rejected successfully'
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to reject post']);
        }
        exit;
    }
    
    /**
     * Send notification to sponsor about post status
     */
    private function notifySponsor($post, $status, $reason = null) {
        $message = new Message();
        
        $subject = $status === 'approved' 
            ? "Your Sponsor Post Has Been Approved!"
            : "Your Sponsor Post Was Not Approved";
        
        $content = $status === 'approved'
            ? "Great news! Your sponsor post for the event \"{$post->event_title}\" has been approved and is now live on the event page."
            : "Unfortunately, your sponsor post for the event \"{$post->event_title}\" was not approved.\n\nReason: {$reason}\n\nPlease review the guidelines and submit a new post if you'd like to try again.";
        
        $messageData = [
            'from_user_id' => $post->approved_by,
            'from_user_type' => 'admin',
            'to_user_id' => $post->sponsor_id,
            'to_user_type' => 'sponsor',
            'subject' => $subject,
            'message' => $content
        ];
        
        return $message->sendMessage($messageData);
    }
    
    /**
     * Get statistics for sponsor posts
     */
    public function stats($a = '', $b = '', $c = '') {
        // Require admin authentication
        $currentUser = AuthService::getCurrentUser();
        if (!$currentUser || $currentUser['type'] !== 'admin') {
            header('Location: /unipulse/public/signin');
            exit();
        }
        
        $query = "SELECT 
                    COUNT(*) as total_posts,
                    SUM(CASE WHEN approval_status = 'pending' THEN 1 ELSE 0 END) as pending_posts,
                    SUM(CASE WHEN approval_status = 'approved' THEN 1 ELSE 0 END) as approved_posts,
                    SUM(CASE WHEN approval_status = 'rejected' THEN 1 ELSE 0 END) as rejected_posts,
                    SUM(views_count) as total_views,
                    SUM(clicks_count) as total_clicks
                  FROM sponsor_posts
                  WHERE deleted_at IS NULL";
        
        $stats = $this->sponsorPostModel->getRow($query, []);
        
        $data = [
            'user' => $currentUser,
            'stats' => $stats,
            'page_title' => 'Sponsor Posts Statistics'
        ];
        
        $this->view('Admin/sponsor-posts-stats', $data);
    }
}
