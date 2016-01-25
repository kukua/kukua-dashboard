<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Forecast extends MyController {

    public function __construct() {
        parent::__construct();
        $this->allow('members');
    }

    public function index() {
        $this->data["availableCountries"] = $this->_user->country;
        if (@unserialize($this->_user->country)) {
            $countries = unserialize($this->_user->country);
            $this->data["availableCountries"] = $countries[0];
            $this->data["url"] = GlobalHelper::getForecastMap($countries[0]);
            if (count($countries) > 1) {
                $this->data["availableCountries"] = $countries;
                $this->data["url"] = GlobalHelper::getForecastMap($countries);
            }
        } else {
            $this->data["availableCountries"] = $this->_user->country;
            $this->data["url"] = GlobalHelper::getForecastMap($this->_user->country);
        }
        $this->load->view("forecast/index", $this->data);
    }

    public function get() {
        echo json_encode(["url" => GlobalHelper::getforeCastMap($this->input->post("country"))]);
        exit;
    }
}
