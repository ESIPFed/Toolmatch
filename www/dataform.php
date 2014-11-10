<?php
    $mydocumentroot=$_SERVER["DOCUMENT_ROOT"];
    set_include_path( $mydocumentroot );
    $highlight_nav= "datasets";
    $title="ToolMatch DataForm";
    $content_page="dataform_content.php";
    include("template.php") ;
?>