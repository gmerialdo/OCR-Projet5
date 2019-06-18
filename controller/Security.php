<?php

class Security
{

    public $_post = [];
    public $_uri  = [];

    public function __construct() {
        global $uri_Start;
        $this->_url = filter_input(INPUT_SERVER, 'REQUEST_URI', FILTER_SANITIZE_URL);
        $this->_url = explode("/", $this->_url);
        $this->_url = array_slice($this->_url, $uri_Start);
        if (isset($args["post"])){
          $this->_post = filter_input_array(INPUT_POST, $args["post"]);
        }
        //sanitize all dates!!
        $all_dates = ["", ""]; // add here every date input name!
        foreach ($all_dates as $value) {
            if ($this->_post[$value] != null){
                $this->_post[$value] = $this->sanitizeDate($this->_post[$value]);
            }
        }
        //sanitize all times!!
        $all_times = ["", ""]; // add here every time input name!
        foreach ($all_times as $value) {
            if ($this->_post[$value] != null){
                $this->_post[$value] = $this->sanitizeTime($this->_post[$value]);
            }
        }
    }

    public function sanitizeDate($date){
        $date = explode($date, "-");
        if (count($date)!=3) return false;
        for ($i=0; $i < 3; $i++) {
            $date[$i] = filter_var($date[$i], FILTER_SANITIZE_NUMBER_INT);
        }
        return implode("-", $date);
    }

    public function sanitizeTime($time){
        $time = explode($time, ":");
        if (count($time)>3) return false;
        for ($i=0; $i < count($time); $i++) {
            $time[$i] = filter_var($time[$i], FILTER_SANITIZE_NUMBER_INT);
        }
        return implode(":", $time);
    }

}
