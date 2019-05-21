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
            $nav_bar_accountoption_mobile = file_get_contents("template/navbar_accountoption_signin.html");
        }
        else {
            if ($_SESSION["admin_mode"]){
                $navbar_switch = file_get_contents("template/navbar_switchtoadmin.html");
                $navbar_accountoption = file_get_contents("template/navbar_accountoption_admin.html");
                $nav_bar_accountoption_mobile = file_get_contents("template/navbar_accountoption_admin_mobile.html");
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
                $nav_bar_accountoption_mobile = file_get_contents("template/navbar_accountoption_logged_mobile.html");
            }
        }
        return view::makeHtml([
            "{{ navbar_switch }}" => $navbar_switch,
            "{{ navbar_accountoption }}" => $navbar_accountoption,
            "{{ navbar_accountoption_mobile }}" => $nav_bar_accountoption_mobile
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

    public function login($message = "", $event_id = 0){
        if ($this->_rights == "logged_visitor" OR $this->_rights == "admin"){
            return $this->see_all_events();/////////////////////////////////////////////////// faut-il mettre autre chose si admin????????
        }
        else {
            if ($event_id == 0) {
                $may_be_event_id = "";
            }
            else {
                $may_be_event_id = "/".$event_id;
            }
            $content = View::makeHtml(["{{ may_be_event_id }}" => $may_be_event_id], "content_login.html");
            if ($message == "error") $content.= file_get_contents("template/msg_login_error.html");
            if ($message == "existing_email") $content.= file_get_contents("template/msg_login_existing_email.html");
            if ($message == "booking") $content.= file_get_contents("template/msg_login_booking.html");
        }
        return ["login", $content];
    }

    public function checklogin(){
        if (!empty($_POST) && (empty($_SESSION["user_name"]))){
            $user_name = filter_input(INPUT_POST, "user_name", FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
            ?>
            <!--keep user name in localStorage-->
            <script>
                window.localStorage.clear();
                var keep_user_name ='<?php echo $user_name;?>';
                window.localStorage.setItem('user_name', keep_user_name);
            </script>
            <?php
            $password = filter_input(INPUT_POST, "password", FILTER_SANITIZE_SPECIAL_CHARS,FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
            require_once "controller/Account.php";
            $logged = Account::validateLogin($user_name, $password);
            if ($logged){
                $_SESSION["user_name"]=$user_name;
                $_SESSION["evt_managing_rights"]=$data["data"][0]["managing_rights"];
                $_SESSION["admin_mode"]=false;
                if (isset($this->_url[1])){
                    header('Location: ../logged/book_tickets/'.$this->_url[1]);
                }
                else{
                    header('Location: logged/see_all_events');
                }
            }
            else {
                return $this->login("error");
            }
        }
        elseif (!empty($_SESSION["user_name"])){
            header('Location: ');
        }
        else {
            return $this->login("error");;
        }
    }

    public function logout(){
        session_destroy();
        header('Location: see_all_events');
    }

    public function signin(){
        if ($this->_rights == "logged_visitor" OR $this->_rights == "admin"){
            return $this->see_all_events();/////////////////////////////////////////////////// faut-il mettre autre chose si admin????????
        }
        else {
            if (isset($this->_url[1])) {
                $may_be_event_id = "/".$this->_url[1];
            }
            else {
                $may_be_event_id = "";
            }
            $content = View::makeHtml(["{{ may_be_event_id }}" => $may_be_event_id], "content_signin.html");
        }
        return ["signin", $content];
    }

    public function create_account(){
        if (!empty($_POST) && (empty($_SESSION["user_name"]))){
            $email = filter_input(INPUT_POST, "new_email", FILTER_VALIDATE_EMAIL);
            ?>
            <!--keep email in localStorage-->
            <script>
                window.localStorage.clear();
                var keep_email='<?php echo $email;?>';
                window.localStorage.setItem('email', keep_email);
            </script>
            <?php
            //check if email not already used
            require_once "controller/Account.php";
            $email_exists = Account::emailExists($email);
            if ($email_exists){
                return $this->login("existing_email");
            }
            else {
                $data["email"] = $email;
                $data["first_name"] = filter_input(INPUT_POST, "new_first_name", FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
                $data["last_name"] = filter_input(INPUT_POST, "new_last_name", FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
                $data["password"] = filter_input(INPUT_POST, "new_password", FILTER_SANITIZE_SPECIAL_CHARS,FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
                $new_account = new Account("create", $data);
                if ($new_account->getVarAccount("_created") == false){

                }
                else {
                    $_SESSION["user_name"]=$data["email"];
                    $_SESSION["evt_managing_rights"]=0;
                    $_SESSION["admin_mode"]=false;
                    if (isset($this->_url[1])){
                        header('Location: ../logged/book_tickets/'.$this->_url[1]);
                    }
                    else{
                        header('Location: logged/see_all_events');
                    }
                }
            }
        }
        else {
            header('Location: ');
        }
    }



//TO DO LATER -------------------------------------------------------------------------------------------------
    public function forgot_password(){

    }



    public function help(){

    }

    public function account_settings(){

    }

    public function my_tickets(){

    }






}

