<?php

class Moderators extends Controller{

    public function index($a = '', $b = '', $c = ''){
        // Redirect to moderators list
        $moderatorsList = new Moderators_list();
        $moderatorsList->index($a, $b, $c);
    }
    
    public function create($a = '', $b = '', $c = ''){
        // Handle moderator creation
        $moderatorCreate = new Moderator_create();
        $moderatorCreate->index($a, $b, $c);
    }
}
