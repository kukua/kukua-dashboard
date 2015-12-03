#!/bin/bash

DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

docker run \
	-i --rm --link dashboard_influxdb_1 -v "$DIR/..:/data" --workdir=/data --env-file "$DIR/../.env" \
	diegomarangoni/hhvm:fastcgi \
	php tools/cronjob_import.php
