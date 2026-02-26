<?php
/*
Partial Name: block_photostrip
*/
?>

<!-- BEGIN PHOTO STRIP -->
<?php $block = $args; 
    $settings = $block['photostrip_settings'];
    $classes = ''; 
    if (isset($settings['style'])) { 
		$style = $settings['style'];
		$classes .= ' ' . $style; 
	}
	$focus = false;
    if (isset($settings['focus']) && $settings['focus']) { 
		$focus = $settings['focus'];
		$classes .= ' focus'; 
	}
    if (get_sub_field('custom_class') !== null) { 
        $class = get_sub_field('custom_class');
		if (strlen($class) > 0) {
        	$classes .= ' ' . $class;
		}
    }
	$photos = $block['photostrip_photos'];
?>
<?php if ($style == 'carousel') { ?>
	<?php $mypath = substr(dirname(__FILE__), strpos(dirname(__FILE__), '/wp-content')); ?>
	<script type="text/javascript" src="<?php bloginfo('url'); ?><?php echo($mypath); ?>/block_photostrip.js"></script>
<?php } ?>
<div class="block-photostrip block full<?php echo($classes); ?>">
    <div class="container">
        <div class="row">
			<div class="col-12">
				<?php if ($style == 'carousel') { ?>
					<div class="back vert-center">
						<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48.68 80.4">
							<polyline points="44.44 4.24 8.48 40.2 44.44 76.16"/>
						</svg>
					</div>
				<?php } ?>
				<div class="photos-container">
					<?php foreach ($photos as $i => $photo) { ?>
						<div class="photo image-<?php echo($i+1); ?>"><img src="<?php echo($photo['photo']['url']); ?>" alt="<?php echo($photo['photo']['alt']); ?>"/></div>
					<?php } ?>
				</div>
				<?php if ($style == 'carousel') { ?>
					<div class="next vert-center">
						<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48.68 80.4">
							<polyline points="4.24 4.24 40.2 40.2 4.24 76.16"/>
						</svg>
					</div>
				<?php } ?>
			</div>
        </div>
    </div>
</div>
<!-- END PHOTO STRIP --> 