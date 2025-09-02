<?php

class PublisherEventdetail extends Controller {
    
    public function index($id = null) {
        // Call the eventdetail view for publishers
        $this->view('eventdetail');
    }
}
