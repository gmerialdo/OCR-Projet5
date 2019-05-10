<?php

class Event
{

    private $_event_id;
    private $_location_id;
    private $_owner_account_id;
    private $_image_id;
    private $_active_event;
    private $_name;
    private $_description;
    private $_start_weekday;
    private $_start_date;
    private $_start_time;
    private $_finish_weekday;
    private $_finish_date;
    private $_finish_time;
    private $_category;
    private $_max_tickets;
    private $_type_tickets;
    private $_private;
    private $_members_only;
    private $_price_adult_member;
    private $_price_adult;
    private $_price_child_member;
    private $_price_child;
    private $_smallest_price;
    private $_limit_booking_time;

    public function __construct($todo, $args){
        switch ($todo){
            case "read":
                $this->_event_id = $args["id"];
                $this->setEventDataFromDB();
                break;
            case "create":
                break;
        }
    }

    public function setEventDataFromDB(){
        $req = [
            "fields" => [
                'location_id',
                'owner_account_id',
                'image_id',
                'active_event',
                'name',
                'description',
                'DATE_FORMAT(start_datetime, \'%W\') AS "start_weekday"',
                'DATE_FORMAT(start_datetime, \'%b %D, %Y\') AS "start_date"',
                'DATE_FORMAT(start_datetime, \'%r\') AS "start_time"',
                'DATE_FORMAT(finish_datetime, \'%W\') AS "finish_weekday"',
                'DATE_FORMAT(finish_datetime, \'%b %D, %Y\') AS "finish_date"',
                'DATE_FORMAT(finish_datetime, \'%r\') AS "finish_time"',
                'category',
                'max_tickets',
                'type_tickets',
                'public',
                'members_only',
                'price_adult_member',
                'price_adult',
                'price_child_member',
                'price_child',
                'LEAST (price_adult_member, price_adult, price_child_member, price_child) AS "smallest_price"',
                'limit_booking_time'],
            "from" => "evt_events",
            "where" => [ "event_id = ".$this->_event_id],
            "limit" => 1
        ];
        $data = Model::select($req);
        if ($data["succeed"]){
            $newKey;
            foreach ($data["data"][0] as $key => $value){
            $newKey = "_".$key;
            $this->$newKey = $value;
            }
        }
    }

    public function addEventOnAll(){
        $public = $this->addPublic();
        $price = $this->addPrice();
        require_once "controller/Image.php";
        $image = new Image("read", ["id" => $this->_image_id]);
        require_once "controller/Location.php";
        $location = new Location("read", ["id" => $this->_location_id]);
        $where = $location->getLocationInfo(["name", "city"]);

        //TO DO : ADD NEW LOCATION ET NEW IMAGE!!!!!!------------------
        return View::makeHtml([
            "{{ event_id }}" => $this->_event_id,
            "{{ image_src }}" => $image->src(),
            "{{ image_alt }}" => $image->alt(),
            "{{ start_weekday }}" => $this->_start_weekday,
            "{{ start_date }}" => $this->_start_date,
            "{{ start_time }}" => $this->_start_time,
            "{{ name }}" => $this->_name,
            "{{ location }}" => ucfirst($where["name"]).", ".ucfirst($where["city"])." ",
            "{{ public }}" => $public,
            "{{ price }}" => $price
        ], "each_event.html");
    }

    public function addPublic(){
        switch ($this->_public){
            //case free
            case 1:
                $public = "for all ages";
                break;
            //case paid
            case 2:
                $public = "for adults";
                break;
            //case donation
            case 3:
                $public = "for children";
                break;
            };
        if ($this->_members_only == true){
            $public .= " - members only";
        }
        return $public;
    }

    public function addPrice(){
        switch ($this->_type_tickets){
            //case no booking
            case 0:
                return "no reservation";
                break;
            //case free
            case 1:
                return "free";
                break;
            //case paid
            case 2:
                return "from $".$this->_smallest_price;
                break;
            //case donation
            case 3:
                return "donations welcome";
                break;
        }
    }

}
