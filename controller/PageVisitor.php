<?php

require_once "controller/Page.php";

class PageVisitor extends Page
{

    public function __construct($url){
        parent::__construct($url);
        $this->_defaultPage = "see_all_events";
    }

    public function see_all_events(){
        $req = [
            "fields" => ['event_id'],
            "from" => "evt_events",
            "where" => [ "active_event = 1" ]
        ];
        $data = Model::select($req);
        //if no events
        if (!isset($data["data"][0])){
            $content = View::addTitleHtml(2, "No current event");
        }
        else {
            $each_event;
            $content = View::addTitleHtml(2, "Our events");
            $content .= View::addDiv("start", "row");
            foreach ($data["data"] as $row){
                $each_event = new Event("read", ["id" => $row["event_id"]]);
                $content .= $each_event->addEventOnAll();
            }
            $content .= View::addDiv("end");
        }
        return ["All events", $content];
    }


}
