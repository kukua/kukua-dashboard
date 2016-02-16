#!/bin/bash

DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

docker exec -it dashboard_hhvm_1 bash \
    php index.php cli cronjobs smsForecast

#docker run \
#	-i --rm --link dashboard_influxdb_1 -v "$DIR/..:/data" --workdir=/data --env-file "$DIR/../.env" \
#	diegomarangoni/hhvm:fastcgi \
#	php index.php cli cronjobs smsForecast
