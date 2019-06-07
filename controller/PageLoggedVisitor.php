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
        global $session;
        //check if visitor or admin rights
        if ($this->_rights == "logged_visitor" OR $this->_rights == "admin"){
            $session->add("admin_mode", false);
            return Page::getPage();
        }
        else {
            return $this->login();
        }
    }


    /*-------------------------------------------BOOK TICKETS------------------------------------------------*/

    public function book_tickets(){
        if (!isset($this->_url[1])){
            header('Location: see_all_events');
        }
        else {
            //if user has already tickets booked for this event
            require_once "controller/Ticket.php";
            if (Ticket::alreadyBookedTickets($this->_url[1])){
                ?>
                <script>
                    var msg = '<?php echo "You already booked tickets for this event.";?>';
                    var link = '<?php echo "../my_tickets";?>';
                    alert(msg);
                    window.location.href=link;
                </script>
                <?php
            }
            else {
                require_once "controller/Event.php";
                $event = new Event("read", ["id" => $this->_url[1]]);
                $tickets_choice = "";
                switch ($event->getVarEvent("_type_tickets")){
                    case 1:
                        $tickets_choice .= $this->addOptionTickets("quantity", "nb_tickets_all", "free", "", "");
                        break;
                    case 2:
                        switch ($event->getVarEvent("_public")){
                            case 1:
                                if (null !== $event->getVarEvent("_price_adult_mb")){
                                    $tickets_choice .= $this->addOptionTickets("adult (member)", "nb_tickets_adult_mb", $event->getVarEvent("_price_adult_mb"), "$", "price_adult_mb_booked");
                                }
                                if (null !== $event->getVarEvent("_price_adult")){
                                    $tickets_choice .= $this->addOptionTickets("adult", "nb_tickets_adult", $event->getVarEvent("_price_adult"), "$", "price_adult_booked");
                                }
                                if (null !== $event->getVarEvent("_price_child_mb")){
                                    $tickets_choice .= $this->addOptionTickets("child (member)", "nb_tickets_child_mb", $event->getVarEvent("_price_child_mb"), "$", "price_child_mb_booked");
                                }
                                if (null !== $event->getVarEvent("_price_child")){
                                    $tickets_choice .= $this->addOptionTickets("child", "nb_tickets_child", $event->getVarEvent("_price_child"), "$", "price_child_booked");
                                }
                                break;
                            case 2:
                                if (null !== $event->getVarEvent("_price_adult_mb")){
                                    $tickets_choice .= $this->addOptionTickets("adult (member)", "nb_tickets_adult_mb", $event->getVarEvent("_price_adult_mb"), "$", "price_adult_mb_booked");
                                }
                                if (null !== $event->getVarEvent("_price_adult")){
                                    $tickets_choice .= $this->addOptionTickets("adult", "nb_tickets_adult", $event->getVarEvent("_price_adult"), "$", "price_adult_booked");
                                }
                                break;
                            case 3:
                                if (null !== $event->getVarEvent("_price_child_mb")){
                                    $tickets_choice .= $this->addOptionTickets("child (member)", "nb_tickets_child_mb", $event->getVarEvent("_price_child_mb"), "$", "price_child_mb_booked");
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
                if (null !== $event->getVarEvent("_nb_available_tickets")){
                    $nb_available_tickets = $event->getVarEvent("_nb_available_tickets");
                }
                else {
                    $nb_available_tickets = "";
                }
                $content = View::makeHtml([
                    "{{ event_id }}" => $event->getVarEvent("_event_id"),
                    "{{ event_name }}" => $event->getVarEvent("_name"),
                    "{{ tickets_choice }}" => $tickets_choice,
                    "{{ action }}" => "logged/bookingConfirm",
                    "{{ title }}" => "Book your tickets",
                    "{{ btn_action }}" => "Book tickets",
                    "{{ nb_available_tickets }}" => $nb_available_tickets,
                    "{{ nb_tickets_adult_mb }}" => 0,
                    "{{ nb_tickets_adult }}" => 0,
                    "{{ nb_tickets_culid_mb }}" => 0,
                    "{{ nb_tickets_child }}" => 0,
                    "{{ nb_tickets_all }}" => 0,
                    "{{ donation }}" => ""
                ], "content_book_tickets.html");
                return ["Book tickets", $content];
            }
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
        global $session;
        if (!empty($_POST) && (!empty($session->get("user_name")))){
            foreach($_POST as $key => $value) {
                $data[$key] = filter_input(INPUT_POST, $key, FILTER_VALIDATE_INT);
            }
            $data["evt_account_id"] = $session->get("evt_account_id");
            // if not enough tickets left
            $nb_tickets_wanted = 0;
            if (isset($data["nb_tickets_adult_mb"])) $nb_tickets_wanted += $data["nb_tickets_adult_mb"];
            if (isset($data["nb_tickets_adult"])) $nb_tickets_wanted += $data["nb_tickets_adult"];
            if (isset($data["nb_tickets_child_mb"])) $nb_tickets_wanted += $data["nb_tickets_child_mb"];
            if (isset($data["nb_tickets_child"])) $nb_tickets_wanted += $data["nb_tickets_child"];
            if (isset($data["nb_tickets_all"])) $nb_tickets_wanted += $data["nb_tickets_all"];
            if ($nb_tickets_wanted == 0){
                ?>
                <script>
                    var msg = '<?php echo "No tickets selected. Please indicate the number of tickets you want to book.";?>';
                    var link = '<?php echo "../logged/book_tickets/".$data["event_id"];?>';
                    alert(msg);
                    window.location.href=link;
                </script>
                <?php
            }
            else {
                if (!empty($data["nb_available_tickets"])){
                    if ($data["nb_available_tickets"] < $nb_tickets_wanted){
                        echo "data=".$data["nb_available_tickets"];
                        ?>
                        <script>
                            var msg = '<?php echo "Not enough tickets available.";?>';
                            var link = '<?php echo "../logged/book_tickets/".$data["event_id"];?>';
                            alert(msg);
                            window.location.href=link;
                        </script>
                        <?php
                    }
                }
                require_once "controller/Ticket.php";
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
        }
        else {
            header('Location: see_all_events');
        }
    }


    /*-------------------------------------------SEE TICKETS------------------------------------------------*/

    public function my_tickets(){
        global $session;
        $req = [
            "fields" => ["*"],
            "from" => "evt_tickets AS t",
            "join" => "evt_events AS e",
            "on" => "t.event_id = e.event_id",
            "where" => [
                "t.evt_account_id = ".$session->get("evt_account_id"),
                "e.finish_datetime > NOW()",
                "t.cancelled_time is NULL"
            ],
            "order" => "e.start_datetime"
        ];
        $data = Model::select($req);
        $my_tickets = "";
        //if no tickets
        if (!isset($data["data"][0])){
            $title = "No tickets booked";
        }
        else {
            $title ="Your tickets";
            $each_ticket;
            foreach ($data["data"] as $row){
                require_once "controller/Ticket.php";
                $each_ticket = new Ticket("read", ["id" => $row["ticket_id"]]);
                $data = $each_ticket->getTicketData();
                $data["{{ evt_name }}"] = $row["name"];
                $my_tickets .= View::makeHtml($data,"elt_each_ticket.html");
            }
        }
        $content = View::makeHtml([
            "{{ title }}" => $title,
            "{{ my_tickets }}" => $my_tickets
        ], "content_see_my_tickets.html");

        return ["My tickets", $content];
    }


    /*-------------------------------------------TO DO LATER-----------------------------------------------------*/
    /*-------------------------------------------MANAGE ACCOUNT SETTINGS--------------------------------------------*/

    public function account_settings(){

    }

}
