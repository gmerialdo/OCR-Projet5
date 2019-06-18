<?php

class Event
{

    /**
     * @var int $_event_id, string $_name, string $_description, int $_location_id, int $_image_id, string $_category, boolean $_active_event, datetime $_start_datetime, datetime $_finish_datetime, int $_max_tickets, int $_type_tickets, int $_public, boolean $_members_only, decimal $_price_adult_mb, decimal $_price_adult, decimal $_price_child_mb, decimal $_price_child, boolean $_enable_booking: data for the event in database (table evt_events)
     */
    private $_event_id;

    /**
     * @var int $_event_id, string $_name, string $_description, int $_location_id, int $_image_id, string $_category, boolean $_active_event, datetime $_start_datetime, datetime $_finish_datetime, int $_max_tickets, int $_type_tickets, int $_public, boolean $_members_only, decimal $_price_adult_mb, decimal $_price_adult, decimal $_price_child_mb, decimal $_price_child, boolean $_enable_booking: data for the event in database (table evt_events)
     */
    private $_name;

    /**
     * @var int $_event_id, string $_name, string $_description, int $_location_id, int $_image_id, string $_category, boolean $_active_event, datetime $_start_datetime, datetime $_finish_datetime, int $_max_tickets, int $_type_tickets, int $_public, boolean $_members_only, decimal $_price_adult_mb, decimal $_price_adult, decimal $_price_child_mb, decimal $_price_child, boolean $_enable_booking: data for the event in database (table evt_events)
     */
    private $_description;

    /**
     * @var int $_event_id, string $_name, string $_description, int $_location_id, int $_image_id, string $_category, boolean $_active_event, datetime $_start_datetime, datetime $_finish_datetime, int $_max_tickets, int $_type_tickets, int $_public, boolean $_members_only, decimal $_price_adult_mb, decimal $_price_adult, decimal $_price_child_mb, decimal $_price_child, boolean $_enable_booking: data for the event in database (table evt_events)
     */
    private $_location_id;

    /**
     * @var int $_event_id, string $_name, string $_description, int $_location_id, int $_image_id, string $_category, boolean $_active_event, datetime $_start_datetime, datetime $_finish_datetime, int $_max_tickets, int $_type_tickets, int $_public, boolean $_members_only, decimal $_price_adult_mb, decimal $_price_adult, decimal $_price_child_mb, decimal $_price_child, boolean $_enable_booking: data for the event in database (table evt_events)
     */
    private $_image_id;

    /**
     * @var int $_event_id, string $_name, string $_description, int $_location_id, int $_image_id, string $_category, boolean $_active_event, datetime $_start_datetime, datetime $_finish_datetime, int $_max_tickets, int $_type_tickets, int $_public, boolean $_members_only, decimal $_price_adult_mb, decimal $_price_adult, decimal $_price_child_mb, decimal $_price_child, boolean $_enable_booking: data for the event in database (table evt_events)
     */
    private $_category;

    /**
     * @var int $_event_id, string $_name, string $_description, int $_location_id, int $_image_id, string $_category, boolean $_active_event, datetime $_start_datetime, datetime $_finish_datetime, int $_max_tickets, int $_type_tickets, int $_public, boolean $_members_only, decimal $_price_adult_mb, decimal $_price_adult, decimal $_price_child_mb, decimal $_price_child, boolean $_enable_booking: data for the event in database (table evt_events)
     */
    private $_active_event;

    /**
     * @var int $_event_id, string $_name, string $_description, int $_location_id, int $_image_id, string $_category, boolean $_active_event, datetime $_start_datetime, datetime $_finish_datetime, int $_max_tickets, int $_type_tickets, int $_public, boolean $_members_only, decimal $_price_adult_mb, decimal $_price_adult, decimal $_price_child_mb, decimal $_price_child, boolean $_enable_booking: data for the event in database (table evt_events)
     */
    private $_start_datetime;

    /**
     * @var int $_event_id, string $_name, string $_description, int $_location_id, int $_image_id, string $_category, boolean $_active_event, datetime $_start_datetime, datetime $_finish_datetime, int $_max_tickets, int $_type_tickets, int $_public, boolean $_members_only, decimal $_price_adult_mb, decimal $_price_adult, decimal $_price_child_mb, decimal $_price_child, boolean $_enable_booking: data for the event in database (table evt_events)
     */
    private $_finish_datetime;

    /**
     * @var int $_event_id, string $_name, string $_description, int $_location_id, int $_image_id, string $_category, boolean $_active_event, datetime $_start_datetime, datetime $_finish_datetime, int $_max_tickets, int $_type_tickets, int $_public, boolean $_members_only, decimal $_price_adult_mb, decimal $_price_adult, decimal $_price_child_mb, decimal $_price_child, boolean $_enable_booking: data for the event in database (table evt_events)
     */
    private $_max_tickets;

