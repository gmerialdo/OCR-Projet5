<?php

class Account
{

    private $_valid;
    private $_evt_account_id;
    private $_email;
    private $_user_name;
    private $_password ;
    private $_first_name;
    private $_last_name;
    private $_managing_rights;
    private $_orga_id;


    public function __construct($todo, $args){
        switch ($todo){
            case "read":
                $this->_valid = $this->validateLogin($args["user_name"], $args["password"]);
                if ($this->_valid){
                    $this->setAccountDataFromDB();
                    $this->logSession();
                }
                break;
            case "create":
                $this->_email = $args["email"];
                $this->_valid = $this->emailFree();
                if ($this->_valid == false){
                    return false;
                }
                else {
                    return $this->createAccount($args);
                }
                break;
        }
    }

    public function setAccountDataFromDB(){
        $req = [
            "fields" => [
                'evt_account_id',
                'email',
                'user_name',
                'password',
                'first_name',
                'last_name',
                'managing_rights',
                'orga_id'
            ],
            "from" => "evt_accounts",
            "where" => [ "evt_account_id = ".$this->_evt_account_id],
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

    public function getVarAccount($_var){
        return $this->$_var;
    }

    public function validateLogin($user_name, $password){
        $hash = hash("sha256", $password);
        $req = [
                "fields" => ["*"],
                "from" => "evt_accounts",
                "where" => [
                    "user_name ='$user_name'",
                    "password ='$hash'"
                    ]
        ];
        $data = Model::select($req);
        if (!empty($data["data"])){
            $this->_evt_account_id = $data["data"][0]["evt_account_id"];
        }
        //return true if not empty or false otherwise
        return !empty($data["data"]);
    }

    public function logSession(){
        global $session;
        $session->add("user_name", $this->_user_name);
        $session->add("first_name", $this->_first_name);
        $session->add("last_name", $this->_last_name);
        $session->add("evt_managing_rights", $this->_managing_rights);
        $session->add("admin_mode", $this->false);
        $session->add("evt_account_id", $this->_evt_account_id);
    }

    public function emailFree(){
        $req = [
                "fields" => ["*"],
                "from" => "evt_accounts",
                "where" => ["email ='$this->_email'"]
        ];
        $data = Model::select($req);
        //return true if not empty or false otherwise
        return empty($data["data"]);
    }

    public function createAccount($args){
        global $orga_id;
        $email = $args["email"];
        $data = [
            $email ,
            hash("sha256", $args["password"]),
            $args["email"],
            ucfirst($args["first_name"]),
            ucfirst($args["last_name"]),
            0,
            $orga_id
        ];
        $req = [
            "table"  => "evt_accounts",
            "fields" => [
                'email',
                'password',
                'user_name',
                'first_name',
                'last_name',
                'managing_rights',
                'orga_id'
            ]
        ];
        $create = Model::insert($req, $data);
        $this->_evt_account_id = $create["data"];
        $this->setAccountDataFromDB();
        $this->logSession();
        return $create["succeed"];
    }


}
