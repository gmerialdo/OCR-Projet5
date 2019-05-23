<?php

class Account
{

    private $_created;
    private $_evt_account_id;
    private $_tbl_persons_perso_id;
    private $_tbl_users_user_id;
    private $_email;
    private $_user_name;
    private $_password ;
    private $_managing_rights;
    private $_orga_id;


    public function __construct($todo, $args){
        switch ($todo){
            case "create":
                $this->createAccount($args);
                break;
        }
    }

    public function getVarAccount($_var){
        return $this->$_var;
    }

    public static function validateLogin($user_name, $password){
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
        //return true if not empty or false otherwise
        return !empty($data["data"]);
    }

    public static function emailExists($email){
        $req = [
                "fields" => ["*"],
                "from" => "evt_accounts",
                "where" => ["email ='$email'"]
        ];
        $data = Model::select($req);
        //return true if not empty or false otherwise
        return !empty($data["data"]);
    }

    public function createAccount($args){
        $data = [
            $args["email"],
            hash("sha256", $args["password"]),
            $args["email"],
            $args["first_name"],
            $args["last_name"],
            0,
            $GLOBALS["orga_id"]
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
        $this->_created = $create["succeed"];
    }


}
