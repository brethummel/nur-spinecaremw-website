<?php 

// ======================================= //
//        CAPTURE / RETRIEVE VALUES        //
// ======================================= // 

// get page values
if( have_rows('content_blocks') ):
	$use_page_values = false;
	while ( have_rows('content_blocks') ) : the_row();
		if( get_row_layout() == 'block_contactform' ):
			if (isset(get_sub_field('contactform_settings')['attribution'])) {
				$use_page_values = get_sub_field('contactform_settings')['attribution'];
				$page_values = get_sub_field('contactform_attribution');
			} else {
				$use_page_values = false;
			}
		endif;
	endwhile;
endif;

// which domain to use when setting cookies
if (!strpos($_SERVER['SERVER_NAME'], $GLOBALS['produrl'])) {
	$domain = '.' . $GLOBALS['stageurl'];
} else {
	$domain = '.' . $GLOBALS['produrl'];
}

$global_values = get_field('attribution', 'option');
$cookie_lifespan = $GLOBALS['cookie_lifespan'];
$slug = $post->post_name;

// if cookie exists, put the data into container, otherwise initialize container
if (isset($_COOKIE["spr_touchpoints"])) { 
	$touchpoints = $_COOKIE["spr_touchpoints"];
//    $cookiedata = explode(',', $_COOKIE["spr_touchpoints"]);
//    $source = $cookiedata[0];
//    $campaign = $cookiedata[1];
//	  $attrsrc = $cookiedata[2];
	// echo('<pre>');
	// echo('Cookie Values!<br/>');
	// print_r($cookiedata);
	// echo('</pre>');
} else {
	$touchpoints = '';
}

//echo('<pre>');
// 
if (isset($_GET['utm_source'])) {
	$source = $_GET['utm_source']; // current query string
	$campaign = $_GET['utm_campaign']; // current query string
	$attrsrc = 'utm_source-params';
} elseif (isset($_GET['source'])) {
	$source = $_GET['source']; // current query string
	$campaign = $_GET['campaign']; // current query string
	$attrsrc = 'source-params';
} elseif ($use_page_values && $page_values) {
	$source = $page_values['values']['source']; // page value
	$campaign = $page_values['values']['campaign']; // page value
	$attrsrc = 'page-values';
} else {
	if (!isset($global_values)) { // fallbacks if options page has not been configured
		$campaign = $GLOBALS['produrl'] . ' (' . $slug . ')';
		$source = 'Website (direct traffic)';
		$attrsrc = 'global-slug';
	} else {
		if ($global_values['campaign_setting'] == 'slug') {
			$campaign = $GLOBALS['produrl'] . ' (' . $slug . ')';
			$attrsrc = 'global-slug';
		} elseif ($global_values['campaign_setting'] == 'value') {
			$campaign = $global_values['campaign'];
			$attrsrc = 'global-values';
		}
		$source = $global_values['source'];
	}

//	if (get_post_type() == "post") { // blog post?
//		$source = "Website (direct traffic)"; // default
//		$campaign = "7012o000001Xk0V"; // default for blog posts
//	} else {
//		$source = "Website (direct traffic)"; // default
//		$campaign = "7012o000001XYnM"; // default fallback
//	}

}

// echo('cookie auditor!<br/>');
// echo($source . ',' . $campaign . '<br/>');
// echo(time() + $cookie_lifespan . '<br/>');
// echo($domain . '<br/>');

// add activity to cookie if page visit has campaign/source values
if (isset($_GET['attrsrc'])) {
	// only exists on the thankyou page which means we need to record the 
	// conversion as a touchpoint
	$touchpoints .= $_GET['source'] . ',' . $_GET['campaign'] . ',' . $_GET['attrsrc'] . '|';
	setcookie('spr_touchpoints', $touchpoints, time() + $cookie_lifespan, '/', $domain);	
} elseif ($attrsrc == 'utm_source-params' || $attrsrc == 'source-params') {
	// echo('setting cookie!<br/>');
	// echo($source . ',' . $campaign . '<br/>');
	// echo(time() + $cookie_lifespan . '<br/>');
	// echo($domain . '<br/>');
	$touchpoints .= $source . ',' . $campaign . ',' . $attrsrc . '|';
//	echo('setting spr_touchpoints with : ' . $touchpoints . '<br/>');
	setcookie('spr_touchpoints', $touchpoints, time() + $cookie_lifespan, '/', $domain);	
}

$attribution_fieldmap = $GLOBALS['attribution_fieldmap'];
$delimiter = $attribution_fieldmap['visits_field']['delimiter'];

// capture page visit
if (isset($_COOKIE["spr_pagevisits"])) { // prefer cookie values over all else
	$pagevisits = $_COOKIE["spr_pagevisits"];
} else {
	$pagevisits = '';
}
date_default_timezone_set(wp_timezone_string());
$timestamp = date('n/j/Y g:ia'); // 3/10/2022 5:16pm
$pagevisits .= $timestamp . ' : ' . $slug . $delimiter;
//echo('setting spr_pagevisits with : ' . $pagevisits . '<br/>');
setcookie('spr_pagevisits', $pagevisits, time() + $cookie_lifespan, '/', $domain);


// get attribution model
if (!isset($global_values)) {
	$model = 'last'; // fallback to "last" if options page has not been configured
} else {
	$model = $global_values['model'];
}
$touchpoints = explode('|', $touchpoints);
$throwaway = array_pop($touchpoints);
//print_r($touchpoints);
//echo('<br/>');
if ($model == 'first') {
	$attribution = explode(',', $touchpoints[0]);
//	echo('using first touchpoint!<br/>');
} elseif ($model == 'last') {
//	echo('using last touchpoint!<br/>');
	$attribution = array($source, $campaign, $attrsrc);
} elseif ($model == 'last-non-direct') {
//	echo('using last-non-direct touchpoint!<br/>');
	foreach (array_reverse($touchpoints) as $touchpoint) {
		$touchpoint = explode(',', $touchpoint);
		if ($touchpoint[count($touchpoint) - 1] == 'utm_source-params' || $touchpoint[count($touchpoint) - 1] == 'source-params') {
			$attribution = $touchpoint;
			break;
		}
	}
	if (!isset($attribution)) {
		$attribution = array($source, $campaign, $attrsrc);
	}
}
//echo('this is the touchpoint that will be used!<br/>');
//print_r($attribution);
$source = $attribution[0];
$campaign = $attribution[1];
$attrsrc = $attribution[2];


//echo('</pre>');



?>