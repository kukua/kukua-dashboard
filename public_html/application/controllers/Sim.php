<?php defined("BASEPATH") OR exit("No direct script access allowed");

use \Curl\Curl;

class Graph extends MyController {

    public function __construct() {
        public function __construct();
        $this->allow("admin");
    }

    public function index() {
        $sims = new Sim();
        $sims->getSims();

        $this->load->view("sim/index", $this->data);
    }
}
