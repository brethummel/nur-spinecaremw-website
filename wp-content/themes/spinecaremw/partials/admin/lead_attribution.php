<?php 

// ======================================= //
//        CAPTURE / RETRIEVE VALUES        //
// ======================================= // 

$use_page_values = false;
$produrl = $GLOBALS['produrl'];
$stageurl = $GLOBALS['stageurl'];

// which domain to use when setting cookies
if ($_SERVER['SERVER_NAME'] != $produrl && strpos($_SERVER['SERVER_NAME'], $produrl) == '') {
	$domain = '.' . $stageurl;
} else {
	$domain = '.' . $produrl;
}

$global_values = get_field('attribution', 'option');
$cookie_lifespan = $GLOBALS['cookie_lifespan'];
$slug = $post->post_name;

if (!isset($global_values)) { // get attribution model
	$model = 'last'; // fallback to "last" if options page has not been configured
} else {
	$model = $global_values['model'];
}

$delimiter = ' | ';

$attr_settings = ' data-usepagevals="' . $use_page_values . '"';
$attr_settings .= ' data-domain="' . $domain . '"';
$attr_settings .= ' data-lifespan="' . $cookie_lifespan . '"';
$attr_settings .= ' data-slug="' . $slug . '"';
$attr_settings .= ' data-model="' . $model . '"';
$attr_settings .= ' data-delimiter="' . $delimiter . '"';
$attr_settings .= ' data-siteurl="' . get_site_url() . '"';

// populate fall-back values to bake into page
if (!isset($global_values)) { // fallbacks if options page has not been configured
	$campaign = $produrl . ' (' . $slug . ')';
	$source = 'Website (direct traffic)';
	$medium = 'website';
	$attrsrc = 'global-slug';
} else {
	if ($global_values['campaign_setting'] == 'slug') {
		$campaign = $produrl . ' (' . $slug . ')';
		$attrsrc = 'global-slug';
	} elseif ($global_values['campaign_setting'] == 'value') {
		$campaign = $global_values['campaign'];
		$attrsrc = 'global-values';
	}
	$medium = $global_values['medium'];
	$source = $global_values['source'];
}



?>