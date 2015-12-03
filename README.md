# Dashboard
> Graphical interface for sensor data from ConCaVa (via InfluxDB).

## Requirements

### Server

* [Docker](http://docs.docker.com/linux/started/)
* [Docker Compose](https://docs.docker.com/compose/install/)
* [InfluxDB v0.8.8](https://influxdb.com/docs/v0.8/)
* [Grafana v2.5](http://docs.grafana.org/v2.5/)
* [Nginx](http://nginx.org/)

### Development

* [CodeIgniter](http://www.codeigniter.com/user_guide/)
* [NodeJS](https://nodejs.org/en/docs/)
* [Gulp](https://github.com/gulpjs/gulp/blob/master/docs/getting-started.md)

### How to use
```bash
$ cp .env.sample .env
# > Edit configuration in .env

$ docker-compose up -d
# Navigate to http://<container ip>:80/ for the dashboard
# Navigate to http://<container ip>:8083/ for InfluxDB
# Navigate to http://<container ip>:9000/ for Grafana

$ cp public_html/.environment/production.php public_html/.environment/development.php
# Set your environment to development if needed error reporting etc

# CSS / JS compiling
$ bower install
$ npm install
$ gulp build

# Setup cronjob
$ sudo cp tools/cronjob /etc/cron.d/import_sensor_data
$ sudo chown root:root /etc/cron.d/import_sensor_data
$ sudo service cron restart
```

# Notes

## Tools

Import tab separated values into InfluxDB:

```bash
env TSV_TS_KEY=ts ./tools/import_tsv.sh /folder/with/tsv/files/* /another/file.tsv
# TSV_TS_KEY is the key used for the timestamp (default: ts)
```

## Grafana
The users are connected to a country (Nigeria or Tanzania), which both have cities.
To load the correct graphic, the name in Grafana must be:

```bash
<username>_<city>
```

For the nationwide views, you only need to name the dashboard as the username.
Later we will make this more dynamic using the Grafana web API. Panels in that 
dashboard gets populated automatically in the "graph" dropdown.
