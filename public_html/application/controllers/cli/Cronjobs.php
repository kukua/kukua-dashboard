<?php

/**
 * @package Controllers
 * @subpackage CLI
 * @since	24-05-2016
 * @version 1.0
 * @author	Siebren Kranenburg <siebren@kukua.cc>
 */
class Cronjobs extends CI_Controller {

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
	public function report() {
		require_once(APPPATH . "models/Sources/Measurements.php");
		log_message ('error', 'Starting error report');

		/* Gathering stations */
		log_message ('error', 'Gathering stations...');
		$stations = (new Station())->load();
		$startTS  = (new DateTime())->setTime(00, 00, 00);
		$endTS    = (new DateTime())->setTime(06, 00, 00);

		$data['dateFrom'] = $startTS->getTimestamp();
		$data['dateTo']   = $endTS->getTimestamp();
		$data['interval'] = '5m';

		/* Looping through stations  */
		foreach ($stations as $station) {
			log_message('error', 'Debugging station ' . $station->getName());
			$this->_debug($station, $data);
		}
	}

	/**
	 * @access protected
	 * @param  Station $station
	 * @param  Array $data
	 * @return void
	 */
	protected function _debug(Station $station, $data) {
		$measurements = new Measurements();
		$columns = $measurements->_default_columns;
		foreach ($columns as $column) {
			$data['region'] = $station->getRegionId();
			$data['type']   = $column["name"];
			$this->_execDebug($station, $data);
		}
	}

	/**
	 * @access protected
	 * @param  Station $station
	 * @param  Array $data
	 * @return void
	 */
	protected function _execDebug(Station $station, $data) {
		$curl = new \Curl\Curl();
		$curl->setOpt(CURLOPT_SSL_VERIFYPEER, false);
		$curl->post("https://dashboard.kukua.cc/api/sensordata/get",
			$data
		);

		$result = json_decode($curl->response);
		print_r($station->getName() . "\t-\t" . count($result));
	}
}
