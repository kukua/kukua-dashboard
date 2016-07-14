<?php
#This file can be called directly

/**
 * @package Controllers
 * @subpackage Api
 * @since	01-03-2016
 * @version 1.0
 * @author	Siebren Kranenburg <siebren@kukua.cc>
 */
class Stations extends MyController {

	/**
	 * @access public
	 * @return void
	 */
	public function get() {
		$stations = (new Station())->load();
		foreach($stations as $key => $station) {
			if ($station->getLatitude() && $station->getLongitude()) {
				$result[$key]["elevation"] = $station->getElevation();
				$result[$key]["title"] = $station->getName();
				$result[$key]["lat"] = (float) $station->getLatitude();
				$result[$key]["lng"] = (float) $station->getLongitude();
			}
		}
		echo json_encode($result);
		exit;
	}

	/**
	 * Request stations by device IDs
	 *
	 * @access public
	 * @return void
	 */
	public function find() {
		if (!isset($_GET["ids"])) {
			echo json_encode(['error' => 'An ; separated list of unique ids is expected, not given.']);
			exit;
		}
		$deviceIds = explode(';', $_GET['ids']);
		if (!is_array($deviceIds)) {
			echo json_encode(['error' => 'An ; separated list of unique ids is expected, not given.']);
			exit;
		}

		foreach($deviceIds as $key => $deviceId) {
			try {
				$station = (new Station())->findByDeviceId($deviceId);
				if ($station !== false) {
					$result[$key]['elevation'] = $station->getElevation();
					$result[$key]["title"] = $station->getName();
					$result[$key]["lat"] = (float) $station->getLatitude();
					$result[$key]["lng"] = (float) $station->getLongitude();
				}
			} catch (Exception $e) {
				echo json_encode(['error' => 'The deviceID ' . $deviceId . ' is invalid']);
				exit;
			}
		}

		echo json_encode($result);
		exit;
	}
}
