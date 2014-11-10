<span class="page_title">Tool List
	<span>Click on a tool to see more info</span>
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
			  PREFIX tool: <http://toolmatch.esipfed.org/schema#>

			  SELECT ?tool ?label ?description ?page ?version ?image
			  WHERE
			  {
				  ?tool a <http://toolmatch.esipfed.org/schema#Tool>.
				  ?tool rdfs:label ?label .
				  ?tool dc:description ?description .
				  ?tool doap:homepage ?page .
				  ?tool doap:release ?version .
				  OPTIONAL { ?tool foaf:depiction ?image . }
			  } ORDER BY ?label';
			
	$content = sparqlSelect($query);
	$data = json_decode($content, true);
	
	foreach($data['results']['bindings'] as $result) {
		$tool = $result['tool']['value'];
		$label = $result['label']['value'];
		$description = $result['description']['value'];

		//truncate and remove underscores from label
		$label_changed = str_replace("_"," ", $label);
		$label_limit = 100;
		if (strlen($label_changed) > $label_limit) {
			$label_short = substr($label_changed, 0, $label_limit);
			$label_changed = substr($label_short, 0, strrpos($label_short, ' ')) . "... "; 
		}
		
		// truncate description
		$desc_limit = 225;
		if (strlen($description) > $desc_limit) {
			$desc_short = substr($description, 0, $desc_limit);
			$description = substr($desc_short, 0, strrpos($desc_short, ' ')) . " "; 
		}
		?>
		
		<div class="instance_row">
			  <a href="#" class="instance_title" onClick="showHideInstanceInfo('<?php echo $label_changed; ?>')"> <?php echo $label_changed; ?></a>
			  <div style="float:right;"><a href="http://toolmatch.esipfed.org/delete.php?tool=<?php echo $label ?>"><img src="/images/delete_icon.png" alt="Delete Tool" title="Delete Tool" height="24px" width="24px" onClick="return confirmDelete()" /></a></div>
			  <div style="float:right;"><a href="http://toolmatch.esipfed.org/toolform.php?uri=<?php echo $tool; ?>"><img src="/images/pencil-icon-128.png" alt="Edit Tool" title="Edit Tool" height="24px" width="24px" /></a></div>
			  <div id="<?php echo $label; ?>" style="display:none;"><?php echo $description; ?><a id="more_info" href="http://toolmatch.esipfed.org/tool.php?uri=<?php echo $tool; ?>">[...]</a><br/><br/></div>
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
function showHideInstanceInfo(div){
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
