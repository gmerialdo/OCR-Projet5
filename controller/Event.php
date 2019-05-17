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
                'DATE_FORMAT(start_datetime, \'%l:%i%p\') AS "start_time"',
                'DATE_FORMAT(finish_datetime, \'%W\') AS "finish_weekday"',
                'DATE_FORMAT(finish_datetime, \'%b %D, %Y\') AS "finish_date"',
                'DATE_FORMAT(finish_datetime, \'%l:%i%p\') AS "finish_time"',
                'category',
                'max_tickets',
                'type_tickets',
                'public',
                'members_only',
                'price_adult_member',
                'price_adult',
                'price_child_member',
                'price_child',
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

    public function getVarEvent($_var){
        return $this->$_var;
    }

    public function getEventData(){
        require_once "controller/Image.php";
        $image = new Image("read", ["id" => $this->_image_id]);
        require_once "controller/Location.php";
        $location = new Location("read", ["id" => $this->_location_id]);
        return [
            "{{ event_id }}" => $this->_event_id,
            "{{ image_src }}" => $image->src(),
            "{{ image_alt }}" => $image->alt(),
            "{{ start_weekday }}" => $this->_start_weekday,
            "{{ start_date }}" => $this->_start_date,
            "{{ start_time }}" => $this->_start_time,
            "{{ finish_weekday }}" => $this->_finish_weekday,
            "{{ finish_date }}" => $this->_finish_date,
            "{{ finish_time }}" => $this->_finish_time,
            "{{ name }}" => $this->_name,
            "{{ description }}" => $this->_description,
            "{{ location }}" => ucfirst($location->getVarLocation("_name")),
            "{{ location_city }}" => ucfirst($location->getVarLocation("_city")),
            "{{ location_adress }}" => $location->getVarLocation("_address").", ".ucfirst($location->getVarLocation("_city")).", ".ucfirst($location->getVarLocation("_state"))." ".$location->getVarLocation("_zipcode").", ".ucfirst($location->getVarLocation("_country")),
            "{{ public }}" => $this->addPublic(),
            "{{ price }}" => $this->addPrice(true),
            "{{ prices }}" => $this->addPrice()
        ];
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

    public function addPrice($smallestOnly = false){
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
                if ($smallestOnly){
                    $smallest_price = min(array_filter([$this->_price_child, $this->_price_adult, $this->_price_child_member, $this->_price_adult_member], function ($v){ return !is_null($v); }));
                    return "starts at $".$smallest_price;
                break;
                }
                else {
                    $prices ="";
                    if ($this->_price_child) $prices .= "Child : $".$this->_price_child."<br/>";
                    if ($this->_price_child_member) $prices .= "Child (member): $".$this->_price_child_member."<br/>";
                    if ($this->_price_adult) $prices .= "Adult : $".$this->_price_adult."<br/>";
                    if ($this->_price_adult_member) $prices .= "Adult (member): $".$this->_price_adult_member."<br/>";
                    return $prices;
                    break;
                }

            //case donation
            case 3:
                return "donations welcome";
                break;
        }
    }

}
