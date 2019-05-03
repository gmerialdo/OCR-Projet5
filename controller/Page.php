<?php

// www.kiwiversity.com/aftucson/events/... dc uri = /aftucson/events/...
//nothing (page with all events) or event/id or modify/id or create or login or logout or delete/id or book/id or cancel/id

require_once "model/Model.php";
require_once "view/View.php";


class Page
{

    protected $_url;
    //html to display for the whole page
    protected $_html;
    //indicate if logged in and if admin rights: either "visitor" or "logged_visitor" or "logged_admin"
    protected $_rights;


    public function __construct($url){
        $this->_url=$url;
        $this->setRights();
        $this->_html=$this->getHtml();
    }

    //to set the user profile
    public function setRights(){
        if (Session::get('user_name')==null){
            $this->_rights="visitor";
        }
        elseif (Session::get('evt_managing_rights')==true) {
            $this->_rights="logged_admin"
        }
        else {
            $this->_rights="logged_visitor";
        }
    }

    //to return the html for the page
    public function getHtml(){
        //add navbar
        $this->addNavbar();
        //add inside content
        //add footer
        return view::makeHtml([
            "{{ pageTitle }}" => "?????",
            "{{ content }}" => "??????",
            "{{ path }}" => $GLOBALS["path"]
        ], "page_template");
    }


    public function addNavbar(){
        //navbar_template.html with: {{ orga_logo_src }}, {{ orga_link }}
        $navbar_manageeventsoption = "";
        switch ($this->_rights){
            case "logged_visitor":
                $navbar_accountoption = view::giveHtml("navbar_accountoption_logged_template");
                break;
            case "logged_admin":
                $navbar_accountoption = view::giveHtml("navbar_accountoption_logged_template");
                $navbar_manageeventsoption = view::giveHtml("navbar_manageeventsoption_template");
                break;
            default:
                $navbar_accountoption = view::giveHtml("navbar_accountoption_signin_template");
                break;
        }
        $navbar_html = view::makeHtml([
            "{{ orga_logo_src }}" => "?????",
            "{{ orga_link }}" => "??????",
            "{{ navbar_manageeventsoption }}" => $navbar_manageeventsoption,
            "{{ navbar_accountoption }}" => $navbar_accountoption
        ], "navbar_template");
    }





    //adds a complement before using parent::getPage() to securize all the admin interface: only connect if logged!
    public function getPage(){
        //check if input and not logged
        if (!empty($_POST) && (Session::get("username")==null)){
            $logged = $this->checkLogin(
                filter_input(INPUT_POST, "user", FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH),
                filter_input(INPUT_POST, "pw", FILTER_SANITIZE_SPECIAL_CHARS,FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH)
            );
            //if correct username and password then login
            if ($logged){
                Session::put("username", filter_input(INPUT_POST, "user", FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
            }
            else{
                echo "<p style=\"text-align:center;\">L'identification a échoué ; veuillez entrer à nouveau votre identifiant et votre mot de passe.</p>";
            }
        }
        if (Session::get("username")==null){
            return $this->loginPage();
        }
        //else the user is logged in so go to the page in admin interface
        else {

        //see first part of the url and call the function
        $fct_to_call = $this->_url[0];
        //if empty then default page
        if ($fct_to_call == "") $fct_to_call = $this->_defaultPage;
        // if not valid name, then go to default page
        if (!method_exists($this, $fct_to_call)) $fct_to_call = $this->_defaultPage;
        //else call the function named
        return $this->$fct_to_call();
    }
        }




}

