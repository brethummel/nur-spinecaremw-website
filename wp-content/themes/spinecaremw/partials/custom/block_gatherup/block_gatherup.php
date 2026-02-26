<?php
/*
Partial Name: block_gatherup
*/
?>

<!-- BEGIN GATHERUP -->
<?php $block = $args; 
    $classes = ''; 
    $settings = $block['gatherup_settings'];
    if (isset($settings['background'])) { 
        $background = explode("|", $settings['background']);
        foreach ($background as $value) {
            $classes .= ' ' . $value;
        }
    }
    if ($block['gatherup_display']['custom_class'] !== null) { 
        $class = $block['gatherup_display']['custom_class'];
		if (strlen($class) > 0) {
			$classes .= ' ' . $class;
		}
	}
	$siteid = $block['gatherup_content']['site_id'];
	$displayblock = true;
	if (isset($block['gatherup_display']['display_block']) && $block['gatherup_display']['display_block'] != true) {
		$displayblock = false;
	}
?>
<?php if ($displayblock) { ?>
<div class="block-gatherup block padded <?php echo($classes); ?>">
	<div class="container">
		<div class="row">
			<div class="col-12">
				<div data-bid="<?php echo($siteid); ?>" data-url="https://app.gatherup.com" ><script src="https://widget.reviewability.com/js/widgetAdv.min.js" async></script></div><script class="json-ld-content" type="application/ld+json"></script>
			</div>
		</div>
	</div>
</div>
<?php } ?>
<!-- END GATHERUP --> 
