<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Foreca_model extends CI_Model {

    public $select = "";
    public $from = "FROM Foreca";
    public $where = "";

    public $query = "";
    public $base = "http://dashboard.kukua.cc:9003/query";

    public function __construct() {
        parent::__construct();
    }

    public function request($type = 'temp') {
        $this->setSelect($type);
        $this->setWhere();

        $this->query = urlencode("
            " . $this->select . "
            " . $this->from . "
            " . $this->where);
        return $this;
    }

    public function setSelect($type) {
        $col1  = "tempLow";
        $name1 = "Low";
        $col2  = "tempHigh";
        $name2 = "High";

        if ($type == "rain") {
            $col1  = "precip";
            $name1 = "Rainfall";
            $col2  = false;
            $name2 = false;
        }

        $select = "SELECT $col1 as $name1";
        if ($col2 !== false) {
            $select .= ", $col2 as $name2";
        }
        $this->select = $select;
    }

    public function setWhere() {
        $this->where = "
            WHERE type = 'daily'
              AND id='100156918'
              AND time < now() + 10d
              AND time > now()
        ";
    }

    /**
     * The curl call to influxDb
     *
     * @access public
     * @throws Exception
     * @return void
     */
    public function call() {
        $headers = [
            "Content-type: application/json",
            "Accept: application/json",
            "Authorization: Basic cm9vdDo1NTdhYTg1NDVjYmM2MTE1ZWY0Yjk1OTYz",
        ];

        $init = $this->base . "?q=" . $this->query . "&db=data";
        $ch = curl_init($init);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($output);
        return $data->results[0]->series;
    }

    public function debug($var) {
        echo "<pre>";
        echo print_r($var, true);
        echo "</pre>";
    }
}
