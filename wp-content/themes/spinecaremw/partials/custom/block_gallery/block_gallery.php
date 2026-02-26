<?php
/*
Partial Name: block_gallery
*/
?>

<!-- BEGIN GALLERY -->
<?php $block = $args; 
    $settings = $block['gallery_settings'];
    $classes = '';
    if (isset($settings['style'])) { 
        $style = $settings['style'];
    } else {
    	$style = 'columns';
    }
    $classes .= ' ' . $style;
    if (isset($settings['background'])) { 
        $background = explode("|", $settings['background']);
        foreach ($background as $value) {
            $classes .= ' ' . $value;
        }
    }
	$columns = $block['gallery_columns'];
	$columnnum = $columns['columns'];
	if (array_key_exists('full_width', $settings)) {
		$fullwidth = $settings['full_width'];
	} else {
		$fullwidth = false;
	}
	if ($fullwidth) {
        $classes .= ' full-width';
	}
	if (array_key_exists('gutters', $columns)) {
		$gutters = $columns['gutters'];
	} else {
		$gutters = true;
	}
	if (!$gutters) {
		$classes .= ' no-gutters';
	}
	if (isset($style) && $style == 'columns') {
		$alignment = $columns['alignment'];
		$itemclasses = 'col-12 col-md-6 col-lg-' . (12/$columnnum);
		if ($alignment == 'center') {
			$rowclasses = " justify-content-center";
		} elseif ($alignment == 'right') {
			$rowclasses = " justify-content-end";
		}
	}
	$hasvideo = false;
	$items = $block['gallery_images'];
	foreach ($items as $item) {
		if ($item['settings']['style'] == 'video' || $item['settings']['style'] == 'html5' || $item['settings']['style'] == 'iframe') {
			$hasvideo = true;
		}
	}
	if ($hasvideo) {
		$classes .= ' has-video';
	}
    if ($block['gallery_display']['custom_class'] !== null) { 
        $class = $block['gallery_display']['custom_class'];
		if (strlen($class) > 0) {
			$classes .= ' ' . $class;
		}
	}
	$displayblock = true;
	if (isset($block['gallery_display']['display_block']) && $block['gallery_display']['display_block'] != true) {
		$displayblock = false;
	}
?>
<?php if ($displayblock) { ?>
<div class="block-gallery block padded<?php echo($classes); ?>">
    <div class="container">
        <div class="row<?php if (isset($rowclasses)) { echo($rowclasses); } ?>">
			<?php $links = $settings['links']; ?>
			<?php if ($style == 'columns') { ?>
				<?php $aspectratio = 'widescreen';
					if (isset($columns['aspect_ratio'])) {
						$aspectratio = $columns['aspect_ratio'];
					}
					$itemclasses .= ' ' . $aspectratio;
				?>
				<?php foreach ($items as $item) { ?>
					<?php // echo('<pre>'); print_r($item); echo('</pre>'); ?>
					<?php $type = 'image';
						if ($item['settings']['style'] != null) {
							$type = $item['settings']['style'];
						} 
						if ($type == 'image') {
							$span = intval($item['settings']['span']);
						} else {
							$span = 1;
						}
					?>
					<?php if ($span !== 1) {
						$lg = (12 / intval($columnnum)) * $span;
						if ($lg > 12) { $lg = 12; }
						$md = 6 * $span;
						if ($md > 12) { $md = 12; }
						$sm = 6 * $span;
						if ($sm > 12 || count($items) == 2) { $sm = 12; }
						$xs = 6 * $span;
						if ($xs > 12 || count($items) == 2) { $xs = 12; }
						$itemclasses .= ' col-' . $xs . ' col-sm-' . $sm . ' col-md-' . $md . ' col-lg-' . $lg;
					} ?>
					<div class="gallery-item <?php echo($itemclasses); ?>">
		                <?php if ($type == 'image') { ?>
		                    <div class="image-container">
								<img class="img-fluid" src="<?php echo($item['content']['image']['url']); ?>" alt="<?php echo($item['content']['image']['alt']); ?>"/>
							</div>
							<?php if ($settings['captions'] && strlen($item['content']['caption']) > 0) { ?><p class="caption"><?php echo($item['content']['caption']); ?></p><?php } ?>
		                <?php } elseif ($type == 'video') { ?>
		                    <div class="video-container">
								<?php echo($item['content']['video']); ?>
							</div>
							<?php if ($settings['captions'] && strlen($item['content']['caption']) > 0) { ?><p class="caption"><?php echo($item['content']['caption']); ?></p><?php } ?>
		                <?php } elseif ($type == 'html5') { ?>
		                    <div class="image-container">
								<video autoplay="true" muted loop="true" poster="<?php echo($item['content']['image']['url']); ?>">
									<source src="<?php echo($item['content']['video_files']['mp4']['url']); ?>" type="video/mp4">
									<source src="<?php echo($item['content']['video_files']['webm']['url']); ?>" type="video/webm">
									Your browser does not support the video tag.
								</video>
							</div>
							<?php if ($settings['captions'] && strlen($item['content']['caption']) > 0) { ?><p class="caption"><?php echo($item['content']['caption']); ?></p><?php } ?>
		                <?php } elseif ($type == 'iframe') { ?>
		                    <div class="video-container">
								<iframe loading="lazy" src="<?php echo($item['content']['iframe']['source']); ?>" style="aspect-ratio:16/9;"<?php if (isset($item['content']['iframe']['allow'])) { ?> allow="<?php echo($item['content']['iframe']['allow']); ?>"<?php } ?><?php if (str_contains($item['content']['iframe']['allow'], 'fullscreen')) { ?> allowfullscreen<?php } ?> frameborder="0"></iframe>
							</div>
							<?php if ($settings['captions'] && strlen($item['content']['caption']) > 0) { ?><p class="caption"><?php echo($item['content']['caption']); ?></p><?php } ?>
		                <?php } ?>
	            	</div>
				<?php } ?>
			<?php } ?>
        </div>
    </div>
</div>
<?php } ?>
<!-- END GALLERY --> 
