<?php

function sanitize($str,$esc=TRUE) {
  if(strpos($str,"<")===FALSE && strpos($str,">")===FALSE) {
    return $esc ? urlencode($str) : $str;
  }
  else die("Invalid input");
}

function twerror()
{
    $str = "#\n" ;
    $str .= "# No parameters were specified for this query. Expected parameters are:\n" ;
    $str .= "#     i=<instance> - where instance is the local tw instance http://tw.rpi.edu/instances/<instance> (i.e. http://tw.rpi.edu/instances/PatrickWest)\n" ;
    $str .= "#     uri=<uri> - the full uri of the instance (i.e. http://tw.rpi.edu/instances/PatrickWest)\n" ;
    $str .= "#     s=<type> - where type is a class (i.e. s=Faculty)\n" ;
    $str .= "#\n" ;
    $str .= "# For more information: http://tw.rpi.edu/web/project/TWWebsite/rdf2html/Documentation\n" ;
    $str .= "# For more information: http://logd.tw.rpi.edu/tutorial/how_use_drupal_sparql_module\n" ;
    $str .= "#\n" ;
    $str .= "\n" ;
    echo $str ;
}

