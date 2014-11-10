<style>
.red-star {
    color: red;
}
</style>

<div style="margin:5px;">
	<form name="tool_input" action="dataset_submit.php" method="post" enctype="multipart/form-data">
		<span class="red-star">*</span>Dataset Name: </br>
		<textarea name="dataname" rows="1" cols="65"  required></textarea></br></br>
		
		<span class="red-star">*</span>Dataset DOI: </br>
		<textarea name="datadoi" rows="1" cols="65"  required></textarea></br></br>
		
		<span class="red-star">*</span>Dataset Description: </br>
		<textarea name="datadesc" rows="4" cols="65" required ></textarea></br></br>
	
		<input class="button" type="submit" value="Add Dataset" name="submit"></br>
		
		<p style="font-size:10pt;">Note: <span class="red-star">*</span> Starred fields are required. </p>
		
	</form>
</div>
