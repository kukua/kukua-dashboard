<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Forecast extends MyController {

	public function __construct() {
		parent::__construct();
		$this->allow('members');
	}

	public function index() {
		$this->load->view("forecast/index", $this->data);
	}

	public function content() {
		$url = 'http://vip.foreca.com/kukua/maps-tanzania.html?rain';
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		$body = curl_exec($ch);
		$body = str_replace('<head>', '<head><base href="http://vip.foreca.com/kukua/" />', $body);
		echo $body;
		exit;
	}

	public function get() {
		echo json_encode(["url" => GlobalHelper::getforeCastMap($this->input->post("country"))]);
		exit;
	}
}
