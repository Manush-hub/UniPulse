<?php

class UserDashboard extends Controller{

    public function index($a = '', $b = '' , $c = ''){
        $this->view('dashboard');
    } 
}