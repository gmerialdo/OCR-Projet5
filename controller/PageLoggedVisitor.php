<?php

require_once "controller/PageVisitor.php";

class PageLoggedVisitor extends PageVisitor
{

    public function __construct($url){
        $url = array_slice($url, 1);
        parent::__construct($url);
    }

    //adds a complement before using parent::getPage() to securize the logged_visitor interface: only connect if logged!
    public function getPage(){
        //check if visitor or admin rights
        if ($this->_rights == "logged_visitor" OR $this->_rights == "admin"){
            return Page::getPage();
        }
        else {
            return $this->login();
        }
    }

    public function book_tickets(){
        return ["Book tickets", ""];
        }

        //if not logged --> loginpage with message (to book tickets you need to be registered...) keeping in memory the {{ event_id }} even if they go to sign in!!!!!!!!!
        //if logged --> {{ path }}/logged/book_tickets/{{ event_id }}

}
