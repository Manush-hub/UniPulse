<?php

class PublisherLanding extends Controller{

    public function index($a = '', $b = '' , $c = ''){
        $eventModel = new Event();
        
        // Get current user for visibility filtering
        $currentUser = AuthService::getCurrentUser();
        
        // Get boosted events for carousel
        $data['boosted_events'] = $eventModel->getActiveBoostedEvents(10);
        
        // Get events starting in the next 24 hours
        $data['upcoming_24h_events'] = $eventModel->getEventsStartingIn24Hours(10, $currentUser);
        
        // Get next 3 upcoming public events for More Events section
        $data['more_events'] = $eventModel->getNextUpcomingPublicEvents(3, $currentUser);
        
        $data['userRole'] = 'Publisher';
        $this->view('landing', $data);
    } 
}
