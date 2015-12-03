#!/bin/bash

mkdir /etc/nginx/sites-enabled 2> /dev/null

for file in /etc/nginx/sites-available/*.conf; do
	TPL=$(cat $file)
	for row in $(env); do
		IFS_OLD=$IFS
		IFS="="
		read key val <<< "$row"
		IFS=$IFS_OLD
		[[ $val == tcp://* ]] && val=$(echo -n $val | sed 's#^tcp://#http://#')
		[[ $val == *"$"* ]] && val=$(echo -n $val | sed 's#\$#\\\$#') # Escape dollar sign
		TPL=$(echo -en "$TPL" | sed "s\$\\\${$key}\$$val\$")
	done
	echo -en "$TPL" > "/etc/nginx/sites-enabled/$(basename $file)"
done

nginx -g "daemon off;"
