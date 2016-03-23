# Dashboard
> Graphical interface for sensor data from ConCaVa.

## Requirements

### Server

* [Docker](http://docs.docker.com/linux/started/)
* [Docker Compose](https://docs.docker.com/compose/install/)
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

$ cp public_html/.environment/default.php public_html/.environment/config.php
# Set your environment to development if needed error reporting etc

# CSS / JS compiling
$ bower install
$ npm install
$ gulp build
```
