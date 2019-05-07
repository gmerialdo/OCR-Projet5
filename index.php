<?php

session_start();

require_once "conf.php";
require_once "model/Model.php";
require_once "controller/Page.php";

Model::init();

// show errors if not in envProd
if (!$GLOBALS["envProd"]){
    ini_set('display_startup_errors', 1);
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
}

// get a securized instance of url
$url = explode ( "/", filter_var($_SERVER['REQUEST_URI'], FILTER_SANITIZE_URL, FILTER_FLAG_STRIP_HIGH | FILTER_FLAG_STRIP_LOW));
// remove first empty entry
$url = array_slice($url, $GLOBALS["uri_Start"]);

// create and display page
$page = new Page($url);
echo $page->getHtmlPage();
