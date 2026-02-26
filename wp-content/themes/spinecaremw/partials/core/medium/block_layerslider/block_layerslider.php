<?php
/*
Partial Name: block_layerslider
*/
?>

<!-- BEGIN LAYERSLIDER -->
<?php $block = $args; 
    $settings = $block['layerslider_settings'];
    $classes = ''; 
	if (isset($settings['background'])) { 
		$background = explode("|", $settings['background']);
		foreach ($background as $value) {
			$classes .= ' ' . $value;
		}
	}
    if ($block['layerslider_display']['custom_class'] !== null) { 
        $class = $block['layerslider_display']['custom_class'];
		if (strlen($class) > 0) {
			$classes .= ' ' . $class;
		}
	}
	$displayblock = true;
	if (isset($block['layerslider_display']['display_block']) && $block['layerslider_display']['display_block'] != true) {
		$displayblock = false;
	}
?>
<?php if ($displayblock) { ?>
<?php $mypath = substr(dirname(__FILE__), strpos(dirname(__FILE__), '/wp-content')); ?>
<script type="text/javascript" src="<?php bloginfo('url'); ?><?php echo($mypath); ?>/block_layerslider.js"></script>
<link rel="stylesheet" href="<?php echo($mypath); ?>/layerslider/css/layerslider.css" type="text/css">
<script src="<?php echo($mypath); ?>/layerslider/js/layerslider.utils.js" type="text/javascript"></script>
<script src="<?php echo($mypath); ?>/layerslider/js/layerslider.transitions.js" type="text/javascript"></script>
<script src="<?php echo($mypath); ?>/layerslider/js/layerslider.kreaturamedia.jquery.js" type="text/javascript"></script>
<div class="block-layerslider block<?php echo($classes); ?>" data-layerslider="<?php echo($settings['layerslider_file']); ?>" data-folderpath="<?php echo(get_stylesheet_directory_uri()); ?>">
	<div class="container">
		<div class="row">
			<div class="layerslider-container col-12">
				<?php // echo('<pre>'); echo(substr($settings['layerslider_file'], 1)); echo('</pre>'); ?>
				<?php
					get_template_part('partials/custom/_templates/_layerslider/' . $settings['layerslider_file'] . '/' . substr($settings['layerslider_file'], 1), null); 
				?>
			</div>
		</div>
	</div>
</div>
<?php } ?>
<!-- END LAYERSLIDER --> 