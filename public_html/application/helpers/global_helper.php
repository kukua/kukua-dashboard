<?php

class GlobalHelper {

    public static $validUsers = ["kukuang", "kukuatz"];

    public static function requireLogin() {
        if (GlobalHelper::getUser() === False) {
            redirect("/auth/login", "refresh", 403);
        }
    }

    public static function getUser() {
        if (isset($_SESSION["user"]) && in_array($_SESSION["user"], GlobalHelper::$validUsers)) {
            return $_SESSION["user"];
        }
        return false;
    }

    public static function getDefaultDate($intval) {
        $today = new DateTime();
        $today->sub(new DateInterval($intval));
        return $today->format("Y/m/d");
    }

    public static function getCountry() {
        $user = GlobalHelper::getUser();
        if ($user === 'kukuang') {
            return "Nigeria";
        }
        if ($user === 'kukuatz') {
            return 'Tanzania';
        }
    }

    public static function curl($request) {
        $headers = [
            "Content-type: application/json",
            "Accept: application/json"
        ];

        $ch = curl_init($request);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_HEADERS, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }

    public static function getForecastMap() {
        $user = GlobalHelper::getUser();
        $url = "";
        switch($user) {
            case 'kukuang':
                $url = "http://vip.foreca.com/kukua/maps-nigeria.html?rain";
                break;
            case 'kukuatz':
                $url = "http://vip.foreca.com/kukua/maps-tanzania.html?rain";
                break;
            default:
                $url = "";
                break;
        }
        return $url;
    }

    public static function outputCsv($fileName, $assocDataArray) {
        ob_clean();
        header('Pragma: public');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Cache-Control: private', false);
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment;filename=' . $fileName);

        if (count($assocDataArray)) {
            $fp = fopen('php://output', 'w');
            $i = 0;
            foreach($assocDataArray as $key => $station) {

                #Only the first time, set headers.
                if ($i == 0) {
                    fputcsv($fp, $station["columns"]);
                }
                $i++;

                foreach($station["points"] as $values) {
                    fputcsv($fp, $values);
                }
            }
            fclose($fp);
        }
        ob_flush();
    }
}