    /**
     * @var int $_event_id, string $_name, string $_description, int $_location_id, int $_image_id, string $_category, boolean $_active_event, datetime $_start_datetime, datetime $_finish_datetime, int $_max_tickets, int $_type_tickets, int $_public, boolean $_members_only, decimal $_price_adult_mb, decimal $_price_adult, decimal $_price_child_mb, decimal $_price_child, boolean $_enable_booking: data for the event in database (table evt_events)
     */
    private $_type_tickets;

    /**
     * @var int $_event_id, string $_name, string $_description, int $_location_id, int $_image_id, string $_category, boolean $_active_event, datetime $_start_datetime, datetime $_finish_datetime, int $_max_tickets, int $_type_tickets, int $_public, boolean $_members_only, decimal $_price_adult_mb, decimal $_price_adult, decimal $_price_child_mb, decimal $_price_child, boolean $_enable_booking: data for the event in database (table evt_events)
     */
    private $_public;

    /**
     * @var int $_event_id, string $_name, string $_description, int $_location_id, int $_image_id, string $_category, boolean $_active_event, datetime $_start_datetime, datetime $_finish_datetime, int $_max_tickets, int $_type_tickets, int $_public, boolean $_members_only, decimal $_price_adult_mb, decimal $_price_adult, decimal $_price_child_mb, decimal $_price_child, boolean $_enable_booking: data for the event in database (table evt_events)
     */
    private $_members_only;


    /**
     * @var int $_event_id, string $_name, string $_description, int $_location_id, int $_image_id, string $_category, boolean $_active_event, datetime $_start_datetime, datetime $_finish_datetime, int $_max_tickets, int $_type_tickets, int $_public, boolean $_members_only, decimal $_price_adult_mb, decimal $_price_adult, decimal $_price_child_mb, decimal $_price_child, boolean $_enable_booking: data for the event in database (table evt_events)
     */
    private $_price_adult_mb;

    /**
     * @var decimal $_price_adult, decimal $_price_child_mb, decimal $_price_child, boolean $_enable_booking: data for the event in database (table evt_events)
     */
    private $_price_adult;

    /**
     * @var  decimal $_price_child_mb, decimal $_price_child, boolean $_enable_booking: data for the event in database (table evt_events)
     */
    private $_price_child_mb;


    /**
     * @var decimal $_price_child: data for the event in database (table evt_events)
     */
    private $_price_child;


    /**
     * @var  boolean $_enable_booking: data for the event in database (table evt_events)
     */
    private $_enable_booking;

    /**
     * @var string $_start_weekday: format %W for the starting date of the event
     * @example 'Thursday'
     */
    private $_start_weekday;

    /**
     * @var string finish_weekday: format %W for the ending date of the event
     * @example 'Thursday'
     */
    private $_finish_weekday;

    /**
     * @var string $_start_date: format %b %D, %Y for the starting date of the event
     * @example 'Jul 18th, 2019'
     */
    private $_start_date;

    /**
     * @var string string finish_date: format %b %D, %Y for the ending date of the event
     * @example 'Jul 18th, 2019'
     */
    private $_finish_date;

    /**
     * @var string $_start_time: format %l:%i%p for the starting date of the event
     * @example '6:00AM'
     */
    private $_start_time;

    /**
     * @var string finish_date: format %b %D, %Y for the  ending date of the event
     * @example 'Jul 18th, 2019'
     */
    private $_finish_time;

    /**
     * @var int $_nb_booked_tickets: number of all booked tickets for this event
     */
    private $_nb_booked_tickets;

    /**
     * @var int $_available_tickets: (only if max_tickets is defined) number of available tickets left calculated with the booked tickets
     */
    private $_nb_available_tickets;

    CONST FIELDS_TO_SET = ['name','description','location_id','image_id','category','active_event','start_datetime','finish_datetime','max_tickets','type_tickets','public','members_only','price_adult_mb','price_adult','price_child_mb','price_child','enable_booking'];

