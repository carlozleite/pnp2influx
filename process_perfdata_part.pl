sub process_perfdata {
    if ( keys( %NAGIOS ) == 1 && defined($opt_gm) ) {
        $stats{skipped}++;
        return 1;
    }
    if ( ! defined($NAGIOS{PERFDATA}) && ! defined($opt_gm) ) {
        print_log( "No Performance Data for $NAGIOS{HOSTNAME} / $NAGIOS{SERVICEDESC} ", 1 );
        if ( !$opt_b && !$opt_s ) {
            print_log( "PNP exiting ...", 1 );
            exit 3;
        }
    }

    if ( $NAGIOS{PERFDATA} =~ /^(.*)\s\[(.*)\]$/ ) {
        $NAGIOS{PERFDATA}      = $1;
        $NAGIOS{CHECK_COMMAND} = $2;
        print_log( "Found Perfdata from Distributed Server $NAGIOS{HOSTNAME} / $NAGIOS{SERVICEDESC} ($NAGIOS{PERFDATA})", 1 );
    }
    else {
        print_log( "Found Performance Data for $NAGIOS{HOSTNAME} / $NAGIOS{SERVICEDESC} ($NAGIOS{PERFDATA}) ", 1 );
    }

    $NAGIOS{PERFDATA} =~ s/,/./g;
    $NAGIOS{PERFDATA} =~ s/\s+=/=/g;
    $NAGIOS{PERFDATA} =~ s/=\s+/=/g;
    $NAGIOS{PERFDATA} =~ s/\\n//g;
    $NAGIOS{PERFDATA} .= " ";
    parse_perfstring( $NAGIOS{PERFDATA} );
    system("/usr/local/pnp4nagios/bin/send_influx.sh $NAGIOS{HOSTNAME} $NAGIOS{SERVICEDESC}");
    return 1;
}

