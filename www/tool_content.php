<?php 
include_once("utils.php"); 
include_once( "common.php" ) ;
try
{
	include_once( "twsparql/TWSparqlHTML.inc" ) ;

	TWSparql::getEngine()->enableDebug( true ) ;

	$query = "tool.rq" ;
	$xslt = "tool.xsl" ;
	$uri = htmlspecialchars($_GET["uri"]) ;
	$sparql = "<sparql query=\"$query\" xslt=\"$xslt\" uri=\"$uri\"/>" ;

	$contents = TWSparql::getEngine()->render( $sparql ) ;

	print( "$contents" ) ;
}
catch( Exception $e )
{
	$msg = $e->getMessage() ;
	print( "$msg\n" ) ;
}
if( isset( $_GET["uri"] ) )
{
	$instance = $_GET["uri"];
	
	//query for all tool input formats
	$query_inputs = 'PREFIX tool: <http://toolmatch.esipfed.org/schema#>
					 SELECT ?input
					 WHERE { OPTIONAL { <' . $instance . '> tool:hasInputFormat ?input . } }';
	$content_inputs = sparqlSelect($query_inputs);
	$data_inputs = json_decode($content_inputs, true);
					
	//query for all tool output formats				
	$query_outputs = 'PREFIX tool: <http://toolmatch.esipfed.org/schema#>
					  SELECT ?output
					  WHERE { OPTIONAL { <' . $instance . '> tool:hasOutputFormat ?output . } }';
	$content_outputs = sparqlSelect($query_outputs);
	$data_outputs = json_decode($content_outputs, true);
	
	//query for all tool capabilities		
	$query_capabilities = 'PREFIX tool: <http://toolmatch.esipfed.org/schema#>
						  SELECT ?capability
						  WHERE { OPTIONAL { <' . $instance . '> tool:hasCapability ?capability . } }';
	$content_capabilities = sparqlSelect($query_capabilities);
	$data_capabilities = json_decode($content_capabilities, true);

	//query for all tool types		
	$query_types = 'PREFIX tool: <http://toolmatch.esipfed.org/schema#>
					SELECT ?type
					WHERE { OPTIONAL { <' . $instance . '> tool:isOfType ?type . } }';
	$content_types = sparqlSelect($query_types);
	$data_types = json_decode($content_types, true);
	
	//prints out all input and output formats, types, and capabilities
	if (!empty($data_inputs)) {
		$count = 0;
		foreach($data_inputs['results']['bindings'] as $result) {
			$result = explode("/", $result['input']['value']);
			$result = end($result);
			if ($count == 0 && $result != "") {
				echo '<span style="font-weight:bold;">Input Formats: </span>';
				$count++;
			}
			echo $result . ' ';	
		}
	}
	
	if (!empty($data_outputs)) {
		$count = 0;
		foreach($data_outputs['results']['bindings'] as $result) {
			$result = explode("/", $result['output']['value']);
			$result = end($result);
			if ($count == 0 && $result != ""){
				echo '<br/><span style="font-weight:bold;">Output Formats: </span>';
				$count++;
			}
			echo $result . ' ';
		}
	}

	if (!empty($data_capabilities)) {
		$count = 0;
		foreach($data_capabilities['results']['bindings'] as $result) {
			$result = explode("/", $result['capability']['value']);
			$result = end($result);
			if ($count == 0 && $result != "") {
				echo '<br/><span style="font-weight:bold;">Tool Capabilities: </span>';
				$count++;
			}
			echo $result . ' ';
		}
	}
	
	if (!empty($data_types)) {
		$count = 0;
		foreach($data_types['results']['bindings'] as $result) {
			$result = explode("/", $result['type']['value']);
			$result = end($result);
			if ($count == 0 && $result != "") {
				echo '<br/><span style="font-weight:bold;">Tool Type: </span>';
				$count++;
			}
			echo $result . ' ';
		}
	}
}


?>
