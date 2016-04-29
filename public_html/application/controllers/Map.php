<?php defined("BASEPATH") OR exit("No direct script access allowed");

class Map extends MyController {

	public function __construct() {
		parent::__construct();
	}

	public function index() {
		$this->load->view("map/index", $this->data);
	}
}
