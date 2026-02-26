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
    $imagecol = $block['textimage_contenttable']['imagecol'];
    $contentcol = $block['textimage_contenttable']['contentcol'];
    $classes = ''; 
	$imageclasses = ''; 
    if (isset($settings['background'])) { 
        $background = explode("|", $settings['background']);
        foreach ($background as $value) {
            $classes .= ' ' . $value;
        }
    }
    if (isset($imagecol['style'])) { 
        $style = $imagecol['style'];
        $classes .= ' ' . $style;
        if ($style == 'image') {
            $image = $imagecol['image'];
            $imagestyle = $image['style'];
            $classes .= ' ' . $imagestyle;		
			$anchor = 'middle';
			if (isset($image['anchor'])) { $anchor = $image['anchor']; }
			$imageclasses = ' ' . $anchor;
        } elseif ($style == 'video') {
            $video = $imagecol['video'];
            $imagestyle = $video['style'];
            $classes .= ' ' . $imagestyle;
        } elseif ($style == 'html5') {
            $image = $imagecol['image'];
            $imagestyle = 'rect';
        } elseif ($style == 'iframe') {
            $image = $imagecol['iframe'];
            $imagestyle = 'rect';
        }
    }
	$split = 'equal';
	if (isset($settings['columns'])) {
		$split = $settings['columns'];;
		$classes .= ' ' . $split;
		switch ($split) {
			case 'equal':
				$columnstyles = array('col-12 col-lg-6', 'col-12 col-lg-6');
				$columnpercents = array('fifty', 'fifty');
				break;
			case 'one-third':
				$columnstyles = array('col-12 col-lg-5', 'col-12 col-lg-7');
				$columnpercents = array('one-third', 'two-thirds');
				break;
			case 'two-thirds':
				$columnstyles = array('col-12 col-lg-7', 'col-12 col-lg-5');
				$columnpercents = array('two-thirds', 'one-third');
				break;
			case 'one-fourth':
				$columnstyles = array('col-12 col-lg-3', 'col-12 col-lg-9');
				$columnpercents = array('one-fourth', 'three-fourths');
				break;
			case 'three-fourths':
				$columnstyles = array('col-12 col-lg-9', 'col-12 col-lg-3');
				$columnpercents = array('three-fourths', 'one-fourth');
				break;
		}
	}
    if ($block['textimage_display']['custom_class'] !== null) { 
        $class = $block['textimage_display']['custom_class'];
		if (strlen($class) > 0) {
			$classes .= ' ' . $class;
		}
	}
    if (isset($settings['orientation'])) { 
        $orientation = $settings['orientation'];
        $classes .= ' image-' . $orientation; 
		if ($orientation == 'right') {
			$imageclasses .= ' ' . $columnstyles[1];
			$fullimageclass .= ' ' . $columnpercents[1];
			$textclasses = $columnstyles[0];
		} elseif ($orientation == 'left') {
			$imageclasses .= ' ' . $columnstyles[0];
			$fullimageclass .= ' ' . $columnpercents[0];
			$textclasses = $columnstyles[1];
		}
    }
    if (isset($settings['include_button']) && $settings['include_button']) {
        $buttons = $contentcol['buttons'];
    }
	$displayblock = true;
	if (isset($block['textimage_display']['display_block']) && $block['textimage_display']['display_block'] != true) {
		$displayblock = false;
	}
?>
<?php if ($displayblock) { ?>
<?php $mypath = substr(dirname(__FILE__), strpos(dirname(__FILE__), '/wp-content')); ?>
<div class="block-textimage block padded<?php echo($classes); ?>">
	<?php if ($style == 'image' && $imagestyle == 'full') { ?>
		<div class="full-image-row">
			<div class="image-col<?php echo($fullimageclass); ?><?php if ($orientation == 'right') { ?> right<?php } ?>">
				<img src="<?php echo($imagecol['image']['image']['url']); ?>" alt="<?php echo($imagecol['image']['image']['alt']); ?>"/>
			</div>
		</div>
	<?php } ?>
    <div class="container">
        <div class="row">
            <div class="image-col<?php echo($imageclasses); ?> <?php echo($imagestyle); ?><?php if ($orientation == 'right') { ?> order-lg-last<?php } ?>">
                <?php if ($style == 'image') { ?>
                    <div class="image-container">
						<img class="img-fluid" src="<?php echo($imagecol['image']['image']['url']); ?>" alt="<?php echo($imagecol['image']['image']['alt']); ?>"/>
					</div>
					<?php if ($settings['captions'] && isset($imagecol['image']['caption'])) { ?><p class="caption"><?php echo($imagecol['image']['caption']); ?></p><?php } ?>
                <?php } elseif ($style == 'video') { ?>
                    <div class="video-container">
						<?php echo($imagecol['video']['video']); ?>
					</div>
					<?php if ($settings['captions'] && isset($imagecol['video']['caption'])) { ?><p class="caption"><?php echo($imagecol['video']['caption']); ?></p><?php } ?>
                <?php } elseif ($style == 'html5') { ?>
                    <div class="image-container">
						<video autoplay="true" muted loop="true" poster="<?php echo($imagecol['image']['image']['url']); ?>">
							<source src="<?php echo($imagecol['image']['video_files']['mp4']['url']); ?>" type="video/mp4">
							<source src="<?php echo($imagecol['image']['video_files']['webm']['url']); ?>" type="video/webm">
							Your browser does not support the video tag.
						</video>
					</div>
					<?php if ($settings['captions'] && isset($imagecol['image']['caption'])) { ?><p class="caption"><?php echo($imagecol['image']['caption']); ?></p><?php } ?>
                <?php } elseif ($style == 'iframe') { ?>
                    <div class="video-container">
						<iframe loading="lazy" src="<?php echo($imagecol['iframe']['source']); ?>" style="aspect-ratio:16/9;"<?php if (isset($imagecol['iframe']['allow'])) { ?> allow="<?php echo($imagecol['iframe']['allow']); ?>"<?php } ?><?php if (str_contains($imagecol['iframe']['allow'], 'fullscreen')) { ?> allowfullscreen<?php } ?> frameborder="0"></iframe>
					</div>
					<?php if ($settings['captions'] && isset($imagecol['iframe']['caption'])) { ?><p class="caption"><?php echo($imagecol['iframe']['caption']); ?></p><?php } ?>

                <?php } ?>
            </div>
            <div class="text-col <?php echo($textclasses); ?><?php if ($orientation == 'right') { ?> order-lg-first<?php } ?> vert-center">
				<div class="content-container">
					<?php echo($contentcol['content']); ?>
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
