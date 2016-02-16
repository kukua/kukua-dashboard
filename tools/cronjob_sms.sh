#!/bin/bash

docker exec -it dashboard_hhvm_1 php index.php cli cronjobs smsForecast
