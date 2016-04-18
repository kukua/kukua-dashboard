<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Forecast extends MyController {

    public function __construct() {
        parent::__construct();
        $this->allow('members');
    }

    public function index() {
        $this->data["country"] = "Tanzania";
        $this->load->view("forecast/index", $this->data);
    }

    public function get() {
        echo json_encode(["url" => GlobalHelper::getforeCastMap($this->input->post("country"))]);
        exit;
    }
}
