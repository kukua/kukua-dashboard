<?php

class GlobalHelper {

	public static function meaningOf($values) {
		$return = null;

		switch($values) {
			case 'Timestamp':
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
			case 'WindSpeed':
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
