<?php

class GlobalHelper {

	public static function meaningOf($values) {
		$return = null;

		switch(true) {
			case stristr($values, 'timestamp'):
				$return = 'UTC (d-m-Y)';
			break;
			case stristr($values, 'temp'):
				$return = 'celcius';
				break;
			case stristr($values, 'rain'):
				$return = 'mm';
				break;
			case stristr($values, 'soilmoist'):
			case stristr($values, 'humid'):
				$return = '%';
				break;
			case stristr($values, 'gustspeed'):
			case stristr($values, 'windspeed'):
				$return = 'km/h';
				break;
			case stristr($values, 'windgustdir'):
			case stristr($values, 'winddir'):
				$return = 'degrees';
				break;
			case stristr($values, 'batvolt'):
			case stristr($values, 'batt'):
				$return = 'voltage';
				break;
			case stristr($values, 'gas'):
				$return = 'bar';
				break;
			case stristr($values, 'press'):
				$return = 'hPa';
				break;
			case stristr($values, 'solar'):
				$return = 'w/m2';
				break;
		}
		return $return;
	}

	public static function outputCsv($fileName, $assocDataArray = Array(), $specificStationId = false) {
		$zipFile = '/tmp/' . $fileName . '.zip';
		$zip = new ZipArchive;
		if ($zip->open($zipFile, ZipArchive::CREATE) !== true) {
			throw new Exception("Cannot open zip archive");
		}

		include (APPPATH . "models/Sources/Measurements.php");
		$meas = (new Measurements());
		$columns = $meas->_default_columns;
		try {
			if (is_array($assocDataArray) && !empty($assocDataArray)) {
				$iterator = 0;
				foreach($assocDataArray as $i => $station) {

					$fp = fopen('php://output', 'w');
					if ($fp === false) {
						throw new Exception("unable to open php's output buffer");
					}

					ob_start();
					$arr = [];

					$names = [];
					$types = [];

					if (isset($station->data)) {
						if ($specificStationId !== false) {

							$sensorData = (new StationMeasurement())->findByStationId($specificStationId);
							foreach($sensorData as $sensor) {
								$columns[$sensor->getName()]["name"] = $sensor->getColumn();
								$columns[$sensor->getName()]["calc"] = "AVG";
							}
						}

						$names[0] = "Timestamp";
						foreach($columns as $columnName => $value) {
							$names[] = $columnName;
						}

						foreach($names as $name) {
							$types[] = GlobalHelper::meaningOf($name);
						}

						fputcsv($fp, $names);
						fputcsv($fp, $types);
						foreach($station->data as $data) {
							fputcsv($fp, $data);
						}
					}

					$currentStationName = $station->name;
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
