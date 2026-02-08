<?php

class Volunteerreg extends Controller
{

    public function index($a = '', $b = '', $c = '')
    {
        $data = [];

        // Handle form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Get user info from session
            $userId = $_SESSION['user_id'] ?? null;
            $userType = $_SESSION['user_type'] ?? null;

            if (!$userId || !$userType) {
                $data['error'] = 'You must be logged in to register as a volunteer';
                $this->view('volunteerreg', $data);
                return;
            }

            // Get event ID from query parameter or form data
            $eventId = $_GET['event_id'] ?? $_POST['event_id'] ?? null;

            if (!$eventId) {
                $data['error'] = 'Event ID is required';
                $this->view('volunteerreg', $data);
                return;
            }

            // Validate volunteer registration data
            $volunteerData = [
                'user_id' => $userId,
                'user_type' => $userType,
                'event_id' => $eventId,
                'volunteer_position' => $_POST['volunteerPosition'] ?? '',
                'availability' => $_POST['Availability'] ?? '',
                'experience' => $_POST['Experience'] ?? '',
                'motivation' => $_POST['ask'] ?? '',
                'skills' => $_POST['Skills'] ?? '',
                'have_transportation' => isset($_POST['interests']) && in_array('academic', $_POST['interests']) ? 1 : 0,
                'commitment_understanding' => isset($_POST['interests']) && in_array('sports', $_POST['interests']) ? 1 : 0,
                'receive_updates' => isset($_POST['interests']) && in_array('cultural', $_POST['interests']) ? 1 : 0,
                'terms_accepted' => isset($_POST['terms']) ? 1 : 0
            ];

            // Validate required fields
            if (empty($volunteerData['volunteer_position']) || empty($volunteerData['availability'])) {
                $data['error'] = 'Please fill in all required fields';
                $this->view('volunteerreg', $data);
                return;
            }

            if (!$volunteerData['terms_accepted']) {
                $data['error'] = 'You must accept the Terms & Conditions';
                $this->view('volunteerreg', $data);
                return;
            }

            // Save volunteer registration
            $result = $this->saveVolunteerRegistration($volunteerData);

            if ($result) {
                // Log activity
                $this->logVolunteerActivity($userId, $userType, $eventId);

                $data['success'] = 'Thank you! Your volunteer registration has been submitted successfully!';
                $data['redirect'] = '/unipulse/public/user/dashboard';
                $this->view('volunteerreg', $data);
            } else {
                $data['error'] = 'Failed to submit volunteer registration. Please try again.';
                $this->view('volunteerreg', $data);
            }
        } else {
            // Show the form
            $data['event_id'] = $_GET['event_id'] ?? null;
            $this->view('volunteerreg', $data);
        }
    }

    /**
     * Save volunteer registration to database
     */
    private function saveVolunteerRegistration($data)
    {
        try {
            // Create volunteer registration record
            // This assumes you have a volunteer_registrations table
            // You may need to create this table and model

            $volunteerReg = new VolunteerRegistration();
            return $volunteerReg->insert($data);
        } catch (Exception $e) {
            error_log("Error saving volunteer registration: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Log activity for volunteer registration
     */
    private function logVolunteerActivity($userId, $userType, $eventId)
    {
        try {
            // Make sure we have all required data
            if (empty($userId) || empty($userType) || empty($eventId)) {
                return; // Skip if required data is missing
            }

            @$activity = new Activity(); // Suppress warnings
            @$event = new Event(); // Suppress warnings

            // Get event details
            $eventDetails = $event->getEventById($eventId);

            // Use event details if found, otherwise use defaults
            if ($eventDetails && !empty($eventDetails->title)) {
                $title = "Applied as volunteer for " . substr($eventDetails->title, 0, 50);
                $description = "You applied as a volunteer for the event \"" . $eventDetails->title . "\"";
                $eventTitle = $eventDetails->title;
            } else {
                // Fallback when event not found
                $title = "Applied as volunteer for Event #" . $eventId;
                $description = "You applied as a volunteer for event #" . $eventId;
                $eventTitle = null;
            }

            // Log the activity
            @$activity->logActivity(
                $userId,
                $userType,
                'volunteer_registration',
                $title,
                $description,
                'bell',  // Icon
                $eventId,
                $eventTitle,
                ['status' => 'pending']
            );
        } catch (Exception $e) {
            // Don't fail if activity logging fails
            error_log("Volunteer activity logging failed: " . $e->getMessage());
        }
    }
}
