<?php

error_reporting(E_ALL & ~E_WARNING & ~E_DEPRECATED & ~E_USER_DEPRECATED & ~E_NOTICE);

// ======================================= //
//              QUICK CONFIG               //
// ======================================= // 

// custom settings per implementation are stored in config.php
require('config.php');
// echo("<pre>Checking functions.php...</pre>");

$theme = wp_get_theme();
$GLOBALS['themename'] = get_stylesheet();
$GLOBALS['themeversion'] = $theme->get('Version');


// ======================================= //
//            INITIALIZE OPTIONS           //
// ======================================= // 

if (!get_option('spr_settings') || (isset($_GET['spr_settings']) && $_GET['spr_settings'] == 'reset')) {
	// echo("<pre>spr_settings has been initialized as blank...</pre>");
	$spr_settings = array(
		'block_updater' => array (
			
		)
	);
	add_option('spr_settings', $spr_settings);
} else {
	$spr_settings = get_option('spr_settings');
}


// ======================================= //
//        ADD THEME SCRIPTS + FONTS        //
// ======================================= // 

function spr_add_theme_scripts() {
	
    $themeurl = get_template_directory_uri();
	$themedir = get_template_directory();
	
    // compiled style.css
	spr_update_css($themeurl, $themedir, $GLOBALS['stageuser'], $GLOBALS['stagepass']);
    wp_enqueue_style('style', $themeurl . '/style.css');
    // wp_enqueue_style('style', $themeurl . '/sass/style.php?p=style.scss');
    
    // font styles
    wp_enqueue_style('bootstrap', 'https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css');
    spr_add_theme_fonts($themeurl); // adds fonts for specific implementation
    
    // bootstrap scripts
    wp_register_script('popper.js', 'https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js', array('jquery'), '1.16.0', true);
    wp_register_script('bootstrap.js', 'https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js', array('jquery'), '4.4.1', true);
    wp_register_script('respond.js', 'https://cdnjs.cloudflare.com/ajax/libs/respond.js/1.4.2/respond.min.js', array('jquery'), '1.4.2', true);
    
    // additional packages
    wp_register_script('slick.js', 'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.js', array('jquery'), '1.9.0', true);
    spr_add_js_packages($themeurl); // adds js packages for specific implementation
    
    // custom header / footer scripts
    wp_register_script('header.js', $themeurl . '/js/header.js', array('jquery'), '1.0.0', false);
    wp_register_script('footer.js', $themeurl . '/js/footer.js', array('jquery'), '1.0.0', false);
	
    // drop all custom js for the implementation in global.js
    wp_register_script('global.js', $themeurl . '/js/global.js', array('jquery'), '1.0.0', false);
    
    wp_enqueue_script('jquery');
    wp_enqueue_script('slick.js');
    wp_enqueue_script('popper.js');
    wp_enqueue_script('bootstrap.js');
    wp_enqueue_script('respond.js');
    wp_enqueue_script('header.js');
    wp_enqueue_script('footer.js');
    wp_enqueue_script('global.js');
    
    $data = array('path' => get_stylesheet_directory_uri());
    wp_localize_script('global.js', 'theme', $data);
    
}
add_action( 'wp_enqueue_scripts', 'spr_add_theme_scripts' );


// ======================================= //
//    COMPILE STYLE.CSS W/ SCSS UPDATES    //
// ======================================= // 

function spr_check_for_css_updates($themedir) {
	// echo("<pre>Checking for css updates...</pre>");
	$style_date = date(filemtime($themedir . '/style.css')); // get modified date of style.css
	$scss_files = spr_get_file_list($themedir, '/\.scss$/i');
	$updated = false;
	foreach($scss_files as $filename) {
		if (filemtime($filename) > $style_date && !strpos($filename, '_custom.scss') && !strpos($filename, '_blocks_admin.scss')) {
			$updated = true;
			break;
		}
	}
	// if (!$updated) {
	// 	echo("<pre>No updates detected!</pre>");
	// } else {
	// 	echo("<pre>Updates detected!</pre>");
	// }
	return $updated;
}

function spr_update_css($themeurl, $themedir, $stageuser, $stagepass, $force=false) {
	if (spr_check_for_css_updates($themedir) || filesize($themedir . '/style.css') < 500 || $force == true) {
		// echo("<pre>SCSS is being recompiled to style.css...</pre>");
		$style_css = file_get_contents($themedir . '/style.css');
		$admin_css = file_get_contents($themedir . '/admin.css');
		if (strpos($style_css, '/* compiled by scssphp')) {
			$style_css = substr($style_css, 0, strpos($style_css, '/* compiled by scssphp'));
		}
		if (strpos($admin_css, '/* compiled by scssphp')) {
			$admin_css = substr($admin_css, 0, strpos($admin_css, '/* compiled by scssphp'));
		}
		if ((strpos($_SERVER['SERVER_NAME'], $GLOBALS['stageurl']) || $_SERVER['SERVER_NAME'] == $GLOBALS['stageurl']) && strlen($stageuser) > 0) {
			$context = stream_context_create(array('http' => array('header' => 'Authorization: Basic ' . base64_encode("$stageuser:$stagepass"))));
			$compiled_css = file_get_contents($themeurl . '/sass/style.php?p=style.scss', false, $context);
			$compiled_admin_css = file_get_contents($themeurl . '/sass/style.php?p=admin.scss', false, $context);
		} else {
			$compiled_css = file_get_contents($themeurl . '/sass/style.php?p=style.scss');
			$compiled_admin_css = file_get_contents($themeurl . '/sass/style.php?p=admin.scss');
		}
		$file = fopen($themedir . '/style.css', "r+");
		fwrite($file, $style_css . $compiled_css);
		fclose($file);
		$file = fopen($themedir . '/admin.css', "r+");
		fwrite($file, $admin_css . $compiled_admin_css);
		fclose($file);
	}
}


// ======================================= //
//             THEME UPDATER               //
// ======================================= // 

function spr_check_for_update($transient) {
	$themename = $GLOBALS['themename'];
	if ($transient != null && !isset($transient->response[$themename])) {
		// echo("<pre>Theme Updater ran because no response was stored in the transient...</pre>");
		$server = 'https://updates.wp-springboard.com/api/latest.php';
		$curl = curl_init($server);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$json = curl_exec($curl);
		if ($e = curl_error($curl)) {
			echo($e);
			return false;
		} else {
			$response = json_decode($json, true);
			if (is_array($response)) {
				$springboard = array(
					'theme'       => $themename,
					'new_version' => $response['update']['new_version'],
					'url'         => $response['update']['url'],
					'package'     => $response['update']['package'],
				);
				$themenewversion = $response['update']['new_version'];
				if (isset($themenewversion) && $themenewversion != $GLOBALS['themeversion']) {
					$GLOBALS['themenewversion'] = $themenewversion;
					$transient->response[$themename] = $springboard;
				} else {
					$springboard['requires'] = '';
					$springboard['requires_php'] = '';
					$transient->no_update[$themename] = $springboard;
				}
				return $transient;
			} else {
				return false;
			}
			
		}
		curl_close($curl);
	}
}
add_filter('pre_set_site_transient_update_themes', 'spr_check_for_update', 10, 2);

function spr_check_package($reply, $package) {
	// echo("<pre>Theme Updater checked updates.wp-springboard.com/update/springboard...</pre>");
	if (strpos($package, 'updates.wp-springboard.com/update/springboard') !== false) {
		$GLOBALS['spr_update_flag'] = true;
		// exit('The package came from springboard! With: ' . $package . ' And: ' . $GLOBALS['spr_update_flag']);
	}
	return $reply;
}
add_filter('upgrader_pre_download', 'spr_check_package', 10, 2);

function spr_theme_backup($response) { // backup existing theme files
	if (isset($GLOBALS['spr_update_flag']) && $GLOBALS['spr_update_flag']) {
		$themename = $GLOBALS['themename'];
		$themedir = get_template_directory();
		$root = ABSPATH;
		$source = $themedir;
		$destination = $root . 'wp-content/upgrade/' . $themename . '-bu';
		spr_recursive_copy($source, $destination);
	}
	return $response;
}
add_filter('upgrader_pre_install', 'spr_theme_backup', 10, 2);

function spr_rename_source($source) { // rename expanded intall file to theme name
	if (isset($GLOBALS['spr_update_flag']) && $GLOBALS['spr_update_flag']) {
		$themeurl = get_template_directory_uri();
		$themename = $GLOBALS['themename'];
		$themedir = get_template_directory();
		$root = ABSPATH;
		rename($source, $root . 'wp-content/upgrade/' . $themename);
		$source = $root . 'wp-content/upgrade/' . $themename;
	}
	return $source;
}
add_filter( 'upgrader_source_selection', 'spr_rename_source', 10, 4 );

function spr_theme_recover($response) { // recover theme files that do not exist in update
	if (isset($GLOBALS['spr_update_flag']) && $GLOBALS['spr_update_flag']) {
		$themeurl = get_template_directory_uri();
		$themename = $GLOBALS['themename'];
		$themeversion = $GLOBALS['themeversion'];
		$themedir = get_template_directory();
		$root = ABSPATH;
		$source = $root . 'wp-content/upgrade/' . $themename . '-bu';
		$destination = $themedir;
		spr_recursive_copy($source, $destination);
		spr_update_css($themeurl, $themedir, $GLOBALS['stageuser'], $GLOBALS['stagepass'], true);
		spr_recursive_delete($source);
		spr_recursive_delete($root . 'wp-content/upgrade/' . $themename);
		$transient = get_site_transient('update_themes');
		$themenewversion = $transient->response[$themename]['new_version'];
		spr_update_theme_version($themeversion, $themenewversion);
	}
	return $response;
}
add_filter('upgrader_post_install', 'spr_theme_recover', 10, 2);

function spr_update_theme_version($themeversion, $themenewversion) {
	$themedir = get_template_directory();
	$style_css = file_get_contents($themedir . '/style.css');
	$new_css = str_replace($themeversion, $themenewversion, $style_css);
	$file = fopen($themedir . '/style.css', "r+");
	fwrite($file, $new_css);
	fclose($file);
	set_site_transient('update_themes', null);
	if (function_exists('wp_clean_update_cache')) {
		wp_clean_update_cache();
	}
	if (function_exists('rocket_clean_domain')) {
		rocket_clean_domain();
	}
	unset($GLOBALS['spr_update_flag']);
}


// ======================================= //
//             FILE MANAGEMENT             //
// ======================================= // 

