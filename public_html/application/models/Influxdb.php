<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Influxdb extends CI_Model {

    protected $_url;
    protected $_port;
    protected $_suffix;

    protected $_db;
    protected $_select;
    protected $_from;
    protected $_where;
    protected $_group;
    protected $_order;
    protected $_query;

    public function __construct() {
        parent::__construct();
    }

    /**
     * Return db
     *
     * @access public
     * @return string $this->_db
     */
    public function getDb() {
        return $this->_db;
    }

    /**
     * Return query
     *
     * @access public
     * @return string $this->_query
     */
    public function getQuery() {
        return $this->_query;
    }

    /**
     * Return url
     *
     * @access public
     * @return string
     */
    public function getUrl() {
        return $this->_url . ":" . $this->_port . $this->_suffix;
    }

    /**
     * Populate class
     *
     * @access public
     * @return void
     */
    public static function populate($data) {
        if (is_array($data)) {
            $class = new static;
            foreach($data as $key => $value) {
                $property = "_" . $key;
                if (property_exists($class, $property)) {
                    $class->{$property} = $value;
                }
            }
        }
        return $class;
    }

    /**
     * Get history (influxdb 0.8)
     *
     * @access public
     * @return History
     */
    public static function getHistory($params = Array()) {
        require_once(APPPATH . "models/Influxdb/History.php");
        $db = "data";
        $data = [
            "db"  => $db,
            "url" => "http://dashboard.kukua.cc",
            "port" => "8086",
            "user" => "kukua",
            "password" => "pZhkvQwfP5",
            "suffix" => "/db/" . $db . "/series",
        ];
        $history = History::populate($data);
        $history->find($params);
        return $history;
    }

    /**
     * Get forecast (influxdb 0.9)
     *
     * @access public
     * @return Forecast
     */
    public static function getForecast($params = Array()) {
        require_once(APPPATH . "models/Influxdb/Forecast.php");
        $data = [
            "db" => "data",
            "url" => "http://dashboard.kukua.cc",
            "port" => "9003",
            "token" => "cm9vdDo1NTdhYTg1NDVjYmM2MTE1ZWY0Yjk1OTYz",
            "suffix" => "/query",
        ];
        $forecast = Forecast::populate($data);
        $forecast->find($params);
        return $forecast;
    }

    /**
     * Get download (combo)
     *
     * @access public
     * @return Forecast
     */
    public static function getDownload($params = Array()) {
        require_once(APPPATH . "models/Influxdb/History.php");
        $db = "data";
        $data = [
            "db"  => $db,
            "url" => "http://dashboard.kukua.cc",
            "port" => "8086",
            "user" => "kukua",
            "password" => "pZhkvQwfP5",
            "suffix" => "/db/" . $db . "/series",
        ];
        $history = History::populate($data);
        $history->find($params);
        return $history;
    }

    public function setOrder($order) {
        $this->_order = $order;
    }

    /**
     * Build the query in subobjects
     *
     * @access protected
     * @return void
     */
    protected function _build($params = Array()) {
        if (!empty($params)) {
            foreach($params as $key => $value) {
                $prefix = "set";
                $suffix = ucfirst($key);
                if (method_exists(new static, $prefix . $suffix)) {
                    $this->{$prefix . $suffix}($value);
                }
            }
        }
    }
}
