<?php

class Postponeevent extends Controller
{
    private $eventModel;
    private $postponementModel;
    private $eventRegistrationModel;
    private $notificationModel;

    public function __construct()
    {
        parent::__construct();
        $this->eventModel = new Event();
        $this->postponementModel = new EventPostponement();
        $this->eventRegistrationModel = new EventRegistration();
        $this->notificationModel = new Notification();
    }

    public function index($id = null)
    {
        // 1. Authenticate check
        $currentUser = AuthService::getCurrentUser();
        if (!$currentUser || $currentUser['type'] !== 'publisher') {
            header('Location: /unipulse/public/signin');
            exit();
        }

        if (!$id) {
            header('Location: /unipulse/public/publisher/dashboard');
            exit();
        }

        // 2. Fetch event
        $event = $this->eventModel->first(['id' => $id]);
        if (!$event) {
            header('Location: /unipulse/public/publisher/dashboard');
            exit();
        }

        // 3. Authorization check (publisher owns it)
        if ($event->created_by != $currentUser['id'] || $event->created_by_type != 'publisher') {
            header('Location: /unipulse/public/publisher/dashboard');
            exit();
        }

        // 4. One time postpone rule
        if (isset($event->postponed_count) && $event->postponed_count > 0) {
            $data = [
                'event' => $event,
                'error' => 'You can only postpone an event once.',
                'action_disabled' => true
            ];
            $this->view('Publisher/postponeevent', $data);
            return;
        }

        $data = [
            'event' => $event,
            'error' => null,
            'action_disabled' => false
        ];

        // 5. Handle Form Submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $newDate = $_POST['event_date'] ?? '';
            $newTime = $_POST['event_time'] ?? '';
            $newEndTime = $_POST['event_end_time'] ?? '';
            $newRegDate = $_POST['registration_end_date'] ?? '';
            $newRegTime = $_POST['registration_end_time'] ?? '';
            $postponeReason = $_POST['postpone_reason'] ?? '';

            if (empty($newDate) || empty($newTime) || empty($newEndTime)) {
                $data['error'] = 'New event date, start time, and end time are required.';
                $this->view('Publisher/postponeevent', $data);
                return;
            }

            // Combine previous info to not lose description or update location if needed
            // Currently, updating date/time fields and optionally cover photo
            $updateData = [
                'event_date' => $newDate,
                'event_time' => $newTime,
                'event_end_time' => $newEndTime,
                'postponed_count' => 1
            ];

            // Handle cover image upload
            $newCoverPhoto = null;
            if (isset($_FILES['cover_photo']) && $_FILES['cover_photo']['error'] === UPLOAD_ERR_OK) {
                $uploadResult = $this->handleImageUpload($_FILES['cover_photo']);
                if ($uploadResult['success']) {
                    $newCoverPhoto = $uploadResult['path'];
                    $updateData['image_url'] = $newCoverPhoto;
                    $updateData['cover_image'] = $newCoverPhoto;
                } else {
                    $data['error'] = $uploadResult['error'];
                    $this->view('Publisher/postponeevent', $data);
                    return;
                }
            }

            if ($newRegDate) {
                $updateData['registration_end_date'] = $newRegDate;
                $updateData['registration_end_time'] = $newRegTime ?: null;
            }

            // Temporarily debug the update:
            error_log("Update Data: " . print_r($updateData, true));
            $result = $this->eventModel->update($id, $updateData);
            error_log("Update Result: " . ($result ? 'success' : 'failed'));

            if ($result) {
                // Log to the new separate event_postponements table
                $logData = [
                    'event_id' => $id,
                    'reason' => $postponeReason ?: null,
                    'previous_event_date' => $event->event_date,
                    'previous_event_time' => $event->event_time,
                    'previous_event_end_time' => $event->event_end_time,
                    'previous_registration_end_date' => $event->registration_end_date ?? null,
                    'previous_registration_end_time' => $event->registration_end_time ?? null,
                    'previous_cover_photo' => $event->image_url ?? $event->cover_image ?? null,
                    'new_event_date' => $newDate,
                    'new_event_time' => $newTime,
                    'new_event_end_time' => $newEndTime,
                    'new_registration_end_date' => $newRegDate ?: null,
                    'new_registration_end_time' => $newRegTime ?: null,
                    'new_cover_photo' => $newCoverPhoto
                ];

                $this->postponementModel->insert($logData);

                // Send notification to registered users
                $registrations = $this->eventRegistrationModel->getEventRegistrations($id, 'registered');
                if ($registrations) {
                    $notificationTitle = "Event Postponed: " . $event->title;
                    $notificationMessage = "The event '{$event->title}' has been postponed to " . date('F j, Y', strtotime($newDate)) . " at " . date('g:i A', strtotime($newTime)) . ".";
                    if ($postponeReason) {
                        $notificationMessage .= " Reason: {$postponeReason}.";
                    }
                    $notificationMessage .= " Your existing tickets and registrations remain valid for the new date.";
                    
                    foreach ($registrations as $reg) {
                        $this->notificationModel->sendNotification([
                            'recipient_id' => $reg->user_id,
                            'recipient_type' => $reg->user_type,
                            'type' => 'event_postponed',
                            'title' => $notificationTitle,
                            'message' => $notificationMessage,
                            'related_id' => $id,
                            'related_type' => 'event'
                        ]);
                    }
                }

                header('Location: /unipulse/public/publisher/dashboard');
                exit();
            } else {
                $data['error'] = 'Failed to postpone event. Please try again.';
            }
        }

        $this->view('Publisher/postponeevent', $data);
    }

    private function handleImageUpload($file) {
        $uploadDir = __DIR__ . '/../../../public/uploads/event_covers/';
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $maxSize = 5 * 1024 * 1024; // 5MB
        
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        if (!in_array($file['type'], $allowedTypes)) {
            return ['success' => false, 'error' => 'Invalid file type. Please upload a valid image.'];
        }
        
        if ($file['size'] > $maxSize) {
            return ['success' => false, 'error' => 'File too large. Maximum size is 5MB.'];
        }
        
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid('event_cover_') . '.' . $extension;
        $filepath = $uploadDir . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            return ['success' => true, 'path' => 'uploads/event_covers/' . $filename];
        }
        
        return ['success' => false, 'error' => 'Failed to upload image.'];
    }
}
