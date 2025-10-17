<?php

class Find_events extends Controller{

    public function index($a = '', $b = '' , $c = ''){
        // Get current user information
        $currentUser = AuthService::getCurrentUser();
        $userDetails = AuthService::getCurrentUserDetails();
        $event = new Event();
        
        // Initialize data array
        $data = [
            'events' => [],
            'user' => $currentUser,
            'userDetails' => $userDetails
        ];
        
        // Get filters from GET parameters if any
        $filters = [
            'category' => $_GET['category'] ?? '',
            'university' => $_GET['university'] ?? '',
            'search' => $_GET['search'] ?? ''
        ];
        
        // Remove empty filters
        $filters = array_filter($filters);
        
        if ($currentUser) {
            // User is logged in - get university if they're a university user
            $userUniversity = null;
            $userUniversityName = null;
            if ($currentUser['type'] === 'university') {
                $userUniversity = $event->getUserUniversity($currentUser['id'], $currentUser['type'], $currentUser['table']);
                if ($userUniversity) {
                    $userUniversityName = AuthService::getUserUniversityName($userUniversity);
                }
            }
            
            // Add university info to data
            $data['userUniversity'] = $userUniversity;
            $data['userUniversityName'] = $userUniversityName;
            
            // Get filtered events based on user's profile
            $data['events'] = $event->getEventsForUser($currentUser['type'], $userUniversity, $filters);
        } else {
            // User not logged in - only show public events
            $data['events'] = $event->getEventsForUser('public', null, $filters);
        }
        
        $this->view('find_events', $data);
    }

}
