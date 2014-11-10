<?php
include_once("utils.php");
include_once("common.php");

$instance = htmlspecialchars($_GET["uri"]);

//gets all possible data servers (from json file)
$data_servers_full = array();
$content = file_get_contents("/var/www/results/servers.json");
$data = json_decode($content, true);
foreach($data['results']['bindings'] as $result) {
	$result = $result['server']['value'];
	array_push($data_servers_full, $result);
}
sort($data_servers_full);

//query for all dataset servers
$data_servers_selected = array();
$query = 'PREFIX data: <http://toolmatch.esipfed.org/schema#>
		  SELECT ?server
		  WHERE { OPTIONAL { <' . $instance . '> data:isAccessedBy ?server . } }';
$content = sparqlSelect($query);
$data_servers = json_decode($content, true);

foreach ($data_servers['results']['bindings'] as $result) {							
	$result = $result['server']['value'];
	array_push($data_servers_selected, $result);
}

/*
$username = 'ferrim2';
$password = 'Toolmatch123';
$endpoint = 'http://gcmdservices.gsfc.nasa.gov/mws/dif/CIESIN_SEDAC_LWP2_HII_GEOG';

$curl = curl_init() ;
curl_setopt($curl, CURLOPT_URL, $endpoint);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, 'user='.$username.'&pass='.$password);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$content = curl_exec($curl);
curl_close( $curl ) ;
//echo $content;
*/



