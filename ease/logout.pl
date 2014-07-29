#!/bin/sh                                                                                                                                                                                               
 eval 'if [ -x /usr/local/cpanel/3rdparty/bin/perl ]; then exec /usr/local/cpanel/3rdparty/bin/perl -x -- $0 ${1+"$@"}; else exec /usr/bin/perl -x $0 ${1+"$@"}; fi;'
   if 0;
#!/usr/bin/perl

    use strict;
	
    # change 'central' to the url of your weblogin server.
    my $central = "https://www.ease.ed.ac.uk/logout/logout.cgi";
    my $query_string = "";
	# expire and nullify service cookie
    print( "Set-Cookie: $ENV{ COSIGN_SERVICE }=null; path=/; expires=Wednesday, 27-Jan-77 00:00:00 GMT; secure\n" );

    if ( $ENV{ QUERY_STRING } =~ m|^(https?://.*)$| ) {
        $query_string = "?$1";
    }

    # perform any local cleanup here

    # redirect to central weblogin server
    print( "Location: $central$query_string\n\n" );

    exit( 0 );

