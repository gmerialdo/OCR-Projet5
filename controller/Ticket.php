<?php

class Ticket
{

    private $_ticket_id;
    private $_event_id;
    private $_evt_account_id;
    private $_time_booked;
    private $_nb_tickets_adult_mb;
    private $_nb_tickets_adult;
    private $_nb_tickets_child_mb;
    private $_nb_tickets_child;
    private $_nb_tickets_all;
    private $_price_adult_mb_booked;
    private $_price_adult_booked;
    private $_price_child_mb_booked;
    private $_price_child_booked;
    private $_donation;
    private $_total_to_pay;
    private $_payment_time;
    private $_cancelled_time;
    private $_total_nb_tickets;

    public function __construct($todo, $args){
        switch ($todo){
            case "read":
                $this->_ticket_id = $args["id"];
                $this->setTicketDataFromDB();
                $this->_total_nb_tickets = $this->calculateTotalNbTickets();
                break;
            case "create":
                return $this->createTicket($args);
                break;
        }
    }

    public function getVarTicket($_var){
        return $this->$_var;
    }

    public function setTicketDataFromDB(){
        $req = [
            "fields" => [
                'ticket_id',
                'event_id',
                'evt_account_id',
                'time_booked',
                'nb_tickets_adult_mb',
                'nb_tickets_adult',
                'nb_tickets_child_mb',
                'nb_tickets_child',
                'nb_tickets_all',
                'price_adult_mb_booked',
                'price_adult_booked',
                'price_child_mb_booked',
                'price_child_booked',
                'donation',
                'total_to_pay',
                'payment_time',
                'cancelled_time',
            ],
            "from" => "evt_tickets",
            "where" => [ "ticket_id = ".$this->_ticket_id],
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

    public function getTicketData(){
        $tickets = "";
        $total = "";
        if ($this->_nb_tickets_all != null){
            $tickets .= "Tickets : ".$this->_nb_tickets_all."<br/>";
            if ($this->_donation != null){
                $total .= "Donation welcome. Willing to donate: $".$this->_donation;
            }
            else {
                $total .= "Free event";
            }
        }
        else {
            if ($this->_nb_tickets_adult_mb != null && $this->_nb_tickets_adult_mb != 0){
                $tickets .= "Tickets - adult (member) : ".$this->_nb_tickets_adult_mb." - Price: $".$this->_price_adult_mb_booked."<br/>";
            }
            if ($this->_nb_tickets_adult != null && $this->_nb_tickets_adult != 0){
                $tickets .= "Tickets - adult : ".$this->_nb_tickets_adult." - Price: $".$this->_price_adult_booked."<br/>";
            }
            if ($this->_nb_tickets_child_mb != null && $this->_nb_tickets_child_mb != 0){
                $tickets .= "Tickets - child (member) : ".$this->_nb_tickets_child_mb." - Price: $".$this->_price_child_mb_booked."<br/>";
            }
            if ($this->_nb_tickets_child != null && $this->_nb_tickets_child != 0){
                $tickets .= "Tickets - child : ".$this->_nb_tickets_child." - Price: $".$this->_price_child_booked."<br/>";
            }
            $total = "You need to pay: $".$this->_total_to_pay;
        }
        return [
            "{{ ticket_id }}" => $this->_ticket_id,
            "{{ event_id }}" => $this->_event_id,
            "{{ tickets }}" => $tickets,
            "{{ total }}" => $total
        ];
    }

    public function createTicket($args){
        if (!isset($args["nb_tickets_adult_mb"])) $args["nb_tickets_adult_mb"] = NULL;
        if (!isset($args["nb_tickets_adult"])) $args["nb_tickets_adult"] = NULL;
        if (!isset($args["nb_tickets_child_mb"])) $args["nb_tickets_child_mb"] = NULL;
        if (!isset($args["nb_tickets_child"])) $args["nb_tickets_child"] = NULL;
        if (!isset($args["nb_tickets_all"])) $args["nb_tickets_all"] = NULL;
        if (!isset($args["price_adult_mb_booked"])) $args["price_adult_mb_booked"] = NULL;
        if (!isset($args["price_adult_booked"])) $args["price_adult_booked"] = NULL;
        if (!isset($args["price_child_mb_booked"])) $args["price_child_mb_booked"] = NULL;
        if (!isset($args["price_child_booked"])) $args["price_child_booked"] = NULL;
        if (!isset($args["donation"])) $args["donation"] = NULL;
        foreach ($args as $key => $value){
            $newKey = "_".$key;
            $this->$newKey = $value;
        }
        $data = [
            $this->_event_id,
            $this->_evt_account_id,
            $this->_nb_tickets_adult_mb,
            $this->_nb_tickets_adult,
            $this->_nb_tickets_child_mb,
            $this->_nb_tickets_child,
            $this->_nb_tickets_all,
            $this->_price_adult_mb_booked,
            $this->_price_adult_booked,
            $this->_price_child_mb_booked,
            $this->_price_child_booked,
            $this->_donation,
            $this->calculateTotal()
        ];
        $req = [
            "table"  => "evt_tickets",
            "fields" => [
                'event_id',
                'evt_account_id',
                'nb_tickets_adult_mb',
                'nb_tickets_adult',
                'nb_tickets_child_mb',
                'nb_tickets_child',
                'nb_tickets_all',
                'price_adult_mb_booked',
                'price_adult_booked',
                'price_child_mb_booked',
                'price_child_booked',
                'donation',
                'total_to_pay'
            ]
        ];
        $create = Model::insert($req, $data);
        return $create["succeed"];
    }

    public function calculateTotal(){
        if ($this->_nb_tickets_all != NULL){
            $total = 0;
        }
        if ($this->_donation != NULL){
            $total = $this->_donation;
        }
        else {
        $total = ($this->_nb_tickets_adult_mb * $this->_price_adult_mb_booked)
            + ($this->_nb_tickets_adult * $this->_price_adult_booked)
            + ($this->_nb_tickets_child_mb * $this->_price_child_mb_booked)
            + ($this->_nb_tickets_child * $this->_price_child_booked);
        }
        return $total;
    }

    public static function alreadyBookedTickets($event_id){
        $req = [
                "fields" => ["*"],
                "from" => "evt_tickets",
                "where" => [
                    "event_id ='$event_id'",
                    "evt_account_id = ".$_SESSION["evt_account_id"],
                    "cancelled_time is NULL"
                ]
        ];
        $data = Model::select($req);
        //return true if not empty or false otherwise
        return !empty($data["data"]);
    }

    public function calculateTotalNbTickets(){
        $total = 0;
        if (isset($this->_nb_tickets_adult_mb)) $total += $this->_nb_tickets_adult_mb;
        if (isset($this->_nb_tickets_adult)) $total += $this->_nb_tickets_adult;
        if (isset($this->_nb_tickets_child_mb)) $total += $this->_nb_tickets_child_mb;
        if (isset($this->_nb_tickets_child)) $total += $this->_nb_tickets_child;
        if (isset($this->_nb_tickets_all)) $total += $this->_nb_tickets_all;
        return $total;
    }

}
