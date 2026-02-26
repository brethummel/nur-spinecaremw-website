<?php

require_once("../../../../../../../wp-load.php");

$mypath = substr(dirname(__FILE__), strpos(dirname(__FILE__), '/wp-content'));

if (isset($_GET["action"])) {
	$method = 'get';
	$action = $_GET["action"];
} elseif (isset($_POST["action"])) {
	$method = 'post';
	$action = $_POST["action"];
} else {
	$method = 'hard';
	$action = "boop";
}

//echo('<pre>');
//echo($method . '<br/>');
//echo($action . '<br/>');
//echo('</pre>');

// $base = explode('/', get_site_url())[2];
//$referrer = explode('/', $_SERVER['HTTP_REFERER']);
//$local = false;
//if (in_array($base, $referrer)) {
//	if (in_array('pay-online', $referrer)) {
//		$local = true;
//	}
//}
//echo('<pre>');
//echo($local);
//echo('</pre>');

if ($action == 'geolocate') {

	if ($method == 'get') {
		$address = $_GET['address'];
	} elseif ($method == 'post') {
		$address = $_POST['address'];
	} else {
		$street = '3647 Colfax Ave N';
		$street2 = '';
		$city = 'Minneapolis';
		$state = 'MN';
		$address = urlencode($street . " " . $street2 . ", " . $city . ", " . $state);
	}

	$curl = curl_init();

	curl_setopt_array($curl, [
		CURLOPT_URL => "https://google-maps-geocoding.p.rapidapi.com/geocode/json?address=" . $address . "&language=en",
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_ENCODING => "",
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 30,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => "GET",
		CURLOPT_HTTPHEADER => [
			"X-RapidAPI-Host: google-maps-geocoding.p.rapidapi.com",
			"X-RapidAPI-Key: 50bfff6f31msh97176a03763ce97p1af44fjsnf1f0a00a6725"
		],
	]);

	$response = curl_exec($curl);
	$err = curl_error($curl);

	curl_close($curl);

	if ($err) {
		echo "cURL Error #:" . $err;
	} else {
		// if ($method == 'get') { echo('<pre>'); }
		echo $response;
		// if ($method == 'get') { echo('</pre>'); }
	}
	
}

