<?php

class Event
{

    private $_event_id;
    private $_name;
    private $_description;
    private $_location_id;
    private $_image_id;
    private $_category;
    private $_active_event;
    private $_start_datetime;
    private $_start_weekday;
    private $_start_date;
    private $_start_time;
    private $_finish_datetime;
    private $_finish_weekday;
    private $_finish_date;
    private $_finish_time;
    private $_max_tickets;
    private $_type_tickets;
    private $_public;
    private $_members_only;
    private $_price_adult_mb;
    private $_price_adult;
    private $_price_child_mb;
    private $_price_child;
    private $_enable_booking;
    private $_nb_available_tickets;
    private $_error;

    public function __construct($todo, $args){
        switch ($todo){
            case "read":
                $this->_event_id = $args["id"];
                $this->setEventDataFromDB();
                $this->_nb_available_tickets = $this->calculateAvailableTickets();
                break;
            case "create":
                return $this->createEvent($args);
                break;
            case "update":
            //$args consists of an array with id, array of fields and array of data to update in those fields
                $this->_event_id = $args["id"];
                $this->updateEventInDB($args["fields"], $args["data"]);
                break;
        }
    }

    public function setEventDataFromDB(){
        $req = [
            "fields" => [
                'name',
                'description',
                'location_id',
                'image_id',
                'category',
                'active_event',
                'DATE_FORMAT(start_datetime, \'%W\') AS "start_weekday"',
                'DATE_FORMAT(start_datetime, \'%b %D, %Y\') AS "start_date"',
                'DATE_FORMAT(start_datetime, \'%l:%i%p\') AS "start_time"',
                'DATE_FORMAT(finish_datetime, \'%W\') AS "finish_weekday"',
                'DATE_FORMAT(finish_datetime, \'%b %D, %Y\') AS "finish_date"',
                'DATE_FORMAT(finish_datetime, \'%l:%i%p\') AS "finish_time"',
                'max_tickets',
                'type_tickets',
                'public',
                'members_only',
                'price_adult_mb',
                'price_adult',
                'price_child_mb',
                'price_child',
                'enable_booking'
            ],
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
        if ($this->_enable_booking){$enabled = "Open for booking";} else {$enabled = "Booking disabled";}
        return [
            "{{ event_id }}" => $this->_event_id,
            "{{ image_id }}" => $this->_image_id,
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
            "{{ location_id }}" => $this->_location_id,
            "{{ location }}" => ucfirst($location->getVarLocation("_name")),
            "{{ location_city }}" => ucfirst($location->getVarLocation("_city")),
            "{{ location_adress }}" => $location->getVarLocation("_address").", ".ucfirst($location->getVarLocation("_city")).", ".ucfirst($location->getVarLocation("_state"))." ".$location->getVarLocation("_zipcode").", ".ucfirst($location->getVarLocation("_country")),
            "{{ public }}" => $this->_public,
            "{{ public_txt }}" => $this->addPublic(),
            "{{ price }}" => $this->addPrice(false, true),
            "{{ prices }}" => $this->addPrice(false),
            "{{ prices_inline }}" => $this->addPrice(true, false),
            "{{ price_adult_mb }}" => $this->_price_adult_mb,
            "{{ price_adult }}" => $this->_price_adult,
            "{{ price_child_mb }}" => $this->_price_child_mb,
            "{{ price_child }}" => $this->_price_child,
            "{{ enable_booking }}" => $this->_enable_booking,
            "{{ enabled }}" => $enabled,
            "{{ active_event }}" => $this->_active_event,
            "{{ type_tickets }}" => $this->_type_tickets,
            "{{ members_only }}" => $this->_members_only,
            "{{ max_tickets }}" => $this->_max_tickets,
            "{{ category }}" => $this->_category
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

    public function addPrice($inline, $smallestOnly = false){
        switch ($this->_type_tickets){
            //case no booking
            case 0:
                return "free - no reservation";
                break;
            //case free
            case 1:
                return "free";
                break;
            //case paid
            case 2:
                if ($smallestOnly){
                    $smallest_price = min(array_filter([$this->_price_child, $this->_price_adult, $this->_price_child_mb, $this->_price_adult_mb], function ($v){ return !is_null($v); }));
                    return "starts at $".$smallest_price;
                break;
                }
                else {
                    $prices ="";
                    if ($this->_price_child) $prices .= "Child : $".$this->_price_child." ";
                    if (!$inline){$prices .= "<br/>";}
                    if ($this->_price_child_mb) $prices .= "Child (member): $".$this->_price_child_mb." ";
                    if (!$inline){$prices .= "<br/>";}
                    if ($this->_price_adult) $prices .= "Adult : $".$this->_price_adult." ";
                    if (!$inline){$prices .= "<br/>";}
                    if ($this->_price_adult_mb) $prices .= "Adult (member): $".$this->_price_adult_mb." ";
                    return $prices;
                    break;
                }

            //case donation
            case 3:
                return "free - donations welcome";
                break;
        }
    }

    public function calculateAvailableTickets(){
        if (!isset($this->_max_tickets)){
            return null;
        }
        else {
            $req = [
                "fields" => ["ticket_id"],
                "from" => "evt_tickets",
                "where" => [ "event_id = ".$this->_event_id]
            ];
            $data = Model::select($req);
            if ($data["succeed"]){
                $booked_tickets = 0;
                if (isset($data["data"][0])){
                    $ticket;
                    foreach ($data["data"] as $row) {
                        require_once("controller/Ticket.php");
                        $ticket = new Ticket("read", ["id" => $row["ticket_id"]]);
                        $booked_tickets += $ticket->getVarTicket("_total_nb_tickets");
                    }
                }
            }
            return $this->_max_tickets - $booked_tickets;
        }
    }

    public function updateEventInDB($fields, $data){
        $req = [
            "table"  => "evt_events",
            "fields" => $fields,
            "where" => ["event_id = ".$this->_event_id],
            "limit" => 1
        ];
        $update = Model::update($req, $data);
        return $update["succeed"];
    }

    public function createEvent($data){
        $data = array_slice($data, 0, 17);
        $req = [
            "table"  => "evt_events",
            "fields" => [
                'name',
                'description',
                'location_id',
                'image_id',
                'category',
                'active_event',
                'start_datetime',
                'finish_datetime',
                'max_tickets',
                'type_tickets',
                'public',
                'members_only',
                'price_adult_mb',
                'price_adult',
                'price_child_mb',
                'price_child',
                'enable_booking'
            ]
        ];
        $create = Model::insert($req, $data);
        $this->_event_id = $create["data"];
        return $create["succeed"];
    }
}