function spr_get_file_list($dir, $filter=false) {
	$directory = new RecursiveDirectoryIterator($dir);
	$files = new RecursiveIteratorIterator($directory);
	if ($filter) {
		$files = new RegexIterator($files, $filter);
	}
	$inventory = [];
	foreach ($files as $file) {
		$inventory[] = $file->getPathname();
	}
    return $inventory;
}

function spr_recursive_copy($source, $destination) {
    if (!file_exists($destination)) {
        mkdir($destination);
    }
	$files = new RecursiveIteratorIterator( new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS), RecursiveIteratorIterator::SELF_FIRST);
    foreach ($files as $fullPath => $file) {
        $path = str_replace($source, "", $file->getPathname()); //get relative path of source file or folder
        if ($file->isDir() && !file_exists($destination . "/" . $path)) {
            mkdir($destination . "/" . $path);
        } elseif (!file_exists($destination . "/" . $path)) {
        	copy($fullPath, $destination . "/" . $path);
		}
    }
}

function spr_recursive_delete($directory) {
	$files = new RecursiveIteratorIterator( new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST);
    foreach ($files as $file) {
		$todo = ($file->isDir() ? 'rmdir' : 'unlink');
		$todo($file->getRealPath());
	}
	rmdir($directory);
}


// ======================================= //
//     SPRINGBOARD CONTENT BLOCK MGMT      //
// ======================================= // 

// Add content block fields
function spr_add_blocks_acf() {
	if(function_exists('acf_add_local_field_group')):
		require('partials/_blocks.acf');
	endif;
}
add_action('init', 'spr_add_blocks_acf', 9999999999);

// Add custom colors
if (function_exists('acf_add_local_field_group')):
    require('partials/custom/_custom_colors.php');
endif;


// ======================================= //
//        SPRINGBOARD BLOCK UPDATER        //
// ======================================= // 

function spr_register_block_changes($blockid, $acf_migration_map) {
	
	$spr_settings = get_option('spr_settings');
	if (isset($spr_settings['block_updater'][$blockid])) {
		// echo("<pre>" . $blockid . " exists in spr_settings...</pre>");
		foreach($acf_migration_map as $a => $update) {
			if (!isset($spr_settings['block_updater'][$blockid][$a])) {
				// echo("<pre>NEW UPDATE: " . $update . " in " . $blockid . "...</pre>");
				$spr_settings['block_updater'][$blockid][$a] = $update;
			} else {
				if (strtotime($a) !== false) {
					$update_fields = $spr_settings['block_updater'][$blockid][$a]['fields'];
					foreach ($update_fields as $f => $field) { // removes completed fields for the purpose of comparing to the needed update
						if (isset($update_fields[$f]['completed'])) { unset($update_fields[$f]['completed']); } 
						if (isset($update_fields[$f]['completed_date'])) { unset($update_fields[$f]['completed_date']); }
					}
					if ((isset($update['mode']) && $update['mode'] == 'reset') || $update['fields'] != $update_fields) { // reset if hard reset or updates don't match
						$spr_settings['block_updater'][$blockid][$a] = $update;
						// echo("<pre>reset update entirely...</pre>");
					}
				}
			}
		}
	} else {
		$spr_settings['block_updater'][$blockid] = $acf_migration_map;
	}
	update_option('spr_settings', $spr_settings);
	
}

function spr_update_blocks() {
	
	$themedir = get_template_directory();
	
	// $test = true; // bypasses database modifications
	$verbose = false; // logging
	
	global $wpdb;
	$spr_settings = get_option('spr_settings');
	
	if (isset($verbose) && $verbose) {																		   
		$file = $themedir . '/partials/test/update.log';
		if (file_exists($file)) {
			$log = file_get_contents($file);
		} else {
			$log = '';
		}
		$log .= '====================' . PHP_EOL . date('Y-m-d H:i:s',time()) . ': spr_update_blocks() called by init' . PHP_EOL;
	}
	
	if (isset($verbose) && $verbose) { $log .= 'loading spr_settings... '; }
	foreach ($spr_settings['block_updater'] as $k => $block) {
		$block_subfield_id = $block['subfield_id'];
		if (!isset($block_subfield_id)) { // means some of the data in spr_settings is using and old data structure
			echo("<p>Problem detected with " . $k . "'s acf_migration_map data.'</p>");
			// $spr_settings['block_updater'] = array();  // wipe out block_updater data
			// delete_option('spr_settings');
			// add_option('spr_settings', $spr_settings);
			// spr_add_blocks_acf();
			// return false;
		} else {
			$needsupdate = false;
			foreach($block as $d => $update) { // $d = date of changes
				if (strtotime($d) !== false) {
					foreach($update['fields'] as $f => $field) { // $f = old field key
						if (!isset($field['completed'])) {
							// echo("<pre>updating field " . $f . "...</pre>");
							$needsupdate = true;
							if (isset($verbose) && $verbose) { $log .= PHP_EOL . '-----' . PHP_EOL . $k . ' has updates'; }
							if (isset($verbose) && $verbose) { $log .= ' from ' . $d . ' that need to be made' . PHP_EOL; }
							if (isset($verbose) && $verbose) { $log .= '==> ' . $field['old']['name'] . ' (' . $field['old']['key'] . ') ==> ' . $field['new']['name'] . ' (' . $field['new']['key'] . ')' . PHP_EOL; }

							$old_field_group = 'false';
							if (isset($field['old']['group'])) {
								$old_field_group = $field['old']['group'];
							}
							$old_field_key = $field['old']['key'];
							$old_field_clone = 'false';
							if (isset($field['old']['clone'])) {
								$old_field_clone = $field['old']['clone'];
							}

	//						echo('<pre>');
	//						if ($k == 'block_related') {
	//							echo($k . '<br/>');
	//						}

							if ($old_field_group == 'false') {
								if ($old_field_clone == 'false') {
									$blocks = $wpdb->get_results(
										// SELECT DISTINCT `post_id`, `meta_key` FROM `wp_sjdjzp_postmeta` INNER JOIN `wp_sjdjzp_posts` ON `ID` = `post_id` WHERE `post_type` NOT IN ('revision') AND `meta_value` LIKE "%field_6137c3c787f00%field_6178670b4ade0%";
										// $wpdb->prepare("SELECT * FROM $wpdb->postmeta WHERE `meta_value` LIKE %s", array("%$block_subfield_id%$old_field_key%"))
										$wpdb->prepare("SELECT DISTINCT `post_id`, `meta_key` FROM $wpdb->postmeta INNER JOIN $wpdb->posts ON `ID` = `post_id` WHERE `post_type` NOT IN ('revision') AND `meta_value` LIKE %s", array("%$block_subfield_id%$old_field_key%"))
									);
								} else { // clone
									$blocks = $wpdb->get_results(
										//$wpdb->prepare("SELECT * FROM $wpdb->postmeta WHERE `meta_value` LIKE %s", array("%$block_subfield_id%$old_field_key%$old_field_clone%"))
										$wpdb->prepare("SELECT DISTINCT `post_id`, `meta_key` FROM $wpdb->postmeta INNER JOIN $wpdb->posts ON `ID` = `post_id` WHERE `post_type` NOT IN ('revision') AND `meta_value` LIKE %s", array("%$block_subfield_id%$old_field_key%$old_field_clone%"))
									);
								}
							} else { // in a group
								if ($old_field_clone == 'false') {
									$blocks = $wpdb->get_results(
										// $wpdb->prepare("SELECT * FROM $wpdb->postmeta WHERE `meta_value` LIKE %s", array("%$old_field_key%"))
										$wpdb->prepare("SELECT DISTINCT `post_id`, `meta_key` FROM $wpdb->postmeta INNER JOIN $wpdb->posts ON `ID` = `post_id` WHERE `post_type` NOT IN ('revision') AND `meta_value` LIKE %s", array("%$old_field_key%"))
									);
								} else { // clone
									$blocks = $wpdb->get_results(
										// $wpdb->prepare("SELECT * FROM $wpdb->postmeta WHERE `meta_value` LIKE %s", array("%$old_field_key%$old_field_clone%"))
										$wpdb->prepare("SELECT DISTINCT `post_id`, `meta_key` FROM $wpdb->postmeta INNER JOIN $wpdb->posts ON `ID` = `post_id` WHERE `post_type` NOT IN ('revision') AND `meta_value` LIKE %s", array("%$old_field_key%$old_field_clone%"))
									);
								}
							}

	//						if ($k == 'block_related') {
	//							print_r($blocks);
	//						}

							// prepare block data
							$blocks_data = [];
							foreach ($blocks as $blockd) {
								$blocks_data[] = array(
									'post_id' => $blockd->post_id,
									'old_field_key_meta_key' => $blockd->meta_key, // grabs meta_key of rows containing the old field key
								);
							}

							// assemble field data from old blocks
							foreach ($blocks_data as $bd => $blockd) {
								$old_field_value_meta_key = substr($blockd['old_field_key_meta_key'], 1); // strips leading underscore off of old key
								$value = $wpdb->get_results(
									$wpdb->prepare("SELECT `meta_value` FROM $wpdb->postmeta WHERE `meta_key` = %s AND `post_id` = %d", array($old_field_value_meta_key, $blockd['post_id']))
								);
								$new_field_key_meta_key = '_' . str_replace($field['old']['name'], $field['new']['name'], substr($blockd['old_field_key_meta_key'], 1));
								$blocks_data[$bd]['new_field_key_meta_key'] = $new_field_key_meta_key;
								$blocks_data[$bd]['new_field_group'] = $field['new']['group'];
								$blocks_data[$bd]['new_field_key'] = $field['new']['key'];
								$blocks_data[$bd]['new_field_clone'] = $field['new']['clone'];
								$new_field_value_meta_key = substr($new_field_key_meta_key, 1);
								$blocks_data[$bd]['new_field_value_meta_key'] = $new_field_value_meta_key;
								$blocks_data[$bd]['new_field_value'] = $value[0]->meta_value;
							}

	//						if ($k == 'block_related') {
	//							echo($k . '<br/>');
	//							print_r($blocks_data);
	//						}
	//						echo('</pre>');

							// insert old field data into new fields
							foreach ($blocks_data as $bd => $blockd) {

								$post_id = $blockd['post_id'];
								$new_field_key_meta_key = $blockd['new_field_key_meta_key'];
								$new_field_group = $blockd['new_field_group'];
								$new_field_key = $blockd['new_field_key'];
								$new_field_clone = $blockd['new_field_clone'];
								$new_field_value_meta_key = $blockd['new_field_value_meta_key'];
								$new_value = $blockd['new_field_value'];

								// MAKE SURE NEW KEY EXISTS
								$check_key = $wpdb->get_results(
									$wpdb->prepare("SELECT * FROM $wpdb->postmeta WHERE `post_id` = %d AND `meta_key` = %s AND `meta_value` = %s", array($post_id, $new_field_key_meta_key, $new_field_key))
								);

								if (!$check_key) { // if the new field does not exist yet, create new rows and add data
									if (isset($test) && $test) {
										// echo('[TEST MODE] : Added field rows and added value to ' . $new_field_key_meta_key . '!<br/>');
									} else {
										if ($new_field_group == 'false') {
											if ($new_field_clone == 'false') {
												$wpdb->insert($wpdb->postmeta, array('post_id' => $post_id, 'meta_key' => $new_field_key_meta_key, 'meta_value' => $block_subfield_id . '_' . $new_field_key));
												$wpdb->insert($wpdb->postmeta, array('post_id' => $post_id, 'meta_key' => $new_field_value_meta_key, 'meta_value' => $new_value));
												if (isset($verbose) && $verbose) { $log .=  '  - inserted: (post_id => ' . $post_id . ', meta_key => ' . $new_field_key_meta_key . ', meta_value => ' . $block_subfield_id . '_' . $new_field_key . ')' . PHP_EOL; }
												if (isset($verbose) && $verbose) { $log .=  '  - inserted: (post_id => ' . $post_id . ', meta_key => ' . $new_field_value_meta_key . ', meta_value => ' . $new_value . ')' . PHP_EOL; }
											} else { // clone
												$wpdb->insert($wpdb->postmeta, array('post_id' => $post_id, 'meta_key' => $new_field_key_meta_key, 'meta_value' => $block_subfield_id . '_' . $new_field_key . '_' . $new_field_clone));
												$wpdb->insert($wpdb->postmeta, array('post_id' => $post_id, 'meta_key' => $new_field_value_meta_key, 'meta_value' => $new_value));
												if (isset($verbose) && $verbose) { $log .=  '  - inserted: (post_id => ' . $post_id . ', meta_key => ' . $new_field_key_meta_key . ', meta_value => ' . $block_subfield_id . '_' . $new_field_key . '_' . $new_field_clone . ')' . PHP_EOL; }
												if (isset($verbose) && $verbose) { $log .=  '  - inserted: (post_id => ' . $post_id . ', meta_key => ' . $new_field_value_meta_key . ', meta_value => ' . $new_value . ')' . PHP_EOL; }
											}
										} else { // in a group
											if ($new_field_clone == 'false') {
												$wpdb->insert($wpdb->postmeta, array('post_id' => $post_id, 'meta_key' => $new_field_key_meta_key, 'meta_value' => $new_field_key));
												$wpdb->insert($wpdb->postmeta, array('post_id' => $post_id, 'meta_key' => $new_field_value_meta_key, 'meta_value' => $new_value));
												if (isset($verbose) && $verbose) { $log .=  '  - inserted: (post_id => ' . $post_id . ', meta_key => ' . $new_field_key_meta_key . ', meta_value => ' . $new_field_key . ')' . PHP_EOL; }
												if (isset($verbose) && $verbose) { $log .=  '  - inserted: (post_id => ' . $post_id . ', meta_key => ' . $new_field_value_meta_key . ', meta_value => ' . $new_value . ')' . PHP_EOL; }
											} else { // clone
												$wpdb->insert($wpdb->postmeta, array('post_id' => $post_id, 'meta_key' => $new_field_key_meta_key, 'meta_value' => $new_field_key . '_' . $new_field_clone));
												$wpdb->insert($wpdb->postmeta, array('post_id' => $post_id, 'meta_key' => $new_field_value_meta_key, 'meta_value' => $new_value));
												if (isset($verbose) && $verbose) { $log .=  '  - inserted: (post_id => ' . $post_id . ', meta_key => ' . $new_field_key_meta_key . ', meta_value => ' . $new_field_key . '_' . $new_field_clone . ')' . PHP_EOL; }
												if (isset($verbose) && $verbose) { $log .=  '  - inserted: (post_id => ' . $post_id . ', meta_key => ' . $new_field_value_meta_key . ', meta_value => ' . $new_value . ')' . PHP_EOL; }
											}
										}
									}
								} else { // dump old field data into new field rows
									$data = array('meta_value' => $new_value);
									$where = array('post_id' => $post_id, 'meta_key' => $new_field_value_meta_key);
									if (isset($test) && $test) {
										// echo('[TEST MODE] : Updated value for ' . $new_field_key_meta_key . '!<br/>');
									} else {
										$wpdb->update($wpdb->postmeta, $data, $where);
										if (isset($verbose) && $verbose) { $log .=  '  - copied: (post_id => ' . $post_id . ', meta_key => ' . $new_field_value_meta_key . ', meta_value => ' . $new_value . ')' . PHP_EOL; }
									}
								}
							}
							if (isset($verbose) && $verbose) { $log .= '-----' . PHP_EOL . count($blocks) . ' blocks updated: ' . date('Y-m-d H:i:s',time()) . PHP_EOL; }
							$block[$d]['fields'][$f]['completed'] = count($blocks) . ' blocks updated';
							$block[$d]['fields'][$f]['completed_date'] = date('Y-m-d H:i:s',time());
							$spr_settings['block_updater'][$k] = $block;
							if (isset($verbose) && $verbose) { $log .= 'updating spr_settings with results...'; }
							update_option('spr_settings', $spr_settings);
						} else {
							// if (isset($verbose) && $verbose) { $log .= 'were previously completed' . PHP_EOL; }
						}
					}
				}
			}
		}
	}
	if (!$needsupdate) {
		if (isset($verbose) && $verbose) { $log .= 'no changes necessary!'; }
	}
	if (isset($verbose) && $verbose) { 
		$log .= PHP_EOL . '====================' . PHP_EOL . 'DONE!' . PHP_EOL; 			
		file_put_contents($file, $log);
	}
										
	
}
add_action('init', 'spr_update_blocks', 999999999999);


