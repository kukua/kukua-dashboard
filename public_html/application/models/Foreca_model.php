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

    public function request($type = 'temp', $dateFrom = '', $dateTo = '') {
        $this->setSelect($type);

        $today = new DateTime();
        $date  = new DateTime();
        $date->setTimestamp($dateTo);
        $interval = $today->diff($date);

        $this->setWhere($interval->format("%d"));

        $query = $this->select . "
             " . $this->from . "
             " . $this->where;
        $this->query = urlencode($query);
        return $this;
    }

    public function requestDaily($type = 'temp') {
        $this->setDailySelect($type);
        $this->setDailyWhere();
        $query = $this->select . "
             " . $this->from . "
             " . $this->where;
        $this->query = urlencode($query);
        return $this;
    }

    public function setDailySelect($type) {
        switch($type) {
            case 'temp':
                $col1  = "tempLow";
                $name1 = "Low";
                $col2  = "tempHigh";
                $name2 = "High";
                break;
            case 'rain':
                $col1  = "precip";
                $name1 = "Rainfall";
                $col2  = false;
                $name2 = false;
                break;
        }

        $select = "SELECT $col1 as $name1";
        if ($col2 != false) {
            $select .= ", $col2 as $name2";
        }
        $this->select = $select;
    }

    public function setSelect($type) {
        switch($type) {
            case 'temp':
                $col1  = "temp";
                $name1 = "Temperature";
                break;
            case 'rain':
                $col1  = "precip";
                $name1 = "Rainfall";
                break;
        }

        $select = "SELECT $col1 as $name1";
        $this->select = $select;
    }

    public function setWhere($interval = false) {
        $this->where = "
            WHERE type = 'hourly'
              AND id='100156918'
              AND time < NOW() + " . $interval . "d
              AND time > NOW()
        ";
    }

    public function setDailyWhere() {
        $this->where = "
            WHERE type = 'daily'
              AND id='100156918'
              AND time < NOW() + 10d
              AND time > NOW()
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
