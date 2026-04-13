<?php

class Sponsorpublic extends Controller{

    public function index($sponsorId = '', $b = '' , $c = ''){
        // Check if sponsor ID is provided
        if (empty($sponsorId) && isset($_GET['id'])) {
            $sponsorId = $_GET['id'];
        }
        
        if (empty($sponsorId)) {
            // Redirect to home if no sponsor ID provided
            header('Location: /unipulse/public/home');
            exit();
        }
        
        $sponsorModel = new Sponsor();
        
        // Get sponsor basic info
        $sponsor = $sponsorModel->findById($sponsorId);
        
        if (!$sponsor) {
            // Sponsor not found
            $this->view('Sponsor/public', ['error' => 'Sponsor not found']);
            return;
        }
        
        // Get sponsor profile data
        $sponsorProfile = $sponsorModel->getProfileData($sponsorId);
        
        // Get sponsored events
        $sponsoredEvents = $sponsorModel->getSponsoredEvents($sponsorId);
        if (!is_array($sponsoredEvents)) {
            $sponsoredEvents = [];
        }
        error_log("Sponsor Public - Sponsor ID: $sponsorId");
        error_log("Sponsor Public - Sponsored Events Count: " . count($sponsoredEvents));
        error_log("Sponsor Public - Sponsored Events Data: " . print_r($sponsoredEvents, true));
        
        // Get galleries (placeholder for now - you'll need to implement this)
        $galleries = [];
        
        // Get news items (placeholder for now - you'll need to implement this)
        $newsItems = [];
        
        // Prepare data for view
        $data = [
            'sponsor' => $sponsor,
            'sponsorProfile' => $sponsorProfile,
            'sponsoredEvents' => $sponsoredEvents,
            'galleries' => $galleries,
            'newsItems' => $newsItems
        ];
        
        $this->view('Sponsor/public', $data);
    }
}
