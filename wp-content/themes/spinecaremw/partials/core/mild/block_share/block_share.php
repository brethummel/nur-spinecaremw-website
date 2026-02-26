<?php
/*
Partial Name: block_share
*/
?>

<!-- BEGIN SHARE -->
<?php $block = $args; 
    $settings = $block['share_settings'];
    $classes = '';
    if (isset($settings['background'])) { 
        $background = explode("|", $settings['background']);
        foreach ($background as $value) {
            $classes .= ' ' . $value;
        }
    }
    if ($block['share_display']['custom_class'] !== null) { 
        $class = $block['share_display']['custom_class'];
		if (strlen($class) > 0) {
			$classes .= ' ' . $class;
		}
	}
    if (isset($settings['pair_with'])) {
        $pairwith = $settings['pair_with'];
        foreach ($pairwith as $pair) {
            $classes .= ' ' . $pair;
        }
    }
    if (isset($settings['share_to'])) { $options = $settings['share_to']; }
	$displayblock = true;
	if (isset($block['share_display']['display_block']) && $block['share_display']['display_block'] != true) {
		$displayblock = false;
	}
?>
<?php if ($displayblock) { ?>
<div class="block-share block padded<?php echo($classes); ?>">
    <div class="container">
        <div class="row">
            <div class="col-12 col-sm-8 ol-md-6 content-container">
				<?php $categories = get_the_category($post->ID); ?>
				<h3>Share this<?php if (isset($categories[0])) { echo(' ' . strtolower($categories[0]->name)); } ?>:</h3>
                <?php echo(do_shortcode('[social_warfare buttons="' . implode(',', $options) . '"]')); ?>
            </div>
        </div>
    </div>
</div>
<?php } ?>
<!-- END SHARE --> 
