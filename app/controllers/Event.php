<?php

class EventView extends Controller {
    
    public function index($eventId = '') {
        if (empty($eventId)) {
            header('Location: /unipulse/public/find_events');
            exit();
        }
        
        $event = new Event();
        $eventData = $event->getEventById($eventId);
        
        if (!$eventData) {
            // Event not found, redirect to find events
            header('Location: /unipulse/public/find_events');
            exit();
        }
        
        // Check if user has permission to view this event
        $currentUser = AuthService::getCurrentUser();
        $canView = $this->canUserViewEvent($eventData, $currentUser);
        
        if (!$canView) {
            // User doesn't have permission to view this event
            $_SESSION['error_message'] = 'You do not have permission to view this event.';
            header('Location: /unipulse/public/find_events');
            exit();
        }
        
        // Get similar events
        $similarEvents = [];
        if ($currentUser) {
            if ($currentUser['type'] === 'university') {
                $userUniversity = $event->getUserUniversity($currentUser['id'], $currentUser['type'], $currentUser['table']);
                $similarEvents = $event->getEventsForUser($currentUser['type'], $userUniversity, ['category' => $eventData->category, 'limit' => 3]);
            } else {
                $similarEvents = $event->getEventsForUser('public', null, ['category' => $eventData->category, 'limit' => 3]);
            }
        } else {
            $similarEvents = $event->getEventsForUser('public', null, ['category' => $eventData->category, 'limit' => 3]);
        }
        
        // Filter out the current event from similar events
        $similarEvents = array_filter($similarEvents, function($e) use ($eventId) {
            return $e->id != $eventId;
        });
        
        $data = [
            'event' => $eventData,
            'user' => $currentUser,
            'similarEvents' => array_slice($similarEvents, 0, 3)
        ];
        
        $this->view('event_view', $data);
    }
    
    public function join($eventId = '') {
        // Require authentication
        $currentUser = AuthService::getCurrentUser();
        if (!$currentUser) {
            header('Location: /unipulse/public/signin');
            exit();
        }
        
        if (empty($eventId)) {
            header('Location: /unipulse/public/find_events');
            exit();
        }
        
        $event = new Event();
        $eventData = $event->getEventById($eventId);
        
        if (!$eventData) {
            $_SESSION['error_message'] = 'Event not found.';
            header('Location: /unipulse/public/find_events');
            exit();
        }
        
        // Check if user has permission to view/join this event
        $canView = $this->canUserViewEvent($eventData, $currentUser);
        
        if (!$canView) {
            $_SESSION['error_message'] = 'You do not have permission to join this event.';
            header('Location: /unipulse/public/find_events');
            exit();
        }
        
        // Attempt to join the event
        $joinResult = $event->joinEvent($eventId);
        
        if ($joinResult) {
            $_SESSION['success_message'] = 'Successfully joined the event!';
        } else {
            $_SESSION['error_message'] = 'Failed to join the event. It may be full or you may already be registered.';
        }
        
        header("Location: /unipulse/public/event/view/$eventId");
        exit();
    }
    
    private function canUserViewEvent($eventData, $currentUser) {
        // Public events can be viewed by anyone
        if (!isset($eventData->visibility) || $eventData->visibility === 'public') {
            return true;
        }
        
        // University-only events
        if ($eventData->visibility === 'university') {
            if (!$currentUser || $currentUser['type'] !== 'university') {
                return false;
            }
            
            // Check if user belongs to the same university
            $event = new Event();
            $userUniversity = $event->getUserUniversity($currentUser['id'], $currentUser['type'], $currentUser['table']);
            
            return $userUniversity === $eventData->university;
        }
        
        return true;
    }
}