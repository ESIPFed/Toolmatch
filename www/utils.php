<?php 

///
/// Global Variables
///

//endpoint info
$endpoint = "http://toolmatch.esipfed.org/virtuoso/sparql";
$param = "query";
$format = "application/sparql-results+json";
$dataset_graph_base = "" ;
$dataset_dir = "" ;
$tool_graph_base = "" ;
$tool_dir = "" ;

$debug = true ;
$debug_file = "/var/www/logs/toolmatch.log" ;
global $debug ;
global $debug_file ;

/*
 * to send debug information to /var/www/logs/toolmatch.log use the
 * following function
tm_debug( "This is a test" ) ;
 */

///
/// RDF globals
///
$rdf_beg = '<?xml version="1.0"?>
<!DOCTYPE rdf:RDF [
  <!ENTITY toolmatchi "http://toolmatch.esipfed.org/instances/">
  <!ENTITY toolmatch "http://toolmatch.esipfed.org/schema#">
  <!ENTITY xsd "http://www.w3.org/2001/XMLSchema#">
  <!ENTITY doap "http://usefulinc.com/ns/doap#">
   
]>
<rdf:RDF
  xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
  xmlns:doap="&doap;"
  xmlns:dcat="http://www.w3.org/ns/dcat#"
  xmlns:foaf="http://xmlns.com/foaf/0.1/"
  xmlns:dc="http://purl.org/dc/terms/"
  xmlns:owl="http://www.w3.org/2002/07/owl#"
  xmlns:toolmatch="&toolmatch;"
  xmlns:toolmatchi="&toolmatchi;"
  xmlns:rdfs="http://www.w3.org/2000/01/rdf-schema#"
  xmlns="&toolmatch;"
  xml:base="&toolmatch;">';

///
/// Utility Functions
///

/**
 * Builds a basic SPARQL query
 * @param string $query SPARQL query string
 * @return array an array of associative arrays containing the bindings
 */
function sparqlSelect( $query )
{
	global $endpoint ;
	$curl = curl_init() ;
	curl_setopt( $curl, CURLOPT_URL, $endpoint ) ;
	curl_setopt( $curl, CURLOPT_POST, true ) ;
	curl_setopt( $curl, CURLOPT_POSTFIELDS, getQueryData( $query ) ) ;
	curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true ) ;
	$content = curl_exec( $curl ) ;
	curl_close( $curl ) ;
	return $content ;
}

/**
 * Builds a basic SPARQL query
 * @param string $query SPARQL query string
 * @param string $suffix other options to append to request
 * @return string a URL to make a SPARQL query request
 */
function getQueryData( $query, $suffix = '' )
{
	global $param, $format ;
	return $param . '=' . urlencode( $query ) . "&format=" . urlencode( $format ) ;	
}

/**
 * Write the new rdf to the specified graph
 * @param string $rdf_file the file that the rdf/xml was written
 * @param string $rdf_graph the graph that the rdf is to be written to
 * @param bool $del_graph whether to delete the graph before adding the rdf
 */
function storeRDF( $rdf_file, $rdf_graph = '', $del_graph = true )
{
	$loader = "/project/virtuoso/scripts/vload" ;
	$deleter = "/project/virtuoso/scripts/vdelete" ;
	$lockfile = "tmp/toolmatch.lck" ;
	$delcmd = $deleter . " " . $rdf_graph . " > /dev/null 2>&1" ;
	$loadcmd = $loader . " rdf " . $rdf_file . " " . $rdf_graph . " > /dev/null 2>&1" ;
	$try_lock = 0 ;

	/* get an exclusive lock on the lock file before trying to write to
	 * the triple store
	 */
	$lockhandle = fopen( $lockfile, "w+" ) ;
	while( !flock( $lockhandle, LOCK_EX ) && $try_lock < 3 )
	{
		$try_lock++ ;
		print( "tring again\n" ) ;
		// FIXME: yes I know, sleeping like this is bad
		sleep( 2 ) ;
	}

	/* we tried 3 times. If try_lock is 3 then we failed to get the lock
	 */
	if( $try_lock == 3 )
	{
		print( "coudn't get lock\n" ) ;
		flock( $lockhandle, LOCK_UN ) ;
		fclose( $lockhandle ) ;
		return false ;
	}

	if( $del_graph )
	{
		exec( $delcmd ) ;
	}

	exec( $loadcmd ) ;

	flock( $lockhandle, LOCK_UN ) ;
	fclose( $lockhandle ) ;
	return true ;
}

function deleteRDF($rdf_graph, $del_graph = true) 
{
	$deleter = "/project/virtuoso/scripts/vdelete" ;
	$delcmd = $deleter . " " . $rdf_graph . " > /dev/null 2>&1" ;
	if( $del_graph )
	{
		tm_debug( "Deleting the graph using $delcmd" ) ;
		exec( $delcmd ) ;
	}
	else
	{
		tm_debug( "NOT deleting the graph $rdf_graph" ) ;
	}
	return true;
}

function tm_debug( $msg )
{
	global $debug, $debug_file ;
	if( $debug )
	{
		error_log( $msg . "\n", 3, $debug_file ) ;
	}
}

