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
        $content = file_get_contents("template/content_admin_create_event.html");
        return ["Ereate event", $content];




        // require_once "controller/Event.php";
        // $event = new Event("create", $data);
        // //$html: replace dashboard content ( {{ content_admin_page }} in backadmin_template)
        // if (isset($_POST["title"]) && isset($_POST["content"])) {
        //     $title = filter_input(INPUT_POST, "title", FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
        //     $title = ucfirst($title);
        //     $content = filter_input(INPUT_POST, "content", FILTER_SANITIZE_SPECIAL_CHARS);
        //     $post->cancelFeatured();
        //     //count how many chapters there are to add the good number
        //     $nb = $post->countPosts();
        //     $chapter_nb = $nb["nb"] + 1;
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
        // }
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
