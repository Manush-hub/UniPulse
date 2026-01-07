<?php

class SponsorSponsorships extends Controller {
    
    private $sponsorshipModel;
    private $eventModel;
    
    public function __construct() {
        parent::__construct();
        $this->sponsorshipModel = new Sponsorship();
        $this->eventModel = new Event();
    }
    
    // View all my sponsorships
    public function index() {
        $currentUser = AuthService::getCurrentUser();
        if (!$currentUser || $currentUser['type'] !== 'sponsor') {
            header('Location: /unipulse/public/signin');
            exit();
        }
        
        $sponsorships = $this->sponsorshipModel->getBySponsorId($currentUser['id']);
        
        $data = [
            'user' => $currentUser,
            'sponsorships' => $sponsorships,
            'page_title' => 'My Sponsorships'
        ];
        
        $this->view('Sponsor/sponsorships', $data);
    }
    
    // Create sponsorship proposal
    public function propose($eventId = null) {
        $currentUser = AuthService::getCurrentUser();
        if (!$currentUser || $currentUser['type'] !== 'sponsor') {
            echo json_encode(['success' => false, 'error' => 'Unauthorized']);
            exit();
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $proposalData = [
                'event_id' => $_POST['event_id'],
                'sponsor_id' => $currentUser['id'],
                'amount' => $_POST['amount'],
                'benefits_requested' => $_POST['benefits'],
                'conditions' => $_POST['conditions'],
                'status' => 'pending'
            ];
            
            if ($this->sponsorshipModel->createProposal($proposalData)) {
                echo json_encode(['success' => true, 'message' => 'Proposal submitted successfully']);
            } else {
                echo json_encode(['success' => false, 'error' => 'Failed to submit proposal']);
            }
            exit();
        }
    }
    
    // View sponsorship details
    public function details($sponsorshipId) {
        $currentUser = AuthService::getCurrentUser();
        if (!$currentUser || $currentUser['type'] !== 'sponsor') {
            header('Location: /unipulse/public/signin');
            exit();
        }
        
        $sponsorship = $this->sponsorshipModel->getById($sponsorshipId);
        
        // Verify ownership
        if (!$sponsorship || $sponsorship->sponsor_id != $currentUser['id']) {
            header('Location: /unipulse/public/sponsor/sponsorships');
            exit();
        }
        
        $event = $this->eventModel->getEventById($sponsorship->event_id);
        
        $data = [
            'user' => $currentUser,
            'sponsorship' => $sponsorship,
            'event' => $event,
            'page_title' => 'Sponsorship Details'
        ];
        
        $this->view('Sponsor/sponsorship-details', $data);
    }
    
    // Cancel sponsorship
    public function cancel($sponsorshipId) {
        header('Content-Type: application/json');
        
        $currentUser = AuthService::getCurrentUser();
        if (!$currentUser || $currentUser['type'] !== 'sponsor') {
            echo json_encode(['success' => false, 'error' => 'Unauthorized']);
            exit();
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $sponsorship = $this->sponsorshipModel->getById($sponsorshipId);
            
            if (!$sponsorship || $sponsorship->sponsor_id != $currentUser['id']) {
                echo json_encode(['success' => false, 'error' => 'Sponsorship not found']);
                exit();
            }
            
            if ($this->sponsorshipModel->cancel($sponsorshipId)) {
                echo json_encode(['success' => true, 'message' => 'Sponsorship cancelled']);
            } else {
                echo json_encode(['success' => false, 'error' => 'Failed to cancel']);
            }
        }
        exit();
    }
}