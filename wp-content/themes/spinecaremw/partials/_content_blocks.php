<?php

// uncomment if testing the file directly
// $rootdir = substr(dirname(__FILE__), 0, strpos(dirname(__FILE__), 'wp-content'));
// require_once($rootdir . '/wp-load.php');

$themedir = get_template_directory();

// inventory core blocks
$core = new RecursiveDirectoryIterator($themedir . '/partials/core/');
$blocks = [];
foreach(new RecursiveIteratorIterator($core) as $file) {
    if ($file->isDir()) {
		$path = $file->getPathname();
		if (strpos($path, 'block_') != false) {
			$catstart = strpos($path, 'core') + 5;
			$catend = strpos($path, '/', intval($catstart)) - $catstart;
			$blockstart = strpos($path, 'block_');
			$blockend = strpos($path, '/', intval($blockstart)) - $blockstart;
			$cat = substr($path, $catstart, $catend);
			$block = substr($path, $blockstart, $blockend);
			// echo('cat: ' . $cat . ', block: ' . $block . '<br/>');
			$found = false;
			foreach ($blocks as $id) {
				if ($id['id'] == $block) { $found = true; }
			}
			if (!$found) {
				$blocks[] = array(
					'id' => $block, 
					'cat' => $cat
				);
			}
		}
    }
}

// inventory custom blocks
$custom = new RecursiveDirectoryIterator($themedir . '/partials/custom/');
foreach(new RecursiveIteratorIterator($custom) as $file) {
    if ($file->isDir()) {
		$path = $file->getPathname();
		if (strpos($path, 'block_') != false) {
			$blockstart = strpos($path, 'block_');
			$blockend = strpos($path, '/', intval($blockstart)) - $blockstart;
			$cat = 'custom';
			$block = substr($path, $blockstart, $blockend);
			// echo('cat: ' . $cat . ', block: ' . $block . '<br/>');
			$found = false;
			$index = '';
			foreach ($blocks as $i => $id) {
				if ($id['id'] == $block) { $found = true; $index = $i; }
			}
			if (!$found) {
				$blocks[] = array(
					'id' => $block, 
					'cat' => $cat
				);
			} else {
				$blocks[$index] = array(
					'id' => $block, 
					'cat' => $cat
				);
			}
		}
    }
}


foreach ($blocks as $i => $block) {
	$blockid = $block['id']; // this is the folder name
	if ($block['cat'] != 'custom') {
		$blockpath = $themedir . '/partials/core/' . $block['cat'] . '/' . $blockid; // this is the path to the block's folder
	} else {
		$blockpath = $themedir . '/partials/custom/' . $blockid; // this is the path to the block's folder
	}
	$blockacf = file_get_contents($blockpath . '/' . $blockid . '.acf');
	$blockacf = preg_replace('/\s+/', ' ', $blockacf);
	$label = get_val('Name: ', $blockacf, ' ID:');
	$name = get_val('ID: ', $blockacf, ' ');
	$status = get_val('Status: ', $blockacf, ' ');
	$layoutid = get_val('Layout ID: ', $blockacf, ' ');
	$fieldid = get_val('Subfield ID: ', $blockacf, ' ');
	$groupid = get_val('Group ID: ', $blockacf, ' ');
	$block['name'] = $label;
	if ($status != '') {
		$block['status'] = $status;
	} else {
		$block['status'] = 'public';
	}
	$block['layoutid'] = $layoutid;
	$block['fieldid'] = $fieldid;
	$block['groupid'] = $groupid;
	$blocks[$i] = $block;
}

$names = array_column($blocks, 'name');
array_multisort($names, SORT_ASC, $blocks);

// extrapolate key fields from blocks .acf file
function get_val($val, $blockacf, $endind) {
	$startpos = strpos($blockacf, $val) + strlen($val);
	$endpos = strpos($blockacf, $endind, intval($startpos)) - $startpos;
	$result = substr($blockacf, $startpos, $endpos);
	return $result;
}

