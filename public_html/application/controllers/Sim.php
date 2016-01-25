<?php defined("BASEPATH") OR exit("No direct script access allowed");

use \Curl\Curl;

class Sim extends MyController {

    public function __construct() {
        parent::__construct();
        $this->allow("admin");
    }

    public function index() {
        $sims = new Eseye();
        GlobalHelper::debug($sims->getSims());

        $this->load->view("sim/index", $this->data);
    }
}
