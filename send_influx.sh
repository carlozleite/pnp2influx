#!/bin/bash

unset http_proxy
unset https_proxy

/usr/local/php/bin/php /usr/local/pnp4nagios/bin/send_influx.php $1 $2 >> /tmp/send_influx.log 2>&1

