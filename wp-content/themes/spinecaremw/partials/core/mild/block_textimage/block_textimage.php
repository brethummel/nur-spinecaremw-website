<?php
/*
Partial Name: block_textimage_lottie
*/

/* Be sure to add the following to your functions.php file to allow .lottie uploads

// ======================================= //
//           ALLOW LOTTIE FILES            //
// ======================================= // 

function my_custom_mime_types( $mimes ) {
 
	// New allowed mime types.
    $mimes['json'] = 'application/json';
    $mimes['svg'] = 'image/svg+xml';
    $mimes['lottie'] = 'application/json';

	return $mimes;
}
add_filter( 'upload_mimes', 'my_custom_mime_types' );

*/

?>

<!-- BEGIN TEXT + IMAGE -->
<?php $block = $args; 
    $settings = $block['textimage_settings'];
    $classes = ''; 
	$imageclasses = ''; 
    if (isset($settings['background'])) { 
        $background = explode("|", $settings['background']);
        foreach ($background as $value) {
            $classes .= ' ' . $value;
        }
    }
    if (isset($settings['style'])) { 
        $style = $settings['style'];
        $classes .= ' ' . $style;
        if ($style == 'image') {
            $image = $block['textimage_image'];
            $imagestyle = $image['style'];
            $classes .= ' ' . $imagestyle;		
			$anchor = 'middle';
			if (isset($image['anchor'])) { $anchor = $image['anchor']; }
			$imageclasses = $anchor;
        } elseif ($style == 'lottie') {
            $image = $block['textimage_lottie'];
            $imagestyle = $image['style'];
            $classes .= ' ' . $imagestyle;
        } elseif ($style == 'video') {
            $video = $block['textimage_video'];
            $imagestyle = $video['style'];
            $classes .= ' ' . $imagestyle;
        } elseif ($style == 'html5') {
            $image = $block['textimage_image'];
            $imagestyle = 'rect';
        } elseif ($style == 'carousel') {
            $carousel = $block['textimage_slides'];
			$captions = $block['textimage_carousel']['captions'];
			$arrows = $block['textimage_carousel']['arrows'];
            $imagestyle = $carousel['style'];
            $classes .= ' ' . $imagestyle;
        }
    }
	$config = 'equal';
	$columns = $block['textimage_columns'];
	if (isset($columns['configuration'])) {
		$config = $columns['configuration'];
	}
	if ($config == 'equal') {
		$col1 = "col-12 col-lg-6";
		$col2 = "col-12 col-lg-6";
	} else if ($config == 'two-thirds') {
		$col1 = "col-12 col-lg-7";
		$col2 = "col-12 col-lg-5";
	} else if ($config == 'one-third') {
		$col1 = "col-12 col-lg-5";
		$col2 = "col-12 col-lg-7";
	}
    if ($block['textimage_display']['custom_class'] !== null) { 
        $class = $block['textimage_display']['custom_class'];
		if (strlen($class) > 0) {
			$classes .= ' ' . $class;
		}
	}
    if (isset($settings['align_text'])) { $classes .= ' ' . $settings['align_text']; }
    if (isset($settings['orientation'])) { 
        $orientation = $settings['orientation'];
        $classes .= ' image-' . $orientation; 
		if ($orientation == 'right') {
			$imageclasses .= ' ' . $col2;
			$textclasses = $col1;
		} elseif ($orientation == 'left') {
			$imageclasses .= ' ' . $col1;
			$textclasses = $col2;
		}
    }
    if (isset($settings['include_button']) && $settings['include_button']) {
        $buttons = $block['textimage_buttons'];
    }
	$displayblock = true;
	if (isset($block['textimage_display']['display_block']) && $block['textimage_display']['display_block'] != true) {
		$displayblock = false;
	}
