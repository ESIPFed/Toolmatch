<?php
    $mydocumentroot=$_SERVER["DOCUMENT_ROOT"];
    set_include_path( $mydocumentroot );
    $highlight_nav= "datasets";
    $title="ToolMatch Data Collection List";
    $content_page="datalist_content.php";
    include("template.php") ;
?>