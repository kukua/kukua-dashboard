<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Forecast extends Influxdb {

    protected $_token;

    /**
     * Class constructor
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();

        $this->_token = "";
    }

    /**
     * @access public
     * @return string
     */
    public function getToken() {
        return $this->_token;
    }

    /**
     * Build query and return results
     *
     * @access public
     * @return Array
     */
    public function find($params) {
        $this->_build($params);
        $this->_query =
            $this->_select .
            $this->_from .
            $this->_where .
            $this->_group;
    }

    /**
     * Set select clause
     *
     * @access public
     * @param  Array $select
     * @return void
     */
    public function setSelect($select = Array()) {
        $query = "SELECT time, ";
        foreach($select as $column => $name) {
            $query .= " " . $column . " as " . $name . ",";
        }
        $query = rtrim($query, ",");
        $this->_select = $query;
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
     * Set where clause
     *
     * @access public
     * @param  Array $where
     * @return void
     */
    public function setWhere($where) {
        if (is_array($where)) {
            if (isset($where["dateFrom"]) && isset($where["dateTo"])) {
                $query  = "time > " . $where["dateFrom"] . "s AND time < " . $where["dateTo"] . "s";
                $query .= " AND type='hourly'";
                $query .= " AND id='100156918'";
                $query .= " AND time > NOW()";
            }
        } else {
            $query  = "time > now() AND time < now() + 10d";
            $query .= " AND type='daily'";
            $query .= " AND id='100156918'";
        }
        $this->_where = " WHERE " . $query;
    }
}
