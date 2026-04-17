<?php
require 'app/Core/config.php';
require 'app/Core/Database.php';
require 'app/Core/Model.php';
require 'app/models/Event.php';

$eventModel = new Event();
$event = $eventModel->first(['id' => 6]); // Use another ID if you have
if(!$event) die("No event");

$updateData = [
    'event_date' => '2026-06-01'
];

print_r($event->title); echo "\n";
$res = $eventModel->update(6, $updateData);
$event2 = $eventModel->first(['id' => 6]);
print_r($event2->title); echo "\n";
