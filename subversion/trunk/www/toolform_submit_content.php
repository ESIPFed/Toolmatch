<?php
include_once("utils.php");

try {
	if(isset($_POST['submit'])) {
	
		//variables for form data
		$toolname = trim($_POST['toolname']);
		$toolname = preg_replace("~[\W]~","_", $toolname);
		$toolpage = trim($_POST['toolpage']);
		$tooldesc = trim($_POST['tooldesc']);
		
		$cur_toollogo = $_POST['cur_toollogo']; //current tool logo (if there is one)
		$toollogo = $_POST['toollogo']; //new tool logo (if there is one)
		
		$toolvers = $_POST['toolvers'];
		
		$tool_input_formats = array();
		$tool_input_formats = $_POST['toolinput'];
		
		$tool_output_formats = array();
		$tool_output_formats = $_POST['tooloutput'];
		
		$tooltypes = $_POST['tooltype'];
		$vistypes = $_POST['vistype'];
		
		//status is either add or edit
		$status = $_POST['status'];
	
		//checks URL
		//if (!preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i", $toolpage)) {
		//	throw new RuntimeException("<p class="failure">Submission failed! Please enter a valid URL.</p>");
		//}

		//handles image upload (checks for new image first, if no new image then checks for existing image)
		if($_FILES['toollogo']['name']) {
		
			//variables for image upload
			$filename=$_FILES['toollogo']['name'];
			$filetype=$_FILES['toollogo']['type'];
			$filename = strtolower($filename);
			$filetype = strtolower($filetype);
			
			
			//make sure it's not a php file
			$pos = strpos($filename,'php');
			if(!($pos === false)) {
				throw new RuntimeException('<p class="failure">Submission failed! Please make sure the uploaded file is an image.</p>');
			}
			
			//get the file ext
			$file_ext = strrchr($filename, '.');

			//check if its allowed or not
			$extlist = array(".jpg",".jpeg",".gif",".png"); 
			if (!(in_array($file_ext, $extlist))) {
				throw new RuntimeException('<p class="failure">Submission failed! Please make sure the uploaded file is an image.</p>');
			}
			
			//check upload type
			$pos = strpos($filetype,'image');
			if($pos === false) {
				throw new RuntimeException('<p class="failure">Submission failed! Please make sure the uploaded file is an image3.</p>');
			}
			$imageinfo = getimagesize($_FILES['toollogo']['tmp_name']);
			if($imageinfo['mime'] != 'image/gif' && $imageinfo['mime'] != 'image/jpeg'&& $imageinfo['mime']!= 'image/jpg'&& $imageinfo['mime'] != 'image/png') {
				throw new RuntimeException('<p>Submission failed! Please make sure the uploaded file is an image.</p>');
			}
			
			//check filesize
			$max_file_size = 100000; // size in bytes 
			if ($_FILES['toollogo']['size'] > $max_file_size) {
				throw new RuntimeException('<p class="failure">Submission failed! Exceeded filesize limit.  Please choose a smaller file.</p>');
			}
			
			//moves uploaded image to /var/www/added_tools/logos
			$temp = explode(".",$_FILES['toollogo']['name']);
			$filename = $toolname . '_logo.png';
			move_uploaded_file($_FILES['toollogo']['tmp_name'],  '/var/www/added_tools/logos/'.$filename);
			
			$filename = explode("/",$filename);
			$filename = end($filename);
			$toollogo_rdf = '
    <foaf:depiction>
      <foaf:Image rdf:about="http://toolmatch.esipfed.org/added_tools/logos/' . $filename. '"/>
    </foaf:depiction>';
		} else if (!empty($cur_toollogo)){
			$toollogo_rdf = '
    <foaf:depiction>
      <foaf:Image rdf:about="' . $cur_toollogo. '"/>
    </foaf:depiction>';
		} else {
			$toollogo_rdf = '';
		}
		
		//beginning of rdf
  $rdf_start = '<toolmatch:Tool rdf:about="&toolmatchi;' . $toolname . '">
    <rdf:type rdf:resource="&doap;Project"/>
    <rdfs:label rdf:datatype="http://www.w3.org/2001/XMLSchema#string">' . $toolname . '</rdfs:label>
    <dc:title rdf:datatype="http://www.w3.org/2001/XMLSchema#string">' . $toolname . '</dc:title>
    <doap:homepage rdf:resource="' . $toolpage . '"/>
    <dc:description rdf:parseType="Literal">' . $tooldesc. '</dc:description>' .
	$toollogo_rdf . '
    <doap:release>
      <doap:Version rdf:about="&toolmatchi;' . $toolname . '_' . $toolvers . '">
        <doap:revision>' . $toolvers . '</doap:revision>
      </doap:Version>
    </doap:release>';
			
		//rdf for tool input format(s) (optional)	
		$rdf_tool_inputs = '';
		if (!empty($tool_input_formats)) {
			foreach ($tool_input_formats as $input) {
				$rdf_tool_inputs = $rdf_tool_inputs . '
    <toolmatch:hasInputFormat rdf:resource="' . $input . '"/>';
			}
		}
		//rdf for tool output format(s) (optional)	
		$rdf_tool_outputs = '';
		if (!empty($tool_output_formats)) {
			foreach ($tool_output_formats as $output) {
				$rdf_tool_outputs = $rdf_tool_outputs . '
    <toolmatch:hasOutputFormat rdf:resource="' . $output . '"/>';
			}
		}
		
		//rdf for tool capabilities (optional)	
		$rdf_vistypes = '';
		if (!empty($vistypes)) {
			foreach ($vistypes as $type) {
				$rdf_vistypes = $rdf_vistypes . '
    <toolmatch:hasCapability rdf:resource="' . $type . '"/>';
			}
		}
		
		//rdf for tool type(s) (optional)
		$rdf_tooltypes = '';
		if (!empty($tooltypes)) {
			foreach ($tooltypes as $type) {
				$rdf_tooltypes = $rdf_tooltypes . '
    <toolmatch:isOfType rdf:resource="' . $type . '"/>';
			}
		}
		
		//end of rdf
		$rdf_end = '
  </toolmatch:Tool>
</rdf:RDF>';
		
		//concatenates rdf together into one string
		$rdf = $rdf_beg . $rdf_start . $rdf_tool_inputs . $rdf_tool_outputs . $rdf_vistypes . $rdf_tooltypes . $rdf_end;
		
		//creates names for file and graph
		$fname = $toolname . ".rdf";
		$gname = "http://toolmatch.esipfed.org/tools/graph/" . $toolname ;

		//creates new RDF file, writes to it, and closes it
		$new_file = '/project/toolmatch/toolmatch-svn/trunk/data/added_tools/rdf/'. $fname;
		$file = fopen($new_file,"w+");
		$fwrite = fwrite($file, $rdf);
		if ($fwrite == false) {
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
			echo '<p class="success">' . $toolname . ' has been successfully added! <img class="check" src="/images/success.png" alt="Success" height="30px" width="30px"></p>';
		} else if ($status == 'edit') {
			echo '<p class="success">' . $toolname . ' has been successfully updated! <img class="check" src="/images/success.png" alt="Success" height="30px" width="30px"></p>';
		}
	}
} catch(RuntimeException $e) {
    echo $e->getMessage();
}
?>

<div class="button_div">
<form action="toollist.php" style="display:inline-block;">
	<input class="success_button" type="submit" value="View Tool List">
</form>

<?php 
if ($status == 'add') {
	echo '<form action="toolform.php" style="display:inline-block;">
			<input class="success_button" type="submit" value="Add Another Tool" >
		  </form>';
} else if ($status == 'edit') {
	echo '<form action="toolform.php" style="display:inline-block;">
			<input class="success_button" type="submit" value="Add a Tool" >
		  </form>';
}
echo '</div></div>';
?>

