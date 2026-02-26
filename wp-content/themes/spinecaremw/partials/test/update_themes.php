<?php

require_once("../../../../../wp-load.php");

$transient = get_site_transient('update_themes');
echo('<pre>'); 
var_dump($transient);
echo('<pre>'); 

?>