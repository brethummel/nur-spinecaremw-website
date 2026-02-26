<?php
/*
Partial Name: block_rule
*/
?>

<!-- BEGIN RULE -->
<?php $block = $args; 
    $settings = $block['rule_settings'];
    $classes = '';
    if (isset($settings['background'])) { 
        $background = explode("|", $settings['background']);
        foreach ($background as $value) {
            $classes .= ' ' . $value;
        }
    }
    if ($block['rule_display']['custom_class'] !== null) { 
        $class = $block['rule_display']['custom_class'];
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
    if (isset($settings['rule_color'])) { $color = $settings['rule_color']; }
	$displayblock = true;
	if (isset($block['rule_display']['display_block']) && $block['rule_display']['display_block'] != true) {
		$displayblock = false;
	}
?>
<?php if ($displayblock) { ?>
<div class="block-rule block padded<?php echo($classes); ?>">
    <div class="container">
        <div class="row">
            <div class="col-12 content-container">
                <hr class="<?php echo($color); ?>" />
            </div>
        </div>
    </div>
</div>
<?php } ?>
<!-- END RULE --> 
