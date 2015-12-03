<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Forecast extends MyController {

    public function __construct(){
        parent::__construct();
        GlobalHelper::requireLogin();
    }

    public function index() {
        $this->data["url"] = GlobalHelper::getForecastMap();
        $this->load->view("forecast/index", $this->data);
    }
}
