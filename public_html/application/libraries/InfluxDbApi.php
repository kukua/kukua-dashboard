<?php

class InfluxDbApi {
    protected $_url;
    protected $_port;
    protected $_user;
    protected $_password;

    protected $_select;
    protected $_group;
    protected $_order;
    protected $_output;

    public function __construct() {
        $this->_url = "http://dashboard.kukua.cc";
        $this->_port = "8086";
        $this->_user = "kukua";
        $this->_password = "pZhkvQwfP5";

        $this->_select = "SELECT time,rainTicks,windTicks,windGustTicks,windDir,windGustDir,temp,presBMP,hum";
        $this->_group  = "5m";
        $this->_order  = "asc";
    }

    public function buildQuery($from = null, $dateFrom = null, $dateTo = null) {
        if (!$this->_validTimestamp($dateFrom) || !$this->_validTimestamp($dateTo)) {
            throw new InvalidArgumentException("Please supply a from and/or to date as timestamp");
        }

        $user = GlobalHelper::getUser();
        if ($from === null) {
            $from = implode(",", Graph::$stations[$user]);
        } else {
            $from = Graph::$stations[$user][$this->input->post("nation")];
        }

        $query = $this->_select . "
            FROM " . $from . "
            WHERE time > " . $dateFrom . "s
            AND time < " . $dateTo . "s
            GROUP BY time(" . $this->_group . ")
            ORDER asc";
        $this->_query = urlencode($query);
    }

    public function call() {
        $headers = [
            "Content-type: application/json",
            "Accept: application/json"
        ];

        $init = $this->_url . ":" . $this->_port . "/db/data/series?p=" . $this->_password . "&u=" . $this->_user . "&q=" . $this->_query;
        $ch = curl_init($init);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_HEADERS, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $output = curl_exec($ch);
        curl_close($ch);
        $this->_output = json_decode($output, true);

        if (empty($this->_output)) {
            Throw new Exception("No results");
        }
    }

    public function getOutput() {
        return $this->_output;
    }

    protected function _validTimestamp($time) {
        return ((int) $time === $time)
            && ($time <= PHP_INT_MAX)
            && ($time >= ~PHP_INT_MAX);
    }
}
