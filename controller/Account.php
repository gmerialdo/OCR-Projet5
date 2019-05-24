<?php

class Account
{

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
                return $this->createAccount($args);
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
        if (!empty($data["data"])){
            Account::logSession($user_name, $data["data"][0]["first_name"], $data["data"][0]["last_name"], $data["data"][0]["managing_rights"], false, $data["data"][0]["evt_account_id"]);
        }
        //return true if not empty or false otherwise
        return !empty($data["data"]);
    }

    public static function logSession($user, $first_name, $last_name, $rights, $admin_mode, $id){
        $_SESSION["user_name"]=$user;
        $_SESSION["first_name"]=$first_name;
        $_SESSION["last_name"]=$last_name;
        $_SESSION["evt_managing_rights"]=$rights;
        $_SESSION["admin_mode"]=$admin_mode;
        $_SESSION["evt_account_id"]=$id;
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
            ucfirst($args["first_name"]),
            ucfirst($args["last_name"]),
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
        return $create["succeed"];
    }


}
