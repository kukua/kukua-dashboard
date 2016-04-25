# Basics
The URL of our api is

```url
https://dashboard.kukua.cc/api/sensordata/get
```

## POST Parameters
We require 5 parameters to get a successfull call. If any of these are missing you will get a Bad Request.

### Region
This is currently the ID of the region you want to request data from:

* 1 Tanzania
* 2 Nigeria
* 3 Kenya
* 4 Ghana
* 5 Mozambique
* 7 London Business School

### Type
* Temperature
* Rainfall
* Humidity
* WindSpeed
* **all**

The **all** parameter returns all 4 parameters.

### DateFrom & DateTo
Must be a UNIX Timestamp.

### Interval
* 5m
* 1h
* 12h
* 24h

## PHP Example (curl)
Using composer: [curl](https://packagist.org/packages/curl/curl)

```PHP
$data = [
	"region"	=> 1
	"type"		=> Temperature,
	"dateFrom"	=> now(),
	"dateTo"	=> now(),
	"interval"	=> '5m'
];

$curl = new \Curl\Curl();
$curl->post("https://dashboard.kukua.cc/api/sensordata/get",
	$data
);
if ($curl->error) {
	return $curl->errorMessage;
}
return $curl->response;
```

# Response
The return value is a json string with the data per station.
```JSON
[
	{"name":"<StationName>","data":[[<microseconds>,<value>],[<microseconds>,<value>], ...]},
	{"name":"<StationName>","data":[[<microseconds>,<value>],[<microseconds>,<value>], ...]},
	...
]
```
