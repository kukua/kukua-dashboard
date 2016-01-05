# Dashboard
> Graphical interface for sensor data from ConCaVa (via InfluxDB).

## Requirements

### Server

* [Docker](http://docs.docker.com/linux/started/)
* [Docker Compose](https://docs.docker.com/compose/install/)
* [InfluxDB v0.8.8](https://influxdb.com/docs/v0.8/)
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
