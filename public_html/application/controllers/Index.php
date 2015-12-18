<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Index extends MyController {

    public function __construct() {
        parent::__construct();
        $this->allow("members");
    }

    public function index() {
        $this->load->view("index/index", $this->data);
    }
}