// ======================================= //
//           USER/UI MANAGEMENT            //
// ======================================= // 


// admin css stylesheet
function spr_load_admin_style() {
	$themeurl = get_template_directory_uri();
    wp_enqueue_style('style', $themeurl . '/admin.css');
	wp_register_script('admin.js', $themeurl . '/js/admin.js', array('jquery'), '1.0.0', false);
	wp_enqueue_script('admin.js');
}
add_action( 'admin_enqueue_scripts', 'spr_load_admin_style' );

// enable menus
if ($usemenus && function_exists('add_theme_support')) {
    add_theme_support('menus');
}

// add formats dropdown to WYSIWYG editor
if ($mcestyles) {
	function spr_mce_buttons($buttons) {
		array_splice($buttons, 1, 0, 'styleselect');
		return $buttons;
	}
	add_filter('mce_buttons', 'spr_mce_buttons');
	
	function spr_tiny_mce_before_init($settings) {  
		print_r($GLOBALS['styleformats']);
		// echo('Running tiny_mce_before_init');
		$settings['style_formats'] = json_encode($GLOBALS['styleformats']);
		return $settings;
	}
	add_filter('tiny_mce_before_init', 'spr_tiny_mce_before_init'); 
	
}

if (!isset($pagetaxonomy)) {
	$pagetaxonomy = false; // backwards compatibility 
}

if (array_key_exists('page', $GLOBALS['posttypes'])) {
	if (in_array('excerpts', $GLOBALS['posttypes']['page'])) {
		$pagetaxonomy = true;
	}
} else { // backwards compatability
	if (!isset($pagetaxonomy)) {
		$pagetaxonomy = false;
	}
}

if ($pagetaxonomy) { // If we want categories and tags on pages
	
	function spr_add_categories_to_pages() {
		register_taxonomy_for_object_type( 'category', 'page' );
	}
	add_action( 'init', 'spr_add_categories_to_pages' );
	
	function spr_add_tags_to_pages() {
		register_taxonomy_for_object_type( 'post_tag', 'page' );
	}
	add_action( 'init', 'spr_add_tags_to_pages');

}

if (!isset($useexcerpts)) {
	$useexcerpts = false; // backwards compatibility 
}

if (array_key_exists('page', $GLOBALS['posttypes'])) { // new method
	$excerptlocations = [];
	foreach ($GLOBALS['posttypes'] as $t => $type) {
		if (in_array('excerpts', $type)) { // backwards compatability
			$useexcerpts = true;
			$excerptlocations[] = $t;
		} elseif (in_array('post_title', $type)) {
			$useexcerpts = true;
			$excerptlocations[] = $t;
		} else {
			foreach ($type as $term) {
				// echo('<pre>'); echo('seeing if ' . $term . ' is an array...'); echo('</pre>');
				if (gettype($term) == 'array') {
					if (in_array('image', $term) || in_array('title', $term) || in_array('excerpt', $term)) {
						$useexcerpts = true;
						$excerptlocations[] = $t;
					}
				}
			}
		}
	}
} else { // backwards compatability
	$excerptlocations = $GLOBALS['posttypes'];
	if ($useexcerpts || $useposts || $usearticles) {
		$useexcerpts = true;
	}
}

// echo('<pre>'); print_r($excerptlocations); echo('</pre>');
$GLOBALS['excerptlocations'] = $excerptlocations;


if ($useexcerpts) { // if we're using excerpts
		
	// set order of sidebar meta boxes if using excerpts
	function spr_custom_order_sidebars($sidebar) {

		global $post;
		global $wp_meta_boxes;

		$posttype = $post->post_type;
		// echo('<pre>'); echo($posttype); echo('</pre>');

		if (in_array($posttype, $GLOBALS['excerptlocations'])) {

			// echo('<pre>'); echo("I think I'm in excerptlocations!"); echo('</pre>');
			$sidebar = $wp_meta_boxes[$posttype]['side']['core'];

			$submitdiv = $sidebar['submitdiv'];
			unset($sidebar['submitdiv']);
			$sortedmetas = array($submitdiv);
			
			if (isset($sidebar['acf-group_5ea063c4b5bda'])) {
				$excerpt = $sidebar['acf-group_5ea063c4b5bda'];
				unset($sidebar['acf-group_5ea063c4b5bda']);
				$sortedmetas[] = $excerpt;
			}

			foreach($sidebar as $meta) {
				array_push($sortedmetas, $meta);
			}

			$wp_meta_boxes[$posttype]['side']['core'] = $sortedmetas;

		}

	//	echo "<pre>";
	//	var_dump($wp_meta_boxes);
	//	echo "</pre>";	

	}
	add_action('acf/add_meta_boxes', 'spr_custom_order_sidebars');	
	
}


