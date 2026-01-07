<?php

class AdminSponsorshipProposals extends Controller {
    
    private $sponsorshipProposalModel;
    private $messageModel;
    
    public function __construct() {
        parent::__construct();
        $this->sponsorshipProposalModel = new SponsorshipProposal();
        $this->messageModel = new Message();
    }
    
    /**
     * View pending proposals for admin review
     */
    public function index($a = '', $b = '', $c = '') {
        // Require admin authentication
        $currentUser = AuthService::getCurrentUser();
        if (!$currentUser || $currentUser['type'] !== 'admin') {
            header('Location: /unipulse/public/signin');
            exit();
        }
        
        $status = $_GET['status'] ?? 'submitted';
        
        if ($status === 'submitted') {
            $query = "SELECT sp.*, s.company_name, e.title as event_title
                      FROM sponsorship_proposals sp
                      LEFT JOIN sponsors s ON sp.sponsor_id = s.id
                      LEFT JOIN events e ON sp.event_id = e.id
                      WHERE sp.status IN ('submitted', 'under_review')
                      AND sp.deleted_at IS NULL
                      ORDER BY sp.created_at ASC";
            $proposals = $this->sponsorshipProposalModel->query($query, []);
        } else {
            // Get proposals by other statuses (accepted, rejected, negotiating)
            $proposals = $this->sponsorshipProposalModel->getProposalsByEvent(null, $status);
            
            // Get all proposals by status from all events
            $query = "SELECT sp.*, s.company_name, e.title as event_title
                      FROM sponsorship_proposals sp
                      LEFT JOIN sponsors s ON sp.sponsor_id = s.id
                      LEFT JOIN events e ON sp.event_id = e.id
                      WHERE sp.status = :status
                      AND sp.deleted_at IS NULL
                      ORDER BY sp.created_at DESC";
            $proposals = $this->sponsorshipProposalModel->query($query, ['status' => $status]);
        }
        
        $data = [
            'user' => $currentUser,
            'proposals' => $proposals,
            'status' => $status,
            'page_title' => ucfirst($status) . ' Sponsorship Proposals'
        ];
        
        $this->view('Admin/sponsorship-proposals', $data);
    }
    
    /**
     * View proposal details for review
     */
    public function view($proposalId = '') {
        // Require admin authentication
        $currentUser = AuthService::getCurrentUser();
        if (!$currentUser || $currentUser['type'] !== 'admin') {
            header('Location: /unipulse/public/signin');
            exit();
        }
        
        if (!$proposalId || !is_numeric($proposalId)) {
            header('Location: /unipulse/public/admin/sponsorshipproposals');
            exit();
        }
        
        $proposal = $this->sponsorshipProposalModel->getProposalById($proposalId);
        
        if (!$proposal) {
            header('Location: /unipulse/public/admin/sponsorshipproposals');
            exit();
        }
        
        // Track view
        $this->sponsorshipProposalModel->trackView($proposalId);
        
        $data = [
            'user' => $currentUser,
            'proposal' => $proposal,
            'page_title' => 'Review Sponsorship Proposal'
        ];
        
        $this->view('Admin/sponsorship-proposal-details', $data);
    }
    
