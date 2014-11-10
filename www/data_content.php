<?php 
include_once("utils.php"); 
include_once("common.php");

if( isset( $_GET["uri"] ) )
{
	$instance = $_GET["uri"];
	
	//query for all info but servers
	$query = 'PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
			  PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
			  PREFIX owl: <http://www.w3.org/2002/07/owl#>
			  PREFIX tw: <http://tw.rpi.edu/schema/>
			  PREFIX twi: <http://tw.rpi.edu/instances/>
			  PREFIX time: <http://www.w3.org/2006/time#>
			  PREFIX foaf: <http://xmlns.com/foaf/0.1/>
			  PREFIX dc: <http://purl.org/dc/terms/>
			  PREFIX doap: <http://usefulinc.com/ns/doap#>
			  PREFIX data: <http://toolmatch.esipfed.org/schema#>
			  PREFIX dcat: <http://www.w3.org/ns/dcat#>

			  SELECT ?label ?doi ?url ?description ?format ?convention
			  WHERE
			  {
				  <' . $instance . '> a <http://toolmatch.esipfed.org/schema#DataCollection> .
				  <' . $instance . '> rdfs:label ?label .
				  OPTIONAL { <' . $instance . '> dc:identifier ?doi . }
		          OPTIONAL { <' . $instance . '> data:hasAccessURL ?url . }
				  OPTIONAL { <' . $instance . '> dcat:description ?description . }
				  OPTIONAL { <' . $instance . '> data:hasDataFormat ?format . }
			      OPTIONAL { <' . $instance . '> data:usesConvention ?convention . }
			  }';
			
	$content = sparqlSelect($query);
	$data_info = json_decode($content, true);
	
	//query for all dataset servers
	$query = 'PREFIX data: <http://toolmatch.esipfed.org/schema#>
			  SELECT ?server
			  WHERE { OPTIONAL { <' . $instance . '> data:isAccessedBy ?server . } }';
	$content = sparqlSelect($query);
	$data_servers = json_decode($content, true);
	
	foreach($data_info['results']['bindings'] as $result) {
		$label = $result['label']['value'];
		$doi = $result['doi']['value'];
		$url = end(explode("#", $result['url']['value']));
		$description = $result['description']['value'];
		$format = end(explode("/", $result['format']['value']));
		$convention = end(explode("/", $result['convention']['value']));
	}
	$label_changed = str_replace("_"," ", $label);
	if (!empty($label)) {
		echo '<span class="page_title" style="font-weight:bold;font-size:20pt;float:left;">' . $label_changed;
		echo '<span style="float:right;margin-right:20px;"><a href="http://toolmatch.esipfed.org/delete.php?data=' . $label . '"><img src="/images/delete_icon.png" alt="Delete Data Collection" title="Delete Data Collection" height="24px" width="24px" onClick="return confirmDelete()" /></a></span>
		<span style="float:right;margin-right:5px;"><a href="http://toolmatch.esipfed.org/dataform.php?uri=' . $instance . '"><img src="/images/pencil-icon-128.png" alt="Edit Data Collection" title="Edit Data Collection" height="24px" width="24px" /></a></span></span>
		<br/><br/></br>';
	}
	
	if (!empty($doi)) {
		echo '<span style="font-weight:bold;">DOI: </span>' . $doi . '<br/>';
	}
	
	if (!empty($url)) {
		echo '<span style="font-weight:bold;">Access URL: </span>' . $url . '<br/>';
	}
	
	if (!empty($description)) {
		echo '<span style="font-weight:bold;">Description: </span>' . $description . '<br/>';
	}
	
	if (!empty($format)) {
		echo '<span style="font-weight:bold;">Format: </span>' . $format . '<br/>';
	}
	
	if (!empty($convention)) {
		echo '<span style="font-weight:bold;">Convention </span>' . $convention . '<br/>';
	}
	
	if (!empty($data_servers)) {
		$count = 0;
		foreach($data_servers['results']['bindings'] as $result) {
			$result = explode("/", $result['server']['value']);
			$result = end($result);
			if ($count == 0 && $result != "") {
				echo '<span style="font-weight:bold;">Servers: </span>';
				$count++;
			}
			echo $result . ' ';	
		}
	}
}
?>

<script>
function confirmDelete() {
		var x = window.confirm("Are you sure you want to delete this data collection?");
		return x;
	}
</script>
