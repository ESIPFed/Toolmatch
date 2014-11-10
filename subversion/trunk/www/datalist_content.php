<span class="page_title">Data Collection List
	<span>Click on a data collection to see more info</span>
</span></br>
<?php
include_once("utils.php"); 
try
{
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

			  SELECT ?data ?label ?description
			  WHERE
			  {
				  ?data a <http://toolmatch.esipfed.org/schema#DataCollection> .
				  ?data rdfs:label ?label .
				  OPTIONAL { ?data dcat:description ?description . }
			  } ORDER BY ?label';
			
	$content = sparqlSelect($query);
	$data = json_decode($content, true);
	
	foreach($data['results']['bindings'] as $result) {
		$data = $result['data']['value'];
		$label = $result['label']['value'];
		$description = $result['description']['value'];

		//truncate and remove underscores from label
		$label_changed = str_replace("_"," ", $label);
		$label_limit = 100;
		if (strlen($label_changed) > $label_limit) {
			$label_short = substr($label_changed, 0, $label_limit);
			$label_changed = substr($label_short, 0, strrpos($label_short, ' ')) . "..."; 
		}
		
		// truncate description
		$desc_limit = 225;
		if (strlen($description) > $desc_limit) {
			$desc_short = substr($description, 0, $desc_limit);
			$description = substr($desc_short, 0, strrpos($desc_short, ' ')) . " ";
		}
		 ?>
		 
		<div class="instance_row">
			  <a href="#" class="instance_title" onClick="showHideToolInfo('<?php echo $label; ?>')"> <?php echo $label_changed; ?></a>
			  <div style="float:right;"><a href="http://toolmatch.esipfed.org/delete.php?data=<?php echo $label ?>"><img src="/images/delete_icon.png" alt="Delete Data Collection" title="Delete Data Collection" height="24px" width="24px" onClick="return confirmDelete()" /></a></div>
			  <div style="float:right;"><a href="http://toolmatch.esipfed.org/dataform.php?uri=<?php echo $data; ?>"><img src="/images/pencil-icon-128.png" alt="Edit Data Collection" title="Edit Data Collection" height="24px" width="24px" /></a></div>
			  <div id="<?php echo $label; ?>" style="display:none;"><?php echo $description; ?><a id="more_info" href="http://toolmatch.esipfed.org/data.php?uri=<?php echo $data; ?>">[...]</a><br/><br/></div>
		  </div>
		<?php }
} 
catch( Exception $e )
{
	$msg = $e->getMessage() ;
	print( "$msg\n" ) ;
}
?>

<script>
function showHideToolInfo(div){
    var x = document.getElementById(div);
    if(x.style.display == 'none'){
        x.style.display = 'block';
    } else {
        x.style.display = 'none';
    }
	return true;
}

function confirmDelete() {
		var x = window.confirm("Are you sure you want to delete this data collection?");
		return x;
	}
</script>