    /**
     * Accept a sponsorship proposal
     */
    public function accept($proposalId = '') {
        header('Content-Type: application/json');
        
        // Require admin authentication
        $currentUser = AuthService::getCurrentUser();
        if (!$currentUser || $currentUser['type'] !== 'admin') {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }
        
        if (!$proposalId || !is_numeric($proposalId)) {
            echo json_encode(['success' => false, 'message' => 'Invalid proposal ID']);
            exit;
        }
        
        // Check proposal exists
        $proposal = $this->sponsorshipProposalModel->getProposalById($proposalId);
        if (!$proposal) {
            echo json_encode(['success' => false, 'message' => 'Proposal not found']);
            exit;
        }
        
        if (!in_array($proposal->status, ['submitted', 'under_review', 'negotiating'])) {
            echo json_encode(['success' => false, 'message' => 'Proposal has already been reviewed']);
            exit;
        }
        
        // Accept the proposal
        $result = $this->sponsorshipProposalModel->acceptProposal($proposalId, $currentUser['id']);
        
        if ($result) {
            // Send notification to sponsor
            $this->notifySponsor($proposal, 'accepted');
            
            echo json_encode([
                'success' => true,
                'message' => 'Proposal accepted successfully'
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to accept proposal']);
        }
        exit;
    }
    
    /**
     * Reject a sponsorship proposal
     */
    public function reject($proposalId = '') {
        header('Content-Type: application/json');
        
        // Require admin authentication
        $currentUser = AuthService::getCurrentUser();
        if (!$currentUser || $currentUser['type'] !== 'admin') {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }
        
        if (!$proposalId || !is_numeric($proposalId)) {
            echo json_encode(['success' => false, 'message' => 'Invalid proposal ID']);
            exit;
        }
        
        // Get rejection reason from POST
        $reason = trim($_POST['reason'] ?? '');
        if (empty($reason)) {
            echo json_encode(['success' => false, 'message' => 'Rejection reason is required']);
            exit;
        }
        
        // Check proposal exists
        $proposal = $this->sponsorshipProposalModel->getProposalById($proposalId);
        if (!$proposal) {
            echo json_encode(['success' => false, 'message' => 'Proposal not found']);
            exit;
        }
        
        if (!in_array($proposal->status, ['submitted', 'under_review', 'negotiating'])) {
            echo json_encode(['success' => false, 'message' => 'Proposal has already been reviewed']);
            exit;
        }
        
        // Reject the proposal
        $result = $this->sponsorshipProposalModel->rejectProposal($proposalId, $currentUser['id'], $reason);
        
        if ($result) {
            // Send notification to sponsor
            $this->notifySponsor($proposal, 'rejected', $reason);
            
            echo json_encode([
                'success' => true,
                'message' => 'Proposal rejected successfully'
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to reject proposal']);
        }
        exit;
    }
    
    /**
     * Mark proposal as under review
     */
    public function markUnderReview($proposalId = '') {
        header('Content-Type: application/json');
        
        // Require admin authentication
        $currentUser = AuthService::getCurrentUser();
        if (!$currentUser || $currentUser['type'] !== 'admin') {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }
        
        if (!$proposalId || !is_numeric($proposalId)) {
            echo json_encode(['success' => false, 'message' => 'Invalid proposal ID']);
            exit;
        }
        
        $query = "UPDATE sponsorship_proposals
                  SET status = 'under_review',
                      updated_at = CURRENT_TIMESTAMP
                  WHERE id = :proposal_id AND status = 'submitted'";
        
        $conn = $this->sponsorshipProposalModel->connect();
        $stm = $conn->prepare($query);
        $result = $stm->execute(['proposal_id' => $proposalId]);
        
        if ($result && $stm->rowCount() > 0) {
            echo json_encode(['success' => true, 'message' => 'Marked as under review']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update status']);
        }
        exit;
    }
    
    /**
     * Mark proposal as negotiating (request changes)
     */
    public function requestChanges($proposalId = '') {
        header('Content-Type: application/json');
        
        // Require admin authentication
        $currentUser = AuthService::getCurrentUser();
        if (!$currentUser || $currentUser['type'] !== 'admin') {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }
        
        if (!$proposalId || !is_numeric($proposalId)) {
            echo json_encode(['success' => false, 'message' => 'Invalid proposal ID']);
            exit;
        }
        
        // Get feedback from POST
        $feedback = trim($_POST['feedback'] ?? '');
        if (empty($feedback)) {
            echo json_encode(['success' => false, 'message' => 'Feedback is required']);
            exit;
        }
        
        // Get proposal
        $proposal = $this->sponsorshipProposalModel->getProposalById($proposalId);
        if (!$proposal) {
            echo json_encode(['success' => false, 'message' => 'Proposal not found']);
            exit;
        }
        
        // Update status to negotiating
        $query = "UPDATE sponsorship_proposals
                  SET status = 'negotiating',
                      rejection_reason = :feedback,
                      updated_at = CURRENT_TIMESTAMP
                  WHERE id = :proposal_id";
        
        $conn = $this->sponsorshipProposalModel->connect();
        $stm = $conn->prepare($query);
        $result = $stm->execute([
            'proposal_id' => $proposalId,
            'feedback' => $feedback
        ]);
        
        if ($result && $stm->rowCount() > 0) {
            // Send notification to sponsor with feedback
            $this->notifySponsor($proposal, 'negotiating', $feedback);
            
            echo json_encode([
                'success' => true,
                'message' => 'Feedback sent to sponsor'
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update proposal']);
        }
        exit;
    }
    
    /**
     * Send notification to sponsor about proposal status
     */
    private function notifySponsor($proposal, $status, $reason = null) {
        $messages = [
            'accepted' => [
                'subject' => 'Your Sponsorship Proposal Has Been Accepted!',
                'body' => "Excellent news! Your sponsorship proposal \"{$proposal->title}\" for the event \"{$proposal->event_title}\" has been accepted. We look forward to partnering with you. Please check your account for next steps."
            ],
            'rejected' => [
                'subject' => 'Sponsorship Proposal Review Update',
                'body' => "Thank you for your sponsorship proposal \"{$proposal->title}\" for the event \"{$proposal->event_title}\".\n\nUnfortunately, we cannot accept this proposal at this time.\n\nReason: {$reason}\n\nWe encourage you to review our sponsorship guidelines and submit a revised proposal if you'd like to try again."
            ],
            'negotiating' => [
                'subject' => 'Feedback on Your Sponsorship Proposal',
                'body' => "Thank you for submitting your sponsorship proposal \"{$proposal->title}\" for the event \"{$proposal->event_title}\".\n\nWe have some feedback and would like to discuss potential modifications:\n\n{$reason}\n\nPlease review the feedback and let us know if you'd like to revise your proposal."
            ]
        ];
        
        $messageData = $messages[$status] ?? null;
        if (!$messageData) {
            return false;
        }
        
        $messagePayload = [
            'from_user_id' => 1, // Admin user ID
            'from_user_type' => 'admin',
            'to_user_id' => $proposal->sponsor_id,
            'to_user_type' => 'sponsor',
            'subject' => $messageData['subject'],
            'message' => $messageData['body']
        ];
        
        return $this->messageModel->sendMessage($messagePayload);
    }
    
    /**
     * Get statistics for sponsorship proposals
     */
    public function stats($a = '', $b = '', $c = '') {
        // Require admin authentication
        $currentUser = AuthService::getCurrentUser();
        if (!$currentUser || $currentUser['type'] !== 'admin') {
            header('Location: /unipulse/public/signin');
            exit();
        }
        
        $query = "SELECT 
                    COUNT(*) as total_proposals,
                    SUM(CASE WHEN status = 'submitted' THEN 1 ELSE 0 END) as submitted_proposals,
                    SUM(CASE WHEN status = 'under_review' THEN 1 ELSE 0 END) as under_review_proposals,
                    SUM(CASE WHEN status = 'negotiating' THEN 1 ELSE 0 END) as negotiating_proposals,
                    SUM(CASE WHEN status = 'accepted' THEN 1 ELSE 0 END) as accepted_proposals,
                    SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected_proposals,
                    SUM(CASE WHEN status = 'accepted' AND proposal_type = 'monetary' THEN monetary_amount ELSE 0 END) as total_monetary_value,
                    AVG(views_count) as avg_views,
                    COUNT(DISTINCT sponsor_id) as total_sponsors,
                    COUNT(DISTINCT event_id) as total_events
                  FROM sponsorship_proposals
                  WHERE deleted_at IS NULL";
        
        $stats = $this->sponsorshipProposalModel->getRow($query, []);
        
        // Get top sponsors
        $topSponsorsQuery = "SELECT sponsor_id, s.company_name, COUNT(*) as proposal_count, 
                            SUM(CASE WHEN status = 'accepted' THEN 1 ELSE 0 END) as accepted_count
                            FROM sponsorship_proposals sp
                            LEFT JOIN sponsors s ON sp.sponsor_id = s.id
                            WHERE sp.deleted_at IS NULL
                            GROUP BY sponsor_id, s.company_name
                            ORDER BY accepted_count DESC
                            LIMIT 10";
        $topSponsors = $this->sponsorshipProposalModel->query($topSponsorsQuery, []);
        
        // Get proposals by type
        $typeQuery = "SELECT proposal_type, COUNT(*) as count,
                      SUM(CASE WHEN status = 'accepted' THEN 1 ELSE 0 END) as accepted_count
                      FROM sponsorship_proposals
                      WHERE deleted_at IS NULL
                      GROUP BY proposal_type";
        $proposalsByType = $this->sponsorshipProposalModel->query($typeQuery, []);
        
        $data = [
            'user' => $currentUser,
            'stats' => $stats,
            'topSponsors' => $topSponsors,
            'proposalsByType' => $proposalsByType,
            'page_title' => 'Sponsorship Proposals Statistics'
        ];
        
        $this->view('Admin/sponsorship-proposals-stats', $data);
    }
}
