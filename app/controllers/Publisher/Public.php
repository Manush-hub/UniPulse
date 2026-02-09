<?php

class PublisherPublic extends Controller{

    public function index($a = '', $b = '' , $c = ''){
        // Get publisher ID from query parameter
        $publisherId = $_GET['id'] ?? null;
        
        // Debug logging
        error_log("PublisherPublic: Requested ID = " . ($publisherId ?? 'NULL'));
        error_log("PublisherPublic: Full GET params = " . print_r($_GET, true));
        
        if (!$publisherId) {
            // Redirect to home or show error
            header('Location: /unipulse/public/home');
            exit;
        }
        
        // Load Publisher model
        $publisherModel = $this->model('Publisher');
        
        // Get publisher basic info
        $publisher = $publisherModel->getPublisherById($publisherId);
        
        error_log("PublisherPublic: Found publisher = " . ($publisher ? $publisher->society_name : 'NULL'));
        
        if (!$publisher) {
            // Publisher not found
            header('Location: /unipulse/public/home');
            exit;
        }
        
        // Check if publisher is approved
        if ($publisher->approval_status !== 'approved') {
            // Show not available message
            $data['error'] = 'This publisher profile is not available.';
            $this->view('Publisher/public', $data);
            return;
        }
        
        // Get publisher profile data
        $profile = $publisherModel->getProfileData($publisherId);
        
        // Get publisher galleries
        $galleries = $publisherModel->getPublisherGalleries($publisherId);
        
        // Get current user for visibility filtering
        $currentUser = AuthService::getCurrentUser();
        
        // Get upcoming and past events
        $upcomingEvents = $publisherModel->getUpcomingEvents($publisherId, $currentUser);
        $pastEvents = $publisherModel->getPastEvents($publisherId, $currentUser);
        
        // Prepare data for view
        $data = [
            'publisher' => $publisher,
            'publisherProfile' => $profile,
            'galleries' => $galleries,
            'upcomingEvents' => $upcomingEvents,
            'pastEvents' => $pastEvents,
            'events' => array_merge($upcomingEvents, $pastEvents) // Add merged events for convenience
        ];
        
        $this->view('Publisher/public', $data);
    }

}
