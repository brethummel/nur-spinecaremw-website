<?php
/*
Partial Name: block_text
*/
?>

<!-- BEGIN TEXT -->
<?php $block = $args; 
	// echo('<pre style="padding-left: 100px;">'); print_r($block); echo('</pre>');
    $settings = $block['text_settings'];
    $classes = ''; 
    if (isset($settings['style'])) { 
		$style = $settings['style'];
		$classes .= ' ' . $style; 
	}
    if ($block['text_display']['custom_class'] !== null) { 
        $class = $block['text_display']['custom_class'];
		if (strlen($class) > 0) {
			$classes .= ' ' . $class;
		}
	}
    if (isset($settings['align_text'])) { $classes .= ' ' . $settings['align_text']; }
    if (isset($settings['include_button']) && $settings['include_button']) {
        $buttons = $block['text_buttons'];
		$classes .= ' has-buttons';
    }
    $columns = $block['text_cols'];
	if ($style == 'one-col') {
		$columnstyles = "col-12";
	} else if ($style == 'two-col') {
		$split = $settings['col_split'];
		$classes .= ' ' . $split;
		switch ($split) {
			case 'equal':
				$columnstyles = array('col-12 col-lg-6', 'col-12 col-lg-6');
				break;
			case 'one-third':
				$columnstyles = array('col-12 col-lg-5', 'col-12 col-lg-7');
				break;
			case 'two-thirds':
				$columnstyles = array('col-12 col-lg-7', 'col-12 col-lg-5');
				break;
			case 'one-fourth':
				$columnstyles = array('col-12 col-lg-3', 'col-12 col-lg-9');
				break;
			case 'three-fourths':
				$columnstyles = array('col-12 col-lg-9', 'col-12 col-lg-3');
				break;
		}
	}
	if (isset($settings['background'])) { 
		if ($settings['background'] == 'columns') {
			$background = array($columns['column_one']['background'], $columns['column_two']['background']);
			$classes .= ' split-bkgnds';
		} else {
			$background = $settings['background'];
			$background = implode(' ', explode('|', $background));
			$classes .= ' ' . $background;
		}
	}
	$displayblock = true;
	if (isset($block['text_display']['display_block']) && $block['text_display']['display_block'] != true) {
		$displayblock = false;
	}
?>
<?php if ($displayblock) { ?>
<div class="block-text block padded<?php echo($classes); ?>">
    <div class="container">
        <div class="row">
        	<?php if (!is_array($columnstyles)) { // single column ?>
	            <div class="column-one <?php echo($columnstyles); ?>">
					<div class="content-container">
						<?php echo($columns['column_one']['content']); ?>
						<?php if (isset($buttons)) { ?>
							<div class="buttons">
								<?php get_template_part('partials/custom/master_fields/buttons', null, $buttons); ?>
							</div>
						<?php } ?>
					</div>
	            </div>
        	<?php } else { // two columns ?>
	            <div class="column-one<?php echo(' ' . implode(' ', explode('|', $columnstyles[0]))); ?><?php if ($settings['background'] == 'columns') { echo(' ' . implode(' ', explode('|', $background[0]))); } ?>">
					<div class="content-container">
						<?php echo($columns['column_one']['content']); ?>
					</div>
	            </div>
	            <div class="column-two<?php echo(' ' . implode(' ', explode('|', $columnstyles[1]))); ?><?php if ($settings['background'] == 'columns') { echo(' ' . implode(' ', explode('|', $background[1]))); } ?>">
					<div class="content-container">
						<?php echo($columns['column_two']['content']); ?>
					</div>
	            </div>
				<?php if (isset($buttons)) { ?>
					<div class="col-12 buttons-container<?php echo($blockbkgnd); ?>">
						<div class="content-container">
							<div class="buttons">
								<?php get_template_part('partials/custom/master_fields/buttons', null, $buttons); ?>
							</div>
						</div>
					</div>
				<?php } ?>
        	<?php } ?>
        </div>
    </div>
</div>
<?php } ?>
<!-- END TEXT --> 
