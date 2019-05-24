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
            $_SESSION["admin_mode"] = false;
            return Page::getPage();
        }
        else {
            return $this->login();
        }
    }

    public function book_tickets(){
        if (!isset($this->_url[1])){
            header('Location: see_all_events');
        }
        else {
            require_once("controller/event.php");
            $event = new Event("read", ["id" => $this->_url[1]]);
            $tickets_choice = "";
            switch ($event->getVarEvent("_type_tickets")){
                case 1:
                    $tickets_choice .= $this->addOptionTickets("quantity", "nb_tickets_all", "free", "", "");
                    break;
                case 2:
                    switch ($event->getVarEvent("_public")){
                        case 1:
                            if (null !== $event->getVarEvent("_price_adult_member")){
                                $tickets_choice .= $this->addOptionTickets("adult (member)", "nb_tickets_adult_mb", $event->getVarEvent("_price_adult_member"), "$", "price_adult_mb_booked");
                            }
                            if (null !== $event->getVarEvent("_price_adult")){
                                $tickets_choice .= $this->addOptionTickets("adult", "nb_tickets_adult", $event->getVarEvent("_price_adult"), "$", "price_adult_booked");
                            }
                            if (null !== $event->getVarEvent("_price_child_member")){
                                $tickets_choice .= $this->addOptionTickets("child (member)", "nb_tickets_child_mb", $event->getVarEvent("_price_child_member"), "$", "price_child_mb_booked");
                            }
                            if (null !== $event->getVarEvent("_price_child")){
                                $tickets_choice .= $this->addOptionTickets("child", "nb_tickets_child", $event->getVarEvent("_price_child"), "$", "price_child_booked");
                            }
                            break;
                        case 2:
                            if (null !== $event->getVarEvent("_price_adult_member")){
                                $tickets_choice .= $this->addOptionTickets("adult (member)", "nb_tickets_adult_mb", $event->getVarEvent("_price_adult_member"), "$", "price_adult_mb_booked");
                            }
                            if (null !== $event->getVarEvent("_price_adult")){
                                $tickets_choice .= $this->addOptionTickets("adult", "nb_tickets_adult", $event->getVarEvent("_price_adult"), "$", "price_adult_booked");
                            }
                            break;
                        case 3:
                            if (null !== $event->getVarEvent("_price_child_member")){
                                $tickets_choice .= $this->addOptionTickets("child (member)", "nb_tickets_child_mb", $event->getVarEvent("_price_child_member"), "$", "price_child_mb_booked");
                            }
                            if (null !== $event->getVarEvent("_price_child")){
                                $tickets_choice .= $this->addOptionTickets("child", "nb_tickets_child", $event->getVarEvent("_price_child"), "$", "price_child_booked");
                            }
                            break;
                    }
                    break;
                case 3:
                    $tickets_choice .= $this->addOptionTickets("quantity", "nb_tickets_all", "donation welcome", "", "");
                    $tickets_choice .= file_get_contents("template/elt_nb_tickets_donation.html");
                    break;
            }
            $content = View::makeHtml([
                "{{ event_id }}" => $event->getVarEvent("_event_id"),
                "{{ event_name }}" => $event->getVarEvent("_name"),
                "{{ tickets_choice }}" => $tickets_choice
            ], "content_book_tickets.html");
            return ["Book tickets", $content];
        }
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

    public function bookingConfirm(){
        if (!empty($_POST) && (!empty($_SESSION["user_name"]))){
            foreach($_POST as $key => $value) {
                $data[$key] = filter_input(INPUT_POST, $key, FILTER_VALIDATE_INT);
            }
            $data["evt_account_id"] = $_SESSION["evt_account_id"];
            // require_once("controller/Event.php"); ---------------- a enlever ou pas???????????????????
            // $event = new Event("read", ["id" => $data["event_id"]]);
            // $data["price_adult_mb_booked"] = $event->getVarEvent("_price_adult_member");
            // $data["price_adult_booked"] = $event->getVarEvent("_price_adult");
            // $data["price_child_mb_booked"] = $event->getVarEvent("_price_child_member");
            // $data["price_child_booked"] = $event->getVarEvent("_price_child");
            require_once("controller/Ticket.php");
            $new_ticket = new Ticket("create", $data);
            if ($new_ticket){
                ?>
                <script>
                    var msg = '<?php echo "Your tickets are booked!";?>';
                    var link = '<?php echo "../logged/my_tickets";?>';
                    alert(msg);
                    window.location.href=link;
                </script>
                <?php
            }
        }
        else {

        }
    }

    public function my_tickets(){
        $req = [
            "fields" => ["*"],
            "from" => "evt_tickets",
            "where" => ["evt_account_id = ".$_SESSION["evt_account_id"]],
            "order" => "time_booked"
        ];
        $data = Model::select($req);
        //if no tickets
        $my_tickets = "";
        if (!isset($data["data"][0])){
            $title = "No tickets booked";
        }
        else {
            $title ="Your tickets";
            $each_ticket;
            foreach ($data["data"] as $row){
                require_once("controller/Ticket.php");
                $each_ticket = new Ticket("read", ["id" => $row["ticket_id"]]);
                $my_tickets .= "";//View::makeHtml($each_ticket->getTicketData(), "elt_each_ticket.html");
            }
        }
        $content = View::makeHtml([
            "{{ title }}" => $title,
            "{{ my_tickets }}" => $my_tickets
        ], "content_see_my_tickets.html");

        return ["My tickets", $content];
    }

    public function account_settings(){

    }


}
