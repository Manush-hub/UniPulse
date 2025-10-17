<?php

class UserDashboard extends BaseUserController {

    public function index($a = '', $b = '' , $c = ''){
        // Get events based on user profile
        $event = new Event();
        
        // Get upcoming events that user can see
        $upcomingEvents = $event->getEventsForUser($this->currentUser['type'], $this->userUniversity, ['status' => 'upcoming', 'limit' => 5]);
        
        // Get recent events that user can see
        $recentEvents = $event->getEventsForUser($this->currentUser['type'], $this->userUniversity, ['limit' => 10]);
        
        // Pass data to view using enhanced method
        $this->userView('User/dashboard', [
            'upcomingEvents' => $upcomingEvents,
            'recentEvents' => $recentEvents
        ]);
    } 
}