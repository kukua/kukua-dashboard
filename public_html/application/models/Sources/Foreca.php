<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @package Models
 * @subpackage Sources
 * @since	22-02-2016
 * @version 1.0
 * @author	Siebren Kranenburg <siebren@kukua.cc>
 */
class Foreca extends Source {

	private $_db_host;
	private $_db_user;
	private $_db_pass;
	private $_db_name;
	private $_db;

	protected $_default_columns;

	/**
	 * @access public
	 * @return void
	 */
	public function __construct() {
		parent::__construct();

		$this->_db_host = MEASUREMENTS_HOST;
		$this->_db_user = MEASUREMENTS_USER;
		$this->_db_pass = MEASUREMENTS_PASS;
		$this->_db_name = "forecasts";
		$this->_db = new MySQLi($this->_db_host, $this->_db_user, $this->_db_pass, $this->_db_name);
		if ($this->_db->connect_errno) {
			throw new Exception("No connection to the forecasts database");
			exit;
		}

		$this->_default_columns = [
			'Temperature' => [
				'calc' => 'AVG',
				'name' => 'temp'
			],
			'Rainfall' => [
				'calc' => 'sum',
				'name' => 'precip'
			],
			'Humidity' => [
				'calc' => 'avg',
				'name' => 'humid'
			],
			'WindSpeed' => [
				'calc' => 'avg',
				'name' => 'windSpeed'
			],
			'WindDir' => [
				'calc' => 'avg',
				'name' => 'windDir'
			]
		];
	}

	/**
	 * @access public
	 * @param  Source
	 * @return Array
	 */
	public function get($source, $stations = Array()) {
		$select = $this->buildSelect($source->getMeasurement(), $source->getInterval());
		$where  = $this->buildWhere($source->getDateFrom(), $source->getDateTo());
		$group  = $this->buildGroup($source->getInterval());
		$sort   = $this->buildSort();

		$data = [];
		foreach($stations as $key => $station) {
			$from  = $this->buildFrom($station);
			$query = $select . $from . $where . $group . $sort;

			log_message("ERROR", $query);
			$dbResult = $this->_db->query($query);

			if ($dbResult) {
				$data[$key]["name"] = "Forecast";
				while($rows = $dbResult->fetch_assoc()) {
					$fin = $this->_measurement($source->getMeasurement());
					$data[$key]["data"][] = [
						(int) $rows["timestamp"],
						(float) round($rows[$fin['name']], 2)
					];
				}
			}
		}

		return $data;
	}

	/**
	 * Set select clause
	 *
	 * @access public
	 * @param  Array $select
	 * @return void
	 */
	public function buildSelect($measurement) {
		$column = $this->_measurement($measurement);

		$div = 3600;
		$select  = "SELECT ";
		$select .= " (UNIX_TIMESTAMP(timestamp) - mod(UNIX_TIMESTAMP(timestamp)," . $div . ")) * 1000 as timestamp,";
		$select .= $column['calc'] . "(" . $column['name'] . ") AS " . $column['name'] . ",";
		return trim($select, ",");
	}

	protected function _measurement($measurement) {
		switch($measurement) {
			case 'rain':
				return [
					'name' => 'precip',
					'calc' => 'sum'
				];
				break;
			default:
				return [
					'name' => $measurement,
					'calc' => 'AVG',
				];
				break;
		}
	}
	/**
	 * Build FROM
	 *
	 * @access public
	 * @param  deviceId
	 * @return void
	 */
	public function buildFrom($deviceId) {
		return " FROM " . $deviceId;
	}

	/**
	 * Set where clause
	 *
	 * @access public
	 * @param  timestamp $from
	 * @param  timestamp $to
	 * @param  string	 $extra
	 * @return string
	 */
	public function buildWhere($from, $to, $extra = null) {
		$where = " WHERE timestamp > FROM_UNIXTIME(" . $from . ") AND timestamp < FROM_UNIXTIME(" . $to . ")";
		$where .=" AND timestamp > NOW()";
		if ($extra !== null) {
			$where .= $extra;
		}
		return $where;
	}

	/**
	 * Build group by query
	 *
	 * @access public
	 * @param  string $interval (5m, 1h, etc)
	 * @return string $group
	 */
	public function buildGroup($interval) {
		$group = " GROUP BY UNIX_TIMESTAMP(timestamp) - UNIX_TIMESTAMP(timestamp) % 3600";
		return $group;
	}

	/**
	 * Build the sort query
	 *
	 * @access public
	 * @return string
	 */
	public function buildSort() {
		$sort = " ORDER BY timestamp ASC";
		return $sort;
	}
}
