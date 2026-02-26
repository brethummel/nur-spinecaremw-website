<?php
/*
Partial Name: block_pullquote
*/
?>

<!-- BEGIN PULLQUOTE -->
<?php $block = $args; 
    $settings = $block['pullquote_settings'];
    $classes = ''; 
    if ($block['pullquote_display']['custom_class'] !== null) { 
        $class = $block['pullquote_display']['custom_class'];
		if (strlen($class) > 0) {
			$classes .= ' ' . $class;
		}
	}
	if (isset($settings['background'])) { 
		$background = explode("|", $settings['background']);
		foreach ($background as $value) {
			$classes .= ' ' . $value;
		}
	}
	$displayblock = true;
	if (isset($block['pullquote_display']['display_block']) && $block['pullquote_display']['display_block'] != true) {
		$displayblock = false;
	}
?>
<?php if ($displayblock) { ?>
<div class="block-pullquote block padded<?php echo($classes); ?>">
    <div class="container">
        <div class="row">
			<div class="col-12 quote"><?php echo($block['pullquote_quote']); ?></div>
			<?php if (strlen($block['pullquote_attribution']) > 0) { ?>
				<div class="col-12 attribution"><p><?php echo($block['pullquote_attribution']); ?></p></div>
			<?php } ?>
        </div>
    </div>
</div>
<?php } ?>
<!-- END PULLQUOTE --> 
