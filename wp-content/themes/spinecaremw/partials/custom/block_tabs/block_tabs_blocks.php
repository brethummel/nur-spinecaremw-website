<?php
/*
Partial Name: block_tab_blocks
*/
?>

<!-- BEGIN TAB BLOCKS -->
<?php $detail = $args; 
	$themedir = get_template_directory();
	foreach ($detail as $block) {
		switch ($block['acf_fc_layout']) {
			case 'block_fullimage':
				if (file_exists($themedir . '/partials/custom/block_fullimage/block_fullimage.php')) {
					get_template_part('partials/custom/block_fullimage/block_fullimage', null, $block);
				} else {
					get_template_part('partials/core/mild/block_fullimage/block_fullimage', null, $block);
				}
				break;
			case 'block_legal':
				if (file_exists($themedir . '/partials/custom/block_legal/block_legal.php')) {
					get_template_part('partials/custom/block_legal/block_legal', null, $block);
				} else {
					get_template_part('partials/core/mild/block_legal/block_legal', null, $block);
				}
				break;
			case 'block_text':
				if (file_exists($themedir . '/partials/custom/block_text/block_text.php')) {
					get_template_part('partials/custom/block_text/block_text', null, $block);
				} else {
					get_template_part('partials/core/mild/block_text/block_text', null, $block);
				}
				break;
			case 'block_textimage':
				if (file_exists($themedir . '/partials/custom/block_textimage/block_textimage.php')) {
					get_template_part('partials/custom/block_textimage/block_textimage', null, $block);
				} else {
					get_template_part('partials/core/mild/block_textimage/block_textimage', null, $block);
				}
				break;
		}
	} ?>
<!-- END TAB BLOCKS --> 
