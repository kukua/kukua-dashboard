<?php

class GlobalHelper {

	public static function meaningOf($values) {
		$return = null;

		switch($values) {
			case 'time':
				$return = "UTC (d-m-Y)";
			break;
			case 'Temperature':
				$return = "Celcius";
				break;
			case 'Rainfall':
				$return = "mm";
				break;
			case 'Humidity':
				$return = "%";
				break;
			case 'Wind':
				$return = "km/h";
				break;
			case 'WindGust':
				$return = "km/h";
				break;
			case 'WindDirection':
				$return = "degrees";
				break;
			case 'WindGustDirection':
				$return = "degrees";
				break;
			case 'Battery':
				$return = "Voltage";
				break;
		}
		return $return;
	}

	public static function outputCsv($fileName, $assocDataArray = Array()) {
		$zipFile = '/tmp/' . $fileName . '.zip';
		$zip = new ZipArchive;
		if ($zip->open($zipFile, ZipArchive::CREATE) !== true) {
			throw new Exception("Cannot open zip archive");
		}

		try {
			if (is_array($assocDataArray) && !empty($assocDataArray)) {
				foreach($assocDataArray as $i => $station) {

					#GlobalHelper::debug($station);
					#continue;


					$fp = fopen('php://output', 'w');
					if ($fp === false) {
						throw new Exception("unable to open php's output buffer");
					}

					ob_start();
					$arr = [];


					$timeKey = null;
					foreach($station->columns as $columnKey => $column) {
						if ($column == "time") {
							$timeKey = $columnKey;
						}
						$types[$columnKey] = GlobalHelper::meaningOf($column);
					}

					/* Header */
					fputcsv($fp, $station->columns);

					/* Second header */
					fputcsv($fp, $types);

					/* Content */
					foreach($station->values as $values) {
						if ($timeKey !== null) {
							if (isset($values[$timeKey])) {
								$miliseconds = $values[$timeKey];
								$seconds = $miliseconds / 1000;

								$now = DateTime::createFromFormat('U', $seconds);
								$values[$timeKey] = $now->format("d-m-Y H:i:s");
							}
						}

						fputcsv($fp, $values);
					}

					/*if ($station->values[0][2] !== null) {
						$stationKey = $station->name . ";deviceId=" . $station->values[0][2];
					} else {
						$stationKey = $station->name;
					}*/

					$currentStationName = $station->name . " - " . $i;
					//$station = (new Station())->findByStationId($stationKey);
					$station = false;
					if ($station !== false) {
						$currentStationName = ucfirst($station->getName());
					}

					$string = ob_get_contents();
					$zip->addFromString($currentStationName . ".csv", $string);
					ob_clean();
					fclose($fp);
				}
				$zip->close();

				#die();
				if (file_exists($zipFile)) {
					header('Pragma: public');
					header('Expires: 0');
					header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
					header('Cache-Control: private', false);
					header('Content-Type: application/zip');
					header('Content-Disposition: attachment;filename=' . $fileName . '.zip');
					header('Content-Length: ' . filesize($zipFile));
					header("Content-Transfer-Encoding: binary");
					readfile($zipFile);
					ob_flush();
					unlink($zipFile);
				} else {
					throw Exception("The zip file couldn't handle this much data.");
				}
			} else {
				die("There was no data");
			}
		} catch (Exception $e) {
			throw $e;
		}
	}

	public static function debug($var) {
		echo "<pre class='debug'>";
		echo print_r($var, true);
		echo "</pre>";
	}
}
