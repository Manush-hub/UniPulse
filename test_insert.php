<?php
require 'app/Core/config.php';
require 'app/Core/Database.php';
require 'app/Core/Model.php';
require 'app/models/EventPostponement.php';

$postponementModel = new EventPostponement();
$logData = [
    'event_id' => 6,
    'reason' => 'test',
    'previous_event_date' => '2026-06-01',
    'previous_event_time' => '10:00:00',
    'previous_event_end_time' => '12:00:00',
    'previous_registration_end_date' => null,
    'previous_registration_end_time' => null,
    'previous_cover_photo' => null,
    'new_event_date' => '2026-07-01',
    'new_event_time' => '10:00:00',
    'new_event_end_time' => '12:00:00',
    'new_registration_end_date' => null,
    'new_registration_end_time' => null,
    'new_cover_photo' => null
];

$res = $postponementModel->insert($logData);
var_dump($res);
