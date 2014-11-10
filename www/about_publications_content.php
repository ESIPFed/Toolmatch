<h2 class="page_title">Publications</h2>
<?php
try
{
	include_once( "twsparql/TWSparqlHTML.inc" ) ;

	$query = "http://tw.rpi.edu/queries/project-publications.rq" ;
	$xslt = "http://tw.rpi.edu/xslt/publication-list2.xsl" ;
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
