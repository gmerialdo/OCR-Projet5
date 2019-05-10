<?php

class Image {

    private $_image_id;
    //private $_private;
    //private $_orga_id;
    private $_src;
    private $_alt;

    public function __construct($todo, $args){
        switch ($todo){
            case "read":
                $this->_image_id = $args["id"];
                $this->setImageDataFromDB();
                break;
            //TO DO LATER -----------------------------------------------------------
            case "create":
                break;
        }
    }

    public function setImageDataFromDB(){
        $req = [
            "fields" => [
                'src',
                'alt'],
            "from" => "evt_images",
            "where" => ["image_id = ".$this->_image_id],
            "limit" => 1
        ];
        $data = Model::select($req);
        if ($data["succeed"]){
             $newKey;
            foreach ($data["data"][0] as $key => $value){
                $newKey = "_".$key;
                $this->$newKey = $value;
            }
        }

    }

    public function src(){
        return $this->_src;
    }

    public function alt(){
        return $this->_alt;
    }


}
