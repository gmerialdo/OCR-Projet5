<?php

require_once "controller/Page.php";

class PageAdmin extends Page
{

    public function __construct($url){
        $url = array_slice($url, 1);
        Page::__construct($url);
        $this->_defaultPage = "dashboard";/////////TO CHANGE
    }

    //adds a complement before using parent::getPage() to securize all the admin interface: only connect if logged!
    public function getPage(){
        global $session;
        //check if no admin rights
        if ($this->_rights != "admin"){
            header('Location: see_all_events');
        }
        //else the user is logged in so go to the page in admin interface
        else {
            $session->add("admin_mode", true);
            return Page::getPage();
        }
    }

    public function dashboard(){
        $content = file_get_contents("template/content_admin_dashboard.html");
        return ["dashboard", $content];
    }

    /*-------------------------------------------MANAGING EVENTS----------------------------------------------------*/

    public function create_event(){
        $content = View::makeHtml([
            "{{ name }}" => "",
            "{{ description }}" => "",
            "{{ location_id }}" => 1,
            "{{ image_id }}" => 1,
            "{{ category }}" => "",
            "{{ active_event }}" => 1,
            "{{ start_date }}" => "",
            "{{ start_time }}" => "",
            "{{ finish_date }}" => "",
            "{{ finish_time }}" => "",
            "{{ type_tickets }}" => 0,
            "{{ public }}" => 1,
            "{{ members_only }}" => 0,
            "{{ max_tickets }}" => "",
            "{{ price_adult_mb }}" => "",
            "{{ price_adult }}" => "",
            "{{ price_child_mb }}" => "",
            "{{ price_child }}" => "",
            "{{ enable_booking }}" => 1,
            "{{ create_evt_error_msg }}" => "",
            "{{ title }}" => "Create a new event",
            "{{ action }}" => "save_event",
            "{{ button }}" => "Create the event"
        ], "content_admin_create_event.html");
        return ["Create event", $content];
    }

