<?php

require_once "controller/Page.php";

class PageVisitor extends Page
{

    public function __construct($url){
        parent::__construct($url);
        $this->_defaultPage = "see_all_events";
    }

    //adds a complement before using parent
    public function getPage(){
        $_SESSION["admin_mode"] = false;
        return Page::getPage();
    }

    public function see_all_events(){
        $req = [
            "fields" => ['event_id'],
            "from" => "evt_events",
            "where" => [
                "active_event = 1",
                "finish_datetime > NOW()"
            ],
            "order" => "start_datetime"
        ];
        $data = Model::select($req);
        //if no events
        $all_events = "";
        if (!isset($data["data"][0])){
            $title = "No current event";
        }
        else {
            $title ="Our events";
            $each_event;
            foreach ($data["data"] as $row){
                $each_event = new Event("read", ["id" => $row["event_id"]]);
                $all_events .= View::makeHtml($each_event->getEventData(), "elt_each_event.html");
            }
        }
        $content = View::makeHtml([
            "{{ title }}" => $title,
            "{{ events }}" => $all_events
        ], "content_see_all_events.html");
        return ["All events", $content];
    }

    public function see_event(){
        $event = new Event("read", ["id" => $this->_url[1]]);
        $eventData = $event->getEventData();
        if ($event->getVarEvent("_type_tickets") != 0){
            $eventData["{{ book_tickets }}"] = View::makeHtml(["{{ event_id }}" => $this->_url[1]],"elt_book_tickets_btn.html");
        }
        else {
            $eventData["{{ book_tickets }}"] = "";
        }
        $content = View::makeHtml($eventData, "content_see_event.html");
        return [$event->getVarEvent("_name"), $content];
    }

    public function needs_login(){
        if (isset($_SESSION["user_name"])){
            if (!isset($this->_url[1]) OR !isset($this->_url[2])) header('Location: ');
            $link = $this->_url[1]."/".$this->_url[2];
            header('Location: ../../logged/'.$link);
        }
        else {
            return $this->login("booking", $this->_url[2]);
        }
    }


}
