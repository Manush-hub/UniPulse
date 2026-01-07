<?php

class SponsorHistory extends Controller {
    
    private $sponsorshipModel;
    private $donationModel;
    
    public function __construct() {
        parent::__construct();
        $this->sponsorshipModel = new Sponsorship();
        $this->donationModel = new Donation();
    }
    
    public function index() {
        $currentUser = AuthService::getCurrentUser();
        if (!$currentUser || $currentUser['type'] !== 'sponsor') {
            header('Location: /unipulse/public/signin');
            exit();
        }
        
        // Get filter parameters
        $filter = $_GET['filter'] ?? 'all'; // all, sponsorships, donations
        $year = $_GET['year'] ?? date('Y');
        
        $history = [];
        
        switch($filter) {
            case 'sponsorships':
                $history = $this->sponsorshipModel->getHistoryBySponsor($currentUser['id'], $year);
                break;
            case 'donations':
                $history = $this->donationModel->getHistoryBySponsor($currentUser['id'], $year);
                break;
            default:
                // Combine both
                $sponsorships = $this->sponsorshipModel->getHistoryBySponsor($currentUser['id'], $year);
                $donations = $this->donationModel->getHistoryBySponsor($currentUser['id'], $year);
                $history = array_merge($sponsorships, $donations);
                // Sort by date
                usort($history, function($a, $b) {
                    return strtotime($b->created_at) - strtotime($a->created_at);
                });
        }
        
        $data = [
            'user' => $currentUser,
            'history' => $history,
            'filter' => $filter,
            'year' => $year,
            'stats' => [
                'total_sponsorships' => $this->sponsorshipModel->getCountBySponsor($currentUser['id']),
                'total_donations' => $this->donationModel->getTotalBySponsor($currentUser['id']),
                'total_amount' => $this->getTotalContributions($currentUser['id'])
            ],
            'page_title' => 'Sponsorship History'
        ];
        
        $this->view('Sponsor/history', $data);
    }
    
    // Export history as PDF/CSV
    public function export() {
        $currentUser = AuthService::getCurrentUser();
        if (!$currentUser || $currentUser['type'] !== 'sponsor') {
            header('Location: /unipulse/public/signin');
            exit();
        }
        
        $format = $_GET['format'] ?? 'csv';
        $year = $_GET['year'] ?? date('Y');
        
        $history = $this->getAllHistory($currentUser['id'], $year);
        
        if ($format === 'csv') {
            $this->exportCSV($history);
        } else {
            $this->exportPDF($history);
        }
    }
    
    private function getTotalContributions($sponsorId) {
        $totalSponsorships = $this->sponsorshipModel->getTotalAmountBySponsor($sponsorId);
        $totalDonations = $this->donationModel->getTotalBySponsor($sponsorId);
        return $totalSponsorships + $totalDonations;
    }
}