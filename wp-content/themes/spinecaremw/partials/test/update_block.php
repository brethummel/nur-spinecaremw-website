<?php

require_once("../../../../../wp-load.php");
global $wpdb;
$fields = array(
	'field_610c44813c7a8' => array( // v1.1.2.0
		'old' => array('name' => 'fullimage_image', 'key' => 'field_610c44813c7a8'),
		'new' => array('name' => 'fullimage_images_image', 'key' => 'field_623c8f08a9c0c'),
	),
);

echo('<pre>');
$oldkey = $fields['field_610c44813c7a8']['old']['key'];

// get fields
$blocks = $wpdb->get_results(
	$wpdb->prepare("SELECT * FROM $wpdb->postmeta WHERE `meta_value` LIKE %s", array("%$oldkey%"))
);

// prepare block data
$blocks_data = [];
foreach ($blocks as $block) {
	$blocks_data[] = array(
		'post_id' => $block->post_id,
		'old_key_key' => $block->meta_key,
	);
}

// assemble field data from old blocks
foreach ($blocks_data as $k => $block) {
	$old_value_key = substr($block['old_key_key'], 1);
	$value = $wpdb->get_results(
		$wpdb->prepare("SELECT `meta_value` FROM $wpdb->postmeta WHERE `meta_key` = %s AND `post_id` = %d", array($old_value_key, $block['post_id']))
	);
	$new_key_key = '_' . str_replace($fields['field_610c44813c7a8']['old']['name'], $fields['field_610c44813c7a8']['new']['name'], substr($block['old_key_key'], 1));
	$blocks_data[$k]['new_key_key'] = $new_key_key;
	$blocks_data[$k]['new_key'] = $fields['field_610c44813c7a8']['new']['key'];
	$new_value_key = substr($new_key_key, 1);
	$blocks_data[$k]['new_value_key'] = $new_value_key;
	$blocks_data[$k]['new_value'] = $value[0]->meta_value;
	// print_r($blocks_data[$k]);
}

// insert old field data into new fields
foreach ($blocks_data as $k => $block) {
	
	$post_id = $block['post_id'];
	$new_key_key = $block['new_key_key'];
	$new_key = $block['new_key'];
	$new_value_key = $block['new_value_key'];
	$new_value = $block['new_value'];
	
	// MAKE SURE NEW KEY EXISTS
	$check_key = $wpdb->get_results(
		$wpdb->prepare("SELECT * FROM $wpdb->postmeta WHERE `post_id` = %d AND `meta_key` = %s AND `meta_value` = %s", array($post_id, $new_key_key, $new_key))
	);
	
	if (!$check_key) { // if the new field does not exist yet, create new rows and add data
		echo('<br/>' . $new_key_key . ' NOT FOUND! Going to create two rows:<br/>');
		echo("$wpdb->postmeta, array('post_id' => " . $post_id . ", 'meta_key' => " . $new_key_key . ", 'meta_value' => " . $new_key . "<br/>");
		echo("$wpdb->postmeta, array('post_id' => " . $post_id . ", 'meta_key' => " . $new_value_key . ", 'meta_value' => " . $new_value . "<br/><br/>");
//		$wpdb->insert($wpdb->postmeta, array('post_id' => $post_id, 'meta_key' => $new_value_key, 'meta_value' => $new_value));
//		$wpdb->insert($wpdb->postmeta, array('post_id' => $post_id, 'meta_key' => $new_key_key, 'meta_value' => $new_key));
	} else { // dump old field data into new field rows
		$data = array('meta_value' => $new_value);
		$where = array('post_id' => $post_id, 'meta_key' => $new_value_key);
//		$wpdb->update($wpdb->postmeta, $data, $where);
//		echo('<br/>Updated value for ' . $new_key_key . '!<br/>');
	}
	
}

$spr_settings = get_option('spr_settings');
echo('[update_block.php]<br/>');
print_r($spr_settings); echo('<br/><br/>');
//delete_option('spr_settings');
//$spr_settings = get_option('spr_settings');
//print_r($spr_settings); echo('<br/><br/>');
//echo(date('Y-m-d H:i:s',time()));

echo('<pre>'); 

?>