// make sure categories and tags metaboxes aren't hidden
function spr_set_hidden_meta_boxes( $hidden, $screen ) {
	if ($hidden != false) {
		if(array_search('tagsdiv-post_tag', $hidden) != false) {
			unset($hidden[array_search('tagsdiv-post_tag', $hidden)]);
		}
		if(array_search('categorydiv', $hidden) != false) {
			unset($hidden[array_search('categorydiv', $hidden)]);
		}
	}
	return $hidden;
}
add_filter( 'get_user_option_metaboxhidden_page', 'spr_set_hidden_meta_boxes', 10, 2 );
add_filter( 'get_user_option_metaboxhidden_post', 'spr_set_hidden_meta_boxes', 10, 2 );

if (!isset($usearticles)) {
	$usearticles = false; // backwards compatibility 
}
if (array_key_exists('article', $GLOBALS['posttypes'])) {
	$usearticles = true;
}
if ($usearticles) { // If the site uses articles
	add_filter( 'get_user_option_metaboxhidden_article', 'spr_set_hidden_meta_boxes', 10, 2 );
}

// close some meta boxes by default
function spr_closed_meta_boxes($closed) {
    if ( false === $closed ) {
        $closed = array( 'categorydiv', 'tagsdiv-post_tag', 'rocket_post_exclude', 'wpseo_meta', 'revisionsdiv' );
	}
    return $closed;
}
add_filter( 'get_user_option_closedpostboxes_page', 'spr_closed_meta_boxes', 10, 2 );
add_filter( 'get_user_option_closedpostboxes_post', 'spr_closed_meta_boxes', 10, 2 );

if ($usearticles) { // If the site uses articles
	add_filter( 'get_user_option_closedpostboxes_article', 'spr_closed_meta_boxes', 10, 2 );
}

function spr_mod_content_blocks_title($field) {
	$title = $field['label'];
	$field['label'] = '<p>' . $field['label'] . '<span class="collapse-all">Collapse All</span></p>';
	return $field;
}
add_filter('acf/load_field/name=content_blocks', 'spr_mod_content_blocks_title');

// remove author meta from shared  links
add_filter( 'wpseo_meta_author', '__return_false' );
add_filter('wpseo_enhanced_slack_data', '__return_false' );

// add icons to Springboard blocks
function spr_block_titles($title, $field, $layout, $i) {
	
	$blocktype = $layout['name'];

	if (is_array(get_sub_field(str_replace('block_', '', $blocktype) . '_display'))) {
		$blockname = get_sub_field(str_replace('block_', '', $blocktype) . '_display')['block_name'];
	}

	if (!isset($blockname) || $blockname == '') {
		$title = '<span class="icon ' . $blocktype . '">' . $layout['label'] . '</span>';
	} else {
		$title = '<span class="icon ' . $blocktype . '">' . $blockname . ' </span><span class="block-type">(' . $layout['label'] . ')</span>';
	}
	
    return $title;
}
add_filter('acf/fields/flexible_content/layout_title/name=content_blocks', 'spr_block_titles', 10, 4);


// add "add button" button to tinymce
function spr_addbutton_init() {
	//abort early if the user will never see tinymce
	if (!current_user_can('edit_posts') && !current_user_can('edit_pages') && get_user_option('rich_editing') == 'true') {
		return;
	}
	add_filter("mce_external_plugins", "spr_register_tinymce_plugin"); // add callback to register our tinymce plugin
	add_filter('mce_buttons', 'spr_add_tinymce_button'); // add a callback to add our button to the tinymce toolbar
}
add_action('init', 'spr_addbutton_init');

// this callback registers our plug-in
function spr_register_tinymce_plugin($plugin_array) {
    $plugin_array['spr_addbutton_button'] = get_stylesheet_directory_uri() . '/js/admin.js';
    return $plugin_array;
}

// this callback adds our button to the toolbar
function spr_add_tinymce_button($buttons) {
    $buttons[] = "spr_addbutton_button"; // add the button ID to the $button array
    return $buttons;
}

// redirect to pages after login
function spr_login_redirect($url) {
	global $current_user; 
	if (is_array($current_user->roles)) { // is there a user ?
		if (is_plugin_active('wp-nested-pages/nestedpages.php')) {
			$url = admin_url('admin.php?page=nestedpages');
		} else {
			$url = admin_url('edit.php?post_type=page');
		}
		return $url;
	}
}
add_filter('login_redirect', 'spr_login_redirect');   

// redirect dashboard clicks to pages list
function spr_dashboard_redirect(){
	if (is_plugin_active('wp-nested-pages/nestedpages.php')) {
		$url = admin_url('admin.php?page=nestedpages');
	} else {
		$url = admin_url('edit.php?post_type=page');
	}
    wp_redirect($url);
}
add_action('load-index.php','spr_dashboard_redirect');

require('partials/admin/api_copy_blocks.php'); // loads api file

// add "Copy Blocks" button to "content_blocks" field
function spr_copy_blocks_button($field) {
	$button = '</a><a class="acf-button button button-secondary" href="#" data-name="copy-layouts">Copy Blocks';
	$field['button_label'] = $field['button_label'] . $button;
	return $field;
}
add_filter('acf/prepare_field/key=field_610ac7050d4ff', 'spr_copy_blocks_button');

// fix no-value-message
function spr_no_value_message($no_value_message) {
	$no_value_message = 'Click the "Add Block" button below to start creating your layout';	
	return $no_value_message;
}
add_filter('acf/fields/flexible_content/no_value_message', 'spr_no_value_message');

// creates popup to choose the post the user wants to copy blocks from
function spr_copy_blocks_post_selector() {
	$screen = get_current_screen();
	$posttypes = $GLOBALS['posttypes'];
	$addcopyblocks = false;
	if (array_key_exists('page', $posttypes)) {
		if (array_key_exists($screen->post_type, $posttypes) && in_array('blocks', $posttypes[$screen->post_type])) {
			$addcopyblocks = true;
			$blockposttypes = [];
			foreach ($posttypes as $t => $type) {
				if (in_array('blocks', $type)) {
					$blockposttypes[] = $t;
				}
			}
		}
	} else { // backwards compatability
		if (in_array($screen->post_type, $posttypes)) {
			$addcopyblocks = true;
			$blockposttypes = $posttypes;
		}
	}
	if ($addcopyblocks) {
		$postoptions = [];
		$selectorHTML = '<div class="spr-select-source">';
		$selectorHTML .= '<div class="tinter"></div>';
		$selectorHTML .= '<div class="select-container post">';
		$selectorHTML .= '<div class="select-post">';
		$selectorHTML .= '<div class="postbox-header">';
		$selectorHTML .= '<h2>Select Source</h2>';
		$selectorHTML .= '<div class="select-type"><label>Post Type:</label><select data-filter="post_type">';
		$selectorHTML .= '<option value="">Select</option>';
		foreach ($blockposttypes as $posttype) {
			$selectorHTML .= '<option value="'. $posttype . '">' . ucfirst($posttype) . '</option>';
		}
		$selectorHTML .= '</select></div>'; // select-type
		$selectorHTML .= '</div>'; // postbox-header
		$selectorHTML .= '<div class="posts-search"><input type="text" placeholder="Search..." data-filter="s"></div>';
		$selectorHTML .= '<div class="posts-list">';
		$selectorHTML .= '<div class="unsaved-changes">';
		$selectorHTML .= '<h2>There are unsaved changes!</h2>';
		$selectorHTML .= '<p>Save your changes, then try again.</p>';
		$selectorHTML .= '<input type="submit" name="save" id="publish" class="button button-primary button-large" value="Update">';
		$selectorHTML .= '</div>'; // unsaved-changes
		$selectorHTML .= '<ul class="select-list">';
		foreach ($blockposttypes as $posttype) {
			$selectorHTML .= '<li class="post-type-label" data-posttype="' . $posttype . '">' . ucfirst($posttype) . '</li>';
			$postlist = spr_get_hierarchical_posts(0, $posttype, 0);
			$selectorHTML .= $postlist;
		}
		$selectorHTML .= '</ul>';
		$selectorHTML .= '</div>'; // posts-list
		$selectorHTML .= '<div class="posts-actions">';
		$selectorHTML .= '<a class="acf-button button button-secondary" href="#" data-name="posts-cancel">Cancel</a>';
		$selectorHTML .= '<a class="acf-button button button-primary disabled" href="#" data-name="posts-next">Set Source</a>';
		$selectorHTML .= '</div>'; // posts-actions
		$selectorHTML .= '</div>'; // select-post
		$selectorHTML .= '<div class="select-blocks">';
		$selectorHTML .= '<div class="postbox-header">';
		$selectorHTML .= '<h2>Select Blocks</h2>';
		$selectorHTML .= '</div>'; // postbox-header
		$themeurl = get_template_directory_uri();
		$themedir = get_template_directory();
		$selectorHTML .= '<div class="acf-field blocks-list" data-name="content_blocks" data-themeurl="' . $themeurl . '">';
		$selectorHTML .= '<div class="loading"><p><img src="' . $themeurl . '/images/spinner_admin.gif" alt="loading..."/></p><p class="message">Loading blocks...</p></div>';
		$selectorHTML .= '</div>'; // blocks-list
		$selectorHTML .= '<div class="blocks-actions">';
		$selectorHTML .= '<a class="back" href="#" data-name="blocks-back">< back</a>';
		$selectorHTML .= '<a class="acf-button button button-secondary" href="#" data-name="blocks-cancel">Cancel</a>';
		// $selectorHTML .= '<input type="submit" name="save" id="publish" class="button button-primary disabled" value="Copy Blocks">';
		$selectorHTML .= '<a class="acf-button button button-primary disabled" href="#" data-name="blocks-copy">Copy Blocks</a>';
		$selectorHTML .= '</div>'; // blocks-actions
		$selectorHTML .= '</div>'; // select-blocks
		$selectorHTML .= '</div>'; // select-container
		$selectorHTML .= '</div>'; // spr-select-source
		echo($selectorHTML);
	}
}
add_action('edit_form_after_editor', 'spr_copy_blocks_post_selector');

