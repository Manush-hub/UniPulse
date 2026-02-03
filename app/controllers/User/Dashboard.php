<?php

class UserDashboard extends Controller
{

    public function index($a = '', $b = '', $c = '')
    {
        // Require authentication - allow both public and university users
        $currentUser = AuthService::getCurrentUser();
        if (!$currentUser || !in_array($currentUser['type'], ['public', 'university'])) {
            header('Location: /unipulse/public/signin');
            exit();
        }

        // Load profile photo into session for header display
        $this->loadUserProfilePhotoToSession();

        // Pass user data to view
        $data = [
            'user' => $currentUser
        ];

        $this->view('User/dashboard', $data);
    }

    /**
     * API endpoint to get current user data
     */
    public function getUserData()
    {
        header('Content-Type: application/json');

        if (!AuthService::isLoggedIn()) {
            echo json_encode(['success' => false, 'error' => 'Not authenticated']);
            return;
        }

        $currentUser = AuthService::getCurrentUser();
        echo json_encode([
            'success' => true,
            'user' => [
                'name' => $currentUser['name'] ?? 'User',
                'email' => $currentUser['email'] ?? '',
                'type' => $currentUser['type'] ?? 'user',
                'university' => $currentUser['university'] ?? ''
            ]
        ]);
    }

    /**
     * API endpoint to get user's registered upcoming events
     */
    public function getUpcomingEvents()
    {
        header('Content-Type: application/json');

        if (!AuthService::isLoggedIn()) {
            echo json_encode(['success' => false, 'error' => 'Not authenticated']);
            return;
        }

        try {
            $currentUser = AuthService::getCurrentUser();

            if (!$currentUser) {
                echo json_encode(['success' => false, 'error' => 'User data not found']);
                return;
            }

            $userId = $currentUser['id'];
            $userType = $currentUser['type'];

            // Load EventRegistration model
            $eventRegistration = new EventRegistration();

            if (!$eventRegistration) {
                echo json_encode(['success' => false, 'error' => 'Failed to load EventRegistration model']);
                return;
            }

            // Get user's registered events (upcoming only)
            $registeredEvents = $eventRegistration->getUserRegisteredEvents($userId, $userType, 'registered');

            // Filter for upcoming events only (event_date >= today)
            $upcomingEvents = [];
            if ($registeredEvents) {
                foreach ($registeredEvents as $event) {
                    $eventDate = strtotime($event->event_date);
                    if ($eventDate >= strtotime('today')) {
                        $upcomingEvents[] = [
                            'id' => $event->id,
                            'title' => $event->title,
                            'description' => isset($event->description) ? substr($event->description, 0, 100) . '...' : '',
                            'date' => $event->event_date,
                            'time' => $event->event_time,
                            'location' => $event->location,
                            'category' => $event->category,
                            'university' => $event->university_name,
                            'image_url' => $event->image_url,
                            'organizer' => $event->organizer,
                            'max_participants' => $event->max_participants,
                            'current_participants' => $event->current_participants ?? 0
                        ];
                    }
                }
            }

            // Sort by event date (earliest first)
            usort($upcomingEvents, function ($a, $b) {
                return strtotime($a['date']) - strtotime($b['date']);
            });

            echo json_encode([
                'success' => true,
                'events' => $upcomingEvents,
                'count' => count($upcomingEvents)
            ]);
        } catch (Exception $e) {
            error_log("Error in getUpcomingEvents: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage(),
                'trace' => DEBUG ? $e->getTraceAsString() : ''
            ]);
        }
    }

    /**
     * API endpoint to get featured events
     */
    public function getFeaturedEvents()
    {
        header('Content-Type: application/json');

        try {
            $event = new Event();

            // Get featured events (you can define your own criteria)
            $filters = [
                'status' => 'upcoming'
            ];

            $featuredEvents = $event->getAllEvents($filters);

            // Format events
            $formatted = [];
            if ($featuredEvents) {
                foreach (array_slice($featuredEvents, 0, 6) as $event) {
                    $formatted[] = [
                        'id' => $event->id,
                        'title' => $event->title,
                        'description' => substr($event->description, 0, 100) . '...',
                        'date' => $event->event_date,
                        'category' => $event->category,
                        'university' => $event->university_name,
                        'image_url' => $event->image_url
                    ];
                }
            }

            echo json_encode([
                'success' => true,
                'events' => $formatted
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * API endpoint to get recent activity
     */
    public function getRecentActivity()
    {
        header('Content-Type: application/json');

        if (!AuthService::isLoggedIn()) {
            echo json_encode(['success' => false, 'error' => 'Not authenticated']);
            return;
        }

        try {
            // This is a placeholder - you can implement your own logic
            $activities = [
                [
                    'title' => 'Event Registration',
                    'description' => 'You registered for an event',
                    'time' => '2 hours ago',
                    'icon' => 'calendar'
                ],
                [
                    'title' => 'New Event Posted',
                    'description' => 'A new event was posted in your university',
                    'time' => '1 day ago',
                    'icon' => 'plus'
                ]
            ];

            echo json_encode([
                'success' => true,
                'activities' => $activities
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }
}
