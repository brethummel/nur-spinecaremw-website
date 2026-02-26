<?php

    if(isset($_POST['sck'])){
        $sck = explode('|', base64_decode($_POST['sck']));
        $CLIENT_ID = $sck[0];
        $CLIENT_SECRET = $sck[1];
    }

    if(isset($_POST['action']) && !empty($_POST['action'])) {
        $action = $_POST['action'];

        switch($action) {
            case 'sc_get_auth' : sc_get_auth($CLIENT_ID, $CLIENT_SECRET);break;
            case 'sc_get_request' : sc_get_request();break;
            case 'sc_get_stream_url' : sc_get_stream_url();break;
            case 'sc_get_dl_url' : sc_get_dl_url();break;
            default: break;
        }
    }
    
    function sc_get_auth($CLIENT_ID, $CLIENT_SECRET){

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, 'https://api.soundcloud.com/oauth2/token');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, "grant_type=client_credentials&client_id=".$CLIENT_ID."&client_secret=".$CLIENT_SECRET);

        $headers = array();
        $headers[] = 'Accept: application/json; charset=utf-8';
        $headers[] = 'Content-Type: application/x-www-form-urlencoded';
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
          echo "cURL Error #:" . $err;
        } else {
            echo json_encode($response);
        }

        exit;

    }

    function sc_get_stream_url(){

        $track_id = $_POST['track_id'];
        $access_token = $_POST['access_token'];

        $url = "https://api.soundcloud.com/tracks/".$track_id."/stream";

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $headers = array(
            "accept: application/json; charset=utf-8",
            "Authorization: OAuth ".$access_token,
        );

        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
          echo "cURL Error #:" . $err;
        } else {
            echo json_encode($response);
        }

        exit;
    }

    function sc_get_dl_url(){

        $track_id = $_POST['track_id'];
        $access_token = $_POST['access_token'];

        $url = "https://api.soundcloud.com/tracks/".$track_id."/download";

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $headers = array(
            "accept: application/json; charset=utf-8",
            "Authorization: OAuth ".$access_token,
        );

        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
          echo "cURL Error #:" . $err;
        } else {
            echo json_encode($response);
        }

        exit;
    }

    function sc_get_refresh_token(){

        $refresh_token = '';

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, 'https://api.soundcloud.com/oauth2/token');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, "grant_type=refresh_token&client_id=".$CLIENT_ID."&client_secret=".$CLIENT_SECRET."&refresh_token=".$refresh_token);

        $headers = array();
        $headers[] = 'Accept: application/json; charset=utf-8';
        $headers[] = 'Content-Type: application/x-www-form-urlencoded';
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
          echo "cURL Error #:" . $err;
        } else {
            echo json_encode($response);
        }

        exit;

    }

    function sc_get_request(){

        $access_token = $_POST['access_token'];
        $url = $_POST['url'];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

        $headers = array();
        $headers[] = 'Accept: application/json; charset=utf-8';
        $headers[] = 'Authorization: OAuth '.$access_token;
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }else{
            echo json_encode($response);
        }

        curl_close($ch);

    }

?>
