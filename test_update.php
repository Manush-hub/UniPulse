<?php
require 'app/Core/config.php';
require 'app/Core/Database.php';
require 'app/Core/Model.php';
require 'app/models/Event.php';

$eventModel = new Event();
$event = $eventModel->first(['id' => 1]); // Try ID 1
if(!$event) die("No event 1");

$updateData = [
    'event_date' => '2026-05-01',
    'event_time' => '10:00:00',
    'event_end_time' => '12:00:00',
    'postponed_count' => 1
];

$res = $eventModel->update(1, $updateData);
echo "Update result: " . ($res ? 'true' : 'false') . "\n";
$event2 = $eventModel->first(['id' => 1]);
echo "New date: " . $event2->event_date . "\n";
