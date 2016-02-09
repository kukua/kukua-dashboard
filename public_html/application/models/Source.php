<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Source extends CI_Model {

    protected $_country;
    protected $_type;
    protected $_dateFrom;
    protected $_dateTo;
    protected $_interval;
    protected $_range;

    /**
     * Class constructor
     * If params given, populate object
     *
     * @fix me
     * @fixme  authentication (JWT?)
     * @fix me
     */
    public function __construct($request = null) {
        parent::__construct();

        if (is_array($request))
            $this->populate($request);
    }

    /**
     * Gathering data from different sources
     * for display
     *
     * @access public
     * @return Array
     */
    public function gather() {
        $result = [];

        if ($this->getCountry() == "7") {
            require_once(APPPATH . "models/Sources/Sodaq.php");
            $sodaq = new Sodaq();
            $sodaResult = $sodaq->get($this);

            if (isset($sodaResult[0])) {
                foreach($sodaResult as $sodaque) {
                    $result[] = $sodaque;
                }
            }
            return $result;

        } else {
            require_once(APPPATH . "models/Sources/Dashboard.php");
            $dashboard = new Dashboard();
            $dashResult = $dashboard->get($this);
            if (isset($dashResult[0])) {
                foreach($dashResult as $dash) {
                    $result[] = $dash;
                }
            }

            require_once(APPPATH . "models/Sources/Foreca.php");
            $foreca = new Foreca();
            $forecaResult = $foreca->get($this);
            if (isset($forecaResult[0])) {
                foreach($forecaResult as $for) {
                    array_push($result, $for);
                }
            }
            return $result;
        }
    }

    /**
     * @access public
     * @param string
     * @throws InvalidArgumentException
     * @return void
     */
    public function setCountry($country) {
        if (!is_string($country))
            throw new InvalidArgumentException("Param supplied not a string");

        $this->_country = $country;
    }

    /**
     * @access public
     * @return string
     */
    public function getCountry() {
        return $this->_country;
    }

    /**
     * @access public
     * @param string
     * @throws InvalidArgumentException
     * @return void
     */
    public function setWeatherType($type) {
        if (!is_string($type))
            throw new InvalidArgumentException("Param supplied not a string");

        $this->_type = $type;
    }

    /**
     * @access public
     * @return string
     */
    public function getWeatherType() {
        return $this->_type;
    }

    /**
     * Set date, if string create datetime object
     * multiple formats (timestamp | yyyy-mm-dd HH:ii)
     *
     * @access public
     * @param string
     * @throws InvalidArgumentException
     * @return void
     */
    public function setDateFrom($date) {
        if (!is_string($date))
            throw new InvalidArgumentException("Param supplied not a string");

        $this->_dateFrom = $date;
    }

    /**
     * @access public
     * @return string
     */
    public function getDateFrom() {
        return $this->_dateFrom;
    }

    /**
     * Set date, if string create datetime object
     * multiple formats (timestamp | yyyy-mm-dd HH:ii)
     *
     * @access public
     * @param string
     * @throws InvalidArgumentException
     * @return void
     */
    public function setDateTo($date) {
        if (!is_string($date))
            throw new InvalidArgumentException("Param supplied not a string");

        $this->_dateTo = $date;
    }

    /**
     * @access public
     * @return string
     */
    public function getDateTo() {
        return $this->_dateTo;
    }

    /**
     * One of 5m, 1h, 12h 24h
     *
     * @access public
     * @param string
     * @throws InvalidArgumentException
     * @return void
     */
    public function setInterval($interval) {
        if (!is_string($interval))
            throw new InvalidArgumentException("Param supplied not a string");

        $this->_interval = $interval;
    }

    /**
     * @access public
     * @return string
     */
    public function getInterval() {
        return $this->_interval;
    }

    /**
     *
     */
    public function setRange($range) {
        if (!is_string($range))
            throw new InvalidArgumentException("Param supplied not a string");

        $this->_range = $range;
    }

    /**
     *
     */
    public function getRange() {
        return $this->_range;
    }

    /**
     * Populate object
     *
     * @access public
     * @param  Array $data
     * @return Source
     */
    public function populate($data) {
        if (isset($data["country"])) 
            $this->setCountry($data["country"]);

        if (isset($data["type"])) 
            $this->setWeatherType($data["type"]);

        if (isset($data["dateFrom"])) 
            $this->setDateFrom($data["dateFrom"]);

        if (isset($data["dateTo"])) 
            $this->setDateTo($data["dateTo"]);

        if (isset($data["interval"])) 
            $this->setInterval($data["interval"]);

        if (isset($data["range"]))
            $this->setRange($data["range"]);

        return $this;
    }
}
