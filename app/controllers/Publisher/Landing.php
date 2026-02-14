<?php

class PublisherLanding extends Controller{

    public function index($a = '', $b = '' , $c = ''){
        $eventModel = new Event();
        $data['boosted_events'] = $eventModel->getActiveBoostedEvents(10);
        $data['userRole'] = 'Publisher';
        $this->view('landing', $data);
    } 
}
