<?php

class AdminDashboard extends Controller{

    public function index($a = '', $b = '' , $c = ''){
        $this->view('dashboard');
    } 
}
