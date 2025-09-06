<?php

class Dashboard extends Controller{

    public function index($a = '', $b = '' , $c = ''){
        // For now, just show a simple dashboard
        $data = [
            'title' => 'UniPulse Dashboard',
            'message' => 'Welcome to your dashboard!'
        ];
        $this->view('dashboard', $data);
    }  
}