    public function __construct($todo, $args){
        switch ($todo){
            case "read":
                $this->_event_id = $args["id"];
                $this->setEventDataFromDB();
                $this->_nb_booked_tickets = $this->countTickets();
                $this->_nb_available_tickets = $this->calculateAvailableTickets();
                return true;
                break;
            case "create":
                return $this->createEvent($args);
                break;
            case "update":
            //$args consists of an array with id, array of fields and array of data to update in those fields
                $this->_event_id = $args["id"];
                return $this->updateEventInDB(Event::FIELDS_TO_SET, $args["data"]);
                break;
            case "delete":
                $this->_event_id = $args["id"];
                return $this->updateEventInDB(["active_event"], [2]);
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
                'start_datetime',
                'DATE_FORMAT(start_datetime, \'%W\') AS "start_weekday"',
                'DATE_FORMAT(start_datetime, \'%b %D, %Y\') AS "start_date"',
                'DATE_FORMAT(start_datetime, \'%l:%i%p\') AS "start_time"',
                'finish_datetime',
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
            "{{ image_src }}" => $image->getVarImage("_src"),
            "{{ image_alt }}" => $image->getVarImage("_alt"),
            "{{ start_weekday }}" => $this->_start_weekday,
            "{{ start_date }}" => $this->_start_date,
            "{{ start_time }}" => $this->_start_time,
            "{{ start_date_format }}" => date("Y-m-d", strtotime($this->_start_datetime)),
            "{{ finish_weekday }}" => $this->_finish_weekday,
            "{{ finish_date }}" => $this->_finish_date,
            "{{ finish_time }}" => $this->_finish_time,
            "{{ finish_date_format }}" => date("Y-m-d", strtotime($this->_finish_datetime)),
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

    public function countTickets(){
        $req = [
            "fields" => ["ticket_id"],
            "from" => "evt_tickets",
            "where" => [ "event_id = ".$this->_event_id, "cancelled_time IS NULL"]
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
        return $booked_tickets;
    }

    public function calculateAvailableTickets(){
        if (!isset($this->_max_tickets)){
            return null;
        }
        else {
            return $this->_max_tickets - $this->_nb_booked_tickets;
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
        $req = [
            "table"  => "evt_events",
            "fields" => Event::FIELDS_TO_SET
        ];
        $create = Model::insert($req, $data);
        $this->_event_id = $create["data"];
        return $create["succeed"];
    }

    public function setTicketChoice(){
        $tickets_choice = "";
        switch ($this->_type_tickets){
            case 1:
                $tickets_choice .= $this->addOptionTickets("quantity", "nb_tickets_all", "free", "", "");
                break;
            case 2:
                switch ($this->_public){
                    case 1:
                        if (null !== $this->_price_adult_mb){
                            $tickets_choice .= $this->addOptionTickets("adult (member)", "nb_tickets_adult_mb", $this->_price_adult_mb, "$", "price_adult_mb_booked");
                        }
                        if (null !== $this->_price_adult){
                            $tickets_choice .= $this->addOptionTickets("adult", "nb_tickets_adult", $this->_price_adult, "$", "price_adult_booked");
                        }
                        if (null !== $this->_price_child_mb){
                            $tickets_choice .= $this->addOptionTickets("child (member)", "nb_tickets_child_mb", $this->_price_child_mb, "$", "price_child_mb_booked");
                        }
                        if (null !== $this->_price_child){
                            $tickets_choice .= $this->addOptionTickets("child", "nb_tickets_child", $this->_price_child, "$", "price_child_booked");
                        }
                        break;
                    case 2:
                        if (null !== $this->_price_adult_mb){
                            $tickets_choice .= $this->addOptionTickets("adult (member)", "nb_tickets_adult_mb", $this->_price_adult_mb, "$", "price_adult_mb_booked");
                        }
                        if (null !== $this->_price_adult){
                            $tickets_choice .= $this->addOptionTickets("adult", "nb_tickets_adult", $this->_price_adult, "$", "price_adult_booked");
                        }
                        break;
                    case 3:
                        if (null !== $this->_price_child_mb){
                            $tickets_choice .= $this->addOptionTickets("child (member)", "nb_tickets_child_mb", $this->_price_child_mb, "$", "price_child_mb_booked");
                        }
                        if (null !== $this->_price_child){
                            $tickets_choice .= $this->addOptionTickets("child", "nb_tickets_child", $this->_price_child, "$", "price_child_booked");
                        }
                        break;
                }
                break;
            case 3:
                $tickets_choice .= $this->addOptionTickets("quantity", "nb_tickets_all", "donation welcome", "", "");
                $tickets_choice .= file_get_contents("template/elt_nb_tickets_donation.html");
                break;
        }
        return $tickets_choice;
    }

    public function addOptionTickets($type, $name, $price, $sign, $price_booked){
        return View::makeHtml([
            "{{ type_ticket }}" => $type,
            "{{ name_ticket }}" => $name,
            "{{ price_ticket }}" => $price,
            "{{ dollar_sign }}" => $sign,
            "{{ price_booked }}" => $price_booked
        ],"elt_nb_tickets.html");
    }

}