function spr_get_hierarchical_posts($post_id, $posttype, $currentlevel) {
	
	$postlist = '';
	
	$args = array(
        'post_type' => $posttype,
		'post_status' => array('publish', 'draft', 'future'),
		'post_parent' => $post_id,
        'numberposts' => -1,
        'order_by' => 'menu_order',
        'order' => 'ASC'
	);
	
    $children = get_posts($args);
    if (empty($children)) {
		return;
	}
	
    foreach ($children as $child) {
        $postlist .= '<li class="level-' . $currentlevel . '" data-postid="' . $child->ID . '" data-posttype="' . $posttype . '">' . $child->post_title . '</li>';
        $postlist .= spr_get_hierarchical_posts($child->ID, $posttype, $currentlevel+1); // call same function for child of this child
	}
	
	return $postlist;

}


// ======================================= //
//            ADD ACF FIELDSETS            //
// ======================================= // 

// load Content Blocks fieldset
require('partials/_content_blocks.php');

// Add Contact Info options page
if (function_exists('acf_add_options_page')){
	acf_add_options_page(array(
		'page_title' => 'Global Info',
		'icon_url' => 'dashicons-admin-site-alt3',
		'position' => 6
	));
}

// Contact Info page
if( function_exists('acf_add_local_field_group') ):

	// Site Banner
	if ($sitebanner) {
		acf_add_local_field_group(array(
			'key' => 'group_62561146520fe',
			'title' => 'Site Banner',
			'fields' => array(
				array(
					'key' => 'field_6256115ad9756',
					'label' => 'Settings',
					'name' => 'sitebanner_settings',
					'type' => 'group',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'layout' => 'table',
					'sub_fields' => array(
						array(
							'key' => 'field_62561175d9757',
							'label' => 'Display',
							'name' => 'display',
							'type' => 'true_false',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => array(
								'width' => '',
								'class' => '',
								'id' => '',
							),
							'message' => '',
							'default_value' => 0,
							'ui' => 1,
							'ui_on_text' => '',
							'ui_off_text' => '',
						),
						array(
							'key' => 'field_62561295d975b',
							'label' => 'Link Text',
							'name' => 'text',
							'type' => 'text',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => array(
								array(
									array(
										'field' => 'field_62561175d9757',
										'operator' => '==',
										'value' => '1',
									),
								),
							),
							'wrapper' => array(
								'width' => '',
								'class' => '',
								'id' => '',
							),
							'default_value' => 'Learn More',
							'placeholder' => '',
							'prepend' => '',
							'append' => '',
							'maxlength' => '',
						),
						array(
							'key' => 'field_62561226d975a',
							'label' => 'Type',
							'name' => 'type',
							'type' => 'select',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => array(
								array(
									array(
										'field' => 'field_62561175d9757',
										'operator' => '==',
										'value' => '1',
									),
									array(
										'field' => 'field_62561295d975b',
										'operator' => '!=empty',
									),
								),
							),
							'wrapper' => array(
								'width' => '',
								'class' => '',
								'id' => '',
							),
							'choices' => array(
								'internal' => 'Internal Page',
								'external' => 'External URL',
								'email' => 'Email Address',
							),
							'default_value' => false,
							'allow_null' => 0,
							'multiple' => 0,
							'ui' => 0,
							'return_format' => 'value',
							'ajax' => 0,
							'placeholder' => '',
						),
						array(
							'key' => 'field_62561315d975c',
							'label' => 'Page',
							'name' => 'page',
							'type' => 'page_link',
							'instructions' => '',
							'required' => 1,
							'conditional_logic' => array(
								array(
									array(
										'field' => 'field_62561226d975a',
										'operator' => '==',
										'value' => 'internal',
									),
								),
							),
							'wrapper' => array(
								'width' => '',
								'class' => '',
								'id' => '',
							),
							'post_type' => array(
								0 => 'post',
								1 => 'page',
							),
							'taxonomy' => '',
							'allow_null' => 0,
							'allow_archives' => 0,
							'multiple' => 0,
						),
						array(
							'key' => 'field_6256133bd975d',
							'label' => 'URL',
							'name' => 'url',
							'type' => 'url',
							'instructions' => '',
							'required' => 1,
							'conditional_logic' => array(
								array(
									array(
										'field' => 'field_62561226d975a',
										'operator' => '==',
										'value' => 'external',
									),
								),
							),
							'wrapper' => array(
								'width' => '',
								'class' => '',
								'id' => '',
							),
							'default_value' => '',
							'placeholder' => '',
						),
						array(
							'key' => 'field_6256134dd975e',
							'label' => 'Email',
							'name' => 'email',
							'type' => 'email',
							'instructions' => '',
							'required' => 1,
							'conditional_logic' => array(
								array(
									array(
										'field' => 'field_62561226d975a',
										'operator' => '==',
										'value' => 'email',
									),
								),
							),
							'wrapper' => array(
								'width' => '',
								'class' => '',
								'id' => '',
							),
							'default_value' => '',
							'placeholder' => '',
							'prepend' => '',
							'append' => '',
						),
						array(
							'key' => 'field_6256136bd975f',
							'label' => 'New Tab?',
							'name' => 'new_tab',
							'type' => 'true_false',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => array(
								array(
									array(
										'field' => 'field_62561226d975a',
										'operator' => '!=',
										'value' => 'email',
									),
									array(
										'field' => 'field_62561175d9757',
										'operator' => '==',
										'value' => '1',
									),
									array(
										'field' => 'field_62561295d975b',
										'operator' => '!=empty',
									),
								),
							),
							'wrapper' => array(
								'width' => '',
								'class' => '',
								'id' => '',
							),
							'message' => '',
							'default_value' => 0,
							'ui' => 1,
							'ui_on_text' => '',
							'ui_off_text' => '',
						),
					),
				),
				array(
					'key' => 'field_62561194d9758',
					'label' => 'Text',
					'name' => 'sitebanner_text',
					'type' => 'wysiwyg',
					'instructions' => '(75 characters or less)',
					'required' => 0,
					'conditional_logic' => array(
						array(
							array(
								'field' => 'field_62561175d9757',
								'operator' => '==',
								'value' => '1',
							),
						),
					),
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'default_value' => '',
					'tabs' => 'all',
					'toolbar' => 'basic',
					'media_upload' => 0,
					'delay' => 0,
				),
			),
			'location' => array(
				array(
					array(
						'param' => 'options_page',
						'operator' => '==',
						'value' => 'acf-options-global-info',
					),
				),
			),
			'menu_order' => 0,
			'position' => 'acf_after_title',
			'style' => 'default',
			'label_placement' => 'top',
			'instruction_placement' => 'label',
			'hide_on_screen' => '',
			'active' => true,
			'description' => '',
			'show_in_rest' => 0,
		));
	}

	$contactfieldset = array(
		'key' => 'group_5fc94ae7a614c',
		'title' => 'Contact Info',
		'fields' => array(
            array(
                'key' => 'field_5fc94af2eb73a',
                'label' => 'Company',
                'name' => 'company',
                'type' => 'text',
                'instructions' => '',
                'required' => 1,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => '',
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
                'maxlength' => '',
            ),
            array(
                'key' => 'field_5fc94c23eb741',
                'label' => 'Address',
                'name' => 'address',
                'type' => 'group',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'layout' => 'block',
                'sub_fields' => array(
                    array(
                        'key' => 'field_5fc94b11eb73b',
                        'label' => 'Address Line 1',
                        'name' => 'street_address',
                        'type' => 'text',
                        'instructions' => '',
                        'required' => 1,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => '',
                        'placeholder' => '',
                        'prepend' => '',
                        'append' => '',
                        'maxlength' => '',
                    ),
                    array(
                        'key' => 'field_5fc94b33eb73c',
                        'label' => 'Address Line 2',
                        'name' => 'street_address2',
                        'type' => 'text',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => '',
                        'placeholder' => '',
                        'prepend' => '',
                        'append' => '',
                        'maxlength' => '',
                    ),
                    array(
                        'key' => 'field_5fc94b4ceb73d',
                        'label' => 'City/State/Zip',
                        'name' => 'city',
                        'type' => 'group',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'layout' => 'table',
                        'sub_fields' => array(
                            array(
                                'key' => 'field_5fc94b95eb73e',
                                'label' => 'City',
                                'name' => 'city',
                                'type' => 'text',
                                'instructions' => '',
                                'required' => 1,
                                'conditional_logic' => 0,
                                'wrapper' => array(
                                    'width' => '',
                                    'class' => '',
                                    'id' => '',
                                ),
                                'default_value' => '',
                                'placeholder' => '',
                                'prepend' => '',
                                'append' => '',
                                'maxlength' => '',
                            ),
                            array(
                                'key' => 'field_5fc94ba5eb73f',
                                'label' => 'State',
                                'name' => 'state',
                                'type' => 'text',
                                'instructions' => '',
                                'required' => 1,
                                'conditional_logic' => 0,
                                'wrapper' => array(
                                    'width' => '',
                                    'class' => '',
                                    'id' => '',
                                ),
                                'default_value' => '',
                                'placeholder' => '',
                                'prepend' => '',
                                'append' => '',
                                'maxlength' => '',
                            ),
                            array(
                                'key' => 'field_5fc94babeb740',
                                'label' => 'Zip',
                                'name' => 'zip',
                                'type' => 'text',
                                'instructions' => '',
                                'required' => 1,
                                'conditional_logic' => 0,
                                'wrapper' => array(
                                    'width' => '',
                                    'class' => '',
                                    'id' => '',
                                ),
                                'default_value' => '',
                                'placeholder' => '',
                                'prepend' => '',
                                'append' => '',
                                'maxlength' => '',
                            ),
                        ),
                    ),
                ),
            ),
            array(
                'key' => 'field_5fc94c78eb743',
                'label' => 'Contact',
                'name' => 'contact',
                'type' => 'group',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'layout' => 'table',
                'sub_fields' => array(
                    array(
                        'key' => 'field_5fc94c9beb744',
                        'label' => 'Phone',
                        'name' => 'phone',
                        'type' => 'text',
                        'instructions' => '',
                        'required' => 1,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '30',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => '952.853.1400',
                        'placeholder' => '',
                        'prepend' => '',
                        'append' => '',
                        'maxlength' => '',
                    ),
					array(
						'key' => 'field_618c14244452d',
						'label' => 'Fax',
						'name' => 'fax',
						'type' => 'text',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '30',
							'class' => '',
							'id' => '',
						),
						'default_value' => '',
						'placeholder' => '',
						'prepend' => '',
						'append' => '',
						'maxlength' => '',
					),
                    array(
                        'key' => 'field_5fc94cc7eb745',
                        'label' => 'Email',
                        'name' => 'email',
                        'type' => 'email',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '70',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => '',
                        'placeholder' => '',
                        'prepend' => '',
                        'append' => '',
                    ),
                ),
            ),
        ),
		'location' => array(
			array(
				array(
					'param' => 'options_page',
					'operator' => '==',
					'value' => 'acf-options-global-info',
				),
			),
		),
		'menu_order' => 5,
		'position' => 'normal',
		'style' => 'default',
		'label_placement' => 'left',
		'instruction_placement' => 'label',
		'hide_on_screen' => '',
		'active' => true,
		'description' => '',
		'show_in_rest' => 0,
	);

	if ($multilocs) {
		$contactfieldset['fields'] = array(
			array(
				'key' => 'field_5fc94af2eb73a',
				'label' => 'Company',
				'name' => 'company',
				'type' => 'text',
				'instructions' => '',
				'required' => 1,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'default_value' => '',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'maxlength' => '',
			),
			array(
				'key' => 'field_618c116acb2cd',
				'label' => 'Locations',
				'name' => 'locations',
				'type' => 'repeater',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'collapsed' => 'field_618c135331bf3',
				'min' => 1,
				'max' => 0,
				'layout' => 'row',
				'button_label' => 'Add Location',
				'sub_fields' => array(
					array(
						'key' => 'field_618c135331bf3',
						'label' => 'Location Name',
						'name' => 'name',
						'type' => 'text',
						'instructions' => '',
						'required' => 1,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'default_value' => '',
						'placeholder' => '',
						'prepend' => '',
						'append' => '',
						'maxlength' => '',
					),
					array(
						'key' => 'field_5fc94c23eb741',
						'label' => 'Address',
						'name' => 'address',
						'type' => 'group',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'layout' => 'block',
						'sub_fields' => array(
							array(
								'key' => 'field_5fc94b11eb73b',
								'label' => 'Address Line 1',
								'name' => 'street_address',
								'type' => 'text',
								'instructions' => '',
								'required' => 1,
								'conditional_logic' => 0,
								'wrapper' => array(
									'width' => '',
									'class' => '',
									'id' => '',
								),
								'default_value' => '',
								'placeholder' => '',
								'prepend' => '',
								'append' => '',
								'maxlength' => '',
							),
							array(
								'key' => 'field_5fc94b33eb73c',
								'label' => 'Address Line 2',
								'name' => 'street_address2',
								'type' => 'text',
								'instructions' => '',
								'required' => 0,
								'conditional_logic' => 0,
								'wrapper' => array(
									'width' => '',
									'class' => '',
									'id' => '',
								),
								'default_value' => '',
								'placeholder' => '',
								'prepend' => '',
								'append' => '',
								'maxlength' => '',
							),
							array(
								'key' => 'field_5fc94b4ceb73d',
								'label' => 'City/State/Zip',
								'name' => 'city',
								'type' => 'group',
								'instructions' => '',
								'required' => 0,
								'conditional_logic' => 0,
								'wrapper' => array(
									'width' => '',
									'class' => '',
									'id' => '',
								),
								'layout' => 'table',
								'sub_fields' => array(
									array(
										'key' => 'field_5fc94b95eb73e',
										'label' => 'City',
										'name' => 'city',
										'type' => 'text',
										'instructions' => '',
										'required' => 1,
										'conditional_logic' => 0,
										'wrapper' => array(
											'width' => '',
											'class' => '',
											'id' => '',
										),
										'default_value' => '',
										'placeholder' => '',
										'prepend' => '',
										'append' => '',
										'maxlength' => '',
									),
									array(
										'key' => 'field_5fc94ba5eb73f',
										'label' => 'State',
										'name' => 'state',
										'type' => 'text',
										'instructions' => '',
										'required' => 1,
										'conditional_logic' => 0,
										'wrapper' => array(
											'width' => '',
											'class' => '',
											'id' => '',
										),
										'default_value' => '',
										'placeholder' => '',
										'prepend' => '',
										'append' => '',
										'maxlength' => '',
									),
									array(
										'key' => 'field_5fc94babeb740',
										'label' => 'Zip',
										'name' => 'zip',
										'type' => 'text',
										'instructions' => '',
										'required' => 1,
										'conditional_logic' => 0,
										'wrapper' => array(
											'width' => '',
											'class' => '',
											'id' => '',
										),
										'default_value' => '',
										'placeholder' => '',
										'prepend' => '',
										'append' => '',
										'maxlength' => '',
									),
								),
							),
						),
					),
					array(
						'key' => 'field_5fc94c78eb743',
						'label' => 'Contact',
						'name' => 'contact',
						'type' => 'group',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'layout' => 'table',
						'sub_fields' => array(
							array(
								'key' => 'field_5fc94c9beb744',
								'label' => 'Phone',
								'name' => 'phone',
								'type' => 'text',
								'instructions' => '',
								'required' => 1,
								'conditional_logic' => 0,
								'wrapper' => array(
									'width' => '30',
									'class' => '',
									'id' => '',
								),
								'default_value' => '',
								'placeholder' => '',
								'prepend' => '',
								'append' => '',
								'maxlength' => '',
							),
							array(
								'key' => 'field_618c14244452d',
								'label' => 'Fax',
								'name' => 'fax',
								'type' => 'text',
								'instructions' => '',
								'required' => 0,
								'conditional_logic' => 0,
								'wrapper' => array(
									'width' => '30',
									'class' => '',
									'id' => '',
								),
								'default_value' => '',
								'placeholder' => '',
								'prepend' => '',
								'append' => '',
								'maxlength' => '',
							),
							array(
								'key' => 'field_5fc94cc7eb745',
								'label' => 'Email',
								'name' => 'email',
								'type' => 'email',
								'instructions' => '',
								'required' => 0,
								'conditional_logic' => 0,
								'wrapper' => array(
									'width' => '40',
									'class' => '',
									'id' => '',
								),
								'default_value' => '',
								'placeholder' => '',
								'prepend' => '',
								'append' => '',
							),
						),
					),
				),
			),
		);
	}

	acf_add_local_field_group($contactfieldset);

    acf_add_local_field_group(array(
        'key' => 'group_5c2fec214fbcb',
        'title' => 'Social',
        'fields' => array(
            array(
                'key' => 'field_5c2fedddafd87',
                'label' => 'Social',
                'name' => 'social',
                'type' => 'group',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'layout' => 'row',
                'sub_fields' => array(
                    array(
                        'key' => 'field_5c2ff7e78c7a5',
                        'label' => 'Display',
                        'name' => 'social_display',
                        'type' => 'true_false',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'message' => '',
                        'default_value' => 1,
                        'ui' => 1,
                        'ui_on_text' => '',
                        'ui_off_text' => '',
                    ),
                    array(
                        'key' => 'field_5c2ff1b269210',
                        'label' => 'LinkedIn',
                        'name' => 'linkedin',
                        'type' => 'group',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => array(
                            array(
                                array(
                                    'field' => 'field_5c2ff7e78c7a5',
                                    'operator' => '==',
                                    'value' => '1',
                                ),
                            ),
                        ),
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'layout' => 'table',
                        'sub_fields' => array(
                            array(
                                'key' => 'field_5c2ff1f269211',
                                'label' => 'Include?',
                                'name' => 'linkedin_include',
                                'type' => 'true_false',
                                'instructions' => '',
                                'required' => 0,
                                'conditional_logic' => 0,
                                'wrapper' => array(
                                    'width' => '20',
                                    'class' => '',
                                    'id' => '',
                                ),
                                'message' => '',
                                'default_value' => 0,
                                'ui' => 1,
                                'ui_on_text' => '',
                                'ui_off_text' => '',
                            ),
                            array(
                                'key' => 'field_5c2ff22169212',
                                'label' => 'LinkedIn',
                                'name' => 'linkedin_url',
                                'type' => 'url',
                                'instructions' => '',
                                'required' => 1,
                                'conditional_logic' => array(
                                    array(
                                        array(
                                            'field' => 'field_5c2ff1f269211',
                                            'operator' => '==',
                                            'value' => '1',
                                        ),
                                    ),
                                ),
                                'wrapper' => array(
                                    'width' => '80',
                                    'class' => '',
                                    'id' => '',
                                ),
                                'default_value' => 'http://www.linkedin.com/',
                                'placeholder' => '',
                            ),
                        ),
                    ),
                    array(
                        'key' => 'field_5c2ff2811e8ab',
                        'label' => 'Facebook',
                        'name' => 'facebook',
                        'type' => 'group',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => array(
                            array(
                                array(
                                    'field' => 'field_5c2ff7e78c7a5',
                                    'operator' => '==',
                                    'value' => '1',
                                ),
                            ),
                        ),
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'layout' => 'table',
                        'sub_fields' => array(
                            array(
                                'key' => 'field_5c2ff2811e8ac',
                                'label' => 'Include?',
                                'name' => 'facebook_include',
                                'type' => 'true_false',
                                'instructions' => '',
                                'required' => 0,
                                'conditional_logic' => 0,
                                'wrapper' => array(
                                    'width' => '20',
                                    'class' => '',
                                    'id' => '',
                                ),
                                'message' => '',
                                'default_value' => 0,
                                'ui' => 1,
                                'ui_on_text' => '',
                                'ui_off_text' => '',
                            ),
                            array(
                                'key' => 'field_5c2ff2811e8ad',
                                'label' => 'Facebook',
                                'name' => 'facebook_url',
                                'type' => 'url',
                                'instructions' => '',
                                'required' => 1,
                                'conditional_logic' => array(
                                    array(
                                        array(
                                            'field' => 'field_5c2ff2811e8ac',
                                            'operator' => '==',
                                            'value' => '1',
                                        ),
                                    ),
                                ),
                                'wrapper' => array(
                                    'width' => '80',
                                    'class' => '',
                                    'id' => '',
                                ),
                                'default_value' => 'http://www.facebook.com/',
                                'placeholder' => '',
                            ),
                        ),
                    ),
                    array(
                        'key' => 'field_5c2ff3109409e',
                        'label' => 'Instagram',
                        'name' => 'instagram',
                        'type' => 'group',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => array(
                            array(
                                array(
                                    'field' => 'field_5c2ff7e78c7a5',
                                    'operator' => '==',
                                    'value' => '1',
                                ),
                            ),
                        ),
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'layout' => 'table',
                        'sub_fields' => array(
                            array(
                                'key' => 'field_5c2ff3109409f',
                                'label' => 'Include?',
                                'name' => 'instagram_include',
                                'type' => 'true_false',
                                'instructions' => '',
                                'required' => 0,
                                'conditional_logic' => 0,
                                'wrapper' => array(
                                    'width' => '20',
                                    'class' => '',
                                    'id' => '',
                                ),
                                'message' => '',
                                'default_value' => 0,
                                'ui' => 1,
                                'ui_on_text' => '',
                                'ui_off_text' => '',
                            ),
                            array(
                                'key' => 'field_5c2ff310940a0',
                                'label' => 'Instagram',
                                'name' => 'instagram_url',
                                'type' => 'url',
                                'instructions' => '',
                                'required' => 1,
                                'conditional_logic' => array(
                                    array(
                                        array(
                                            'field' => 'field_5c2ff3109409f',
                                            'operator' => '==',
                                            'value' => '1',
                                        ),
                                    ),
                                ),
                                'wrapper' => array(
                                    'width' => '80',
                                    'class' => '',
                                    'id' => '',
                                ),
                                'default_value' => 'http://www.instagram.com/',
                                'placeholder' => '',
                            ),
                        ),
                    ),
                    array(
                        'key' => 'field_5c2ff331940a1',
                        'label' => 'Twitter',
                        'name' => 'twitter',
                        'type' => 'group',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => array(
                            array(
                                array(
                                    'field' => 'field_5c2ff7e78c7a5',
                                    'operator' => '==',
                                    'value' => '1',
                                ),
                            ),
                        ),
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'layout' => 'table',
                        'sub_fields' => array(
                            array(
                                'key' => 'field_5c2ff332940a2',
                                'label' => 'Include?',
                                'name' => 'twitter_include',
                                'type' => 'true_false',
                                'instructions' => '',
                                'required' => 0,
                                'conditional_logic' => 0,
                                'wrapper' => array(
                                    'width' => '20',
                                    'class' => '',
                                    'id' => '',
                                ),
                                'message' => '',
                                'default_value' => 0,
                                'ui' => 1,
                                'ui_on_text' => '',
                                'ui_off_text' => '',
                            ),
                            array(
                                'key' => 'field_5c2ff332940a3',
                                'label' => 'Twitter',
                                'name' => 'twitter_url',
                                'type' => 'url',
                                'instructions' => '',
                                'required' => 1,
                                'conditional_logic' => array(
                                    array(
                                        array(
                                            'field' => 'field_5c2ff332940a2',
                                            'operator' => '==',
                                            'value' => '1',
                                        ),
                                    ),
                                ),
                                'wrapper' => array(
                                    'width' => '80',
                                    'class' => '',
                                    'id' => '',
                                ),
                                'default_value' => 'http://www.twitter.com/',
                                'placeholder' => '',
                            ),
                        ),
                    ),
                    array(
                        'key' => 'field_60662ce35d0a9',
                        'label' => 'YouTube',
                        'name' => 'youtube',
                        'type' => 'group',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => array(
                            array(
                                array(
                                    'field' => 'field_5c2ff7e78c7a5',
                                    'operator' => '==',
                                    'value' => '1',
                                ),
                            ),
                        ),
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'layout' => 'table',
                        'sub_fields' => array(
                            array(
                                'key' => 'field_60662ce35d0aa',
                                'label' => 'Include?',
                                'name' => 'youtube_include',
                                'type' => 'true_false',
                                'instructions' => '',
                                'required' => 0,
                                'conditional_logic' => 0,
                                'wrapper' => array(
                                    'width' => '20',
                                    'class' => '',
                                    'id' => '',
                                ),
                                'message' => '',
                                'default_value' => 0,
                                'ui' => 1,
                                'ui_on_text' => '',
                                'ui_off_text' => '',
                            ),
                            array(
                                'key' => 'field_60662ce35d0ab',
                                'label' => 'YouTube',
                                'name' => 'youtube_url',
                                'type' => 'url',
                                'instructions' => '',
                                'required' => 1,
                                'conditional_logic' => array(
                                    array(
                                        array(
                                            'field' => 'field_60662ce35d0aa',
                                            'operator' => '==',
                                            'value' => '1',
                                        ),
                                    ),
                                ),
                                'wrapper' => array(
                                    'width' => '80',
                                    'class' => '',
                                    'id' => '',
                                ),
                                'default_value' => 'http://www.youtube.com/',
                                'placeholder' => '',
                            ),
                        ),
                    ),
                ),
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'options_page',
                    'operator' => '==',
                    'value' => 'acf-options-global-info',
                ),
            ),
        ),
        'menu_order' => 20,
        'position' => 'acf_after_title',
        'style' => 'default',
        'label_placement' => 'left',
        'instruction_placement' => 'label',
        'hide_on_screen' => array(
            0 => 'permalink',
            1 => 'the_content',
            2 => 'excerpt',
            3 => 'discussion',
            4 => 'comments',
            5 => 'revisions',
            6 => 'slug',
            7 => 'author',
            8 => 'format',
            9 => 'page_attributes',
            10 => 'featured_image',
            11 => 'categories',
            12 => 'tags',
            13 => 'send-trackbacks',
        ),
        'active' => true,
        'description' => '',
    ));

