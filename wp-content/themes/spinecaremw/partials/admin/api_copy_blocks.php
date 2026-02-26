<?php

function spr_copy_blocks_scripts() {
	$themeurl = get_template_directory_uri();
	$themedir = get_template_directory();
	wp_register_script('api_copy_blocks_ajax.js', $themeurl . '/partials/admin/api_copy_blocks_ajax.js', array('jquery'), '1.1.2', true);
	$script_data_array = array(
		'ajaxurl' => admin_url('admin-ajax.php'),
		'security' => wp_create_nonce('spr_load_copy_blocks'),
	);
	wp_localize_script('api_copy_blocks_ajax.js', 'blocks', $script_data_array);
	wp_enqueue_script('api_copy_blocks_ajax.js');
}
add_action('admin_enqueue_scripts', 'spr_copy_blocks_scripts');


function spr_load_blocks_by_ajax_callback() {

	check_ajax_referer('spr_load_copy_blocks', 'security');
	$source = $_POST['sourcepost'];
	
	$selectorHTML = '<div class="blocks-container">';
	$sourcetitle = get_the_title($source);
	if (strlen($sourcetitle) > 24) {
		$sourcetitle = substr($sourcetitle, 22) . '...';
	}
	$selectorHTML .= '<h3>' . $sourcetitle . '<span class="sub">(Content Blocks)</span><span class="select-controls">Select:<span class="all">All</span>|<span class="none">None</span></span></h3>';
	$fieldobjects = get_field_objects($source);
	$layouts = $fieldobjects['content_blocks']['layouts'];
	$blockslist = $fieldobjects['content_blocks']['value'];
	$sectioncount = 0;
	$indentflag = false;
	foreach ($blockslist as $i => $block) {
		$blockID = $block['acf_fc_layout'];
		$blockname = str_replace('block_', '', $blockID);
		$blockdisplay = $block[$blockname . '_display'];
		$blocklabel = '';
		foreach ($layouts as $layout) {
			if ($layout['name'] == $blockID) {
				$blocklabel = $layout['label'];
			}
		}
		$classes = '';
		if ($blockname == 'section') {
			$sectioncount++;
			if ($sectioncount % 2 != 0) {  // odd = first
				$classes .= ' section-start show-label';
				$indentflag = true;
			} else {
				$classes .= ' -indent section-end show-label'; // even = second
				$indentflag = false;
			}
		} else {
			if ($indentflag) {
				$classes .= ' -indent';
			}
		}
		$selectorHTML .= '<div class="layout' . $classes . '" data-id="' . $i . '" data-layout="' . $blockID . '">';
		$selectorHTML .= '<div class="acf-fc-layout-handle handle">';
		if (!isset($blockdisplay['block_name']) || $blockdisplay['block_name'] == '') {
			$selectorHTML .= '<span class="icon ' . $blockID . '">' . $blocklabel . '</span>';
		} else {
			$selectorHTML .= '<span class="icon ' . $blockID . '">' . $blockdisplay['block_name'] . ' </span><span class="block-type">(' . $blocklabel . ')</span>';
		}
		$selectorHTML .= '<input type="checkbox" id="row-' . $i . '" name="' . $blockID . '" value="row-' . $i . '">';
		$selectorHTML .= '</div>'; // handle
		$selectorHTML .= '</div>'; // block
	}
	$selectorHTML .= '</div>'; // blocks container
	echo($selectorHTML);

}
add_action('wp_ajax_spr_load_blocks_by_ajax', 'spr_load_blocks_by_ajax_callback');
add_action('wp_ajax_nopriv_spr_load_blocks_by_ajax', 'spr_load_blocks_by_ajax_callback');


function spr_copy_blocks_by_ajax_callback() {

	check_ajax_referer('spr_load_copy_blocks', 'security');
	$dest = $_POST['destpost'];
	$source = $_POST['sourcepost'];
	$blocks = $_POST['selectedblocks'];
	
	$fieldobjects = get_field_objects($source);
	$blockslist = $fieldobjects['content_blocks']['value'];
	
	foreach($blocks as $block) {
		add_row('content_blocks', $blockslist[$block], $dest);
	}
	
	echo('success');

	wp_die();
	
}
add_action('wp_ajax_spr_copy_blocks_by_ajax', 'spr_copy_blocks_by_ajax_callback');
add_action('wp_ajax_nopriv_spr_copy_blocks_by_ajax', 'spr_copy_blocks_by_ajax_callback');
	
?>