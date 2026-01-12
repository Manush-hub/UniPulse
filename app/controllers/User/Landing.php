<?php

class UserLanding extends Controller
{

    public function index($a = '', $b = '', $c = '')
    {
        // Load profile photo into session for header display
        $this->loadUserProfilePhotoToSession();

        $this->view('landing');
    }
}
