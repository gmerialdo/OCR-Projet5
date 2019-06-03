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
            $evt_name = filter_input(INPUT_POST, "evt_name", FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
            $evt_name = ucfirst($evt_name);
            $evt_description = filter_input(INPUT_POST, "evt_description", FILTER_SANITIZE_SPECIAL_CHARS);
            $evt_description = ucfirst($evt_description);
            if (!$_POST['evt_active']){
                $evt_active = false;
            }
            else {
                $evt_active = true;
            }
            $start_datetime = date("Y-m-d H:i:s", strtotime($_POST["start_date"]." ".$_POST["start_time"]));
            $finish_datetime = date("Y-m-d H:i:s", strtotime($_POST["finish_date"]." ".$_POST["finish_time"]));

            if ($start_datetime > $finish_datetime){
                ?>
                <!--keep entered data in localStorage-->
                <script>
                    window.localStorage.clear();
                    var keep_evt_name ='<?php echo $evt_name;?>';
                    var keep_evt_description ='<?php echo $evt_description;?>';
                    window.localStorage.setItem('evt_name', keep_evt_name);
                    window.localStorage.setItem('evt_description', keep_evt_description);
                </script>
                <?php
                $content = View::makeHtml(["{{ create_evt_error_msg }}" => "Ending date must be after the starting date. Please enter again the information."], "content_admin_create_event.html");
                return ["Create event", $content];
            }
            else{
                $type_tickets = $_POST["type_tickets"];
                $public = $_POST["public"];
                if (!$_POST['members_only']){
                    $members_only = false;
                }
                else {
                    $members_only = true;
                }
            }
        }
        else {
            $content = View::makeHtml(["{{ create_evt_error_msg }}" => ""], "content_admin_create_event.html");
            return ["Create event", $content];
        }



        //     $data = [
        //         $chapter_nb,
        //         date('Y-m-d'),
        //         $title,
        //         htmlspecialchars_decode($content),
        //         1
        //     ];
        //     $post_added = $post->addPost($data);
        //     if ($post_added){
        //         $html = View::makeHtml([
        //             "{{ path }}" => $GLOBALS["path"]
        //                 ], "add_post_message");
        //     }
        //     else{
        //         $html = View::errorDisplayBack();
        //     }

        // require_once "controller/Event.php";
        // $event = new Event("create", $data);
        // }
        // else {
        //     $html = View::errorDisplayBack();



 //                <label>
 //                    <input type="checkbox" name="members_only" />
 //                    <span>Members only</span>
 //                </label>
 //            </p>
 //            </div>
 //            <div class="col s12 l4 offset-l2">
 //                Price per adult (member): $
 //                <div class="input-field inline">
 //                    <input id="price_adult_mb" name="price_adult_mb" type="number" min="0" max="10000" step="0.01" class="validate">
 //                    <label for="price_adult_mb">Price</label>
 //                </div>
 //            </div>
 //            <div class="col s12 l4">
 //                Price per adult: $
 //                <div class="input-field inline">
 //                    <input id="price_adult" name="price_adult" type="number" min="0" max="10000" step="0.01" class="validate">
 //                    <label for="price_adult">Price</label>
 //                </div>
 //            </div>
 //            <div class="col s12 l4 offset-l2">
 //                Price per child (member): $
 //                <div class="input-field inline">
 //                    <input id="price_child_mb" name="price_child_mb" type="number" min="0" max="10000" step="0.01" class="validate">
 //                    <label for="price_child_mb">Price</label>
 //                </div>
 //            </div>
 //            <div class="col s12 l4">
 //                Price per child: $
 //                <div class="input-field inline">
 //                    <input id="price_child" name="price_child" type="number" min="0" max="10000" step="0.01" class="validate">
 //                    <label for="price_child">Price</label>
 //                </div>
 //            </div>

 //            <div class="col s12">
 //                <button class="btn waves-effect waves-light user_color" type="submit" name="submit">
 //                    Create the event<i class="material-icons right">send</i>
 //                </button>

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