    public function save_event(){
        if (!$this->postEmpty()){
            $name = filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
            $name = ucfirst($name);
            $description = filter_input(INPUT_POST, "description", FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
            $description = ucfirst($description);
            $location_id = $this->getPostSanitizeInt("location_id");
            $image_id = $this->getPostSanitizeInt("image_id");
            $category = filter_input(INPUT_POST, "category", FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
            if ($this->postEmptyKey("active_event")){$active_event = 0;} else {$active_event = 1;}
            $start_date = $this->getPost("start_date");
            $start_time = $this->getPost("start_time");
            $finish_date = $this->getPost("finish_date");
            $finish_time = $this->getPost("finish_time");
            $start_datetime = date("Y-m-d H:i:s", strtotime($start_date." ".$start_time));
            $finish_datetime = date("Y-m-d H:i:s", strtotime($finish_date." ".$finish_time));
            if ($this->postEmptyKey("type_tickets")){$type_tickets = 0;} else {$type_tickets = $this->getPostSanitizeInt("type_tickets");}
            if ($this->postEmptyKey("public")){$public = 1;} else {$public = $this->getPostSanitizeInt("public");}
            if ($this->postEmptyKey("members_only")){$members_only = 0; $price_adult=null; $price_child=null;} else {$members_only = 1;}
            if ($this->postEmptyKey("max_tickets")){$max_tickets = null;} else {$max_tickets = $this->getPostSanitizeInt("max_tickets");}
            if ($this->postEmptyKey("price_adult_mb")){$price_adult_mb = null;} else {$price_adult_mb = $this->getPostSanitizeInt("price_adult_mb");}
            if ($this->postEmptyKey("price_adult")){$price_adult = null;} else {$price_adult =$this->getPostSanitizeInt("price_adult");}
            if ($this->postEmptyKey("price_child_mb")){$price_child_mb = null;} else {$price_child_mb = $this->getPostSanitizeInt("price_child_mb");}
            if ($this->postEmptyKey("price_child")){$price_child = null;} else {$price_child = $this->getPostSanitizeInt("price_child");}
            if ($this->postEmptyKey("enable_booking")){$enable_booking = 0;} else {$enable_booking = 1;}
            //check if date is correct
            if ($start_datetime > $finish_datetime){
                $args = [
                    "{{ name }}" => $name,
                    "{{ description }}" => $description,
                    "{{ location_id }}" => $location_id,
                    "{{ image_id }}" => $image_id,
                    "{{ category }}" => $category,
                    "{{ active_event }}" => $active_event,
                    "{{ start_date }}" => $start_date,
                    "{{ start_time }}" => $start_time,
                    "{{ finish_date }}" => $finish_date,
                    "{{ finish_time }}" => $finish_time,
                    "{{ type_tickets }}" => $type_tickets,
                    "{{ public }}" => $public,
                    "{{ members_only }}" => $members_only,
                    "{{ max_tickets }}" => $max_tickets,
                    "{{ price_adult_mb }}" => $price_adult_mb,
                    "{{ price_adult }}" => $price_adult,
                    "{{ price_child_mb }}" => $price_child_mb,
                    "{{ price_child }}" => $price_child,
                    "{{ enable_booking }}" => $enable_booking,
                    "{{ create_evt_error_msg }}" => "Ending date must be after the starting date."
                ];
                if (isset($this->_url[1])){
                    $args["{{ title }}"] = "Modify the event";
                    $args["{{ action }}"] = "save_event/".$this->_url[1];
                    $args["{{ button }}"] = "Modify the event";
                }
                else{
                    $args["{{ title }}"] = "Create an event";
                    $args["{{ action }}"] = "save_event";
                    $args["{{ button }}"] = "Create the event";
                }
                $content = View::makeHtml($args, "content_admin_create_event.html");
                return ["Event", $content];
            }
            $data = [$name, $description, $location_id, $image_id, $category, $active_event, $start_datetime, $finish_datetime, $max_tickets, $type_tickets, $public, $members_only, $price_adult_mb, $price_adult, $price_child_mb, $price_child, $enable_booking];
            //if modifying event
            if (isset($this->_url[1])){
                $event = new Event("update", ["id" => $this->_url[1], "data" => $data]);
                if ($event){
                    ?>
                    <script>
                        var msg = '<?php echo "Your changes have been updated.";?>';
                        var link = '<?php echo "../manage_events";?>';
                        alert(msg);
                        window.location.href=link;
                    </script>
                    <?php
                }
                else {header('Location: ../../display_error/admin');}
            }
            //if creating new event
            else {
                $new_event = new Event("create", $data);
                if ($new_event){
                    ?>
                    <script>
                        var msg = '<?php echo "The event has been created.";?>';
                        var link = '<?php echo "manage_events";?>';
                        alert(msg);
                        window.location.href=link;
                    </script>
                    <?php
                }
                else { header('Location: ../display_error/admin');}
            }
        }
        else { header('Location: ');}
    }

    public function manage_events(){
        $msg = "";
        if (isset($this->_url[1])){
            if ($this->_url[1] = "no"){
                $msg = "Some tickets are booked for this event. You can't delete it without cancelling the tickets first.";
            }
        }
        //get active current events
        $current_events = $this->getSelectedEvents(1, 1);
        $draft_events = $this->getSelectedEvents(0);
        $past_events = $this->getSelectedEvents(1, 0, false);
        //$trash_events = $this->getSelectedEvents(2);
        $content = View::makeHtml([
            "{{ msg }}" => $msg,
            "{{ current_events }}" => $current_events,
            "{{ draft_events }}" => $draft_events,
            "{{ past_events }}" => $past_events,
            //"{{ trash_events }}" => $trash_events
        ], "content_admin_manage_events.html");
        return ["Manage events", $content];

    }

    public function getSelectedEvents($active, $current = 2, $modify = true){
        $where[0] = "active_event = ".$active;
        if ($current == 0){$where[1] = "finish_datetime < NOW()";}
        else if ($current == 1){$where[1] = "finish_datetime >= NOW()";}
        $req = [
            "fields" => ['event_id'],
            "from" => "evt_events",
            "where" => $where,
            "order" => "start_datetime"
        ];
        $data = Model::select($req);
        //if no events
        $events = "";
        if (!isset($data["data"][0])){
            $events = "No event";
        }
        else {
            $admin_each_event;
            foreach ($data["data"] as $row){
                $admin_each_event = new Event("read", ["id" => $row["event_id"]]);
                $args = $admin_each_event->getEventData();
                if ($modify == true){$args["{{ modify }}"] = View::makeHtml($args, "elt_admin_each_event_modify.html");}
                else {$args["{{ modify }}"] = "";}
                $events .= View::makeHtml($args, "elt_admin_each_event.html");
            }
        }
        return $events;
    }

    public function modify_event(){
        $event = new Event("read", ["id" => $this->_url[1]]);
        $data = $event->getEventData();
        $data["{{ create_evt_error_msg }}"] = "";
        $data["{{ title }}"] = "Modify the event";
        $data["{{ action }}"] = "save_event/".$this->_url[1];
        $data["{{ button }}"] = "Modify the event";
        $content = View::makeHtml($data, "content_admin_create_event.html");
        return ["Create event", $content];
    }

    public function delete_event(){
        if (!isset($this->_url[1])){
            header('Location: manage_events');
        }
        else {
            $this_event = new Event("read", ["id" => $this->_url[1]]);
            if ($this_event->getVarEvent("_nb_booked_tickets") != 0){
                header('Location: ../manage_events/delete_no');
            }
            else {
                $event = new Event("delete", ["id" => $this->_url[1]]);
                if ($event){
                    ?>
                    <script>
                        var msg = '<?php echo "The event has been deleted.";?>';
                        var link = '<?php echo "../manage_events";?>';
                        alert(msg);
                        window.location.href=link;
                    </script>
                    <?php
                }
                else {header('Location: ../../display_error/admin');}
            }
        }
    }

    public function duplicate_event(){
        if (!isset($this->_url[1])){
            header('Location: manage_events');
        }
        else {
            $event = new Event("read", ["id" => $this->_url[1]]);
            if ($event){
                $data = $event->getEventData();
                $data["{{ name }}"] = "Copy of ".$data["{{ name }}"];
                $data["{{ enable_booking }}"] = "1";
                $data["{{ active_event }}"] = "1";
                $data["{{ create_evt_error_msg }}"] = "";
                $data["{{ title }}"] = "Create a new event";
                $data["{{ action }}"] = "create_event";
                $data["{{ button }}"] = "Create the event";
                $content = View::makeHtml($data, "content_admin_create_event.html");
                return ["Create event", $content];
            }
            else {header('Location: ../../display_error/admin');}
        }
    }

    /*-------------------------------------------MANAGING ACCOUNTS--------------------------------------------------*/

    public function manage_accounts(){
        //get active accounts
        $active_accounts = $this->getSelectedAccounts(1);
        $inactive_accounts = $this->getSelectedAccounts(0);
        $content = View::makeHtml([
            "{{ active_accounts }}" => $active_accounts,
            "{{ inactive_accounts }}" => $inactive_accounts,
        ], "content_admin_manage_accounts.html");
        return ["Manage accounts", $content];
    }

    public function getSelectedAccounts($active){
        $req = [
            "fields" => ['evt_account_id'],
            "from" => "evt_accounts",
            "where" => ["active_account = ".$active]
        ];
        $data = Model::select($req);
        //if no accounts
        $accounts = "";
        if (!isset($data["data"][0])){
            $accounts = "No account";
        }
        else {
            $admin_each_account;
            foreach ($data["data"] as $row){
                $admin_each_account = new Account("read", ["id" => $row["evt_account_id"]]);
                $args = $admin_each_account->getAccountData();
                if ($args["{{ managing_rights }}"] == 1){$args["{{ btn_admin_rights }}"] = View::makeHtml($args, "elt_admin_each_account_remove_rights.html");}
                else {$args["{{ btn_admin_rights }}"] = View::makeHtml($args, "elt_admin_each_account_give_rights.html");}
                if ($args["{{ active_account }}"] == 1){$args["{{ btn_activate }}"] = View::makeHtml($args, "elt_admin_each_account_deactivate.html");}
                else {$args["{{ btn_activate }}"] = View::makeHtml($args, "elt_admin_each_account_activate.html"); $args["{{ btn_admin_rights }}"] = "";}
                $accounts .= View::makeHtml($args, "elt_admin_each_account.html");
            }
        }
        return $accounts;
    }

    public function give_rights(){
        if (!isset($this->_url[1])){
            header('Location: manage_accounts');
        }
        else {
            $account = new Account("update", ["id" => $this->_url[1], "managing_rights" => 1]);
            if ($account){
                ?>
                <script>
                    var msg = '<?php echo "Your changes have been updated.";?>';
                    var link = '<?php echo "../manage_accounts";?>';
                    alert(msg);
                    window.location.href=link;
                </script>
                <?php
            }
            else {header('Location: ../../display_error/admin');}
        }
    }

    public function remove_rights(){
        if (!isset($this->_url[1])){
            header('Location: manage_accounts');
        }
        else {
          $account = new Account("update", ["id" => $this->_url[1], "managing_rights" => 0]);
            if ($account){
                ?>
                <script>
                    var msg = '<?php echo "Your changes have been updated.";?>';
                    var link = '<?php echo "../manage_accounts";?>';
                    alert(msg);
                    window.location.href=link;
                </script>
                <?php
            }
            else {header('Location: ../../display_error/admin');}
        }
    }

    public function activate(){
        if (!isset($this->_url[1])){
            header('Location: manage_accounts');
        }
        else {
          $account = new Account("update", ["id" => $this->_url[1], "active_account" => 1]);
            if ($account){
                ?>
                <script>
                    var msg = '<?php echo "Your changes have been updated.";?>';
                    var link = '<?php echo "../manage_accounts";?>';
                    alert(msg);
                    window.location.href=link;
                </script>
                <?php
            }
            else {header('Location: ../../display_error/admin');}
        }
    }

    public function deactivate(){
        if (!isset($this->_url[1])){
            header('Location: manage_accounts');
        }
        else {
          $account = new Account("update", ["id" => $this->_url[1], "active_account" => 0]);
            if ($account){
                ?>
                <script>
                    var msg = '<?php echo "Your changes have been updated.";?>';
                    var link = '<?php echo "../manage_accounts";?>';
                    alert(msg);
                    window.location.href=link;
                </script>
                <?php
            }
            else {header('Location: ../../display_error/admin');}
        }
    }

    /*-------------------------------------------MANAGING TICKETS---------------------------------------------------*/

    public function manage_tickets(){
        $req = [
            "fields" => ['event_id'],
            "from" => "evt_events",
            "where" => [
                "active_event = 1",
                "finish_datetime >= NOW()"
            ],
            "order" => "start_datetime"
        ];
        $data = Model::select($req);
        //if no events
        $events_tickets = "";
        if (!isset($data["data"][0])){
            $events_tickets = "No event";
        }
        else {
            $admin_each_event;
            $nb_tickets;
            foreach ($data["data"] as $row){
                $admin_each_event = new Event("read", ["id" => $row["event_id"]]);
                $args = $admin_each_event->getEventData();
                if ( $admin_each_event->getVarEvent("_nb_booked_tickets") == 0 ){$args["{{ tickets }}"] = "No ticket booked";}
                else {$args["{{ tickets }}"] = "Tickets booked: ".$admin_each_event->getVarEvent("_nb_booked_tickets");}
                if ($admin_each_event->getVarEvent("_max_tickets") !== null){$args["{{ available_tickets }}"] = $admin_each_event->getVarEvent("_nb_available_tickets");}
                else {$args["{{ available_tickets }}"] = "illimited"; $args["{{ max_tickets }}"] = "undefined";}
                $events_tickets .= View::makeHtml($args, "elt_admin_each_event_tickets.html");
            }
        }
        $content = View::makeHtml(["{{ events_tickets }}" => $events_tickets], "content_admin_manage_tickets.html");
        return ["Manage tickets", $content];
    }

    public function see_tickets(){
        if (!isset($this->_url[1])){
            header('Location: manage_tickets');
        }
        else {
            $event = new Event("read", ["id" => $this->_url[1]]);
            $req = [
                "fields" => ['ticket_id'],
                "from" => "evt_tickets",
                "where" => ["event_id = ".$this->_url[1], "cancelled_time IS NULL"]
            ];
            $data = Model::select($req);
            $tickets = "";
            //if no tickets
            if (!isset($data["data"][0])){
                $tickets = "No ticket";
            }
            else {
                $admin_each_ticket;
                foreach ($data["data"] as $row){
                    $admin_each_ticket = new ticket("read", ["id" => $row["ticket_id"]]);
                    $args = $admin_each_ticket->getticketData();
                    $name = new Account("read", ["id" =>  $admin_each_ticket->getVarTicket("_evt_account_id")]);
                    $args["{{ first_name }}"] = $name->getVarAccount("_first_name");
                    $args["{{ last_name }}"] = $name->getVarAccount("_last_name");
                    $tickets .= View::makeHtml($args, "elt_admin_each_ticket.html");
                }
            }
            $content = View::makeHtml(["{{ tickets }}" => $tickets, "{{ event_name }}" => $event->getVarEvent("_name"), "{{ start_weekday }}" => $event->getVarEvent("_start_weekday"),"{{ start_date }}" =>$event->getVarEvent("_start_date")], "content_admin_see_tickets.html");
            return ["See tickets", $content];
        }
    }

    public function modify_tickets(){
        if (!isset($this->_url[1])){
            header('Location: manage_tickets');
        }
        else {
            require_once "controller/Ticket.php";
            $ticket = new Ticket("read", ["id" => $this->_url[1]]);
            $event = new Event("read", ["id" => $ticket->getVarTicket("_event_id")]);
            $tickets_choice = $event->setTicketChoice();
            if (null !== $event->getVarEvent("_nb_available_tickets")){$nb_available_tickets = $event->getVarEvent("_nb_available_tickets");}
            else { $nb_available_tickets = "";}
            $data["{{ event_id }}"] = $event->getVarEvent("_event_id");
            $data["{{ event_name }}"] = $event->getVarEvent("_name");
            $data["{{ tickets_choice }}"] = $tickets_choice;
            $data["{{ action }}"] = "admin/save_tickets/{{ ticket_id }}";
            $data["{{ title }}"] = "Modify those tickets";
            $data["{{ btn_action }}"] = "Modify tickets";
            $data["{{ ticket_id }}"] = $this->_url[1];
            $data["{{ nb_available_tickets }}"] = $nb_available_tickets;
            $data = array_merge($data, $ticket->getTicketData());
            $content = View::makeHtml($data, "content_book_tickets.html");
            return ["Modify tickets", $content];
        }
    }

    public function save_tickets(){
        if (!isset($this->_url[1])){
            header('Location: manage_tickets');
        }
        else {
            if (!$this->postEmpty()){
                foreach($_POST as $key => $value) {
                    $data[$key] = $this->getPostSanitizeInt($key);
                }
                $data["id"] = $this->_url[1];
                if (isset($data["total_paid"])){
                    require_once "controller/Ticket.php";
                    $ticket = new Ticket("read", ["id" => $this->_url[1]]);
                    $data["payment_datetime"] = $ticket->getVarTicket("_payment_datetime");
                }
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
                        var link = '<?php echo "../admin/modify_tickets/".$this->_url[1];?>';
                        alert(msg);
                        window.location.href=link;
                    </script>
                    <?php
                }
                else {
                    if (!empty($data["nb_available_tickets"])){
                        if ($data["nb_available_tickets"] < $nb_tickets_wanted){
                            ?>
                            <script>
                                var msg = '<?php echo "Not enough tickets available.";?>';
                                var link = '<?php echo "../admin/modify_tickets/".$this->_url[1];?>';
                                alert(msg);
                                window.location.href=link;
                            </script>
                            <?php
                        }
                    }
                    require_once "controller/Ticket.php";
                    $ticket = new Ticket("update", $data);
                    if ($ticket){
                        $event_id = $data["event_id"];
                        ?>
                        <script>
                            var msg = '<?php echo "Your changes have been updated!";?>';
                            var link = '<?php echo "../../admin/see_tickets/".$event_id;?>';
                            alert(msg);
                            window.location.href=link;
                        </script>
                        <?php
                    }
                    else {header('Location: ../../display_error/admin');}
                }
            }
        }
    }

    public function modify_payment(){
        if (!isset($this->_url[1])){
            header('Location: manage_tickets');
        }
        else {
            if (!$this->postEmpty()){
                require_once "controller/Ticket.php";
                $ticket = new Ticket("read", ["id" => $this->_url[1]]);
                $payment_datetime = $this-> getPost("payment_datetime");
                if ($payment_datetime == null){ header('Location: ../display_error/admin');}
                $total_paid = FILTER_INPUT(INPUT_POST, "total_paid", FILTER_VALIDATE_INT);
                $update = $ticket->updateInDB(["payment_datetime", "total_paid"], [$payment_datetime, $total_paid]);
                if ($update){
                    ?>
                    <script>
                        var msg = '<?php echo "Your changes have been updated!";?>';
                        var link = '<?php echo "../modify_payment/".$this->_url[1];?>';
                        alert(msg);
                        window.location.href=link;
                    </script>
                    <?php
                }
                else {header('Location: ../../display_error/admin');}
            }
            else {
                require_once "controller/Ticket.php";
                $ticket = new Ticket("read", ["id" => $this->_url[1]]);
                $name = new Account("read", ["id" =>  $ticket->getVarTicket("_evt_account_id")]);
                $args["{{ first_name }}"] = $name->getVarAccount("_first_name");
                $args["{{ last_name }}"] = $name->getVarAccount("_last_name");
                if ($ticket->getVarTicket("_payment_datetime") != null){
                    $args["{{ cancel_payment_btn }}"] = file_get_contents("template/elt_admin_cancel_payment_btn.html");
                }
                else {$args["{{ cancel_payment_btn }}"] = "";}
                $args = array_merge($args, $args = $ticket->getTicketData());
                $content = View::makeHtml($args, "content_admin_modify_payment.html");
                return ["Modify payment", $content];
            }
        }
    }

    public function cancel_payment(){
        if (!isset($this->_url[1])){
            header('Location: manage_tickets');
        }
        else {
            require_once "controller/Ticket.php";
            $ticket = new Ticket("read", ["id" => $this->_url[1]]);
            $update = $ticket->updateInDB(["payment_datetime", "total_paid"], [null, null]);
            if ($update){
                if (!isset($this->_url[2])){$msg = "../modify_payment/".$this->_url[1];}
                else {$msg = "../../".$this->_url[2];}
                ?>
                <script>
                    var msg = '<?php echo "The payment has been cancelled.";?>';
                    var link = '<?php echo $msg;?>';
                    alert(msg);
                    window.location.href=link;
                </script>
                <?php
            }
            else {header('Location: ../../display_error/admin');}
        }
    }

    public function cancel_tickets(){
        if (!isset($this->_url[1])){
            header('Location: manage_tickets');
        }
        else {
            require_once "controller/Ticket.php";
            $ticket = new Ticket("read", ["id" => $this->_url[1]]);
            $event_id = $ticket->getVarTicket("_event_id");
            $cancelled = $ticket->updateInDB(["cancelled_time"], [date("Y-m-d H:i:s")]);
            if ($cancelled){
                ?>
                <script>
                    var msg = '<?php echo "Those tickets have been cancelled.";?>';
                    var link = '<?php echo "../see_tickets/".$event_id;?>';
                    alert(msg);
                    window.location.href=link;
                </script>
                <?php
            }
            else {header('Location: ../../display_error/admin');}
        }
    }

    public function see_cancelled_tickets(){
        $req = [
            "fields" => ['ticket_id'],
            "from" => "evt_tickets",
            "where" => ["cancelled_time IS NOT NULL"]
        ];
        $data = Model::select($req);
        $tickets = "";
        //if no tickets
        if (!isset($data["data"][0])){
            $tickets = "No cancelled ticket";
        }
        else {
            $admin_each_ticket;
            foreach ($data["data"] as $row){
                require_once "controller/Ticket.php";
                $admin_each_ticket = new ticket("read", ["id" => $row["ticket_id"]]);
                if ($admin_each_ticket->getVarTicket("_payment_datetime") != null){
                    $args["{{ cancel_btn }}"] = file_get_contents("template/elt_admin_cancel_payment_btn_cancelled.html");
                }
                else {$args["{{ cancel_btn }}"] = "";}
                $name = new Account("read", ["id" =>  $admin_each_ticket->getVarTicket("_evt_account_id")]);
                $args["{{ first_name }}"] = $name->getVarAccount("_first_name");
                $args["{{ last_name }}"] = $name->getVarAccount("_last_name");
                $event = new Event("read", ["id" => $admin_each_ticket->getVarTicket("_event_id")]);
                $args["{{ event_name }}"] = $event->getVarEvent("_name");
                $args = array_merge($args, $admin_each_ticket->getticketData());
                $tickets .= View::makeHtml($args, "elt_admin_each_cancelled_ticket.html");
            }
        }
        $content = View::makeHtml(["{{ cancelled_tickets }}" => $tickets], "content_admin_see_cancelled_tickets.html");
        return ["See cancelled tickets", $content];
    }

}
