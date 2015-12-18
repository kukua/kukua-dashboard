<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Forecast extends MyController {

    public function __construct(){
        parent::__construct();
        $this->allow('members');
    }

    public function index() {
        $this->data["url"] = GlobalHelper::getForecastMap($this->_user);
        $this->load->view("forecast/index", $this->data);
    }
}
