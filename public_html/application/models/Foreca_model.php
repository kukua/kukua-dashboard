<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Foreca_model extends CI_Model {
    public $query = "";
    public $base = "http://dashboard.kukua.cc:9003/query";

    public function __construct() {
        parent::__construct();
    }

    public function request($type = 'temp') {
        $col  = "temp";
        $name = "Temperature";

        if ($type == "rain") {
            $col  = "precip";
            $name = "Rainfall";
        }

        $this->query = urlencode("
            SELECT
                " . $col . " as " . $name . "
            FROM Foreca
            WHERE type = 'hourly'
              AND id='02339354'
              AND time < now() + 10d
              AND time > now()
        ");
        return $this;
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
