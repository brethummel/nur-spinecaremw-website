<?php
/*
Partial Name: block_ticker
*/
?>

<!-- BEGIN TICKER -->
<?php $block = $args; 
    $settings = $block['ticker_settings'];
    $classes = ''; 
    if (isset($settings['style'])) { 
		$style = $settings['style'];
		$classes .= ' ' . $style; 
	}
	if (isset($settings['background'])) { 
		$background = explode("|", $settings['background']);
		foreach ($background as $value) {
			$classes .= ' ' . $value;
		}
	}
    if ($block['ticker_display']['custom_class'] !== null) { 
        $class = $block['ticker_display']['custom_class'];
		if (strlen($class) > 0) {
			$classes .= ' ' . $class;
		}
	}
	if ($style == 'single') {
		$line1 = '';
		$snippets = $block['ticker_content']['text_snippets'];
		foreach ($snippets as $snippet) {
			$line1 .= '<span>' . $snippet['text'] . '</span>'; 
		}
	} else {
		$line1 = $block['ticker_content']['text_line1'];
		$line2 = $block['ticker_content']['text_line2'];
	}
	$displayblock = true;
	if (isset($block['ticker_display']['display_block']) && $block['ticker_display']['display_block'] != true) {
		$displayblock = false;
	}
?>
<?php if ($displayblock) { ?>
<div class="block-ticker block<?php echo($classes); ?>" data-direction="<?php echo($settings['direction']); ?>">
	<?php $mypath = substr(dirname(__FILE__), strpos(dirname(__FILE__), '/wp-content')); ?>
	<script type="text/javascript" src="<?php bloginfo('url'); ?><?php echo($mypath); ?>/block_ticker.js"></script>
	<script type="text/javascript" src="<?php bloginfo('url'); ?><?php echo($mypath); ?>/newsticker/eocjs-newsticker.js"></script>
	<div class="line-text line-1"><?php echo($line1); ?></div>
	<?php if ($style != 'single') { ?>
		<div class="line-text line-2"><?php if ($style == 'unique') { echo($line2); } else { echo($line1); } ?></div>
	<?php } ?>
</div>
<?php } ?>
<!-- END TICKER --> 