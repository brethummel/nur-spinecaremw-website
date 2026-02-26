<?php
/*
Partial Name: block_map
*/
?>

<!-- BEGIN MAP -->
<?php $block = $args; 
    $settings = $block['map_settings'];
    $classes = '';
    if (isset($settings['style'])) { 
        $style = $settings['style'];
        $classes .= ' ' . $style;
		if ($settings['full_width']) {
        	$classes .= ' full-width';
		}
    }
    if (get_sub_field('custom_class') !== null) { 
        $class = get_sub_field('custom_class');
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
?>
<?php $mypath = substr(dirname(__FILE__), strpos(dirname(__FILE__), '/wp-content')); ?>
<?php if ($style == 'mapplic') { ?>
	<?php
    	wp_register_script('mousewheel.js', get_site_url() . $mypath . '/mapplic/js/jquery.mousewheel.js', array('jquery'), '1.0.0', true);
    	wp_register_script('mapplic_script.js', get_site_url() . $mypath . '/mapplic/js/script.js', array('jquery'), '1.0.0', true);
    	wp_register_script('mapplic.js', get_site_url() . $mypath . '/mapplic/mapplic/mapplic.js', array('jquery'), '7.1.2', true);
    	wp_enqueue_script('mousewheel.js');
    	wp_enqueue_script('mapplic_script.js');
    	wp_enqueue_script('mapplic.js');
	?>
	<script type="text/javascript" src="<?php bloginfo('url'); ?><?php echo($mypath); ?>/block_map.js"></script>
	<link rel="stylesheet" type="text/css" href="<?php bloginfo('url'); ?><?php echo($mypath); ?>/mapplic/mapplic/mapplic.css">
<?php } ?>
<?php $mapplic_settings = $block['map_mapplic_settings']; ?>
<div class="block-map block padded<?php echo($classes); ?><?php if ($style == 'mapplic' && $mapplic_settings['sidebar']) { ?> sidebar<?php } ?>">
    <div class="container">
		<div class="bkgnd"></div>
        <div class="row">
			<div class="col-12">
				<?php if ($style == 'mapplic') { ?>
					<?php 
						if ($mapplic_settings['sidebar']) {
							$sidebar = 'true';
						} else {
							$sidebar = 'false';
						}
					?>
					<div class="map-container">
						<div id="mapplic" data-path="<?php bloginfo('url'); ?><?php echo($mypath); ?>" data-config='{"source": "/block_map_api.php?action=get_locations&map=<?php echo($mapplic_settings["choose_map"]); ?>&post_id=<?php echo(get_the_ID()); ?>", "sidebar": "<?php echo($sidebar); ?>"}'></div>
					</div>
				<?php } elseif ($style == 'google') { ?>
					<div class="map-container">
						<iframe src="<?php echo($block['embed_url']); ?>" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
					</div>
				<?php } ?>
			</div>
        </div>
    </div>
</div>
<!-- END MAP --> 
