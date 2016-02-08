<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends Source {

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

        $this->_token  = "cm9vdDprMWsxbTB0MA==";
        $this->_url = "http://dashboard.kukua.cc";
        $this->_port = ":8086";
        $this->_db = "data";
        $this->_suffix = "/query";
    }

    /**
     *
     */
    public function getColumnName($type) {
        switch($type) {
            case 'rain':
                return "rainTicks";
                break;
            case 'wind':
                return "windTicks";
                break;
            default:
                return $type;
                break;
        }
    }

    /**
     *
     */
    public function getNiceName($type) {
        switch($type) {
            default:
            case 'temp':
                return "Temperature";
                break;
            case 'rain':
                return "Rainfall";
                break;
            case 'hum':
                return "Humidity";
                break;
            case 'presBMP':
                return "Pressure";
                break;
            case 'wind':
                return "Wind";
                break;
        }
    }

    /**
     *
     */
    public function get($source) {
        if ($source->getWeatherType() == "all") {
            $this->setSelect($this->selectAll());
        } else {
            $weatherType = $source->getWeatherType();
            $select["mean(" . $this->getColumnName($weatherType)  . ")"] = $this->getNiceName($weatherType);
            $this->setSelect($select);
        }
        $this->setFrom($source->getCountry());

        $dates["dateFrom"] = $source->getDateFrom();
        $dates["dateTo"]   = $source->getDateTo();
        $this->setWhere($dates);
        $this->setGroup($source->getInterval());

        $this->_query = $this->getSelect() . $this->getFrom() . $this->getWhere() . $this->getGroup() . $this->getOrder();
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
        if ($request != false || $request != "" || $request != null) {
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

    public function selectAll() {
        return [
            "sum(rainTicks)" => "RainTicks",
            "mean(windTicks)" => "WindTicks",
            "mean(windGustTicks)" => "WindGustTicks",
            "mean(windDir)" => "WindDir",
            "mean(windGustDir)" => "WindGustDir",
            "mean(temp)" => "Temperature",
            "mean(hum)" => "Humidity",
            "mean(presBMP)" => "PresBMP"
        ];
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
        foreach($select as $column => $name) {
            $query .= " " . $column . " as " . $name . ",";
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
     * Set from clause
     *
     * @access public
     * @param  String $from
     * @return void
     */
    public function setFrom($country) {
        $stations = new Stations();
        $result = $stations->findByCountryId($country);

        $query = "";
        if (!empty($result)) {
            foreach($result as $station) {
                $query .= $station->station_id . ",";
            }
            $query = rtrim($query, ",");
        }
        $this->_from = " FROM " . $query;
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
        if (isset($where["dateFrom"]) && isset($where["dateTo"])) {
            $query = "time > " . $where["dateFrom"] . "s AND time < " . $where["dateTo"] . "s";
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
     * Set group clause
     *
     * @access public
     * @param  String
     * @return void
     */
    public function setGroup($group = "1h") {
        $query = "time(1h)";
        if (!is_null($group)) {
            $query = "time(" . $group . ")";
            $this->_group = " GROUP BY " . $query;
        }
    }

    /**
     * @access public
     * @return string
     */
    public function getGroup() {
        return $this->_group;
    }

    /**
     * Set order clause
     *
     * @access public
     * @param  String
     * @return void
     */
    public function setOrder($order) {
        $this->_order = " ORDER BY time ASC";
    }

    /**
     * @access public
     * @return string
     */
    public function getOrder() {
        return $this->_order;
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
