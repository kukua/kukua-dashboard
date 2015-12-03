#!/bin/bash

source .env

limit=20
ts_key=${TSV_TS_KEY:-ts}

for file in "$@"; do
	echo "Importing file $file."

	keys=`head -n 1 $file`
	device_id=`basename $file | sed 's/\.[^\.]*$//'`

	echo "=> Device ID $device_id"

	# Determine timestamp key index
	index=1
	ts_index=
	for key in `echo $keys | cut -d$'\t' -f2`; do
		[[ $key == $ts_key ]] && ts_index=$index
		let index+=1
	done
	if [[ $ts_index == '' ]]; then
		>&2 echo "No '$ts_key' index in file $file"
		continue
	fi

	# Build value query for AWK
	columns='"deviceId"'
	values='\"'$device_id'\"'
	index=1
	for key in `echo $keys | cut -d$'\t' -f2`; do
		if [[ $key == $ts_key ]]; then
			columns+=',"time"'
			values+=',"$'$index'*1000"'
		else
			# Lowercase first letter
			key=`echo ${key:0:1} | tr '[:upper:]' '[:lower:]'`${key:1}

			columns+=',"'$key'"'
			values+=',"$'$index'"'
		fi

		let index+=1
	done

	# Build query
	query='{ print "['"$values"']," }'
	#echo $query; exit
	#tail -n +2 $file | awk "$query"; exit

	# Insert into InfluxDB
	start=2
	size=500
	count=$((`cat $file | wc -l`-1))
	while [[ $start -lt $count ]]; do
		start_row=$((start-1))
		end_row=$(($start_row-1+size))
		[[ $end_row -gt $count ]] && end_row=$count
		echo "=> Importing rows $start_row-$end_row.."

		points=`tail -n+$start $file | head -n$size | awk "$query"`
		#echo $points; exit

		curl -i --user $ADMIN_USER:$INFLUXDB_INIT_PWD -XPOST "http://${INFLUXDB_HOST:-localhost}:8086/db/$PRE_CREATE_DB/series" -d @- <<EOF
[{
	"name": "$device_id",
	"columns": [$columns],
	"points": [${points%?}]
}]
EOF

		let start+=size
	done
done
