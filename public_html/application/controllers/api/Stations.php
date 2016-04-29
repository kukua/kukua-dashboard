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
}