endif;


// add people post type and create fieldset
if ($peopleposts['use'] == true) {
	
	add_action( 'init', function() use ($peopleposts) {

		/**
		 * Post Type: People Posts
		 */
		
		$labels = [
			"name" => __( $peopleposts['plural'], "custom-post-type-ui" ),
			"singular_name" => __( $peopleposts['singular'], "custom-post-type-ui" ),
		];

		$args = [
			"label" => __( $peopleposts['plural'], "custom-post-type-ui" ),
			"labels" => $labels,
			"description" => "",
			"public" => true,
			"publicly_queryable" => true,
			"show_ui" => true,
			"show_in_rest" => true,
			"rest_base" => "",
			"rest_controller_class" => "WP_REST_Posts_Controller",
			"has_archive" => false,
			"show_in_menu" => true,
			"show_in_nav_menus" => true,
			"delete_with_user" => false,
			"exclude_from_search" => false,
			"capability_type" => "post",
			"map_meta_cap" => true,
			"hierarchical" => false,
			"rewrite" => [ "slug" => $peopleposts['slug'], "with_front" => true ],
			"query_var" => true,
			"menu_icon" => $peopleposts['icon'],
			"supports" => [ "title", "editor", "thumbnail" ],
			"taxonomies" => [ "category", "post_tag" ],
			"show_in_graphql" => false,
		];

		register_post_type( $peopleposts['slug'], $args );
		
	});
	

	// first time a peoplepost is saved, set a flag to flush the rewrite rules
	add_action('save_post', function($post_id, $post_object) use ($peopleposts) {
		if (!$post_id || !$post_object) { 
			return false; 
		}
		if ($post_object->post_type != $peopleposts['slug']) { 
			return false; 
		} // only do this for the peoplepost post type
		if (!get_option('first-peoplepost-rewrite-flush')) {  // if option doesn't exist, set it to 1
			update_option('first-peoplepost-rewrite-flush', 1);
		}
		return true;
	}, 10, 2);
	
	// if the flag is set, flush the rewrite rules and set flag to 2 (done)
	function spr_first_peoplepost_flush() {
		if (!$option = get_option('first-peoplepost-rewrite-flush')) {  // option doesn't exist yet 
			return false; 
		}
		if ($option == 1) {
			flush_rewrite_rules( false );
			update_option('first-peoplepost-rewrite-flush', 2); // record flush as having happened
		}
		return true;
	}
	add_action('init', 'spr_first_peoplepost_flush', 9999999);
		

	// // add fieldset
	// if( function_exists('acf_add_local_field_group') ):

	// acf_add_local_field_group(array(
	// 	'key' => 'group_61c22b88f0f9a',
	// 	'title' => $peopleposts['singular'],
	// 	'fields' => array(
	// 		array(
	// 			'key' => 'field_61c22c5c3c0f9',
	// 			'label' => 'Image',
	// 			'name' => 'people_image',
	// 			'type' => 'image',
	// 			'instructions' => '',
	// 			'required' => 1,
	// 			'conditional_logic' => 0,
	// 			'wrapper' => array(
	// 				'width' => '',
	// 				'class' => '',
	// 				'id' => '',
	// 			),
	// 			'return_format' => 'array',
	// 			'preview_size' => 'thumbnail',
	// 			'library' => 'all',
	// 			'min_width' => '',
	// 			'min_height' => '',
	// 			'min_size' => '',
	// 			'max_width' => '',
	// 			'max_height' => '',
	// 			'max_size' => '',
	// 			'mime_types' => '',
	// 		),
	// 		array(
	// 			'key' => 'field_61c22b913c0f6',
	// 			'label' => 'Person',
	// 			'name' => 'people_person',
	// 			'type' => 'group',
	// 			'instructions' => '',
	// 			'required' => 0,
	// 			'conditional_logic' => 0,
	// 			'wrapper' => array(
	// 				'width' => '',
	// 				'class' => '',
	// 				'id' => '',
	// 			),
	// 			'layout' => 'table',
	// 			'sub_fields' => array(
	// 				array(
	// 					'key' => 'field_61c22ba63c0f7',
	// 					'label' => 'Full Name',
	// 					'name' => 'name',
	// 					'type' => 'text',
	// 					'instructions' => '',
	// 					'required' => 1,
	// 					'conditional_logic' => 0,
	// 					'wrapper' => array(
	// 						'width' => '',
	// 						'class' => '',
	// 						'id' => '',
	// 					),
	// 					'default_value' => '',
	// 					'placeholder' => '',
	// 					'prepend' => '',
	// 					'append' => '',
	// 					'maxlength' => '',
	// 				),
	// 				array(
	// 					'key' => 'field_61c22c4e3c0f8',
	// 					'label' => 'Title',
	// 					'name' => 'title',
	// 					'type' => 'text',
	// 					'instructions' => '',
	// 					'required' => 1,
	// 					'conditional_logic' => 0,
	// 					'wrapper' => array(
	// 						'width' => '',
	// 						'class' => '',
	// 						'id' => '',
	// 					),
	// 					'default_value' => '',
	// 					'placeholder' => '',
	// 					'prepend' => '',
	// 					'append' => '',
	// 					'maxlength' => '',
	// 				),
	// 			),
	// 		),
	// 		array(
	// 			'key' => 'field_61c22c7e3c0fa',
	// 			'label' => 'Bio',
	// 			'name' => 'people_bio',
	// 			'type' => 'wysiwyg',
	// 			'instructions' => '',
	// 			'required' => 1,
	// 			'conditional_logic' => 0,
	// 			'wrapper' => array(
	// 				'width' => '',
	// 				'class' => '',
	// 				'id' => '',
	// 			),
	// 			'default_value' => '',
	// 			'tabs' => 'all',
	// 			'toolbar' => 'full',
	// 			'media_upload' => 0,
	// 			'delay' => 0,
	// 		),
	// 	),
	// 	'location' => array(
	// 		array(
	// 			array(
	// 				'param' => 'post_type',
	// 				'operator' => '==',
	// 				'value' => $peopleposts['slug'],
	// 			),
	// 		),
	// 	),
	// 	'menu_order' => 0,
	// 	'position' => 'acf_after_title',
	// 	'style' => 'default',
	// 	'label_placement' => 'top',
	// 	'instruction_placement' => 'label',
	// 	'hide_on_screen' => array(
	// 		0 => 'the_content',
	// 		1 => 'excerpt',
	// 		2 => 'discussion',
	// 		3 => 'comments',
	// 		4 => 'author',
	// 		5 => 'format',
	// 		6 => 'featured_image',
	// 		7 => 'send-trackbacks',
	// 	),
	// 	'active' => true,
	// 	'description' => '',
	// 	'show_in_rest' => 0,
	// ));

	// endif;
	
}

