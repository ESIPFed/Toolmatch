<?php

/***********************************************
 Variables used to construct SPARQL queries 
************************************************/
define("ENDPOINT","http://localhost:8890/sparql");

# if q and output is defined and it's one of the ones we know about then
# just curl the uri with the appropriate accept header
$mimes = array() ;
$mimes["ttl"] = "text/plain" ;
$mimes["json"] = "application/json" ;
$mimes["rdf"] = "application/rdf+xml" ;
$mimes["n3"] = "text/rdf+n3" ;

$baseuri = "http://toolmatch.esipfed.org/instances/" ;

$pageBases = array();
$pageBases["http://toolmatch.esipfed.org/schema#Tool"] = "http://toolmatch.esipfed.org/tool.php" ;
$pageBases["http://toolmatch.esipfed.org/schema#DataCollection"] = "http://toolmatch.esipfed.org/data.php" ;

$output = null;
$query_str = null;

$logger = fopen( "/tmp/view.log", "w" ) ;

/**********************************************
 Code for content lookup
***********************************************/

// Get output type if there is any
if(isset($_GET["output"]))
  $output = $_GET["output"];
fprintf( $logger, "output = $output\n" ) ;

// Get q if there is any
if(isset($_GET["q"]))
  $query_str = $_GET["q"];
fprintf( $logger, "query_str = $query_str\n" ) ;

# If we know the output type and the instance name, and the passed
# output type is known mime, then just curl the url using the
# appropriate mime type
if( $output != "" && isset( $mimes[$output] ) && $query_str != "" )
{
    $uri = $baseuri . $query_str ;
    $cmd = "curl -L -H \"Accept: $mimes[$output]\" $uri " ;
    fprintf( $logger, "curl cmd = $cmd\n" ) ;
    $value = shell_exec( $cmd ) ;
    header("Content-type: $mimes[$output]");
    print( $value ) ;
    exit( 0 ) ;
}

// Check if this is a request to list the namespace content, otherwise
// process based on acceptable content types
if( $query_str == null || $query_str == "" )
{
  // Perform SPARQL lookup
  $query = 'DESCRIBE ?s WHERE { '.
             '?s ?p ?o . '.
         'FILTER(regex(str(?s),"'.$theBase.'")) '.
           '}';
  header( "Content-type: application/rdf+xml" ) ;
  if( FALSE === @readfile( ENDPOINT . "?query=" . urlencode( $query ) ) )
  {
    // SPARQL server did not respond, send a 503.
    header('Location: /503.php');
  }
  die ;
}
else
{
  // Check that the requested resource exists
  $query = 'SELECT ?type WHERE { <'.$baseuri . $query_str . '> a ?type }';
  fprintf( $logger, "query = $query\n" ) ;
  $query = 'http://localhost:8890/sparql' . '?query=' . urlencode( $query ) ;
  $query .= '&format=application%2Fsparql-results%2Bjson' ;

  $content = @file_get_contents( $query ) ;

  if( $content == null || $content == "" )
  {
    header('Location: /404.php');
    die;
  }
  fprintf( $logger, "data = $content\n" ) ;

  $data = json_decode($content, true);

  $pageBase = null ;
  foreach( $data['results']['bindings'] as $result )
  {
    $thetype = $result['type']['value'];
    if( isset( $pageBases[$thetype] ) )
    {
        $pageBase = $pageBases[$thetype] ;
    }
  }
  fprintf( $logger, "I am here\n" ) ;
  if( $pageBase == null )
  {
    header('Location: /405.php');
    die;
  }
  fprintf( $logger, "pageBase = $pageBase\n" ) ;
  $newpage = $pageBase . '?uri=' . $baseuri . $query_str ;
  fprintf( $logger, "newpage = $newpage\n" ) ;

  header('Location: ' . $newpage ) ;
}
die;
?>
