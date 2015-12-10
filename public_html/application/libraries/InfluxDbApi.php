<?php

class InfluxDbApi {
    protected $_url;
    protected $_port;
    protected $_user;
    protected $_password;

    protected $_select;
    protected $_from;
    protected $_where;
    protected $_group;
    protected $_order;
    protected $_output;

    protected $_validGroupedBy;

    /**
     * Class constructor, sets default values
     *
     * @access public
     * @return void
     */
    public function __construct() {
        $this->_url = "http://dashboard.kukua.cc";
        $this->_port = "8086";
        $this->_user = "kukua";
        $this->_password = "pZhkvQwfP5";

        $this->_select = "SELECT time,
                                 count(rainTicks) as RainTicks,
                                 mean(windTicks) as WindTicks,
                                 mean(windGustTicks) as WindGustTicks,
                                 mean(windDir) as WindDir,
                                 mean(windGustDir) as WindGustDir,
                                 mean(temp) as Temperature,
                                 mean(hum) as Humidity,
                                 mean(presBMP) as PresBMP";
        $this->_order  = "asc";
        $this->_validGroupedBy = ["5m", "1h", "12h", "24h"];
    }

    /**
     * Returns the given output by $this->call()
     *
     * @access public
     * @return Array
     */
    public function getOutput() {
        return $this->_output;
    }

    /**
     * Build the query with given parameters
     *
     * @access public
     * @param  mixed $from
     * @param  mixed $dateFrom
     * @param  mixed $dateTo
     * @param  string $group
     */
    public function buildQuery($type = null, $from = null, $dateFrom = null, $dateTo = null, $group = "1h") {
        $this->_populate($type, $from, $dateFrom, $dateTo, $group);

        $query = $this->_select . "
            FROM " . $this->_from . "
            WHERE " . $this->_where . "
            GROUP BY " . $this->_group . "
            ORDER " . $this->_order;
        $this->_query = urlencode($query);
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
            "Accept: application/json"
        ];

        $init = $this->_url . ":" . $this->_port . "/db/data/series?p=" . $this->_password . "&u=" . $this->_user . "&q=" . $this->_query;
        $ch = curl_init($init);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $output = curl_exec($ch);
        curl_close($ch);
        $this->_output = $output;

        if (empty($this->_output)) {
            Throw new Exception("No results");
        }
    }

    /**
     * Populate class variables
     *
     * @access protected
     * @param  string $from
     * @param  string $dateFrom
     * @param  string $dateTo
     * @param  string $group
     * @throws Exception
     * @return void
     */
    protected function _populate($type, $from, $dateFrom, $dateTo, $group) {
        if (!$this->_validTimestamp($dateFrom) || !$this->_validTimestamp($dateTo)) {
            throw new InvalidArgumentException("Please supply a from and/or to date as timestamp");
        }

        //select
        if ($type !== null && is_array($type)) {
            $select = "SELECT time, ";
            foreach($type as $column => $name) {
                $prefix = "mean";
                if ($column === "rain") {
                    $prefix = "count";
                }
                $select .= $prefix . " (" . $column . ") as " . $name . ",";
            }
            $select = rtrim($select, ",");
            $this->_select = $select;
        }

        //from
        $user = GlobalHelper::getUser();
        $this->_from = implode(",", Graph::$stations[$user]);
        if ($from !== null) {
            $this->_from = Graph::$stations[$user][$from];
        }

        //where
        $this->_where = "time > " . $dateFrom . "s AND time < " . $dateTo . "s";

        //group by
        $this->_group = "time(" . $group . ")";
        if ($this->_validateGroup($group) !== true) {
            $this->_group = "time(1h)";
        }
    }

    /**
     * Validate group by
     *
     * @access protected
     * @param  string $group
     * @return boolean
     */
    protected function _validateGroup($group) {
        return in_array($group, $this->_validGroupedBy);
    }

    /**
     * Validates if param is timestamp
     *
     * @access protected
     * @param  int | string $time
     * @return boolean
     */
    protected function _validTimestamp($time) {
        return ((int) $time === $time)
            && ($time <= PHP_INT_MAX)
            && ($time >= ~PHP_INT_MAX);
    }
}
