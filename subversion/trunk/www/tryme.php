<?php

include_once("utils.php");

$afile = "/var/www/added_tools/rdf/ArcGIS.rdf" ;
$agraph = "http://toolmatch.esipfed.org/ArcGIS" ;

$something = storeRDF( $afile, $agraph, true ) ;
if( $something ) print( "good\n" ) ;
else print( "bad\n" ) ;

?>

