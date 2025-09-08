<?php

class Onboarding extends Controller{

    public function index($a = '', $b = '' , $c = ''){
        $this->view('onboarding');
    }

    public function role_selection($a = '', $b = '' , $c = ''){
        $this->view('role-selection');
    }

    public function university_registration($a = '', $b = '' , $c = ''){
        $data['user_category'] = 'university';
        $this->view('multi-step-registration', $data);
    }

    public function public_registration($a = '', $b = '' , $c = ''){
        $data['user_category'] = 'public';
        $this->view('multi-step-registration', $data);
    }
}