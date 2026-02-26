<?php
/*
Partial Name: block_strip
*/
?>

<!-- BEGIN STRIP -->
<?php $block = $args; 
    $settings = $block['strip_settings'];
    $classes = '';
    if (isset($settings['background'])) { 
        $background = explode("|", $settings['background']);
        foreach ($background as $value) {
            $classes .= ' ' . $value;
        }
    }
    if ($block['strip_display']['custom_class'] !== null) { 
        $class = $block['strip_display']['custom_class'];
		if (strlen($class) > 0) {
			$classes .= ' ' . $class;
		}
	}
    if (isset($settings['align_text'])) { $classes .= ' ' . $settings['align_text']; }
	$displayblock = true;
	if (isset($block['strip_display']['display_block']) && $block['strip_display']['display_block'] != true) {
		$displayblock = false;
	}
?>
<?php if ($displayblock) { ?>
<div class="block-strip block <?php echo($classes); ?>">
    <div class="container">
        <div class="row">
            <div class="col-12 content-container">
                <?php echo($block['strip_content']); ?>
            </div>
        </div>
    </div>
</div>
<?php } ?>
<!-- END STRIP --> 
