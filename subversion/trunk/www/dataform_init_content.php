<form name="data_input" action="dataform" method="post" onSubmit="return validate()">
	<!--
	Data Collection Identifier <span style="font-size:11px;">(choose one):</span></br>
	<select id="data_id" onchange="showHideDataForms()">
		<option value="doi">DOI</option>
		<option value="gcmd">Entity ID</option>
		<option value="url">Access URL</option>
	</select></br></br> -->
	
	
	<span class="page_title">Data Collection Form
		<span>Please enter one or more data collection identifiers.</span>
	</span></br>
	
	<div id="data_doi">
		Data Collection DOI: </br>
		<textarea name="datadoi" rows="1" style="width:100%;" placeholder="ex: 10.5067/AQUA/AIRS/DATA301"></textarea></br></br>
	</div> 
	
	<div id="data_gcmd"> <!--style="display:none;"--> 
		GCMD Entry ID: </br>
		<textarea name="datagcmd" rows="1" style="width:100%;" placeholder="ex: GES_DISC_AIRX3STD_V006" ></textarea></br></br>
	</div>
	
	<div id="data_url"> <!--style="display:none;"--> 
		Access URL </br>
		<textarea name="dataurl" rows="1" style="width:100%;" placeholder="ex: http://acdisc.sci.gsfc.nasa.gov/opendap/Aqua_AIRS_Level3/AIRX3STD.006/" ></textarea></br></br>
	</div>

	<input class="button" type="submit" value="Submit Identifier" name="submit"></br>
	
	<p style="font-size:10pt;">Note: At least one field is required.</p>
	
</form>


<script>
function validate() {
	//grab values of 3 fields
	var doi = document.forms["data_input"]["datadoi"].value;
    var gcmd = document.forms["data_input"]["datagcmd"].value;
    var url = document.forms["data_input"]["dataurl"].value;

	if ((doi == null || doi == "") && (gcmd == null || gcmd == "") && (url == null || url == "")) {
		alert("Please fill out at least one field.");
		return false;
	}
}
</script>
