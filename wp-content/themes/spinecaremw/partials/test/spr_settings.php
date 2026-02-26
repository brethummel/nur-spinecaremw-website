<?php

require_once("../../../../../wp-load.php");

echo('<pre>'); ?>

<a href="?a=reset">Reset spr_settings</a><br/>

<?php 

if(isset($_GET['a']) && $_GET['a'] == 'reset') {
	
	delete_option('spr_settings');
	echo('Reset spr_settings!<br/>'); ?>

	<script>
		var clean_uri = location.protocol + "//" + location.host + location.pathname;
		window.history.replaceState({}, document.title, clean_uri);
	</script>

<?php }

$spr_settings = get_option('spr_settings');
print_r($spr_settings); echo('<br/><br/>');

echo('<pre>'); 

?>