<?php

class Postponeevent extends Controller
{
    private $eventModel;
    private $postponementModel;

    public function __construct()
    {
        parent::__construct();
        $this->eventModel = new Event();
        $this->postponementModel = new EventPostponement();
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

            if (empty($newDate) || empty($newTime)) {
                $data['error'] = 'New event date and time are required.';
                $this->view('Publisher/postponeevent', $data);
                return;
            }

            // Combine previous info to not lose description or update location if needed
            // Currently, only updating date/time fields.
            $updateData = [
                'event_date' => $newDate,
                'event_time' => $newTime,
                'event_end_time' => $newEndTime ?: null,
                'postponed_count' => 1
            ];

            if ($newRegDate) {
                $updateData['registration_end_date'] = $newRegDate;
                $updateData['registration_end_time'] = $newRegTime ?: null;
            }

            $result = $this->eventModel->update($id, $updateData);

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
                    'new_event_date' => $newDate,
                    'new_event_time' => $newTime,
                    'new_event_end_time' => $newEndTime ?: null,
                    'new_registration_end_date' => $newRegDate ?: null,
                    'new_registration_end_time' => $newRegTime ?: null
                ];

                $this->postponementModel->insert($logData);

                header('Location: /unipulse/public/publisher/dashboard');
                exit();
            } else {
                $data['error'] = 'Failed to postpone event. Please try again.';
            }
        }

        $this->view('Publisher/postponeevent', $data);
    }
}
