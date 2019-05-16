<?php

// www.kiwiversity.com/aftucson/events/... dc uri = /aftucson/events/...
//nothing (page with all events) or event/id or modify/id or create or login or logout or delete/id or book/id or cancel/id

require_once "controller/Event.php";

class Page
{

    protected $_url;
    //indicate if logged in and if admin rights: either "visitor" or "logged_visitor" or "admin"
    protected $_rights;
    protected $_defaultPage;


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
            $this->_rights="admin";
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

    //to add the nav bar
    public function addNavbar(){
        if ($this->_rights == "visitor"){
            $navbar_switch = "";
            $navbar_accountoption = file_get_contents("template/navbar_accountoption_signin.html");
            $navbar_accountdropdown = "";
        }
        else {
            if ($_SESSION["admin_mode"]){
                $navbar_switch = file_get_contents("template/navbar_switchtoadmin.html");
                $navbar_accountoption = file_get_contents("template/navbar_accountoption_admin.html");
                $navbar_accountdropdown = file_get_contents("template/navbar_accountdropdown_admin.html");
            }
            else {
                 // if user with admin rights
                if ($this->_rights == "admin"){
                    $navbar_switch = file_get_contents("template/navbar_switchtoadmin.html");
                }
                else {
                    $navbar_switch = "";
                }
                $navbar_accountoption = file_get_contents("template/navbar_accountoption_logged.html");
                $navbar_accountdropdown = file_get_contents("template/navbar_accountdropdown_logged.html");
            }
        }
        return view::makeHtml([
            "{{ navbar_switch }}" => $navbar_switch,
            "{{ navbar_accountoption }}" => $navbar_accountoption,
            "{{ navbar_accountdropdown }}" => $navbar_accountdropdown
        ], "navbar_template.html");
    }


    public function getPage(){
        //see first part of the url and call the function
        isset($this->_url[0])? $fct_to_call = $this->_url[0] : $fct_to_call = $this->_defaultPage;
        //if empty then default page
        if ($fct_to_call == "") $fct_to_call = $this->_defaultPage;
        // if not valid name, then go to default page
        if (!method_exists($this, $fct_to_call)) $fct_to_call = $this->_defaultPage;
        //else call the function named
        return $this->$fct_to_call();
    }

    //every function will return an array with $pageTitle and $content

    public function see_event(){

    }

    public function manage_events(){

    }

    public function modify_event(){

    }

    public function signin(){

    }

    public function login($correct = true){
        if ($this->_rights == "logged_visitor" OR $this->_rights == "admin"){
            return $this->see_all_events();/////////////////////////////////////////////////// faut-il mettre autre chose si admin????????
        }
        else {
            $content = file_get_contents("template/content_login_template.html");
            if (!$correct) $content.= file_get_contents("template/error_login.html");
        }
        return ["login", $content];
    }

    public function checklogin(){
        if (!empty($_POST) && (empty($_SESSION["user_name"]))){
            $user = filter_input(INPUT_POST, "user", FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
            $pass = filter_input(INPUT_POST, "pw", FILTER_SANITIZE_SPECIAL_CHARS,FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
            $req = [
            "fields" => ["*"],
                "from" => "evt_accounts",
                "where" => [
                    "user_name ='$user'",
                    "password ='$pass'"
                    ]
            ];
            $data = Model::select($req);
            //return true if not empty or false otherwise
            $logged = !empty($data["data"]);
            if ($logged){
                $_SESSION["user_name"]=$user;
                $_SESSION["evt_managing_rights"]=$data["data"][0]["managing_rights"];
                $_SESSION["admin_mode"]=false;
                header('Location: see_all_events');
            }
            else {
                return $this->login(false);
            }
        }
        elseif ($_SESSION["username"]!=null){
            header('Location: ');
        }
        else {
            return $this->login(false);
        }
    }

    public function logout(){
        session_destroy();
        return $this->see_all_events();
    }

    public function forgot_password(){

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