if( isset( $instance ) && $instance != "" ) { 
	try {		
		//sparql query for individual tool (label, description, page, version, and image)
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
		
		foreach($data_info['results']['bindings'] as $result) {
			$label = $result['label']['value'];
			$doi = $result['doi']['value'];
			$url = end(explode("#", $result['url']['value']));
			$description = $result['description']['value'];
			$format = end(explode("/", $result['format']['value']));
			$convention = end(explode("/", $result['convention']['value']));
		}
		
		$label_changed = str_replace("_"," ", $label);	
		
	} catch( Exception $e ) {
		$msg = $e->getMessage() ;
		print( "$msg\n" ) ;
	}
	
?>
	<form name="data_input" action="dataform_submit.php" method="post" onSubmit="return validate()">
		<span class="page_title">Data Collection Form<span>Please fill out all required fields.</span></span></br>
		
		<?php
		//Name
		echo '<span class="red-star">*</span>Data Collection Name: </br>';
		if (!empty($label)) {
			echo '<textarea name="dataname" rows="2" style="width:100%;">' . $label_changed . '</textarea></br></br>';
		} else {
			echo '<textarea name="dataname" rows="2" style="width:100%;"placeholder="ex: California Biogeographic Information and Observation System (BIOS)" required></textarea></br></br>';
		}
		
		//DOI
		echo 'Data Collection DOI</br>';
		if (!empty($doi)) {
			echo '<textarea name="datadoi" rows="1" style="width:100%;">' . $doi . '</textarea></br></br>';
		} else {
			echo '<textarea name="datadoi" rows="1" style="width:100%;" placeholder="10.5067/AQUA/AIRS/DATA301"></textarea></br></br>';
		}
		
		//GCMD
		echo 'GCMD Entry ID</br>';
		echo '<textarea name="datagcmd" rows="1" style="width:100%;" placeholder="GES_DISC_AIRX3STD_V006"></textarea></br></br>';

		//Access URL
		echo 'Access URL</br>';
		if (!empty($url)) {
			echo '<textarea name="dataurl" rows="1" style="width:100%;">' . $url . '</textarea></br></br>';
		} else {
			echo '<textarea name="dataurl" rows="1" style="width:100%;" placeholder="http://acdisc.sci.gsfc.nasa.gov/opendap/Aqua_AIRS_Level3/AIRX3STD.006/"></textarea></br></br>';
		} 
		
		//Description
		echo 'Data Collection Description: </br>';
		if (!empty($description)) {
			echo '<textarea name="datadesc" rows="4" style="width:100%;"</textarea>' . $description . '</textarea></br></br>';
		} else {
			$placeholder = "ex: California Department of Fish and Game's central repository for biological observation and distribution info. Contains over 600 individual databases including the CA Natural Heritage Program data. Provides tools for querying and reporting. Useful for site specific project work.";
			echo '<textarea name="datadesc" rows="4" style="width:100%;" placeholder="' . $placeholder . '"></textarea></br></br>';
		}?>
		
		<div>
			<div style="display:inline-block;">
				Collection Format <span style="font-size:11px;">(select one):</span></br>		
				<select name="dataformat">
				<option></option>
				<?php 
					$inputs = file_get_contents("/var/www/results/formats.json");
					$data=json_decode($inputs,true);
					foreach($data['results']['bindings'] as $instance) {
						$instance = $instance['format']['value'];
						$name = end(explode('/', $instance));
						if ($format == $name) {
							echo '<option  value="' . $instance . '" selected>' . $name . '</option>';
						} else {
							echo '<option  value="' . $instance . '">' . $name . '</option>';
						}
					} ?> 
				</select></br></br>
			</div>
		
			<div style="display:inline-block;margin-left:10px;">
				Collection Convention <span style="font-size:11px;">(select one):</span></br>		
				<select name="dataconv">
				<option></option>
				<?php 
					$inputs = file_get_contents("/var/www/results/conventions.json");
					$data=json_decode($inputs,true);
					foreach($data['results']['bindings'] as $instance) {
						$instance = $instance['convention']['value'];
						$name = end(explode('/', $instance));
						if ($convention == $name) {
							echo '<option  value="' . $instance . '" selected>' . $name . '</option>';
						} else {
							echo '<option  value="' . $instance . '">' . $name . '</option>';
						}
					}
				?>
				</select></br></br>
			</div>
		</div>
		
		Server Accessibility <span style="font-size:11px;">(select all that apply):</span></br>
		<select multiple name="dataserver[]" >
			<?php 
			foreach ($data_servers_full as $instance) {
				$name = end(explode('/', $instance));
				if (in_array($instance, $data_servers_selected)) {
					echo '<option  value="' . $instance . '" selected>' . $name . '</option>';
				} else {
					echo '<option  value="' . $instance . '">' . $name . '</option>';
				}
			} ?>
		</select></br></br>

		<input type="hidden" name="status" value="edit">
		<input class="button" type="submit" value="Add Data Collection" name="submit" style="display:inline-block;">
		<a class="button" href="/dataform_init.php" style="display:inline-block;">Change Identifier</a>
	</form>
		
		<p style="font-size:10pt;margin-left:5px;">Note: <span class="red-star">*</span> Starred fields are required. </p>
<?php
} else {
if(isset($_POST['submit'])) {
	//variables for form data
	$datadoi = $_POST['datadoi'];
	$datagcmd = $_POST['datagcmd'];
	$dataurl = $_POST['dataurl'];
?>
<form name="data_input" action="dataform_submit.php" method="post" onSubmit="return validate()">
	<span class="page_title">Data Collection Form<span>Please fill out all required fields.</span></span></br>
	
	<span class="red-star">*</span>Data Collection Name: </br>
	<textarea name="dataname" rows="2" style="width:100%;"placeholder="ex: California Biogeographic Information and Observation System (BIOS)" required></textarea></br></br>

	<?php
	//If doi was entered, display it, blank field otherwise
	if (!empty($datadoi)) { ?>
		Data Collection DOI</br>
		<textarea name="datadoi" rows="1" style="width:100%;"><?php echo $datadoi; ?></textarea></br></br>
	<?php } else { ?>
		Data Collection DOI</br>
		<textarea name="datadoi" rows="1" style="width:100%;" placeholder="10.5067/AQUA/AIRS/DATA301"></textarea></br></br>
	<?php } ?>
	
	<?php
	//If gcmd was entered, display it, blank field otherwise
	if (!empty($datagcmd)) { ?>
		GCMD</br>
		<textarea name="datagcmd" rows="1" style="width:100%;"><?php echo $datagcmd; ?></textarea></br></br>
	<?php } else { ?>
		GCMD</br>
		<textarea name="datagcmd" rows="1" style="width:100%;" placeholder="GES_DISC_AIRX3STD_V006"></textarea></br></br>
	<?php } ?>
	
	<?php
	//If url was entered, display it, blank field otherwise
	if (!empty($dataurl)) { ?>
		Access URL</br>
		<textarea name="dataurl" rows="1" style="width:100%;"><?php echo $dataurl; ?></textarea></br></br>
	<?php } else { ?>
		Access URL</br>
		<textarea name="dataurl" rows="1" style="width:100%;" placeholder="http://acdisc.sci.gsfc.nasa.gov/opendap/Aqua_AIRS_Level3/AIRX3STD.006/"></textarea></br></br>
	<?php } ?>
	
	Data Collection Description: </br>
	<textarea name="datadesc" rows="4" style="width:100%;" placeholder="ex: California Department of Fish and Game's central repository for biological observation and distribution info. Contains over 600 individual databases including the CA Natural Heritage Program data. Provides tools for querying and reporting. Useful for site specific project work."></textarea></br></br>
	
	<div>
		<div style="display:inline-block;">
			Collection Format <span style="font-size:11px;">(select one):</span></br>		
			<select name="dataformat">
			<option></option>
			<?php 
				$inputs = file_get_contents("/var/www/results/formats.json");
				$data=json_decode($inputs,true);
				foreach($data['results']['bindings'] as $instance) {
					$instance = $instance['format']['value'];
					$name = end(explode('/', $instance));
					?><option  value="<?php echo $instance; ?>"><?php echo $name; ?></option>
			<?php } ?>
			</select></br></br>
		</div>
	
		<div style="display:inline-block;margin-left:10px;">
			Collection Convention <span style="font-size:11px;">(select one):</span></br>		
			<select name="dataconv">
			<option></option>
			<?php 
				$inputs = file_get_contents("/var/www/results/conventions.json");
				$data=json_decode($inputs,true);
				foreach($data['results']['bindings'] as $instance) {
					$instance = $instance['convention']['value'];
					$name = end(explode('/', $instance));
					?><option  value="<?php echo $instance; ?>"><?php echo $name; ?></option>
				<?php }
			?>
			</select></br></br>
		</div>
	</div>
	
	Server Accessibility <span style="font-size:11px;">(select all that apply):</span></br>
	<select multiple name="dataserver[]" >
			<?php 
			foreach ($data_servers_full as $instance) {
				$name = end(explode('/', $instance));
				echo '<option  value="' . $instance . '">' . $name . '</option>';
			} ?>
	</select></br></br>
	
	<input type="hidden" name="status" value="add">
		
	<input class="button" type="submit" value="Add Data Collection" name="submit" style="display:inline-block;">
	<a class="button" href="/dataform_init.php" style="display:inline-block;">Change Identifier</a>
</form>
	
	<p style="font-size:10pt;margin-left:5px;">Note: <span class="red-star">*</span> Starred fields are required. </p>
	
<?php
}
}
?>
<script>function validate() {
	//grab values of 3 fields
	var doi = document.forms["data_input"]["datadoi"].value;
    var gcmd = document.forms["data_input"]["datagcmd"].value;
    var url = document.forms["data_input"]["dataurl"].value;

	if ((doi == null || doi == "") && (gcmd == null || gcmd == "") && (url == null || url == "")) {
		alert("Please fill out at least one of the data collection ID fields.");
		return false;
	}
}
</script>
