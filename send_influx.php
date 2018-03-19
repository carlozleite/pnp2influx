<?php

//Locations os pnp4nagios perfdata XML's

$path = '/usr/local/pnp4nagios/var/perfdata/*.xml';

//InfluxDB URL

$influx_url="http://127.0.0.1:8086/write?db=NAGIOS";


$time = @date('[d/M/Y:H:i:s]');

function insert_influx($XMLS,$HOST,$influx_url) {


	$xml = simplexml_load_file($XMLS);
	$XPARTS = end(explode("/", $XMLS));
	$FILENAME=$XPARTS;
	$METRIC = pathinfo($XPARTS, PATHINFO_FILENAME);

	foreach ($xml->DATASOURCE as $DS) {

		if ( $METRIC == "_HOST_" ) {
			$METRIC = "HOSTPERF";
		}

		//$HOSTNAME=$xml->NAGIOS_DISP_HOSTNAME;
      		
		$INDATA=$METRIC.",label=".$DS->NAME.",host=".$HOST." value=".$DS->ACT;

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL,            $influx_url );
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1); 
		curl_setopt($ch, CURLOPT_VERBOSE, false);
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt($ch, CURLOPT_POST,           1 );
		curl_setopt($ch, CURLOPT_POSTFIELDS,     "$INDATA" ); 
		curl_setopt($ch, CURLOPT_HTTPHEADER,     array('Content-Type: text/plain')); 

		$result=curl_exec ($ch);


      		
	}

}

echo $time." - Enviando host: ".$argv[1]." Service: ".$argv[2]." ...\n";

$XMLS="/usr/local/pnp4nagios/var/perfdata/".$argv[1]."/".$argv[2].".xml";
$HOST=$argv[1];
insert_influx($XMLS,$HOST,$influx_url);

?>
