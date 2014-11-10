<?php
include_once("utils.php");
$tool = htmlspecialchars($_GET["tool"]);
$data = htmlspecialchars($_GET["data"]);

if (isset($tool) && $tool != "") { 

	//graph name
	$gname = "http://toolmatch.esipfed.org/tools/graph/" . $tool ;
	
	//delete graph
	$deleted = deleteRDF($gname, true);
	if ($deleted) {
		echo '<p class="success">' . $tool . ' has been deleted!</p>';
		echo '<form action="toolform.php" style="display:inline-block;">
				<input class="success_button" type="submit" value="Add a Tool" >
			 </form>';
		echo '<form action="toollist.php" style="display:inline-block;">
				<input class="success_button" type="submit" value="View Tool List">
			  </form>';
	}
} else if (isset($data) && $data != "") { 
	//graph name
	$gname = "http://toolmatch.esipfed.org/datasets/graph/" . $data ;
	//delete graph
	$deleted = deleteRDF($gname, true);
	if ($deleted) {
		echo '<p class="success">' . $data . ' has been deleted!</p>';
		echo '<form action="dataform_init.php" style="display:inline-block;">
				<input class="success_button" type="submit" value="Add a Data Collection" >
			 </form>';
		echo '<form action="datalist.php" style="display:inline-block;">
				<input class="success_button" type="submit" value="View Data Collection List">
			  </form>';
	}
}
?>
