#!/bin/bash

docker exec -t hhvm php index.php cli cronjobs report true
