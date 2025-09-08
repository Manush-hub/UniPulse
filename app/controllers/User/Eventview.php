<?php

class UserEventview extends Controller {
    
    public function index($id = null) {
        // Call the eventview view
        $this->view('eventview');
    }
}
