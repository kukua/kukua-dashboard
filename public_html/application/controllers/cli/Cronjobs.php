<?php

/**
 * @package Controllers
 * @subpackage CLI
 * @since	24-05-2016
 * @version 1.0
 * @author	Siebren Kranenburg <siebren@kukua.cc>
 */
class Cronjobs extends CI_Controller {

	private $_content;

	public function __construct() {
		parent::__construct();

		if (!$this->input->is_cli_request()) {
			log_message('error', 'No valid request type for cronjob');
			throw new Exception('Only accessable through CLI');
		}
	}

	/**
	 * Cronjob that sends SMS Text messages with forecast info
	 *
	 * @access public
	 * @return void
	 */
	public function smsForecast() {
		log_message('error', 'Starting SMS service');
		log_message('error', 'Gathering users...');
		$sId	 = TWILIO_SID;
		$token	 = TWILIO_TOKEN;
		$twilio  = new Services_Twilio($sId, $token);

		$smsClients = new Smsclient();
		$clients = $smsClients->load();
		$number = TWILIO_NUMBER;

		foreach($clients as $client) {
			$content = file_get_contents('http://wap.weather.fi/peek?param1=' . $client->getLocation() . '&lang=en&format=text1');
			$twilio->account->messages->sendMessage(
				$number,
				$client->getNumber(),
				$content
			);
			log_message('error', 'SMS send to ' . $client->getNumber());
		}

		log_message('error', 'Finished the SMS service');
		exit;
	}

	/**
	 * We are measuring data of 6 hours (midnight till 06:00:00)
	 *
	 * Devices upload per 5 minutes
	 * Expected measurement rows: ~72 per device
	 *	  - Exception: device upload per 10 minutes
	 *		- ??
	 *		- ??
	 *
	 * @access public
	 * @return void
	 */
	public function report($save = false) {
		require_once(APPPATH . "models/Sources/Measurements.php");
		log_message ('error', 'Starting error report');

		/* Gathering stations */
		log_message ('error', 'Gathering stations...');
		$regions  = (new Region())->load();

		$today    = (new DateTime());
		$startTS  = (new DateTime())->sub(new DateInterval('P1D'))->setTime(00,00,00);
		$endTS    = (new DateTime())->sub(new DateInterval('P1D'))->setTime(23,59,59);

		$data['dateFrom'] = $startTS->getTimestamp();
		$data['dateTo']   = $endTS->getTimestamp();
		$data['interval'] = '5m';
		$data['created']  = (new DateTime())->format("Y-m-d H:i:s");

		/* Looping through stations  */
		foreach ($regions as $region) {
			log_message('error', 'Debugging station ' . $region->getName());
			$this->_debug($region, $data);
		}

		if ($save == true) {
			$this->_save();
		} else {
			$this->_outputBash();
		}
	}

	/**
	 * @access protected
	 * @param  Station $station
	 * @param  Array $data
	 * @return void
	 */
	protected function _debug(Region $region, $data) {
		$measurements = new Measurements();
		$columns = $measurements->_default_columns;

		foreach ($columns as $column => $values) {
			$data['region'] = $region->getId();
			$data['type']   = $column;
			$this->_execDebug($region, $data);
		}
	}

	/**
	 * @access protected
	 * @param  Station $station
	 * @param  Array $data
	 * @return void
	 */
	protected function _execDebug(Region $region, $data) {
		$curl = new \Curl\Curl();
		$curl->setOpt(CURLOPT_SSL_VERIFYPEER, false);
		$curl->post("https://dashboard.kukua.cc/api/sensordata/get",
			$data
		);

		$rows = json_decode($curl->response);

		foreach($rows as $row) {
			$counter = count($row->data);

			$content = [];
			$content["region"] = $region->getName();
			$content["station"] = $row->name;
			$content["measurement"] = $data["type"];
			$content["count"] = $counter;
			$content["created"] = $data["created"];

			$this->_addContent($content);
		}
	}

	/**
	 * @access protected
	 * @param  array $content
	 * @return void
	 */
	protected function _addContent($content) {
		$this->_content[] = $content;
	}

	/**
	 * @access protected
	 * @return $this->_content
	 */
	protected function getContent() {
		return $this->_content;
	}

	/**
	 * @access protected
	 * @return True
	 */
	protected function _save() {
		foreach($this->getContent() as $content) {
			$object = new Report($content);
			$object->save();
		}
		return true;
	}

	/**
	 * @access public
	 * @return void
	 */
	protected function _outputBash() {
		foreach($this->getContent() as $content) {
			printf("%-20s", $content["region"]);
			printf("%-30s", $content["station"]);
			printf("%-20s", $content["measurement"]);
			printf("%-2s",  $content["count"]);
			echo "\n";
		}
	}
}
