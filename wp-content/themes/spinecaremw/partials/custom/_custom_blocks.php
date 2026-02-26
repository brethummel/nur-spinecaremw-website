<?php

// Manually register unique custom blocks that may have been created for the client
// here, and in both _custom_blocks.acf and _custom_blocks.scss. The main _blocks.php 
// loads this file.

$block = get_row(true);

if( get_row_layout() == 'block_alltreatments' ):
	get_template_part('partials/custom/block_alltreatments/block_alltreatments', null, $block);
elseif( get_row_layout() == 'block_eventinfo' ):
	get_template_part('partials/custom/block_eventinfo/block_eventinfo', null, $block);
elseif( get_row_layout() == 'block_featuredevent' ):
	get_template_part('partials/custom/block_featuredevent/block_featuredevent', null, $block);
elseif( get_row_layout() == 'block_gatherup' ):
	get_template_part('partials/custom/block_gatherup/block_gatherup', null, $block);
elseif( get_row_layout() == 'block_patientstory' ):
	get_template_part('partials/custom/block_patientstory/block_patientstory', null, $block);
elseif( get_row_layout() == 'block_tabs' ):
	get_template_part('partials/custom/block_tabs/block_tabs', null, $block);
endif;

?>