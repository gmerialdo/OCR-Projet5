<?php

require_once "controller/Session.php";
require_once "controller/Security.php";
require_once "controller/Page.php";
require_once "model/Model.php";
require_once "view/View.php";
require_once "conf.php";

Model::init();

global $envProd, $uri_Start;
$session = new Session();

// show errors if not in envProd
if (!$envProd){
    ini_set('display_startup_errors', 1);
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
}

$safeData = new Security([
    "post" => [
        "auteur"      => FILTER_SANITIZE_STRING,
        "commentaire" => FILTER_SANITIZE_STRING,
        "id"          => FILTER_SANITIZE_NUMBER_INT,
    ]
]);

// decide if it's visitor mode or admin mode and create page
switch ($safeData->_url[0]){
    case 'admin':
        require_once "controller/PageAdmin.php";
        $page = new PageAdmin($safeData->_url);
        break;
    case 'logged':
        require_once "controller/PageLoggedVisitor.php";
        $page = new PageLoggedVisitor($safeData->_url);
        break;
    default:
        require_once "controller/PageVisitor.php";
        $page = new PageVisitor($safeData->_url);
        break;
}

// display page
echo $page->getHtmlPage();
