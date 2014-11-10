<?php
include_once( "utils.php" ) ;

$home_back = "white" ;
$home_clr = "#222930" ;
$tools_back = "white" ;
$tools_clr = "#222930" ;
$data_back = "white" ;
$data_clr = "#222930" ;
$ont_back = "white" ;
$ont_clr = "#222930" ;
$about_back = "white" ;
$about_clr = "#222930" ;

if( $highlight_nav == "home" )
{
    $home_back = "#4eb1ba" ;
    $home_clr = "#222930" ;
}
else if( $highlight_nav == "tools" )
{
    $tools_back = "#4eb1ba" ;
    $tools_clr = "#222930" ;
}
else if( $highlight_nav == "datasets" )
{
    $data_back = "#4eb1ba" ;
    $data_clr = "#222930" ;
}
else if( $highlight_nav == "ont" )
{
    $ont_back = "#4eb1ba" ;
    $ont_clr = "#222930" ;
}
else if( $highlight_nav == "about" )
{
    $about_back = "#4eb1ba" ;
    $about_clr = "#222930" ;
}
?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo "$title";?></title>
<link href="/css/toolmatch.css" rel="stylesheet" type="text/css"/>
<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">
</head>
<body>
	<div id="header">
		<header>
			<a href="/index"> <span style="font-size:52pt;">&nbsp;T</span><span style="font-size:40pt;">ool</span><span style="font-size:48pt;">M</span><span style="font-size:36pt;">atch</span></a>
		</header>
	
		<div id="header_nav_outer">
			<nav id="header_nav">
				<ul>
					<li><a href="/index"><i class="fa fa-home fa-fw"></i>&nbsp; Home</a></li>
					<li><a href="#"><i class="fa fa-wrench"></i>&nbsp; Tools</a>
					<ul>
						<li><a href="/toollist"><i class="fa fa-list"></i>&nbsp; Tool List</a></li>
						<li><a href="/toolform"><i class="fa fa-plus"></i>&nbsp; Add A Tool</a></li>
					</li></ul>
		 
					<li><a href="#"><i class="fa fa-database"></i>&nbsp; Data Collections</a>
						<ul>
							<li><a href="/datalist"><i class="fa fa-list"></i>&nbsp; Data Collection List</a></li>
							<li><a href="/dataform_init"><i class="fa fa-plus"></i>&nbsp; Add A Data Collection</a></li>
						</ul>
					</li>
					<li><a href="#"><i class="fa fa-share-alt"></i>&nbsp; Semantics</a>
						<ul>
							<li><a href="#"><i class="fa fa-bars"></i>&nbsp; Schema</a>
							<ul>
								<li><a href="/ont_html"><i class="fa fa-book"></i>&nbsp; Schema - HTML</a></li>
								<li><a href="/ont_ontology"><i class="fa fa-book"></i>&nbsp; Schema - OWL</a></li>
								<li><a href="/ont_cmap"><i class="fa fa-book"></i>&nbsp; Schema - CMAP</a></li>
							</ul>
							</li>
							<li><a href="/ont_sparql"><i class="fa fa-star"></i>&nbsp; SPARQL</a></li>
							<li><a href="/ont_docuspeakr"><i class="fa fa-file-code-o"></i>&nbsp; DocuSPeaKr</a></li>
						</ul>
					</li>
					<li><a href="#"><i class="fa fa-info"></i>&nbsp; About</a>
					<ul>
						<li><a href="/about"><i class="fa fa-info-circle"></i>&nbsp; About ToolMatch</a></li>
						<li><a href="/about_presentations"><i class="fa fa-book"></i></i>&nbsp; Presentations</a></li>
						<li><a href="/about_collaborators"><i class="fa fa-users"></i>&nbsp; Collaborators</a></li>
					</li></ul>
				</ul>
			</nav>
		</div>
	</div>


	<div id="main_content">
		<div class="main_content_inner">
			<?php include("$content_page"); ?>
		</div>
	</div>

	<footer>
		<nav id="footer_nav">
			<ul>
				<a href="/index">Home</a> | 
				<a href="/toollist">Tools</a> | 
				<a href="/dataform_init">Datasets</a> | 
				<a href="/ont">Semantics</a> | 
				<a href="/about">About</a>
			</ul>
		</nav>
	
	
		<div id="footer_images">
			<img class="images" height="50px" width="125px" src="/images/esip_logo.png"/>
			<img class="images" width="125px" src="/images/KMotifs02_40.png"/>
			<img class="images" height="50px" width="125px" src="/images/tetherless_logo.png"/>
		</div>
	</footer>	
</body>
</html>

