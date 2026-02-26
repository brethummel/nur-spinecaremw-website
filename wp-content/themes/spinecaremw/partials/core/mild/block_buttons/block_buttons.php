<?php
/*
Partial Name: block_buttons
*/
?>

<!-- BEGIN BUTTONS -->
<?php $block = $args; 
    $settings = $block['buttons_settings'];
    $classes = '';
    if (isset($settings['background'])) { 
        $background = explode("|", $settings['background']);
        foreach ($background as $value) {
            $classes .= ' ' . $value;
        }
    }
    if ($block['buttons_display']['custom_class'] !== null) { 
        $class = $block['buttons_display']['custom_class'];
		if (strlen($class) > 0) {
			$classes .= ' ' . $class;
		}
	}
    if (isset($settings['align_text'])) { $classes .= ' ' . $settings['align_text']; }
    if (isset($settings['pair_with'])) {
        $pairwith = $settings['pair_with'];
        foreach ($pairwith as $pair) {
            $classes .= ' ' . $pair;
        }
    }
	$displayblock = true;
	if (isset($block['buttons_display']['display_block']) && $block['buttons_display']['display_block'] != true) {
		$displayblock = false;
	}
    $buttons = $block['buttons_buttons'];
?>
<?php if ($displayblock) { ?>
<div class="block-buttons block padded<?php echo($classes); ?>">
    <div class="container">
        <div class="row">
            <div class="col-12 content-container">
                <div class="buttons">
                    <?php get_template_part('partials/custom/master_fields/buttons', null, $buttons); ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php } ?>
<!-- END BUTTONS --> 