if ($action == 'get_locations') {
	
	if ($method == 'get') {
		$map = $_GET['map'];
		$post_id = $_GET['post_id'];
	} elseif ($method == 'post') {
		$map = $_POST['map'];
		$post_id = $_POST['post_id'];
	}
	
	$datapath = get_site_url() . $mypath . '/mapplic/' . $map . '.json';
	$mapdata = json_decode(file_get_contents($datapath), true);
	
	$continents = array(
		"AF" => array(
			"name" => "Africa",
			"countries" => array("AO", "BF", "BI", "BJ", "BW", "CD", "CF", "CG", "CI", "CM", "CV", "DJ", "DZ", "EG", "EH", "ER", "ET", "GA", "GH", "GM", "GN", "GQ", "GW", "KE", "KM", "LR", "LS", "LY", "MA", "MG", "ML", "MR", "MU", "MW", "MZ", "NA", "NE", "NG", "RE", "RW", "SC", "SD", "SH", "SL", "SN", "SO", "SS", "ST", "SZ", "TD", "TG", "TN", "TZ", "UG", "YT", "ZA", "ZM", "ZW")),
		"AS" => array(
			"name" => "Asia",
			"countries" => array("AE", "AF", "AM", "AZ", "BD", "BH", "BN", "BT", "CC", "CN", "CX", "CY", "GE", "HK", "ID", "IL", "IN", "IO", "IQ", "IR", "JO", "JP", "KG", "KH", "KP", "KR", "KW", "KZ", "LA", "LB", "LK", "MM", "MN", "MO", "MV", "MY", "NP", "OM", "PH", "PK", "PS", "QA", "SA", "SG", "SY", "TH", "TJ", "TL", "TM", "TW", "UZ", "VN", "YE")),
		"EU" => array(
			"name" => "Europe",
			"countries" => array("AX", "AL", "AD", "AT", "BY", "BE", "BA", "BG", "HR", "CZ", "DK", "EE", "FO", "FI", "FR", "DE", "GI", "GR", "GG", "VA", "HU", "IS", "IE", "IM", "IT", "JE", "LV", "LI", "LT", "LU", "MK", "MT", "MD", "MC", "ME", "NL", "NO", "PL", "PT", "RO", "RU", "SM", "RS", "SK", "SI", "ES", "SJ", "SE", "CH", "TR", "UA", "GB")),
		"NA" => array(
			"name" => "North America",
			"countries" => array("AG", "AI", "AW", "BB", "BL", "BM", "BQ", "BS", "BZ", "CA", "CR", "CU", "CW", "DM", "DO", "GD", "GL", "GP", "GT", "HN", "HT", "JM", "KN", "KY", "LC", "MF", "MQ", "MS", "MX", "NI", "PA", "PM", "PR", "SV", "SX", "TC", "TT", "US", "VC", "VG", "VI"),
		"OC" => array(
			"name" => "Oceana",
			"countries" => array("AS", "AU", "CK", "FJ", "FM", "GU", "KI", "MH", "MP", "NC", "NF", "NR", "NU", "NZ", "PF", "PG", "PN", "PW", "SB", "TK", "TO", "TV", "UM", "VU", "WF", "WS")),
		"SA" => array(
			"name" => "South America",
			"countries" => array("AR", "BO", "BR", "CL", "CO", "EC", "FK", "GF", "GY", "PE", "PY", "SR", "UY", "VE"))
		)
	);
	$statecodes = array("AL", "AK", "AS", "AZ", "AR", "CA", "CO", "CT", "DE", "DC", "FL", "GA", "GU", "HI", "ID", "IL", "IN", "IA", "KS", "KY", "LA", "ME", "MD", "MA", "MI", "MN", "MS", "MO", "MT", "NE", "NV", "NH", "NJ", "NM", "NY", "NC", "ND", "MP", "OH", "OK", "OR", "PA", "PR", "RI", "SC", "SD", "TN", "TX", "UT", "VT", "VA", "VI", "WA", "WV", "WI", "WY");
	
	$blocks = get_fields($post_id);
	$customsort = array("NA", "EU", "AS");
	$locations = [];
	foreach ($continents as $c => $continent) {
		foreach ($continent['countries'] as $country) {
			if ($country == "US") {
				foreach ($statecodes as $state) {
					$locdata = [];
					$locdata['id'] = strtolower($country) . '-' . strtolower($state);
					$locdata['pin'] = "hidden no-fill";
					$locdata['category'] = $c;
					$locdata['action'] = 'disabled';
					$locdata['hide'] = 'true';
					$locdata['continent'] = $c;
					$locations[] = $locdata;
				}
			} else {
				$locdata = [];
				$locdata['id'] = strtolower($country);
				$locdata['pin'] = "hidden no-fill";
				$locdata['category'] = $c;
				$locdata['action'] = 'disabled';
				$locdata['hide'] = 'true';
				$locdata['continent'] = $c;
				$locations[] = $locdata;
			}
		}
	}
	foreach ($blocks['content_blocks'] as $b => $block) {
		if ($block['acf_fc_layout'] == 'block_map') {
			$locs = $block['map_locations'];
			foreach ($locs as $l => $location) {
				$locdata = [];
				if ($location['country']['country_code'] == 'US') {
					$locdata['id'] = strtolower($location['country']['country_code']) . '-' . strtolower($location['country']['state_code']) . '-' . $l;
				} else {
					$locdata['id'] = strtolower($location['country']['country_code']) . '-' . strtolower($location['name']) . '-' . $l;
				}
				$locdata['title'] = $location['name'];
				foreach ($continents as $c => $continent) {
					if (in_array($location['country']['country_code'], $continent['countries'])) {
						$locdata['category'] = $c;
					}
				}
				$pin = explode("|", $block['map_mapplic_settings']['pin_type']);
				$pins = '';
				foreach ($pin as $value) {
					$pins .= $value . ' ';
				}
				$locdata['pin'] = substr($pins, 0, -1);
				$locdata['about'] = $location['details']['address'];
				$locdata['description'] = $location['details']['address'];
				$locdata['lat'] = $location['geocoordinates']['lat'];
				$locdata['lng'] = $location['geocoordinates']['lng'];
				$locdata['continent'] = $location['country']['continent_code'];
				$locations[] = $locdata;
			}
		}
	}
	
	$locations_continents = [];
	$locations_ids = [];
	foreach ($locations as $k => $loc) {
		$locations_continents[] = $loc['continent'];
		$locations_ids[] = $loc['id'];
	}
	array_multisort($locations_continents, SORT_ASC, $locations_ids, SORT_ASC, $locations);
	
	if (isset($customsort)) {
		usort($locations, function ($a, $b) use ($customsort) {
			$pos_a = array_search($a['continent'], $customsort);
			$pos_b = array_search($b['continent'], $customsort);
			return $pos_a - $pos_b;
		});
		foreach ($customsort as $i => $currcont) {
			foreach ($locations as $p => $loc) {
				if (isset($loc['continent']) && $loc['continent'] == $currcont && !isset($loc['hide'])) {
					// echo('found my first location in '. $currcont . '!<br/>');
					$categoryhead = array(
						"id" => "head-" . strtolower($customsort[$i]),
						"title" => $continents[$customsort[$i]]['name'],
						"pin" => 'hidden no-fill',
						"category" => $currcont,
						"hide" => "false"
					);
					$locations = array_merge(array_slice($locations, 0, $p), array($categoryhead), array_slice($locations, $p));
					break;
				}
			}
		}
	}
	
	$mapdata['levels'][0]['locations'] = $locations;
	
	// if ($method == 'get') { echo('<pre>'); }
	echo(json_encode($mapdata));
	// if ($method == 'get') { echo('</pre>'); }
	
}

?>