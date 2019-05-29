<?php

class Session
{

    private $_data = [];
    private $_sessionId;
    private $_uuid;  //length : 23

    public function __construct() {

        // get safe session ID
        $this->_data = filter_input_array(
            INPUT_COOKIE,
            [
                "PHPSESSID" => [
                    'filter' => FILTER_SANITIZE_STRING,
                    'flags'  => FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH
                ]
            ]
        );

        switch ($this->_data) {
            case null: //create new session ID
                $this->_uuid = uniqid(date('yz').(date('H')*60+date('i'))*60+date('s'));
                $req = [
                    "table"  => "evt_sessions",
                    "fields" => ['uuid']
                ];
                $data = [$this->_uuid];
                $result = Model::insert($req, $data);
                if ($this->_data["succeed"]){
                    $this->_sessionId = $result["data"];
                    setcookie("PHPSESSID", $this->_uuid);
                }
                break;
            default:
                $this->_uuid = $this->_data["PHPSESSID"];
                $req = [
                    "fields" => ['session_id', 'data'],
                    "from" => "evt_sessions",
                    "where" => ["`uuid` = '$this->_uuid'"],
                    "limit" => 1
                ];
                $this->_data = Model::select($req);
                if ($this->_data["succeed"]){
                    $this->_data = $this->_data["data"][0]["data"];
                    $this->_sessionId = $this->_data["data"][0]["session_id"];
                }
                break;
        }
    }

    public function add($key, $value){
        $this->update($key, $value);
    }

    public function get($key){
        return $this->_data[$key];
    }

    public function remove($key){
        unset($this->_data[$key]);
        $this->storeInDatabase();
    }

    public function update($key, $value){
        $this->_data[$key] = $value;
        $this->storeInDatabase();
    }

    private function storeInDatabase(){
        $req = [
            "table"  => "evt_sessions",
            "fields" => ["data"],
            "where" => [
                "'session_id' = '$this->_sessionId'",
                "'uuid' = '$this->_uuid'"
            ],
            "limit" => 1
        ];
        return Model::update($req, [json_encode($this->_data)]);;
    }
}
