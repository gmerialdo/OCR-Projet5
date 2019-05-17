<?php

class Location {

    private $_location_id;
    private $_name;
    private $_address;
    private $_city;
    private $_zipcode;
    private $_state;
    private $_country;
    private $_phone;
    private $_max_occupancy;

    public function __construct($todo, $args){
        switch ($todo){
            case "read":
                $this->_location_id = $args["id"];
                $this->setLocationDataFromDB();
                break;
            //TO DO LATER -----------------------------------------------------------
            case "create":
                break;
        }
    }

    public function setLocationDataFromDB(){
        $req = [
            "fields" => [
                'location_id',
                'name',
                'address',
                'city',
                'zipcode',
                'state',
                'country',
                'phone',
                'max_occupancy'],
            "from" => "evt_locations",
            "where" => [ "location_id = ".$this->_location_id],
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

    public function getVarLocation($_var){
        return $this->$_var;
    }

    public function getLocationInfo($args){
        foreach ($args as $field){
            $newField = "_".$field;
            $result[$field] = $this->$newField;
        }
        return $result;
    }


}
