<?php
include_once("utils.php");
try {
	if(isset($_POST['submit'])) {
		//variables for form data
		$dataname = trim($_POST['dataname']);
		$dataname = preg_replace("~[\W]~","_", $dataname);
		$datadesc = trim($_POST['datadesc']);
		$dataformat = $_POST['dataformat'];
		$dataconv = $_POST['dataconv'];
		$dataservers = $_POST['dataserver'];
						
		$datadoi = trim($_POST['datadoi']);
		$datagcmd = trim($_POST['datagcmd']);
		$dataurl = trim($_POST['dataurl']);
		
		//status is either add or edit
		$status = $_POST['status'];
		
		//rdf for data collection title
		$rdf_dataname = '<toolmatch:DataCollection rdf:about="&toolmatchi;' . $dataname . '">
  <rdfs:label rdf:datatype="http://www.w3.org/2001/XMLSchema#string">' . $dataname . '</rdfs:label>
  <dcat:title rdf:datatype="http://www.w3.org/2001/XMLSchema#string">' . $dataname . '</dcat:title>';
  
		//rdf for DOI
		$rdf_datadoi = '';
		if (!empty($datadoi)) {
			$rdf_datadoi = '
			<dc:identifier rdf:parseType="Literal">' . $datadoi . '</dc:identifier>';
		}
		
		//rdf for GCMD
		$rdf_datagcmd = '';
		if (!empty($datagcmd)) {
			$rdf_datagcmd = '
			<!--GCMD: ' . $datagcmd . '-->';
		}
		
		//rdf for access url
		$rdf_dataurl = '';
		if (!empty($dataurl)) {
			$rdf_dataurl = '
			<toolmatch:hasAccessURL rdf:resource="' . $dataurl . '"/>';
		}
		
		//rdf for description (optional)
		$rdf_datadesc = '';
		if (!empty($datadesc)) {
			$rdf_datadesc = '
			<dcat:description rdf:parseType="Literal">' . $datadesc . '</dcat:description>';
		}
		
		//rdf for data format (optional)
		$rdf_dataformat = '';
		if (!empty($dataformat)) {
			$rdf_dataformat = '
			<toolmatch:hasDataFormat rdf:resource="' . $dataformat . '"/>';
		}
		
		//rdf for data convention (optional)
		$rdf_dataconv = '';
		if (!empty($dataconv)) {
			$rdf_dataconv = '
			<toolmatch:usesConvention rdf:resource="' . $dataconv . '"/>';
		}
		
		//rdf for data server(s) (optional)
		$rdf_dataservers = '';
		if (!empty($dataservers)) {
			foreach ($dataservers as $server) {
				$rdf_dataservers = $rdf_dataservers . '
    <toolmatch:isAccessedBy rdf:resource="' . $server . '"/>';
			}
		}
				
		//end of rdf
		$rdf_end = '
		</toolmatch:DataCollection>
</rdf:RDF>';
		
		//concatenates rdf together into one string
		$rdf = $rdf_beg . $rdf_dataname . $rdf_datadoi . $rdf_datagcmd . $rdf_dataurl. $rdf_datadesc . $rdf_dataformat . $rdf_dataconv . $rdf_dataservers . $rdf_end;
		
		//creates RDF file name (dataname or dataid?)
		$fname = $dataname . ".rdf";
		$gname = "http://toolmatch.esipfed.org/datasets/graph/" . $dataname ;

		//creates new RDF file, writes to it, and closes it
		$new_file = '/project/toolmatch/toolmatch-svn/trunk/data/added_datasets/'. $fname;
		$file = fopen($new_file,"w+");
		$fwrite = fwrite($file, $rdf);
		if ($fwrite === false) {
            throw new RuntimeException('<p class="failure">Submission failed! Please try again.</p>');
        }
		fclose($file);
		
		//store the rdf in the triple store
		$stored = storeRDF( $new_file, $gname, true );
		if ($stored == false) {
			throw new RuntimeException('<p class="failure">Submission failed! Please try again.</p>');
		}
		
		echo '<div class="submission_response">';
		if ($status == 'add') {
			echo '<p class="success">' . $dataname . ' has been successfully added! <img class="check" src="/images/success.png" alt="Success" height="30px" width="30px"></p>';
		} else if ($status == 'edit') {
			echo '<p class="success">' . $dataname . ' has been successfully updated! <img class="check" src="/images/success.png" alt="Success" height="30px" width="30px"></p>';
		}
	}
} catch(RuntimeException $e) {

    echo $e->getMessage();
}

?>
<div class="button_div">
<form action="datalist.php" style="display:inline-block;">
		<input class="success_button" type="submit" value="View Data Collection List" >
</form>

<?php 
if ($status == 'add') {
	echo '<form action="dataform_init.php" style="display:inline-block;">
		    <input class="success_button" type="submit" value="Add Another Data Collection" >
		  </form>';
} else if ($status == 'edit') {
	echo '<form action="dataform_init.php" style="display:inline-block;">
			<input class="success_button" type="submit" value="Add a Data Collection" >
		  </form>';
}
echo '</div></div>';

