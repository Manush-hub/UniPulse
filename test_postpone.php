<?php
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST['event_date'] = '2026-07-01';
$_POST['event_time'] = '10:00';
$_POST['event_end_time'] = '12:00';
$_POST['registration_end_date'] = '2026-06-25';
$_POST['registration_end_time'] = '23:59';
$_POST['postpone_reason'] = 'Weather';

require 'app/Core/config.php';
require 'app/Core/Database.php';
require 'app/Core/Model.php';
require 'app/models/Event.php';
require 'app/models/EventPostponement.php';

$eventModel = new Event();
$event = $eventModel->first(['id' => 6]);
echo "Old Event Date: " . $event->event_date . "\n";

$updateData = [
    'event_date' => $_POST['event_date'],
    'event_time' => $_POST['event_time'],
    'event_end_time' => $_POST['event_end_time'],
    'postponed_count' => 1,
    'registration_end_date' => $_POST['registration_end_date'],
    'registration_end_time' => $_POST['registration_end_time']
];

$result = $eventModel->update(6, $updateData);
echo "Update Result: " . ($result ? 'success' : 'failed') . "\n";

$event2 = $eventModel->first(['id' => 6]);
echo "New Event Date: " . $event2->event_date . "\n";
echo "New Reg Date: " . $event2->registration_end_date . "\n";

