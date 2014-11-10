<?php
    $mydocumentroot=$_SERVER["DOCUMENT_ROOT"];
    set_include_path( $mydocumentroot );
    $highlight_nav= "datasets";
    $title="ToolMatch Datasets";
    $content_page="datasets_content.php";
    include("template.php") ;
?>