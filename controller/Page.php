<?php

// www.kiwiversity.com/aftucson/events/... dc uri = /aftucson/events/...
//nothing (page with all events) or event/id or modify/id or create or login or logout or delete/id or book/id or cancel/id

require_once "controller/Event.php";

class Page
{

    protected $_url;
    //indicate if logged in and if admin rights: either "visitor" or "logged_visitor" or "logged_admin"
    protected $_rights;


    public function __construct($url){
        $this->_url=$url;
        $this->setRights();
    }

    //to set the user profile
    public function setRights(){
        if (!isset($_SESSION['user_name'])){
            $this->_rights="visitor";
        }
        elseif (isset($_SESSION['evt_managing_rights']) && $_SESSION['evt_managing_rights']==true) {
            $this->_rights="logged_admin";
        }
        else {
            $this->_rights="logged_visitor";
        }
    }

    //to return the html for the page
    public function getHtmlPage(){
        //function getPage will return an array with $pageTitle and $content
        $getPage = $this->getPage();
        return view::makeHtml([
            "{{ pageTitle }}" => $getPage[0],
            "{{ navBar }}" => $this->addNavbar(),
            "{{ content }}" => $getPage[1],
            "{{ footer }}" => file_get_contents("template/footer.html"),
            "{{ orga_name }}" => "Alliance Française de Tucson",
            "{{ orga_logo_src }}" => "layout/images/logo_AFTucson.png",
            "{{ orga_website }}" => "www.aftucson.com",
            "{{ orga_address }}" => "2099E. River Road",
            "{{ orga_city }}" => "Tucson",
            "{{ orga_state }}" => "AZ",
            "{{ orga_zipcode }}" => "85718",
            "{{ orga_country }}" => "USA",
            "{{ orga_email }}" => "alliancefrancaisetucson@gmail.com",
            "{{ orga_phone }}" => "+1 520-881-9158",
            "{{ path }}" => $GLOBALS["path"]
        ], "page_template.html");
    }

    //to add the nav bar depending on visitor's profile (logged or not, admin or not)
    public function addNavbar(){
        if ($this->_rights == "visitor"){
            $navbar_accountoption = file_get_contents("template/navbar_accountoption_signin.html");
            $navbar_manageevents = "";
            $navbar_accountdropdown = "";
        }
        else {
            $navbar_accountoption = file_get_contents("template/navbar_accountoption_logged.html");
            $navbar_accountdropdown = file_get_contents("template/navbar_accountdropdown.html");
            if ($this->_rights == "logged_admin"){
                $navbar_manageevents = file_get_contents("template/navbar_manageevents.html");
            }
            else {
                $navbar_manageevents = "";
            }
        }
        return view::makeHtml([
            "{{ navbar_accountdropdown }}" => $navbar_accountdropdown,
            "{{ navbar_manageevents }}" => $navbar_manageevents,
            "{{ navbar_accountoption }}" => $navbar_accountoption
        ], "navbar_template.html");
    }

    //function getPage will return an array with $pageTitle and $content
    public function getPage(){
        //see first part of the url and call the function
        $fct_to_call = $this->_url[0];
        //if empty then default page
        if ($fct_to_call == "") $fct_to_call = "see_all_events";
        // if not valid name, then go to default page
        if (!method_exists($this, $fct_to_call)) $fct_to_call = "see_all_events";
        //else call the function named
        return $this->$fct_to_call();
    }
//each function called by getPage will return an array with $pageTitle and $content


    public function see_all_events(){
        $req = [
            "fields" => ['event_id'],
            "from" => "evt_events",
            "where" => [ "active_event = 1" ]
        ];
        $data = Model::select($req);
        //if no events
        if (!isset($data["data"][0])){
            $content = View::addTitleHtml(2, "No current event");
        }
        else {
            $each_event;
            $content = View::addTitleHtml(2, "Our events");
            $content .= View::addDiv("start", "row");
            foreach ($data["data"] as $row){
                $each_event = new Event("read", ["id" => $row["event_id"]]);
                $content .= $each_event->addEventOnAll();
            }
            $content .= View::addDiv("end");
        }
        return ["All events", $content];
    }


    public function see_event(){

    }

    public function manage_events(){

    }

    public function modify_event(){

    }

    public function signin(){

    }

    public function logout(){

    }

    public function help(){

    }

    public function account_settings(){

    }

    public function my_tickets(){

    }

    public function book_tickets(){

    }




}

