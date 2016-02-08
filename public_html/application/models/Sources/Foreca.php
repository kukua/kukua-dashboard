<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Foreca extends Source {

    private $_token;
    private $_url;
    private $_port;
    private $_db;
    private $_suffix;

    protected $_select;
    protected $_from;
    protected $_where;
    protected $_group;
    protected $_order;

    protected $_query;

    /**
     *
     */
    public function __construct() {
        parent::__construct();

        $this->_token  = "cm9vdDo1NTdhYTg1NDVjYmM2MTE1ZWY0Yjk1OTYz";
        $this->_url = "http://dashboard.kukua.cc";
        $this->_port = ":9003";
        $this->_db = "data";
        $this->_suffix = "/query";
    }

    /**
     *
     */
    public function get($source) {
        if ($source->getRange() == "temp") {
            $dates = false;
            $this->setSelect([
                "tempLow" => "MinTemp",
                "tempHigh"=> "MaxTemp"
            ]);
        }
        elseif ($source->getRange() == "rain") {
            $dates = false; 
            $this->setSelect(["precip" => "Rainfall"]);
        }
        elseif ($source->getRange() == "hum") {
            $dates = false;
            $this->setSelect(["humid" => "Humidity"]);
        }
        elseif ($source->getRange() == "wind") {
            $dates = false;
            $this->setSelect(["windSpeed" => "Wind"]);
        }
        else {
            $dates["dateFrom"] = $source->getDateFrom();
            $dates["dateTo"]   = $source->getDateTo();
            $this->setSelect([$source->getWeathertype() => "Temperature"]);
        }

        $this->setFrom();
        $this->setWhere($dates);

        $this->_query = $this->getSelect() . $this->getFrom() . $this->getWhere();
        return $this->_parse();
    }

    /**
     * @access protected
     * @return Array
     */
    protected function _parse() {
        $opts = [
            "q" => $this->_query,
            "db" => $this->_db,
        ];
        $request = $this->_curl($opts, true);
        if ($request != false || $request != "") {
            $responses = $request->results;
            if (isset($responses[0]->series) !== false) {
                $response = $this->_manipulate($responses[0]->series);
                return $response;
            }
        }
        return [];
    }

    /**
     * @access protected
     * @param  mixed $class
     * @return Array $result
     */
    protected function _curl($opts = [], $headers = False) {
        $curl = new \Curl\Curl();
        if ($headers !== false) {
            $curl->setHeader("Authorization", "Basic " . $this->_token);
        }

        $url = $this->_url . $this->_port . $this->_suffix;
        $result = $curl->get($url, $opts);
        return $result;
    }

    /**
     * Set select clause
     *
     * @access public
     * @param  Array $select
     * @return void
     */
    public function setSelect($select = []) {
        $query = "SELECT ";
        if (is_array($select)) {
            foreach($select as $column => $name) {
                $query .= " " . $column . " as " . $name . ",";
            }
        }
        $query = rtrim($query, ",");
        $this->_select = $query;
    }

    /**
     * @access public
     * @return string
     */
    public function getSelect() {
        return $this->_select;
    }

    /**
     * Set where clause
     *
     * @access public
     * @param  String $from
     * @return void
     */
    public function setFrom($from = null) {
        $this->_from = " FROM Foreca";
    }

    /**
     * @access public
     * @return string
     */
    public function getFrom() {
        return $this->_from;
    }

    /**
     * Set where clause
     *
     * @access public
     * @param  Array $where
     * @return void
     */
    public function setWhere($where) {
        $query = null;
        if (is_array($where)) {
            if (isset($where["dateFrom"]) && isset($where["dateTo"])) {
                $query  = "time > " . $where["dateFrom"] . "s AND time < " . $where["dateTo"] . "s";
                $query .= " AND type='hourly'";
                $query .= " AND id='100156918'";
                $query .= " AND time > NOW()";
            }
        } else {
            $query  = " time > now() AND time < now() + 10d";
            $query .= " AND type='daily'";
            $query .= " AND id='100156918'";
        }
        $this->_where = " WHERE " . $query;
    }

    /**
     * @access public
     * @return string
     */
    public function getWhere() {
        return $this->_where;
    }

    /**
     * Manipulate forecast time display for highcharts
     *
     * @access protected
     * @return Array
     */
    protected function _manipulate($data) {
        if (count($data)) {
            foreach($data as $station => $values) {
                if (count($values->values)) {

                    //Set correct name
                    $niceName = (new Stations())->findByStationId($values->name)->name;
                    if (empty($niceName)) {
                        $niceName = "forecast";
                    }
                    $values->name = ucfirst($niceName);

                    //Set correct date
                    foreach($values->values as $key => $points) {
                        $points[0] = str_replace("Z", "", $points[0]);
                        $points[0] = str_replace("T", " ", $points[0]);
                        $new = DateTime::createFromFormat("Y-m-d H:i:s", $points[0]);

                        //multiply by 1000 (milliseconds)
                        $data[$station]->values[$key][0] = $new->getTimestamp() * 1000;
                    }
                }
            }
        }
        return $data;
    }
}