// Add Article Post Type
if ($usearticles) {

	// Add Article Post Type
	function cptui_register_my_cpts_article() {
		$labels = [
			"name" => __( "Articles", "custom-post-type-ui" ),
			"singular_name" => __( "Article", "custom-post-type-ui" ),
		];
		$args = [
			"label" => __( "Articles", "custom-post-type-ui" ),
			"labels" => $labels,
			"description" => "",
			"public" => true,
			"publicly_queryable" => true,
			"show_ui" => true,
			"show_in_rest" => true,
			"rest_base" => "",
			"rest_controller_class" => "WP_REST_Posts_Controller",
			"has_archive" => false,
			"show_in_menu" => true,
			"show_in_nav_menus" => true,
			"delete_with_user" => false,
			"exclude_from_search" => false,
			"capability_type" => "post",
			"map_meta_cap" => true,
			"hierarchical" => false,
			"rewrite" => [ "slug" => "article", "with_front" => true ],
			"query_var" => true,
			"menu_icon" => "dashicons-index-card",
			"supports" => [ "title", "editor", "thumbnail", "revisions" ],
			"taxonomies" => [ "category", "post_tag" ],
			"show_in_graphql" => false,
		];
		register_post_type( "article", $args );
	}
	add_action('init', 'cptui_register_my_cpts_article', 10, 2);
	


	// first time an article is saved, set a flag to flush the rewrite rules
	function spr_first_article_save( Int $post_id = null, \WP_Post $post_object = null ) {
		if (!$post_id || !$post_object) { 
			return false; 
		}
		if ($post_object->post_type != 'article') { 
			return false; 
		} // only do this for articles
		if (!get_option('first-article-rewrite-flush')) {  // if option doesn't exist, set it to 1
			update_option('first-article-rewrite-flush', 1);
		}
		return true;
	}
	add_action('save_post', 'spr_first_article_save', 10, 2);

	// if the flag is set, flush the rewrite rules and set flag to 2 (done)
	function spr_first_article_flush() {
		if (!$option = get_option('first-article-rewrite-flush')) {  // option doesn't exist yet 
			return false; 
		}
		if ($option == 1) {
			flush_rewrite_rules( false );
			update_option('first-article-rewrite-flush', 2); // record flush as having happened
		}
		return true;
	}
	add_action('init', 'spr_first_article_flush', 9999999);

}

