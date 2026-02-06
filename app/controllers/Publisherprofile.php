<?php

class Publisherprofile extends Controller{

    public function index($a = '', $b = '' , $c = ''){
        // Get publisher ID from query parameter
        $publisherId = $_GET['id'] ?? null;
        
        if (!$publisherId) {
            // Redirect to home or show error
            header('Location: /unipulse/public/home');
            exit;
        }
        
        // Load Publisher model
        $publisherModel = $this->model('Publisher');
        
        // Get publisher basic info
        $publisher = $publisherModel->getPublisherById($publisherId);
        
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
            'profile' => $profile,
            'galleries' => $galleries,
            'upcomingEvents' => $upcomingEvents,
            'pastEvents' => $pastEvents
        ];
        
        $this->view('Publisher/public', $data);
    }

}
