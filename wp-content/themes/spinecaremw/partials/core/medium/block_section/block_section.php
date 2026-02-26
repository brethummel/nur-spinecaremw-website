<?php
/*
Partial Name: block_section
*/
?>

<!-- BEGIN SECTION -->
<?php $block = $args; 
	$sectioncount = $GLOBALS['sectioncount'];
    $settings = $block['section_settings'];
    $classes = '';
    if ($block['section_display']['custom_class'] !== null) { 
        $class = $block['section_display']['custom_class'];
		if (strlen($class) > 0) {
			$classes .= ' ' . $class;
		}
	}
	if (isset($settings['background'])) { 
		if ($settings['background'] == 'image') {
			$classes .= ' bkgnd-image';
			$bkgndimage = $block['section_images']['background'];
		} else {
			$background = explode("|", $settings['background']);
			foreach ($background as $value) {
				$classes .= ' ' . $value;
			}
		}
	}
	$displayblock = true;
	if (isset($block['section_display']['display_block']) && $block['section_display']['display_block'] != true) {
		$displayblock = false;
	}
	$sectioncount++;
	$GLOBALS['sectioncount'] = $sectioncount;
?>
<?php if ($displayblock) { ?>
<?php if ($sectioncount % 2 != 0) { // odd ?>
	<div class="block-section block padded<?php echo($classes); ?>"<?php if ($settings['background'] == 'image') { ?> style="background-image: url(<?php echo($bkgndimage['url']); ?>)"<?php } ?>>
<?php } else { ?>
	</div>
<?php } ?>
<?php } ?>
<!-- END SECTION --> 