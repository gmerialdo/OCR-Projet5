<?php

session_start();

require_once "conf.php";
require_once "model/Model.php";
require_once "view/View.php";
require_once "controller/Page.php";

Model::init();

global $envProd, $uri_Start;

// show errors if not in envProd
if (!$envProd){
    ini_set('display_startup_errors', 1);
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
}

// get a securized instance of url
$url = explode ( "/", filter_var($_SERVER['REQUEST_URI'], FILTER_SANITIZE_URL, FILTER_FLAG_STRIP_HIGH | FILTER_FLAG_STRIP_LOW));
// remove first empty entry
$url = array_slice($url, $uri_Start);

// decide if it's visitor mode or admin mode and create page
switch ($url[0]){
    case 'admin':
        require_once "controller/PageAdmin.php";
        $page = new PageAdmin($url);
        break;
    case 'logged':
        require_once "controller/PageLoggedVisitor.php";
        $page = new PageLoggedVisitor($url);
        break;
    default:
        require_once "controller/PageVisitor.php";
        $page = new PageVisitor($url);
        break;
}

// display page
echo $_SESSION["evt_account_id"];
echo $page->getHtmlPage();
