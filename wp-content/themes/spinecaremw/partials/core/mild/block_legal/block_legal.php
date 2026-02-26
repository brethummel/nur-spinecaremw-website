<?php
/*
Partial Name: block_legal
*/
?>

<!-- BEGIN LEGAL -->
<?php $block = $args; 
    $settings = $block['legal_settings'];
    $classes = ''; 
    if (isset($settings['background'])) { 
        $background = explode("|", $settings['background']);
        foreach ($background as $value) {
            $classes .= ' ' . $value;
        }
    }
    if ($block['legal_display']['custom_class'] !== null) { 
        $class = $block['legal_display']['custom_class'];
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
	if (isset($block['legal_display']['display_block']) && $block['legal_display']['display_block'] != true) {
		$displayblock = false;
	}
?>
<?php if ($displayblock) { ?>
<div class="block-legal block padded<?php echo($classes); ?>">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <?php echo($block['legal_content']); ?>
            </div>
        </div>
    </div>
</div>
<?php } ?>
<!-- END LEGAL --> 
