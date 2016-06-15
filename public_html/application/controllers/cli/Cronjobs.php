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
		log_message ('error', 'Starting error report');

		/* Gathering stations */
		log_message ('error', 'Gathering stations...');
		$stations = (new Station())->load();

		$today    = (new DateTime());
		$startTS  = (new DateTime())->sub(new DateInterval('P1D'))->setTime(00,00,00);
		$endTS    = (new DateTime())->sub(new DateInterval('P1D'))->setTime(23,59,59);

		$data['dateFrom'] = (string) $startTS->getTimestamp();
		$data['dateTo']   = (string) $endTS->getTimestamp();
		$data['interval'] = '5m';

		foreach($stations as $station) {
			log_message('error', 'Debugging station ' . $station->getName());
			$this->_debug($station, $data);
			break;
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
	protected function _debug(Station $station, $data) {
		require(APPPATH . "models/Sources/Measurements.php");
		$measurements = new Measurements();
		$columns = $measurements->_default_columns;

		$final = [];
		$data['station']	= $station->getId();
		$data['multiple']	= false;
		$data['download']	= false;
		foreach($columns as $column => $value) {
			$data['measurement'] = $value['name'];

			$source = new Source($data);
			$res = $source->get();

			if (isset($res[0]['data'])) {
				$result[$value["name"]] = count($res[0]['data']);
			} else {
				$result[$value["name"]] = 0;
			}
		}

		$region = (new Region())->findById($station->getRegionId());
		$final['rows']		= json_encode($result);
		$final['station']   = $station->getName();
		$final['region']	= $region->getName();
		$final['created']   = (new DateTime())->format("Y-m-d H:i:s");

		$this->_addContent($station->getId(), $final);
	}

	/**
	 * @access protected
	 * @param  array $content
	 * @return void
	 */
	protected function _addContent($stationId, $content) {
		$this->_content[$stationId] = $content;
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
		print_r($this->getContent());
		exit;
	}
}
