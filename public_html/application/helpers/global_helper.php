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
			case stristr($values, 'gustdir'):
			case stristr($values, 'winddir'):
				$return = 'degrees';
				break;
			case stristr($values, 'batvolt'):
			case stristr($values, 'battery'):
				$return = 'millivolt';
				break;
			case stristr($values, 'gas'):
				$return = 'bar';
				break;
			case stristr($values, 'pressure'):
				$return = 'hPa';
				break;
			case stristr($values, 'solar'):
				$return = 'w/m2';
				break;
		}
		return $return;
	}

	public static function translate($name) {
		$return = null;

		switch($name) {
			case 'timestamp':
				$return = "Date";
				break;
			case 'temp':
				$return = "Temperature";
				break;
			case 'tempBMP':
				$return = "BMP Temperature";
				break;
			case 'humid':
				$return = "Humidity";
				break;
			case 'battery':
			case 'batVolt':
				$return = "Battery voltage";
				break;
			default:
				$return = ucfirst($name);
				break;
		}
		return $return;
	}

	public static function outputCsv($fileName, $assocData = Array()) {
		$zipFile = '/tmp/' . $fileName . '.zip';
		$zip = new ZipArchive;
		if ($zip->open($zipFile, ZipArchive::CREATE) !== true) {
			throw new Exception("Cannot open zip archive");
		}

		if (is_array($assocData)) {
			foreach($assocData as $data) {
				if (!isset($data["data"])) {
					continue;
				}

				$fp = fopen('php://output', 'w');
				if (!$fp) throw new Exception("Unable to open output buffer");
				ob_start();

				$names = [];
				$types = [];
				foreach($data["header"] as $column) {
					$names[] = GlobalHelper::translate($column);
					$types[] = GlobalHelper::meaningOf($column);
				}

				fputcsv($fp, $names);
				fputcsv($fp, $types);
				foreach($data['data'] as $values) {
					fputcsv($fp, $values);
				}

				$string = ob_get_contents();
				$zip->addFromString($data['station']->getName() . ".csv", $string);
				ob_clean();
				fclose($fp);
			}
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
			throw new Exception("The zip file couldn't handle this much data.");
		}
	}

	public static function debug($var) {
		echo "<pre class='debug'>";
		echo print_r($var, true);
		echo "</pre>";
	}
}
