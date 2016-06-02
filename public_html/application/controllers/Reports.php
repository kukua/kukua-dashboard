<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @package Controllers
 * @since	01-06-2016
 * @version 1.0
 * @author	Siebren Kranenburg <siebren@kukua.cc>
 */
class Reports extends MyController {

	/**
	 * @access public
	 * @return void
	 */
	public function __construct() {
		parent::__construct();
		$this->allow('admin');
	}

	/**
	 * @access public
	 * @return void
	 */
	public function index() {
		$reports = new Report();
		$this->data['reports'] = $reports->load();
		$this->load->view('reports/index', $this->data);
	}
}
