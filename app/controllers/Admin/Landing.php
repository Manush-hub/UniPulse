<?php

class AdminLanding extends Controller {

    private $eventModel;

    public function __construct() {
        parent::__construct();
        $this->eventModel = new Event();
    }

    public function index($a = '', $b = '', $c = '') {
        // Admin sees all events regardless of visibility
        $adminUser = ['type' => 'admin'];

        $data['boosted_events']       = $this->eventModel->getActiveBoostedEvents(20, $adminUser);
        $data['upcoming_24h_events']   = $this->eventModel->getEventsStartingIn24Hours(20, $adminUser);
        $data['more_events']           = $this->eventModel->getNextUpcomingPublicEvents(3, $adminUser);
        $data['userRole']              = 'Admin';

        $this->view('landing', $data);
    }
}
