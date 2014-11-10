<?php
    $mydocumentroot=$_SERVER["DOCUMENT_ROOT"];
    set_include_path( $mydocumentroot );
    $highlight_nav= "tools";
    $title="ToolMatch Tool List";
    $content_page="toollist_content.php";
    include("template.php") ;
?>