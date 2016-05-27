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
	public function report() {
		require_once(APPPATH . "models/Sources/Measurements.php");
		log_message ('error', 'Starting error report');

		/* Gathering stations */
		log_message ('error', 'Gathering stations...');
		$regions  = (new Region())->load();
		$startTS  = (new DateTime())->setTime(00, 00, 00);
		$endTS    = (new DateTime())->setTime(06, 00, 00);

		$data['dateFrom'] = $startTS->getTimestamp();
		$data['dateTo']   = $endTS->getTimestamp();
		$data['interval'] = '5m';

		/* Looping through stations  */
		foreach ($regions as $region) {
			log_message('error', 'Debugging station ' . $region->getName());
			$this->_debug($region, $data);
		}
		$this->_sendMail();
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
			$content = "";
			$counter = count($row->data);

			$color = "#BEF781"; //green
			switch($counter) {
				case $counter <= 0:
					$color = "#FFFFFF"; //white
					break;
				case ($counter <= 30 && $counter > 0):
					$color = "#F78181"; //red
					break;
				case ($counter <= 50 && $counter > 30):
					$color = "#F7BE81"; //orange
					break;
				case ($counter <= 71 && $counter > 50):
					$color = "#81DAF5"; //blue
					break;
			}

			$content .= "<tr>";
			$content .= "<td bgcolor='" . $color . "'>" . $region->getName(). "</td>";
			$content .= "<td bgcolor='" . $color . "'>" . $row->name. "</td>";
			$content .= "<td bgcolor='" . $color . "'>" . $data["type"]. "</td>";
			$content .= "<td bgcolor='" . $color . "'>" . $counter. "</td>";
			$content .= "</tr>";

			$this->_addMailContent($content);
		}
	}

	protected function _addMailContent($content) {
		$this->_content .= $content;
	}

	protected function getMailContent() {
		return $this->_content;
	}

	protected function _sendMail() {

		$content = "
<html>
	<head>
		<title>Error reporting</title>
		<meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />
		<style>
			table {
				width: 100%;
				border: 0;
			}
			table tr td {
				padding: 5px 10px;
			}
		</style>
	</head>
	<body>
		<h1>Error reporting</h1>
		<table border='0' cellpadding='0' width='600px' cellspacing='0'>
			<thead>
				<tr>
					<th align='left'>Region</th>
					<th align='left'>Station</th>
					<th align='left'>Type</th>
					<th align='left'>DB Rows</th>
				</tr>
			</thead>
			<tbody>
				" . $this->getMailContent() . "
			</tbody>
		</table>
	</body>
</html>";

        $lib = new Email();
        $lib->setFrom("Kukua B.V. <info@kukua.cc>");
        $lib->setTo("Siebren Kranenburg <siebren@kukua.cc>");
        $lib->setSubject("Daily report");
        $lib->setContent($content);
		$lib->send();
	}
}
