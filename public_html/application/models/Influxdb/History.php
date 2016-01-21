<?php defined('BASEPATH') OR exit('No direct script access allowed');

class History extends Influxdb {

    protected $_user;
    protected $_password;
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

    public function getToken() {
        return $this->_token;
    }
    /**
     * Returns user
     *
     * @access public
     * @return string $user
     */
    public function getUser() {
        return $this->_user;
    }

    /**
     * Returns password
     *
     * @access public
     * @return string $user
     */
    public function getPassword() {
        return $this->_password;
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
            $this->_group .
            $this->_order;
    }

    /**
     * Set select clause
     *
     * @access public
     * @param  Array $select
     * @return void
     */
    public function setSelect($select = Array()) {
        $query = "SELECT ";
        foreach($select as $column => $name) {
            $query .= " " . $column . " as " . $name . ",";
        }
        $query = rtrim($query, ",");
        $this->_select = $query;
    }

    /**
     * Set from clause
     *
     * @access public
     * @param  String $from
     * @return void
     */
    public function setFrom($from = null) {
        //Get the country a user registered to
        $user    = $this->ion_auth->user()->row();
        $country = strtolower($user->country);

        $query = implode(",", Graph::$stations[$country]);
        if ($from !== null) {
            $query = Graph::$stations[$country][$from];
        }
        $this->_from = " FROM " . $query;
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
     * Set group clause
     *
     * @access public
     * @param  String $group
     * @return void
     */
    public function setGroup($group = "1h") {
        $query = "time(1h)";
        if (!is_null($group)) {
            $query = "time(" . $group . ")";
            $this->_group = " GROUP BY " . $query;
        }
    }
}
