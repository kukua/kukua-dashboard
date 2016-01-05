<?php

// Prepare
error_reporting(E_ALL & ~E_NOTICE);
date_default_timezone_set('Europe/Amsterdam');

$start = time();
$interval = 5 * 60; // 5 minutes

function write ($str) {
	echo '[' . date('Y-m-d H:i:s') . '] ' . $str . "\n";
}

write("Starting..");

// Connect to database
$db = new PDO(
	'mysql:host=' . $_ENV['IMPORT_MYSQL_HOST'] . ';dbname=' . $_ENV['IMPORT_MYSQL_DATABASE'],
	$_ENV['IMPORT_MYSQL_USER'],
	$_ENV['IMPORT_MYSQL_PASSWORD']
);

$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

// Determine tables
$result = $db->query('SELECT * FROM `devices` WHERE Dashboard = 1');

function convert (array & $item) {
	$item['RainTicks'] = round($item['RainTicks'] * 0.2, 1);
	if ($item['WindGustTicks'] < 0.04 * $item['WindTicks']) {
		$item['WindGustTicks'] = round($item['WindGustTicks'] * 3.6 / 3, 1);
	} else {
		$item['WindGustTicks'] = 0;
	}
	$item['WindTicks'] = round($item['WindTicks'] * 3.6 / 300, 1);
	if ($item['WindDir'] <= 900) {
		$item['WindDir'] = $item['WindDir'] % 360;
	} else {
		$item['WindDir'] = 0;
	}
	$item['WindGustDir'] = $item['WindGustDir'] % 360;
	$item['MaxSolar1'] = round($item['MaxSolar1'] * 2.5);
	$item['Temp'] = $item['Temp'] / 10;
	$item['Hum'] = $item['Hum'] / 10;
	$item['PresBMP'] = $item['PresBMP'] / 10;
}

function insert ($deviceId, array $columns, array $points) {
	$data = '';
	foreach ($points as & $point) {
		$timeKey = 0;
		$i = 0;
		$data .= $deviceId . ' ' . implode(',', array_filter(array_map(function ($key, $val) use ( & $i, & $timeKey) {
			if ($key === 'time') {
				$timeKey = $i;
				return null;
			}
			$i += 1;
			return $key . '=' . json_encode($val);
		}, $columns, $point))) . ' ' . ($point[$timeKey] * 1000000) . "\n";
	}
	$ch = curl_init();
	$url = str_replace('tcp://', 'http://', $_ENV['DASHBOARD_INFLUXDB_1_PORT_8086_TCP']) . '/write?db=' . $_ENV['PRE_CREATE_DB'] . '&precision=ns';
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	//curl_setopt($ch, CURLOPT_UPLOAD, 1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, [
		'Content-Type: application/octet-stream',
		'Content-Length: ' . strlen($data),
		'Authorization: Basic ' . base64_encode($_ENV['ADMIN_USER'] . ':' . $_ENV['INFLUXDB_INIT_PWD']),
	]);

	if ( ! curl_exec($ch)) {
		return curl_error($ch);
	}

	$info = curl_getinfo($ch);

	if ($info['http_code'] !== 204) {
		return 'Response code: ' . $info['http_code'];
	}

	curl_close($ch);
}

foreach ($result as $device) {
	$table = $device['tablename'];
	write('Processing table ' . $table);
	$result = $db->query('SELECT *, ts as time FROM `' . $table .'` WHERE ts > ' . $device['Dashboard_TS'])->fetchAll();
	$rowCount = count($result);
	write($rowCount > 0 ? $rowCount . ' rows.' : 'No rows.');
	if ($rowCount === 0) continue;
	$lastTimestamp = 0;

	$deviceId = $table;
	$columns = array_filter(array_map(function ($col) {
		if ($col === 'ts') return;
		return lcfirst($col);
	}, array_keys((array) $result[0])));
	$points = [];

	foreach ($result as & $row) {
		$item = (array) $row;
		unset($item['ts']);
		foreach ($item as & $val) {
			$val = (int) $val;
		}
		if ($item['time'] > $lastTimestamp) $lastTimestamp = $item['time'];
		$item['time'] = $item['time'] * 1000;
		convert($item);
		$points[] = array_values($item);
	}

	$err = insert($deviceId, array_values($columns), $points);

	if ($err) {
		write('Error importing data for ' . $table . ': ' . $err);
		continue;
	}

	$db->query('UPDATE `devices` SET `Dashboard_TS` = ' . $lastTimestamp . ' WHERE `tablename` = "'. $table . '" LIMIT 1');
}

write('Done.');