// build $layouts
$layouts = [];
foreach ($blocks as $block) {
	$addthis = true; // backwards compatability when status does not exist
	$value = '';
	if ($block['status'] == 'private') {
		$addthis = false;
	} elseif ($block['status'] == 'public') {
		$addthis = true;
//	} elseif (strpos($block['status'], 'if') > -1) {
//		echo('<pre>'); echo('found a conditional for ' . $block['name']); echo('</pre>');
//		// echo('conditional: ' . $block['name'] . '<br/>');
//		$conditional = substr($block['status'], strpos($block['status'], '[') + 1, -1);
//		$condition = substr($conditional, 0, strpos($conditional, '=='));
//		$value = substr($conditional, strpos($conditional, '==') + 2);
//		if ($condition == 'post_type') {
//			// echo('looking for ' . $condition . ' of ' . $value . '<br/>');
//			// echo('<pre>');
//			// print_r($GLOBALS['posttypes']);
//			// echo('</pre>');
//			if (array_key_exists('page', $GLOBALS['posttypes'])) {
//				if (array_key_exists($value, $GLOBALS['posttypes'])) {
//					$addthis = true;
//				} else {
//					$addthis = false;
//				}
//			} else { // backwards compatability
//				if (in_array($value, $GLOBALS['posttypes'])) {
//					$addthis = true;
//				} else {
//					$addthis = false;
//				}
//			}
//		}
	}
	// echo('<pre>'); if (!$addthis) { echo('addthis for ' . $block['name'] . ' = false'); } else { echo('addthis for ' . $block['name'] . ' = ' . $addthis); } echo('</pre>');
	if ($addthis) {
		$layouts['layout_' . $block['layoutid']] = array(
			'key' => 'layout_' . $block['layoutid'],
			'name' => $block['id'],
			'label' => $block['name'],
			'display' => 'block',
			'sub_fields' => array(
				array(
					'key' => 'field_' . $block['fieldid'],
					'label' => $block['name'],
					'name' => substr($block['id'], strpos($block['id'], '_') + 1, strlen($block['id'])) . '_fields',
					'type' => 'clone',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'clone' => array(
						0 => 'group_' . $block['groupid'],
					),
					'display' => 'seamless',
					'layout' => 'block',
					'prefix_label' => 0,
					'prefix_name' => 0,
				),
			),
			'min' => '',
			'max' => '',
		);
	}
}

// start $locations
$locations = array(
	array(
		array(
			'param' => 'page_template',
			'operator' => '==',
			'value' => 'default',
		),
	)
);

$posttypes = $GLOBALS['posttypes'];
if (array_key_exists('page', $posttypes)) {
	foreach ($posttypes as $t => $type) {
		// echo('<pre>'); echo($t . '<br/>'); print_r($type); echo('</pre>');
		if (in_array('blocks', $type)) {
			$posttype = array(
				array(
					'param' => 'post_type',
					'operator' => '==',
					'value' => $t,
				)
			);
			$locations[] = $posttype;
		}
	}
} else { // backwards compatability
	foreach ($posttypes as $type) {
		$posttype = array(
			array(
				'param' => 'post_type',
				'operator' => '==',
				'value' => $type,
			)
		);
		$locations[] = $posttype;
	}
}
// echo('<pre>'); print_r($locations); echo('</pre>');
// echo('<pre>'); print_r($layouts); echo('</pre>');


// initialize fieldset

if( function_exists('acf_add_local_field_group') ):

acf_add_local_field_group(array(
	'key' => 'group_610ac6ffac0e4',
	'title' => 'Content Blocks',
	'fields' => array(
		array(
			'key' => 'field_610ac7050d4ff',
			'label' => 'Content Blocks',
			'name' => 'content_blocks',
			'type' => 'flexible_content',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'layouts' => $layouts,
			'button_label' => 'Add Block',
			'min' => '',
			'max' => '',
		),
	),
	'location' => $locations,
	'menu_order' => 0,
	'position' => 'acf_after_title',
	'style' => 'seamless',
	'label_placement' => 'top',
	'instruction_placement' => 'label',
	'hide_on_screen' => array(
		0 => 'the_content',
		1 => 'excerpt',
		2 => 'discussion',
		3 => 'comments',
		4 => 'author',
		5 => 'format',
		6 => 'featured_image',
		7 => 'send-trackbacks',
	),
	'active' => true,
	'description' => '',
	'show_in_rest' => 1,
));

endif;

?>