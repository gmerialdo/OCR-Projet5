<?php

require_once "controller/Page.php";

class PageAdmin extends Page
{

    public function __construct($url){
        $url = array_slice($url, 1);
        Page::__construct($url);
        $this->_defaultPage = "see_all_events";/////////TO CHANGE
    }

    //adds a complement before using parent::getPage() to securize all the admin interface: only connect if logged!
    public function getPage(){
        //check if no admin rights
        if ($this->_rights != "admin"){
            header('Location: see_all_events');
        }
        //else the user is logged in so go to the page in admin interface
        else {
            return Page::getPage();
        }
    }



}
