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

    public function __construct() {
        parent::__construct();

        $this->_token  = "cm9vdDo2NjhiYjg1NDVjYmM2MTE1ZWY0Yjk1OTYz";
        $this->_url = "http://d.kukua.cc";
        $this->_port = ":9003";
        $this->_db = "data";
        $this->_suffix = "/query";
    }

    public function get($source) {
        $stations = (new Stations())->findByCountryId($source->getCountry());
        $dates["dateFrom"] = $source->getDateFrom();
        $dates["dateTo"]   = $source->getDateTo();

        $query = [];
        $query["where"] = $this->getWhere($dates);
        $query["group"] = $this->getGroup($source->getInterval());

        $result = [];

        if (strtolower($source->getWeatherType()) != "all") {
            foreach($stations as $key => $station) {
                $column = (new StationColumns())->find($station->id, $source->getWeatherType());

                if ($column !== false) {
                    $build = $this->_build($query, $station, $column);
                    if ($build !== null) {
                        $result[] = $build;
                    }
                }
            }
        } else {
            foreach($stations as $station) {
                $columns = (new StationColumns())->findByStationId($station->id);

        		#temp hack to add a where clause
        		if (strpos($station->name, ";") !== false) {
        		    $query["where"] = $this->andWhere($query["where"], $station);
					if ($station->station_id == "Foreca") {
        		    	$query["group"] = $this->getGroup("1h");
					}
        		}

        		$query["from"]   = $this->getFrom($station->station_id);
				$query["select"] = "SELECT ";
				foreach($columns as $column) {
					$query["select"] = $this->addSelect($query["select"], $column);
				}

				$query["select"] = rtrim($query["select"], ",");
				if (!empty($query["select"])) {
            		$q = $query["select"] . " " . $query["from"] . " " . $query["where"] . " " . $query["group"];
					log_message('error', $q);
					$values = $this->_parse($q);
					if (count($values) > 0) {
                		$result[] = $values;
					}
				}
            }
        }
        return $result;
    }

    protected function _build($query, $station, $column) {
        #temp hack to add a where clause
        if (strpos($station->name, ";") !== false) {
            $query["where"] = $this->andWhere($query["where"], $station);
		}

		if ($station->station_id == "Foreca") {
            $query["group"] = $this->getGroup("1h");
		}

        $query["select"] = $this->getSelect($column);
        $query["from"]   = $this->getFrom($station->station_id);
        if (!empty($query["select"]) && !empty($query["from"])) {
            $q = $query["select"] . " " . $query["from"] . " " . $query["where"] . " " . $query["group"];
            log_message('error', $q);
            $values = $this->_parse($q);
            if (count($values) >= 1) {
                return $values;
            }
        }
    }

    public function getSelect($column) {
        $prefix = "mean(";
        if ($column->getKey() == "rain") {
            $prefix = "sum(";
        }

        $translate = GlobalHelper::allWeatherTypes();
        return "SELECT " . $prefix . $column->getValue() . ") as " . $translate[$column->getKey()];
    }

    public function addSelect($select, $column) {
        $prefix = "mean(";
        if ($column->getKey() == "rain") {
            $prefix = "sum(";
        }

		$translate = GlobalHelper::allWeatherTypes();
		$select .= " " . $prefix . $column->getValue() . ") as " . $translate[$column->getKey()] . ",";
		return $select;
    }

    public function getFrom($stationId) {
        return " FROM " . $stationId;
    }

    public function getWhere($where) {
        if (isset($where["dateFrom"]) && isset($where["dateTo"])) {
            $query = "time > " . $where["dateFrom"] . "s AND time < " . $where["dateTo"] . "s";
        }
        return "WHERE " . $query;
    }

    public function getGroup($interval) {
        return "GROUP BY time(" . $interval . ")";
    }

    /**
     * @access  public
     * @param   string $where
     * @param   Stations
     * @return  string
     *
     * @example data;deviceId=abc
     * @example foreca;id=abc;column=value
     */
    public function andWhere($where, $station) {
        $extra = explode(";", $station->name);
        foreach($extra as $k => $keyval) {
            if ($k == 0) continue;

            $res = explode("=", $keyval);
            $where .= " AND " . $res[0] . " = '" . $res[1] . "'";
        }

        if ($extra[0] == "forecast") {
            $where .= " AND time > now()";
        }
        return $where;
    }

    protected function _parse($q) {
        $opts = [
            "q" => $q,
            "db" => $this->_db,
        ];
        $request = $this->_curl($opts, true);
        if ($request != false || $request != "" || $request != null) {
            $responses = $request->results;
            if (isset($responses[0]->series) !== false) {
                $response = $this->_manipulate($responses[0]->series);
                return $response[0];
            }
        }
        return [];
    }

    protected function _curl($opts = [], $headers = False) {
        $curl = new \Curl\Curl();
        if ($headers !== false) {
            $curl->setHeader("Authorization", "Basic " . $this->_token);
        }

        $url = $this->_url . $this->_port . $this->_suffix;
        $result = $curl->get($url, $opts);
        return $result;
    }

    protected function _manipulate($data) {
        if (count($data)) {
            foreach($data as $station => $values) {
                if (count($values->values)) {

                    //Set correct name
                    $niceName = (new Stations())->findByStationId($values->name)->name;
                    if (strpos($niceName, ";") !== false) {
                        $extra = explode(";", $niceName);
                        $values->name = ucfirst($extra[0]);
                    } else {
                        $values->name = ucfirst($niceName);
                    }

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
