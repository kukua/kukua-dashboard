<?php

error_reporting(E_ALL);
date_default_timezone_set("Europe/Amsterdam");

$start = microtime(true);
write("Starting..");

define("BASEPATH", "abc");

require_once("../public_html/.environment/config.php");
require_once("../public_html/vendor/autoload.php");
require_once("../public_html/application/libraries/Email.php");

use \Curl\Curl;

if (getSims() !== True) {
    write("ERROR");
    write(" - No simcards found, check API settings.");
    exit;    
}

/**
 * Functions
 */
function write($str) {
	echo '[' . date('Y-m-d H:i:s') . '] ' . $str . "\n";
}

function getSims() {
    $username = ESEYE_USERNAME;
    $password = ESEYE_PASSWORD;
    $portfolioId = ESEYE_PORTFOLIOID;
    $url = "https://siam.eseye.com/Japi/Tigrillo";
    if (ENVIRONMENT === "development") {
        $url = "https://tigrillostaging.eseye.com/Japi/Tigrillo";
    }
    $curl = new Curl();
    $curl->setHeader("Content-type", "application/json");
    $curl->post($url . "/getCookieName");

    $cookieName = $curl->response;
    $cookieValue = login_eseye($url, $username, $password, $portfolioId);

    try {
        $curl->setCookie($cookieName, $cookieValue);
        $curl->post($url . "/getSIMs", [
            'searchCriteria' => [
                'state' => 'provisioned'
            ],
            'sortOrder' => "I",
            'startRec' => 0,
            'numRecs' => 50,
        ]);
        $simcards = isset($curl->response->sims) ? $curl->response->sims : Array();

        $result = False;
        if (!empty($simcards)) {
            $result = True;
            foreach($simcards as $sim) {
                getSim($sim, $cookieName, $cookieValue);
            }
        }
        return $result;

    } catch (Exception $e) {
        return False;
    }
    return False;
}

function getSim($sim, $cookieName, $cookieValue) {
    $url = "https://siam.eseye.com/Japi/Tigrillo";
    if (ENVIRONMENT === "development") {
        $url = "https://tigrillostaging.eseye.com/Japi/Tigrillo";
    }
    $curl = new Curl();
    $curl->setHeader("Content-type", "application/json");
    try {
        $curl->setCookie($cookieName, $cookieValue);
        $curl->post($url . "/getSIMLastActivity", [
            "ICCID" => $sim->ICCID
        ]);
        $response = $curl->response;
        $sim->LastRadiusStop  = $response->info->LastRadiusStop;
        $sim->LastRadiusBytes = $response->info->LastRadiusBytes;

        $difference = 96;
        if (!empty($sim->LastRadiusStop)) {
            $date = DateTime::createFromFormat("Y-m-d H:i:s", $sim->LastRadiusStop);
            $difference = ($date->getTimestamp() / 3600);
        }

        if ($difference >= 48 && $difference < 96) {
            alert($sim, "medium");
        }

        if ($difference >= 96) {
            alert($sim, "high");
        }
    } catch (Exception $e) {
        throw $e;
    }
}

function login_eseye($url, $username, $password, $portfolioId) {
    $curl = new Curl;
    $curl->setHeader("Content-type", "application/json");
    $curl->post($url. "/login", [
        'username' => $username,
        'password' => $password,
        'portfolioId' => $portfolioId
    ]);

    $cookie = isset($curl->response->cookie) ? $curl->response->cookie : false;
    return $cookie;
}

function alert($sim, $type) {
    $lib = new Email();
    $lib->setFrom("Kukua Dashboard <dashboard@kukua.cc>");
    $lib->setTo("siebren@kukua.cc, ollie@kukua.cc");

    $content  = "The sim card with id <strong>" . $sim->ICCID . "</strong> is offline for several hours now.<br>";
    $content .= "Name: " . $sim->friendlyName . "<br>";
    $content .= "Country: " . $sim->country . "<br>";
    switch($type) {
        case 'medium':
            $lib->setSubject("⚠ A SIM IS OFFLINE FOR 48h");
            $lib->setContent($content);
            $lib->send();
            break;
        case 'high':
            $lib->setSubject("⚠ A SIM IS OFFLINE FOR 96h");
            $lib->setContent($content);
            $lib->send();
            break;
        default: break;
    }
}

$end = microtime(true);
write("Done. Finished in " . ($end - $start) / 60 . " seconds");
