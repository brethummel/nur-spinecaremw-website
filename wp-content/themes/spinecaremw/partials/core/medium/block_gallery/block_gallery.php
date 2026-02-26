<?php
/*
Partial Name: block_gallery
*/
?>

<?php $block = $args; 
    $settings = $block['gallery_settings'];
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
	if (isset($style) && ($style == 'columns' || $style == 'columns-full' || $style == 'columns-full-cropped')) {
		$columns = $block['gallery_columns'];
		$alignment = $columns['alignment'];
		$columnnum = $columns['columns'];
        $classes .= ' columns-' . $columnnum;
		// echo('<pre>'); echo($columnnum); echo('</pre>'); 
		// echo('<pre>'); echo(ceil(12/$columnnum)); echo('</pre>'); 
		if ($alignment == 'center') {
			$rowclasses = " justify-content-center";
		} elseif ($alignment == 'right') {
			$rowclasses = " justify-content-end";
		}
	}
    if ($block['text_display']['custom_class'] !== null) { 
        $class = $block['text_display']['custom_class'];
		if (strlen($class) > 0) {
			$classes .= ' ' . $class;
		}
	}
    $images = $block['gallery_images'];
	$options = $settings['options'];

	if ($settings['lightboxed']) {
		$classes .= ' lightboxed';
	}

	$displayblock = true;
	if (isset($block['text_display']['display_block']) && $block['text_display']['display_block'] != true) {
		$displayblock = false;
	}
?>
<!-- BEGIN GALLERY -->
<?php if ($displayblock) { ?>
<?php $mypath = substr(dirname(__FILE__), strpos(dirname(__FILE__), '/wp-content')); ?>
<?php if ($settings['lightboxed']) { ?><script type="text/javascript" src="<?php bloginfo('url'); ?><?php echo($mypath); ?>/block_gallery.js"></script><?php } ?>
<div class="block-gallery block padded<?php echo($classes); ?>">
    <div class="container">
        <div class="row<?php if (isset($rowclasses)) { echo($rowclasses); } ?>">
			<?php foreach ($images as $image) { ?>
				<?php $span = intval($image['settings']['span']); ?>
				<?php 
					if ($span !== 1) {
						if ($span == 12) { // full width
							$imageclasses = 'col-12 col-md-12 col-lg-12';
						} else {
							$lg = ceil((12 / intval($columnnum)) * $span);
							if ($lg > 12) { 
								$lg = 12; 
							}
							$md = ceil(6 * $span);
							if ($md > 12) { 
								$md = 12; 
							}
							$sm = ceil(6 * $span);
							if ($sm > 12 || count($images) == 2) { 
								$sm = 12; 
							}
							$xs = ceil(6 * $span);
							if ($xs > 12 || count($images) == 2) { 
								$xs = 12; 
							}
							$imageclasses = 'col-' . $xs . ' col-sm-' . $sm . ' col-md-' . $md . ' col-lg-' . $lg;
						}
					} else {
						$imageclasses = 'col-12 col-md-6 col-lg-' . ceil(12/$columnnum);
					}
				?>
				<div class="image-container <?php echo($imageclasses); ?>">
					<div class="image">
						<img src="<?php echo($image['image']['url']); ?>" alt="<?php echo($image['image']['alt']); ?>">
					</div>
					<?php if ($settings['lightboxed']) { ?>
					<div class="sub-gallery">
						<div class="slides">
							<div class="slide"><img src="<?php echo($image['image']['url']); ?>" alt="<?php echo($image['image']['alt']); ?>"></div>
							<?php if(in_array('nested', $options)) {
								$subimages = $image['gallery']; 
								// echo('<pre>'); print_r($subimages); echo('</pre>'); 
								foreach($subimages as $subimage) { ?>
									<div class="slide"><img src="<?php echo($subimage['image']['url']); ?>" alt="<?php echo($subimage['image']['alt']); ?>"></div>
								<?php }
							} ?>
						</div>
						<?php if (in_array('captions', $options) && (strlen($image['settings']['caption']) > 0)) { ?>
							<div class="caption"><?php echo($image['settings']['caption']); ?></div>
						<?php } ?>
						<?php if (in_array('pagelink', $options) && $image['settings']['page_link']) { ?>
							<div class="pagelink"><a class="button medium primary" href="<?php echo($image['settings']['page_link']); ?>">View Project</a></div>
						<?php } ?>
						<?php if(in_array('nested', $options)) { ?>
							<div class="dots"></div>
							<div class="arrows">
								<div class="prev"></div>
								<div class="next"></div>
							</div>
						<?php } ?>
					</div>
					<?php } ?>
				</div>
			<?php } ?>
        </div>
    </div>
</div>
<?php } ?>
<!-- END GALLERY --> 
