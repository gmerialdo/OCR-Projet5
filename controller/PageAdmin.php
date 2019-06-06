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

    public function create_event(){
        if (!empty($_POST)){
            $name = filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
            $name = ucfirst($name);
            $description = filter_input(INPUT_POST, "description", FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
            $description = ucfirst($description);
            $location_id = $_POST["location_id"];
            $image_id = $_POST["image_id"];
            $category = $_POST["category"];
            if (empty($_POST['active_event'])){$active_event = 0;} else {$active_event = 1;}
            $start_date = $_POST["start_date"];
            $start_time = $_POST["start_time"];
            $finish_date = $_POST["finish_date"];
            $finish_time = $_POST["finish_time"];
            $start_datetime = date("Y-m-d H:i:s", strtotime($_POST["start_date"]." ".$_POST["start_time"]));
            $finish_datetime = date("Y-m-d H:i:s", strtotime($_POST["finish_date"]." ".$_POST["finish_time"]));
            if (empty($_POST['type_tickets'])){$type_tickets = 0;} else {$type_tickets = $_POST['type_tickets'];}
            if (empty($_POST['public'])){$public = 1;} else {$public = $_POST['public'];}
            if (empty($_POST['members_only'])){$members_only = 0;} else {$members_only = 1;}
            if (empty($_POST['max_tickets'])){$max_tickets = null;} else {$max_tickets = $_POST['max_tickets'];}
            if (empty($_POST['price_adult_mb'])){$price_adult_mb = null;} else {$price_adult_mb = $_POST['price_adult_mb'];}
            if (empty($_POST['price_adult'])){$price_adult = null;} else {$price_adult = $_POST['price_adult'];}
            if (empty($_POST['price_child_mb'])){$price_child_mb = null;} else {$price_child_mb = $_POST['price_child_mb'];}
            if (empty($_POST['price_child'])){$price_child = null;} else {$price_child = $_POST['price_child'];}
            if (empty($_POST['enable_booking'])){$enable_booking = 0;} else {$enable_booking = 1;}

            if ($start_datetime > $finish_datetime){
                $content = View::makeHtml([
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
                    "{{ create_evt_error_msg }}" => "Ending date must be after the starting date.",
                    "{{ title }}" => "Create an event",
                    "{{ action }}" => "create_event",
                    "{{ button }}" => "Create the event"
                ], "content_admin_create_event.html");
                return ["Create event", $content];
            }
            else {
                $data = [
                    $name,
                    $description,
                    $location_id,
                    $image_id,
                    $category,
                    $active_event,
                    $start_datetime,
                    $finish_datetime,
                    $max_tickets,
                    $type_tickets,
                    $public,
                    $members_only,
                    $price_adult_mb,
                    $price_adult,
                    $price_child_mb,
                    $price_child,
                    $enable_booking,
                    $start_date,
                    $start_time,
                    $finish_date,
                    $finish_time
                ];
                $new_event = new Event("create", $data);
                ?>
                <script>
                    var msg = '<?php echo "The event has been created.";?>';
                    var link = '<?php echo "manage_events";?>';
                    alert(msg);
                    window.location.href=link;
                </script>
                <?php
            }
        }
        //case where no $_POST: send to create page
        else {
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
                "{{ title }}" => "Create an event",
                "{{ action }}" => "create_event",
                "{{ button }}" => "Create the event"
            ], "content_admin_create_event.html");
            return ["Create event", $content];
        }
    }

    public function manage_events(){
        //get active current events
        $current_events = $this->getSelectedEvents(1, 1);
        $draft_events = $this->getSelectedEvents(0);
        $past_events = $this->getSelectedEvents(1, 0);
        $trash_events = $this->getSelectedEvents(2);
        $content = View::makeHtml([
            "{{ current_events }}" => $current_events,
            "{{ draft_events }}" => $draft_events,
            "{{ past_events }}" => $past_events,
            "{{ trash_events }}" => $trash_events
        ], "content_admin_manage_events.html");
        return ["Manage events", $content];
    }


    public function getSelectedEvents($active, $current = 2){
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
                $events .= View::makeHtml($admin_each_event->getEventData(), "elt_admin_each_event.html");
            }
        }
        return $events;
    }

    public function modify_event(){
        $event = new Event("read", ["id" => $this->_url[1]]);
        $data = $event->getEventData();
        $data["{{ create_evt_error_msg }}"] = "";
        $data["{{ title }}"] = "Modify the event";
        $data["{{ action }}"] = "modify_event/".$this->_url[1];
        $data["{{ button }}"] = "Modify the event";
        $content = View::makeHtml($data, "content_admin_create_event.html");
        return ["Create event", $content];
    }

}
