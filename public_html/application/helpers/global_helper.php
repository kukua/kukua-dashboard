<?php

class GlobalHelper {

    public static function getDefaultDate($intval, $midnight = false) {
        $today = new DateTime();
        $today->sub(new DateInterval($intval));
        if ($midnight === true) {
            return $today->format("Y/m/d 00:00:00");
        }
        return $today->format("Y/m/d 23:59:59");
    }

    public static function graphWeatherTypes() {
        return [
            "temp" => "Temperature",
            "rain" => "Rainfall",
            "hum"  => "Humidity",
            "pres" => "Pressure",
            "wind" => "Wind"
        ];
    }

    public static function allWeatherTypes() {
        $types = GlobalHelper::graphWeatherTypes();
        $types["winddir"] = "WindDirection";
        $types["windgust"] = "WindGusts";
        $types["windgustdir"] = "WindGustDirection";
        $types["bat"] = "Battery";

        return $types;
    }

    public static function getForecastMap($country) {
        switch($country) {
            case 'Nigeria':
                $url = "http://vip.foreca.com/kukua/maps-nigeria.html?rain";
                break;
            case 'Tanzania':
                $url = "http://vip.foreca.com/kukua/maps-tanzania.html?rain";
                break;
            default:
                $url = "";
                break;
        }
        return $url;
    }

    public static function outputCsv($fileName, $assocDataArray) {
        $zipFile = '/tmp/' . $fileName . '.zip';
        $zip = new ZipArchive;
        if ($zip->open($zipFile, ZipArchive::CREATE) !== true) {
            throw new Exception("Cannot open zip archive");
        }

        foreach($assocDataArray as $station) {
            $fp = fopen('php://output', 'w');
            if ($fp === false) {
                throw new Exception("unable to open php's output buffer");
            }

            ob_start();
            $arr = [];

            foreach($station->columns as $columnKey => $column) {
                $type[$columnKey] = "Epoch"; //some convert function
            }

            /* Header */
            fputcsv($fp, $station->columns);

            /* Second header */
            /* fputcsv($fp, $type); */

            foreach($station->values as $values) {
                fputcsv($fp, $values);
            }

            $string = ob_get_contents();
            $zip->addFromString($station->name . ".csv", $string);
            ob_clean();
            fclose($fp);
        }
        $zip->close();

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
    }

    public static function debug($var) {
        echo "<pre class='debug'>";
        echo print_r($var, true);
        echo "</pre>";
    }
}
