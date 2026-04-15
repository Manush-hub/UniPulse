<?php

class EventPostponement
{
    use Model;

    protected $table = 'event_postponements';
    protected $allowedColumns = [
        'event_id',
        'reason',
        'previous_event_date',
        'previous_event_time',
        'previous_event_end_time',
        'previous_registration_end_date',
        'previous_registration_end_time',
        'new_event_date',
        'new_event_time',
        'new_event_end_time',
        'new_registration_end_date',
        'new_registration_end_time',
        'created_at'
    ];
}