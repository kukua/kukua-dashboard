<?php

class Sensordata extends MyController {

    protected $_request;

    /**
     *
     */
    public function __construct() {
        parent::__construct();
        $this->_request["interval"] = "5m";
    }

    /**
     * @access public
     * @return void
     */
    public function get() {
        if ($this->_validateRequest() !== False) {
            $this->_populate($this->input->post());

            $source = new Source($this->_request);
            echo json_encode($source->gather());
            exit;
        }
    }

    /**
     * @access private
     * @param  Array $data
     * @return Array
     */
    private function _populate($data) {
        if (isset($data["country"]))
            $this->_request["country"] = $data["country"];

        if (isset($data["type"]))
            $this->_request["type"] = $data["type"];

        if (isset($data["dateFrom"]))
            $this->_request["dateFrom"] = $data["dateFrom"];

        if (isset($data["dateTo"]))
            $this->_request["dateTo"] = $data["dateTo"];

        if (isset($data["interval"]))
            $this->_request["interval"] = $data["interval"];

        if (isset($data["range"]))
            $this->_request["range"] = $data["range"];
    }

    /**
     * Check if the request made is valid by
     * checking if all the required parameters
     * are given
     *
     * @access   private
     * @response 200|400
     * @return   void
     */
    private function _validateRequest() {
        $valid = True;
        $err = Array();

        if ($this->input->post("country") == False) {
            $valid = False;
            $err[] = "No country supplied";
        }
        if ($this->input->post("type") == False) {
            $valid = False;
            $err[] = "No type supplied";
        }
        if ($this->input->post("dateFrom") == False) {
            $valid = False;
            $err[] = "No date from supplied";
        }
        if ($this->input->post("dateTo") == False) {
            $valid = False;
            $err[] = "No date to supplied";
        }

        if ($valid !== True) {
            http_response_code(400);
            echo "<h1>400 Bad Request </h1>";
            foreach($err as $err) {
                echo "<p>" . $err . "</p>";
            }
        }

        return $valid;
    }
}
