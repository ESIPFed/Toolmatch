<?php
try
{
	include_once( "twsparql/TWSparqlHTML.inc" ) ;

	$query = "http://tw.rpi.edu/queries/project-participant.rq" ;
	$xslt = "http://tw.rpi.edu/xslt/project-person-list.xsl" ;
	$uri = "http://tw.rpi.edu/instances/project/ToolMatch" ;
	$endpoint="http://tw.rpi.edu/endpoint/books" ;

	$sparql = "<sparql endpoint=\"$endpoint\" query=\"$query\" xslt=\"$xslt\" uri=\"$uri\"/>" ;

	$contents = TWSparql::getEngine()->render( $sparql ) ;

	print( "$contents" ) ;
}
catch( Exception $e )
{
	$msg = $e->getMessage() ;
	print( "$msg\n" ) ;
}

?>