?>
<?php if ($displayblock) { ?>
<?php if ($style == 'lottie') { ?>
	<script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
<?php } ?>
<?php $mypath = substr(dirname(__FILE__), strpos(dirname(__FILE__), '/wp-content')); ?>
<div class="block-textimage block padded<?php echo($classes); ?>">
	<?php if ($imagestyle == 'full') { ?>
		<?php if ($style == 'image') { ?>
			<div class="full-image <?php echo($imageclasses); ?>"><img class="img-fluid" src="<?php echo($image['image']['url']); ?>" alt="<?php echo($image['image']['alt']); ?>"/></div>
		<?php } elseif ($style == 'carousel') { ?>
			<script type="text/javascript" src="<?php bloginfo('url'); ?><?php echo($mypath); ?>/block_textimage.js"></script>
			<div class="full-carousel<?php if (isset($captions) && $captions) { ?> caption<?php } ?> <?php echo($imageclasses); ?>">
				<?php if (isset($arrows) && $arrows) { ?>
				<div class="arrows full">
					<div class="prev">
						<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48.68 80.4">
							<polyline points="44.44 4.24 8.48 40.2 44.44 76.16"/>
						</svg>
					</div>
					<div class="next">
						<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48.68 80.4">
							<polyline points="4.24 4.24 40.2 40.2 4.24 76.16"/>
						</svg>
					</div>
				</div>
				<?php } ?>
				<div class="image-container slides">
					<?php $slides = $carousel['slides']; ?>
					<?php foreach ($slides as $slide) { ?>
						<?php $anchor = 'middle'; ?>
						<?php if (isset($slide['anchor']) > 0) { $anchor = $slide['anchor']; } ?>
						<div class="slide<?php if (isset($captions) && $captions && strlen($slide['caption']) > 0) { ?> caption<?php } ?> <?php echo($anchor); ?>">
							<img class="img-fluid" src="<?php echo($slide['image']['url']); ?>" alt="<?php echo($slide['image']['alt']); ?>"/>
							<?php if (isset($captions) && $captions) { ?>
								<div class="caption"><?php echo($slide['caption']); ?></div>
							<?php } ?>
						</div>
					<?php } ?>
				</div>
				<div class="dots full"></div>
			</div>
        <?php } ?>
	<?php } ?>
    <div class="container">
        <div class="row">
            <div class="<?php echo($imageclasses); ?> image-col <?php echo($imagestyle); ?><?php if ($orientation == 'right') { ?> order-lg-last<?php } ?>">
                <?php if ($style == 'image') { ?>
                    <div class="image-container">
						<img class="img-fluid" src="<?php echo($image['image']['url']); ?>" alt="<?php echo($image['image']['alt']); ?>"/>
					</div>
					<?php if ($settings['captions'] && isset($image['caption'])) { ?><p class="caption"><?php echo($image['caption']); ?></p><?php } ?>
                <?php } elseif ($style == 'lottie') { ?>
                    <div class="lottie-container"<?php if (isset($image['background']['url'])) { ?> style="background-image: url(<?php echo($image['background']['url']); ?>);"<?php } ?>><lottie-player src="<?php echo($image['lottie']['url']); ?>" background="transparent"  speed="1" loop autoplay></lottie-player></div>
                <?php } elseif ($style == 'video') { ?>
                    <div class="video-container">
						<?php echo($video['video']); ?>
					</div>
					<?php if ($settings['captions'] && isset($image['caption'])) { ?><p class="caption"><?php echo($image['caption']); ?></p><?php } ?>
                <?php } elseif ($style == 'html5') { ?>
                    <div class="image-container">
						<video autoplay="true" muted loop="true" poster="<?php echo($image['image']['url']); ?>">
							<source src="<?php echo($image['video_files']['mp4']['url']); ?>" type="video/mp4">
							<source src="<?php echo($image['video_files']['webm']['url']); ?>" type="video/webm">
							Your browser does not support the video tag.
						</video>
					</div>
                <?php } elseif ($style == 'carousel') { ?>
					<script type="text/javascript" src="<?php bloginfo('url'); ?><?php echo($mypath); ?>/block_textimage.js"></script>
					<div class="carousel-container<?php if (isset($captions) && $captions) { ?> caption<?php } ?>">
						<?php if (isset($arrows) && $arrows) { ?>
						<div class="arrows main">
							<div class="prev">
								<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48.68 80.4">
									<polyline points="44.44 4.24 8.48 40.2 44.44 76.16"/>
								</svg>
							</div>
							<div class="next">
								<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48.68 80.4">
									<polyline points="4.24 4.24 40.2 40.2 4.24 76.16"/>
								</svg>
							</div>
						</div>
						<?php } ?>
						<div class="image-container slides">
							<?php $slides = $carousel['slides']; ?>
							<?php foreach ($slides as $slide) { ?>
								<?php $anchor = 'middle'; ?>
								<?php if (isset($slide['anchor']) > 0) { $anchor = $slide['anchor']; } ?>
								<div class="slide<?php if (isset($captions) && $captions && strlen($slide['caption']) > 0) { ?> caption<?php } ?> <?php echo($anchor); ?>">
									<img class="img-fluid" src="<?php echo($slide['image']['url']); ?>" alt="<?php echo($slide['image']['alt']); ?>"/>
									<?php if (isset($captions) && $captions) { ?>
										<div class="caption"><?php echo($slide['caption']); ?></div>
									<?php } ?>
								</div>
							<?php } ?>
						</div>
						<div class="dots main"></div>
					</div>
                <?php } ?>
            </div>
            <div class="<?php echo($textclasses); ?> text-col<?php if ($orientation == 'right') { ?> order-lg-first<?php } ?> vert-center">
				<div class="content-container">
					<?php echo($block['textimage_content']); ?>
					<?php if (isset($buttons)) { ?>
					<div class="buttons">
						<?php get_template_part('partials/custom/master_fields/buttons', null, $buttons); ?>
					</div>
					<?php } ?>
				</div>
            </div>
        </div>
    </div>
</div>
<?php } ?>
<!-- END TEXT + IMAGE --> 