function spr_conditional_hide_field($field) {
	$curposttype = get_post_type();
	$fieldterm = strtolower($field['label']);
	foreach ($GLOBALS['posttypes'][$curposttype] as $term) {
		if (gettype($term) == 'array') {
			if (in_array($fieldterm, $term)) {
				return $field;
			} else {
				return false;
			}
		} else {

		}
	}
}

// Add excerpt fields
if ($useexcerpts) { // already set above
	
	add_action('acf/include_fields', function() {
	
		if (!function_exists('acf_add_local_field_group')) {
			return;
		}
	
		$required = 0;
		$locations =  [];
		
		if (array_key_exists('page', $GLOBALS['posttypes'])) { // new method
			
			// echo('<pre>'); echo('using new excerpts method!'); echo('</pre>');
			
			foreach ($GLOBALS['excerptlocations'] as $t => $posttype) {

				// echo('<pre>'); print_r($GLOBALS['posttypes'][$posttype]); echo('</pre>');
				$showexcerptsmeta = false;
				if (in_array('excerpts', $GLOBALS['posttypes'][$posttype])) { // backwards compatability
					$showexcerptsmeta = true;
				} else {
					foreach ($GLOBALS['posttypes'][$posttype] as $term) {
						if (gettype($term) == 'array') {
							if (in_array('image', $term) || in_array('title', $term) || in_array('excerpt', $term)) {
								$showexcerptsmeta = true;
								if (!in_array('image', $term)) {
									add_filter('acf/prepare_field/key=field_5ea063dcc667f', 'spr_conditional_hide_field');
								}
								if (!in_array('title', $term)) {
									add_filter('acf/prepare_field/key=field_5ea063fcc6680', 'spr_conditional_hide_field');
								}
								if (!in_array('excerpt', $term)) {
									// echo('<pre>'); echo($posttype . '<br/>'); print_r($term); echo('</pre>');
									add_filter('acf/prepare_field/key=field_5ea0640ec6681', 'spr_conditional_hide_field');
								}
							}
						}
					}
				}
				if ($showexcerptsmeta) {
					$required = 1;
					$locations[] = array(
						array(
							'param' => 'post_type',
							'operator' => '==',
							'value' => $posttype,
						)
					);
				}
			}
			// echo('<pre>'); print_r($locations); echo('</pre>');
			
		} else { // backwards compatability

			// echo('<pre>'); echo('using old excerpts method!'); echo('</pre>');			
			if (!$GLOBALS['pageexcerpts']) {
				$locations[0][] =  array(
					'param' => 'post_type',
					'operator' => '!=',
					'value' => 'page',
				);
			}
			foreach ($GLOBALS['posttypes'] as $posttype) {
				if ($posttype != 'page') {
					$required = 1;
					$locations[] = array(
						array(
							'param' => 'post_type',
							'operator' => '==',
								'value' => $posttype,
						)
					);
				}
			}
		}

		acf_add_local_field_group(array(
			'key' => 'group_5ea063c4b5bda',
			'title' => 'Excerpt',
			'fields' => array(
				array(
					'key' => 'field_5ea063dcc667f',
					'label' => 'Featured Image',
					'name' => 'excerpt_image',
					'type' => 'image',
					'instructions' => 'Image template: <a href="https://updates.wp-springboard.com/templates/block_textimage.psd.zip">Text + Image</a>',
					'required' => $required,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'return_format' => 'array',
					'preview_size' => 'thumbnail',
					'library' => 'all',
					'min_width' => '',
					'min_height' => '',
					'min_size' => '',
					'max_width' => '',
					'max_height' => '',
					'max_size' => '',
					'mime_types' => '',
				),
				array(
					'key' => 'field_5ea063fcc6680',
					'label' => 'Title',
					'name' => 'excerpt_title',
					'type' => 'text',
					'instructions' => '',
					'required' => $required,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'default_value' => '',
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
					'maxlength' => '',
				),
				array(
					'key' => 'field_5ea0640ec6681',
					'label' => 'Excerpt',
					'name' => 'excerpt_excerpt',
					'type' => 'textarea',
					'instructions' => '',
					'required' => $required,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'default_value' => '',
					'placeholder' => '',
					'maxlength' => 175,
					'rows' => 3,
					'new_lines' => '',
				),
			),
			'location' => $locations,
			'menu_order' => 0,
			'position' => 'side',
			'style' => 'default',
			'label_placement' => 'top',
			'instruction_placement' => 'label',
			'hide_on_screen' => array(
				0 => 'the_content',
				1 => 'excerpt',
				2 => 'discussion',
				3 => 'comments',
				4 => 'format',
				5 => 'featured_image',
				6 => 'send-trackbacks',
			),
			'active' => true,
			'description' => '',
			'show_in_rest' => 1,
		));
		
	}, 9999999);
	
}


if (class_exists('FrmForm')) {
	
	// Populate formidable forms options for block_contactform
	function spr_list_formidable_form_options($field) {

			$forms = FrmForm::get_published_forms();
			$forms = (array)$forms;

			$field['choices'] = [];
			$field['choices'][null] = "Select Form";

			foreach( $forms as $form ) {
				$form = (array)$form;
				$formID = $form['id'];
				$formKey = $form['form_key'];
				$formName = $form['name'];
				//echo("<p>" . $formID . ", " . $formKey . ", " . $formName . "</p>");
				if ($formID != 2) {
					$field['choices'][ $formID ] = $formName;
				}
			}

			return $field;
	}
	add_filter('acf/load_field/name=contactform_formidable', 'spr_list_formidable_form_options');
		
}


// ======================================= //
//              ACF UTILITES               //
// ======================================= // 

add_filter('acf-autosize/wysiwyg/min-height', function() {
	return 100;
});

function spr_format_field($field, $post_id) { 
	$field = wptexturize($field);
	return $field;
}
add_filter( "acf/load_value/type=text", 'spr_format_field', 10, 2 );



// ======================================= //
//             OEMBED PREVIEW              //
// ======================================= // 

function spr_disable_author_in_preview($data) {
	// echo('<pre><p>boop 1!</p>'); print_r($data); echo('</pre>');
    unset($data['author_url']);
	$data['author_name'] = "Gordon Lightfoot";
    // unset($data['author_name']);
	// echo('<pre><p>boop 2!</p>'); print_r($data); echo('</pre>');
    return $data;
}
add_filter('oembed_response_data', 'spr_disable_author_in_preview', 10, 4);
add_filter('wpseo_meta_author', '__return_false');


// ======================================= //
//       BACKWARDS PHP COMPATIBILITY       //
// ======================================= // 

if (!function_exists('array_key_first')) {
    function array_key_first(array $arr) {
        foreach($arr as $key => $unused) {
            return $key;
        }
        return NULL;
    }
}

?>