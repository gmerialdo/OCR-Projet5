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
            $evt_name = filter_input(INPUT_POST, "evt_name", FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
            $evt_name = ucfirst($evt_name);
            $evt_description = filter_input(INPUT_POST, "evt_description", FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
            $evt_description = ucfirst($evt_description);
            $location_id = $_POST["location_id"];
            $image_id = $_POST["image_id"];
            $category = $_POST["category"];
            if (empty($_POST['evt_active'])){$evt_active = 0;} else {$evt_active = 1;}
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
            //if pb with the date
            if ($start_datetime > $finish_datetime){
                $content = View::makeHtml([
                    "{{ evt_name }}" => $evt_name,
                    "{{ evt_description }}" => $evt_description,
                    "{{ location_id }}" => $location_id-1,
                    "{{ image_id }}" => $image_id-1,
                    "{{ category }}" => $category,
                    "{{ evt_active }}" => $evt_active,
                    "{{ start_date }}" => $_POST["start_date"],
                    "{{ start_time }}" => $_POST["start_time"],
                    "{{ finish_date }}" => $_POST["finish_date"],
                    "{{ finish_time }}" => $_POST["finish_time"],
                    "{{ create_evt_error_msg }}" => "Ending date must be after the starting date.",
                    "{{ type_tickets }}" => $type_tickets,
                    "{{ public }}" => $public-1,
                    "{{ members_only }}" => $members_only,
                    "{{ max_tickets }}" => $max_tickets,
                    "{{ price_adult_mb }}" => $price_adult_mb,
                    "{{ price_adult }}" => $price_adult,
                    "{{ price_child_mb }}" => $price_child_mb,
                    "{{ price_child }}" => $price_child,
                    "{{ enable_booking }}" => $enable_booking,
                ], "content_admin_create_event.html");
                return ["Create event", $content];
            }
            else{
                switch ($category) {
                    case 1:
                        $category = "social";
                        break;
                    case 2:
                        $category = "culture";
                        break;
                    case 3:
                        $category = "workshop";
                        break;
                    case 4:
                        $category = "children";
                        break;
                    default:
                        $category = null;
                        break;
                }
                $data = [
                    $evt_name,
                    $evt_description,
                    $location_id,
                    $image_id,
                    $category,
                    $evt_active,
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
                    $enable_booking
                ];
                $new_event = new Event("create", $data);
                if ($new_event){
                ?>
                <script>
                    var msg = '<?php echo "The event has been created.";?>';
                    var link = '<?php echo "admin/manage_events";?>';
                    alert(msg);
                    window.location.href=link;
                </script>
                <?php
                }
            }
        }
        //case where no $_POST: send to create page
        else {
            $content = View::makeHtml([
                "{{ evt_name }}" => "",
                "{{ evt_description }}" => "",
                "{{ location_id }}" => 0,
                "{{ image_id }}" => 0,
                "{{ category }}" => 0,
                "{{ evt_active }}" => 1,
                "{{ start_date }}" => "",
                "{{ start_time }}" => "",
                "{{ finish_date }}" => "",
                "{{ finish_time }}" => "",
                "{{ create_evt_error_msg }}" => "",
                "{{ type_tickets }}" => 0,
                "{{ public }}" => 0,
                "{{ members_only }}" => 0,
                "{{ max_tickets }}" => "",
                "{{ price_adult_mb }}" => "",
                "{{ price_adult }}" => "",
                "{{ price_child_mb }}" => "",
                "{{ price_child }}" => "",
                "{{ enable_booking }}" => 1,
            ], "content_admin_create_event.html");
            return ["Create event", $content];
        }



    }

    public function manage_events(){

    }

    public function modify_event(){

    }
//RAJOUTER CECI POUR CHANGER DE COULEUR EN MODE ADMIN??
//<script>
//     document.getElementsByClassName("user_color").classList.add("admin_color");
//     document.getElementsByClassName("user_color").classList.remove("user_color");
// </script>


}
