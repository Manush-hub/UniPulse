<?php

class SponsorLanding extends Controller{

    public function index($a = '', $b = '' , $c = ''){
        $eventModel = new Event();
        $data['boosted_events'] = $eventModel->getActiveBoostedEvents(10);
        $this->view('landing', $data);
    } 
}
