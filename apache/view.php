<?php

/***********************************************
 Variables used to construct SPARQL queries 
************************************************/
define("ENDPOINT","http://localhost:8890/sparql/");

$bases = array();
$bases["schema.owl"] = "http://toolmatch.tw.rpi.edu/schema/";
$bases["instances.rdf"] = "http://toolmatch.tw.rpi.edu/instances/";
$pageBase='http://tw.rpi.edu/web/';
$output = null;
$src = null;
$query_str = null;

$logger = fopen( "/tmp/view.log", "w" ) ;

/**********************************************
 Code for content lookup
***********************************************/

// Get output type if there is any
if(isset($_GET["output"]))
  $output = $_GET["output"];
fprintf( $logger, "output = $output\n" ) ;

// Get output type if there is any
if(isset($_GET["src"]))
  $src = $_GET["src"];
fprintf( $logger, "src = $src\n" ) ;

// Get q if there is any
if(isset($_GET["q"]))
  $query_str = $_GET["q"];
fprintf( $logger, "query_str = $query_str\n" ) ;

// Gets the best possible mime type from the HTTP Accept header
function getBestSupportedMimeType($mimeTypes = null) {
  // Values will be stored in this array
  $AcceptTypes = Array ();

  // Accept header is case insensitive, and whitespace isn’t important
  $accept = strtolower(str_replace(' ', '', (isset($_SERVER['HTTP_ACCEPT']) ? $_SERVER['HTTP_ACCEPT'] : "")));
  // divide it into parts in the place of a ","
  $accept = explode(',', $accept);
  foreach ($accept as $a) {
    // the default quality is 1.
    $q = 1;
    // check if there is a different quality
    if (strpos($a, ';q=')) {
      // divide "mime/type;q=X" into two parts: "mime/type" i "X"
      list($a, $q) = explode(';q=', $a);
    }
    // mime-type $a is accepted with the quality $q
    // WARNING: $q == 0 means, that mime-type isn’t supported!
    $AcceptTypes[$a] = $q;
  }
  arsort($AcceptTypes);

  // if no parameter was passed, just return parsed data
  if (!$mimeTypes) return $AcceptTypes;

  $mimeTypes = array_map('strtolower', (array)$mimeTypes);

  // let’s check our supported types:
  foreach ($AcceptTypes as $mime => $q) {
    if ($q && in_array($mime, $mimeTypes)) return $mime;
  }
  // no mime-type found
  return null;
}

// Converts a shortened URI to a full URI
// Deprecated
function toResource($path) {
  return "http://wineagent.tw.rpi.edu/tw.rpi.edu/".$path;
}

// Ask the type of an object
// Deprecated
function asktype($resource, $type) {
  $sparql="PREFIX foaf: <http://xmlns.com/foaf/0.1/> PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#> PREFIX tw: <http://wineagent.tw.rpi.edu/tw.rpi.edu/schema/> ASK WHERE { <$resource> rdf:type $type . }";
  $encoded = "query=".urlencode($sparql);
  $context = array('http' => array("method"=>"POST", "header"=>"Content-Type: application/x-www-form-urlencoded\r\n", "content" => $encoded,),);
  $xcontext = stream_context_create($context);
  $str=file_get_contents("http://wineagent.tw.rpi.edu/sparql",FALSE,$xcontext);
  return (strpos($str,"true") ? TRUE : FALSE);
}

// Check if this is a request to list the namespace content, otherwise process based on acceptable content types
if($_GET["q"]==null||$_GET["q"]=="") {

  // Look up the source namespace
  $theBase = $bases[$_GET["src"]];

  // Check the namespace
  if($theBase==undefined) {
    header("HTTP/1.1 404 Not Found");
    readfile("http://tw.rpi.edu/web/404notfound");
    die;
  }

  // Perform SPARQL lookup
  $query = 'DESCRIBE ?s WHERE { '.
             '?s ?p ?o . '.
	     'FILTER(regex(str(?s),"'.$theBase.'")) '.
           '}';
  header("Content-type: application/rdf+xml");
  if(FALSE===@readfile(ENDPOINT."?query=".urlencode($query))) {
    // SPARQL server did not respond, send a 503.
    header("HTTP/1.1 503 Service Unavailable");
    readfile("errors/503.html");
  }
  die;
}
else {

  // Check that the requested resource exists
  $query = 'SELECT ?type WHERE { <'.$bases[$_GET["src"]].$_GET["q"].'> a ?type }';
  $res = @file_get_contents(ENDPOINT.'?query='.urlencode($query));
  if(1==preg_match("/<results>\s*<\\/results>/",$res)) {
    header("HTTP/1.1 404 Not Found");
    readfile("http://tw.rpi.edu/web/404notfound");
    die;
  }

  // Obtain best mime type
  $mime = getBestSupportedMimeType(Array("application/rdf+xml",
                                         "text/html",
                                         "application/xhtml+xml",
                                         "text/n3",
                                         "text/plain"));

  fprintf( $logger, "mime = $mime\n" ) ;
  if($mime=="application/rdf+xml") {

    // Handle application/rdf+xml Mime Type
    if($output == "rdf") {

      // output=rdf, so the extension on the URI was .rdf
      // Set content type and DESCRIBE the resource
      header("Content-Type: application/rdf+xml");
      $query = 'DESCRIBE <'.$bases[$_GET["src"]].$_GET["q"].'>';

      if(FALSE===@readfile(ENDPOINT."?query=".urlencode($query))) {

        // SPARQL endpoint did not respond, send 503
        header("HTTP/1.1 503 Service Unavailable");
	readfile("errors/503.html");
	die;
      }
    }
    else {

      // No output specified, so 303 to the URI followed by .rdf
      header("Location: ".$bases[$_GET["src"]].$_GET["q"].".rdf",true,303);
      die;
    }
  }
  else if($mime=="text/html"||$mime=="application/xhtml+xml") {

    // Handle text/html Mime Type
    // Look up foaf:page for resource
    $query = 'PREFIX foaf: <http://xmlns.com/foaf/0.1/> '.
             'SELECT ?page WHERE { '.
               '<'.$bases[$_GET["src"]].$_GET["q"].'> foaf:page ?page . '.
	       'FILTER(regex(str(?page),"'.$pageBase.'")) '.
             '}';
    fprintf( $logger, "query = $query\n" ) ;

    $res = @file_get_contents(ENDPOINT."?query=".urlencode($query));
    fprintf( $logger, "result = $res\n" ) ;

    if($res=="") {

      // SPARQL Endpoint did not respond, send 503
      header("HTTP/1.1 503 Not Available");
      readfile("errors/503.html");
      die;
    }
    else {

      // Look for <uri></uri> block
      $arr = array();
      $num = preg_match("/<uri>([^<]*)<\\/uri>/",$res,$arr);

      if(0==$num) {

        // No foaf:page, send 404
        header("HTTP/1.1 404 Not Found");
	readfile("http://tw.rpi.edu/web/404notfound");
	die;
      }
      else {

        // Found a foaf:page, so send a 303
        header("Location: ".$arr[1],true,303);
	die;
      }
    }
  }
  else if($mime=="text/n3" || $mime=="text/plain") {
    
  }
  else {
    header("HTTP/1.1 406 Not Acceptable");
    readfile("errors/406.html");
    die;
  }
}
die;
?>
