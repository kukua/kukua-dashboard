<?php defined("BASEPATH") OR exit("No direct script access allowed");

class StationColumns extends CI_Model {

    const TABLE = "stations_columns";

    protected $_id;
    protected $_station_id;
    protected $_key;
    protected $_value;

    /**
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();

        $this->_id = null;
        $this->_station_id = null;
        $this->_key = null;
        $this->_value = null;
    }

    /**
     * @access public
     * @param  int $id
     * @throws InvalidArgumentException
     * @return void
     */
    public function setId($id) {
        if (!is_numeric($id)) {
            throw new InvalidArgumentException("No valid param id supplied");
        }
        $this->_id = (int) $id;
    }

    /**
     * @access public
     * @return int
     */
    public function getId() {
        return $this->_id;
    }

    /**
     * @access public
     * @param  int $id
     * @throws InvalidArgumentException
     * @return void
     */
    public function setStationId($id) {
        if (!is_numeric($id)) {
            throw new InvalidArgumentException("No valid param supplied");
        }
        $this->_station_id = (int) $id;
    }

    /**
     * @access public
     * @return int
     */
    public function getStationId() {
        return $this->_station_id;
    }

    /**
     * @access public
     * @param  string $key
     * @throws InvalidArgumentException
     * @return void
     */
    public function setKey($key) {
        if (!is_string($key)) {
            throw new InvalidArgumentException("No valid param supplied");
        }
        $this->_key = (string) $key;
    }

    /**
     * @access public
     * @return string
     */
    public function getKey() {
        return $this->_key;
    }

    /**
     * @access public
     * @param  string $value
     * @throws InvalidArgumentException
     * @return void
     */
    public function setValue($value) {
        if (!is_string($value)) {
            throw new InvalidArgumentException("No valid param supplied");
        }
        $this->_value = (string) $value;
    }

    /**
     * @access public
     * @return string
     */
    public function getValue() {
        return $this->_value;
    }

    /**
     * @access public
     * @param  Array $data
     * @return StationColumns
     */
    public function populate($data) {
        if (!is_array($data)) {
            throw new Exception("No valid param supplied");
        }

        if (isset($data["id"]))
            $this->setId($data["id"]);

        if (isset($data["station_id"]))
            $this->setStationId($data["station_id"]);

        if (isset($data["key"]))
            $this->setKey($data["key"]);

        if (isset($data["value"]))
            $this->setValue($data["value"]);

        return $this;
    }

    /**
     * @access public
     * @return Array
     */
    public function toArray() {
        return [
            'id' => $this->getId(),
            'station_id' => $this->getStationId(),
            'key' => $this->getKey(),
            'value' => $this->getValue(),
        ];
    }

    /**
     * @access public
     * @param  int id
     * @return StationColumns
     */
    public function findById($id) {
        $this->db->select("*");
        $this->db->from(self::TABLE);
        $this->db->where("id", $id);
        $get = $this->db->get()->row_array();
        return $this->populate($get);
    }

    /**
     * @access public
     * @param  int stationId
     * @param  string key
     * @return StationColumns
     */
    public function findByStationId($stationId) {
        $this->db->select("*");
        $this->db->from(self::TABLE);
        $this->db->where("station_id", $stationId);
        $get = $this->db->get()->result_array();

        $result = [];
        if (is_array($get)) {
            foreach($get as $row) {
                $result[] = (new StationColumns())->populate($row);
            }
        }
        return $result;
    }

    /**
     * @access protected
     * @param  int stationId
     * @param  string key
     * @return StationColumns
     */
    public function find($stationId, $key) {
        $this->db->select("*");
        $this->db->from(self::TABLE);
        $this->db->where("station_id", $stationId);
        $this->db->where("key", $key);
        $get = $this->db->get()->row_array();

        if (is_array($get) && !empty($get)) {
            return $this->populate($get);
        }
        return false;
    }

    /**
     * Saves current object state
     *
     * @access public
     * @return boolean | Smsclient
     */
    public function save() {
        if ($this->_validate() !== true) {
            return false;
        }

        if (is_null($this->getId()) === true) {
            if ($this->db->insert(self::TABLE, $this->toArray())) {
                $this->setId($this->db->insert_id());
                return $this;
            }
        } else {
            $this->db->where("id", $this->getId());
            if ($this->db->update(self::TABLE, $this->toArray())) {
                return $this;
            }
        }

        return false;
    }

    public function delete($id) {
        if (!is_numeric($id)) {
            throw new invalidargumentexception("param supplied not valid");
        }

        $object = $this->findById($id);
        if ($object->getId() !== null) {
            $this->db->delete(self::TABLE, array('id' => $object->getId()));
            return true;
        }
        return false;
    }

    /**
     * @access public
     * @access int $id
     * @return boolean
     */
    public function deleteByStationId($id) {
        if (!is_numeric($id)) {
            throw new invalidargumentexception("param supplied not valid");
        }

        $objects = $this->findByStationId($id);
        if (!empty($objects)) {
            $this->db->delete(self::TABLE, array('station_id' => $id));
            return true;
        }
        return false;
    }

    /**
     * @access protected
     * @return boolean
     */
    protected function _validate() {
        $validStationId = $this->getStationId() != null;
        $validKey       = $this->getKey() != "" || $this->getKey() != null;
        $validValue     = $this->getValue() != "" || $this->getValue() != null;

        if ($validStationId && $validKey && $validValue) {
            return true;
        }
        return false;
    }
}
