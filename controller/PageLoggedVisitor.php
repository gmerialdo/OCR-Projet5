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
                    alert("You already booked tickets for this event.");
                    window.location.href="../my_tickets";
                </script>
                <?php
            }
            else {
                require_once "controller/Event.php";
                $event = new Event("read", ["id" => $this->_url[1]]);
                $tickets_choice = $event->setTicketChoice();
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
                    "{{ action }}" => "logged/save_tickets",
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

    public function save_tickets(){
        global $session;
        if (!$this->postEmpty() && (!empty($session->get("user_name")))){
            foreach($_POST as $key => $value) {
                $data[$key] = $this->getPostSanitizeFloat($key);
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
                    var link = '<?php echo "../logged/book_tickets/".$data["event_id"];?>';
                    alert("No tickets selected. Please indicate the number of tickets you want to book.";
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
                            var link = '<?php echo "../logged/book_tickets/".$data["event_id"];?>';
                            alert("Not enough tickets available.");
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
                        alert("Your tickets are booked!");
                        window.location.href="../logged/my_tickets";
                    </script>
                    <?php
                }
                else {
                    header('Location: ../display_error');
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

    /*-------------------------------------------MANAGE ACCOUNT SETTINGS--------------------------------------------*/

    public function account_settings(){
        global $session;
        if (!$this->postEmpty()){
            $password = filter_input(INPUT_POST, "password", FILTER_SANITIZE_SPECIAL_CHARS,FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
            $account = new Account("read", ["id" => $session->get("evt_account_id")]);
            $hash = hash("sha256", $password);
            if ($hash == $account->getVarAccount("_password")){
                $new_pw = filter_input(INPUT_POST, "new_password", FILTER_SANITIZE_SPECIAL_CHARS,FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);;
                $update = $account->updateAccountInDB(["password" => hash("sha256", $new_pw)]);
                if ($update){
                    ?>
                    <script>
                        alert("Your password has been updated.");
                        window.location.href="see_all_events";
                    </script>
                    <?php
                }
                else {
                    header('Location: ../display_error');
                }
            }
            else {
                ?>
                <script>
                    alert("Wrong password.");
                    window.location.href="account_settings";
                </script>
                <?php
            }
        }
        else {
            $req = [
                "fields" => ["*"],
                "from" => "evt_accounts",
                "where" => ["evt_account_id = ".$session->get("evt_account_id")],
                "limit" => 1
            ];
            $data = Model::select($req);
            $content = View::makeHtml([
                "{{ first_name }}" => $data["data"][0]["first_name"],
                "{{ last_name }}" => $data["data"][0]["last_name"],
                "{{ email }}" => $data["data"][0]["email"]
            ], "content_account_settings.html");
            return ["Account settings", $content];
        }
    }

}
