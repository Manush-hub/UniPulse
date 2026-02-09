<?php

class UserLanding extends Controller
{

<<<<<<< HEAD
    public function index($a = '', $b = '' , $c = ''){
        $eventModel = new Event();
        $data['boosted_events'] = $eventModel->getActiveBoostedEvents(10);
        $this->view('landing', $data);
    } 
=======
    public function index($a = '', $b = '', $c = '')
    {
        // Load profile photo into session for header display
        $this->loadUserProfilePhotoToSession();

        $eventModel = new Event();
        $data['boosted_events'] = $eventModel->getActiveBoostedEvents(10);
        $this->view('landing', $data);
    }
>>>>>>> 2.9-merge(User_report_generation__&__publisher)
}
