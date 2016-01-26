<?php

class GlobalHelper {

    public static $validUsers = ["kukuang", "kukuatz"];

    public static function getDefaultDate($intval, $midnight = false) {
        $today = new DateTime();
        $today->sub(new DateInterval($intval));
        if ($midnight === true) {
            return $today->format("Y/m/d 00:00:00");
        }
        return $today->format("Y/m/d 23:59:59");
    }

    public static function getCountries() {
        return [
            "Tanzania" => "Tanzania",
            "Nigeria"  => "Nigeria",
            "Test"     => "Test",
        ];
    }

    public static function getStations() {
        return [
            "Tanzania" => [
                //"mwangoi"       => "sivad_ndogo_a5e4d2c1",
                //"mavumo"        => "sivad_ndogo_a687dcd8",
                "migambo"       => "sivad_ndogo_a468d67c",
                "mshizii"       => "sivad_ndogo_9f113b00",
                "baga"          => "sivad_ndogo_890d85ba",
                "makuyuni"      => "sivad_ndogo_1e2e607e",
                "rauya"         => "sivad_ndogo_9f696fb0",
                "mandakamnono"  => "sivad_ndogo_841d300b",
                "sanyo"         => "sivad_ndogo_7aa19521",
            ],
            "Nigeria" => [
                "ibadan"        => "sivad_ndogo_fab23419",
            ]
        ];
    }

    public static function getStationNameById($stationId) {
        foreach(GlobalHelper::getStations() as $country => $stations) {
            foreach($stations as $city => $id) {
                if ($stationId == $id) {
                    return $city;
                }
            }
        }
    }

    public static function getForecastMap($countries) {
        $url = "";
        $list = Array();
        if (!is_array($countries)) {
            $list[] = $countries;
        } else {
            $list = $countries;
        }

        foreach($list as $country) {
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
        }
        return $url;
    }

    public static function outputCsv($fileName, $assocDataArray) {
        $keys = [
            "Epoch",
            "mm",
            "*3.6/300 = m/s",
            "*3.6/100 = m/s",
            "degrees",
            "degrees",
            "celcius",
            "%",
            "hectopascal"
        ];

        $zipFile = '/tmp/stations-' . microtime() . '.zip';
        $zip = new ZipArchive;
        if ($zip->open($zipFile, ZipArchive::CREATE) !== true) {
            throw new Exception("Cannot open zip archive");
        }

        if (count($assocDataArray)) {
            foreach($assocDataArray as $key => $station) {
                $fp = fopen('php://output', 'w');
                if ($fp === false) {
                    throw new Exception("unable to open php's output buffer");
                }

                ob_start();
                fputcsv($fp, $station->columns);
                fputcsv($fp, $keys);
                foreach($station->values as $values) {
                    fputcsv($fp, $values);
                }
                $string = ob_get_contents();
                $zip->addFromString(GlobalHelper::getStationNameById($station->name) . ".csv", $string);
                ob_clean();
                fclose($fp);
            }
            $zip->close();
        }

        header('Pragma: public');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Cache-Control: private', false);
        header('Content-Type: application/zip');
        header('Content-Disposition: attachment;filename=export-stations-csv.zip');
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
