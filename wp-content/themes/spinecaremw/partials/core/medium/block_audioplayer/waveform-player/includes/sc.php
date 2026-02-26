<?php

    if(isset($_REQUEST['sck'])){
        $sck = explode('|', base64_decode($_REQUEST['sck']));
        $CLIENT_ID = $sck[0];
        $CLIENT_SECRET = $sck[1];
    }

	require("sc/vendor/autoload.php");
	require("sc/src/Soundcloud.php");

    $url = $_REQUEST['url'];

    $soundcloud = new Danae\Soundcloud\Soundcloud([
      'client_id' => $CLIENT_ID,
      'client_secret' => $CLIENT_SECRET
    ]);

    $soundcloud->authorizeWithClientCredentials();

    $request = $soundcloud->resolve($url);

    echo json_encode($request);

    exit;

?>
