<?php
/*
Partial Name: block_fullimage
*/
?>

<!-- BEGIN FULL IMAGE -->
<?php $block = $args; 
    $settings = $block['fullimage_settings'];
    $classes = ''; 
    if (isset($settings['style'])) {
        $style = $settings['style'];
        $classes .= ' ' . $style;
    }
    if ($block['fullimage_display']['custom_class'] !== null) { 
        $class = $block['fullimage_display']['custom_class'];
		if (strlen($class) > 0) {
			$classes .= ' ' . $class;
		}
	}
	$scale = false;
	if ($settings['scale']) {
		$scale = true;
		$classes .= ' scale';
	}
    if ($style == "text") {
        $content = $block['fullimage_content'];
        if (isset($settings['align_text'])) { $classes .= ' ' . $settings['align_text']; }
        if (isset($settings['include_button']) && $settings['include_button']) {
            $buttons = $block['fullimage_buttons'];
        }
        if (isset($settings['background'])) { 
            $background = explode("|", $settings['background']);
            foreach ($background as $i => $value) {
                if ($i == 0) {
                    $bkgndcolor = $value;
                }
            }
        }
		if (isset($block['fullimage_images']['image'])) {
			$image = $block['fullimage_images']['image'];
		} else {
        	$image = $block['fullimage_image']; // backwards compatability
		}
        $fullwidth = true;
    } elseif ($style == 'carousel') {
		$carousel = $block['fullimage_carousel'];
        if (isset($settings['full_width'])	) {
            $fullwidth = $settings['full_width'];
        }
	} else {
        if (isset($settings['full_width']) && $style == 'image') {
            $fullwidth = $settings['full_width'];
        } else {
            $fullwidth = false;
        }
        if ($style == 'video' || !$fullwidth) {
            if (isset($settings['background'])) { 
                $background = explode("|", $settings['background']);
                foreach ($background as $value) {
                    $classes .= ' ' . $value;
                }
                $classes .= ' padded';
            }
            if ($style == "image") {
				if (isset($block['fullimage_images']['image'])) {
					$image = $block['fullimage_images']['image'];
				} else {
					$image = $block['fullimage_image']; // backwards compatability
				}
            } else {
                $video = $block['fullimage_video'];
            }
        } else {
			if (isset($block['fullimage_images']['image'])) {
				$image = $block['fullimage_images']['image'];
			} else {
				$image = $block['fullimage_image']; // backwards compatability
			}
        }
    }
    if ($fullwidth) {
        $classes .= ' full-width';
    }
	$displayblock = true;
	if (isset($block['fullimage_display']['display_block']) && $block['fullimage_display']['display_block'] != true) {
		$displayblock = false;
	}
?>
<?php if ($displayblock) { ?>
<?php $mypath = substr(dirname(__FILE__), strpos(dirname(__FILE__), '/wp-content')); ?>
<?php if ($style == 'carousel') { ?><script type="text/javascript" src="<?php bloginfo('url'); ?><?php echo($mypath); ?>/block_fullimage.js"></script><?php } ?>
<div class="block-fullimage block<?php echo($classes); ?>"<?php if ($style == 'text') { ?> style="background-image: url(<?php echo($image['url']); ?>);"<?php } ?>>
	<?php // echo('<pre>'); print_r($block); echo('</pre>'); ?>
	<?php if ($fullwidth) { ?>
    	<div class="container"></div>
	<?php } ?>
    <?php if (!$fullwidth || $style == 'text') { ?>
    <div class="container">
        <div class="row">
            <div class="col-12"><?php } ?>
                <?php if ($style == 'text') { ?>
                    <div class="text-container <?php echo($bkgndcolor); ?> light">
                        <?php echo($content); ?>
                        <?php if (isset($buttons)) { ?>
                        <div class="buttons">
                            <?php get_template_part('partials/custom/master_fields/buttons', null, $buttons); ?>
                        </div>
                        <?php } ?>
                    </div>
                <?php } ?>
                <?php if ($style == 'image') { ?>
                    <img class="img-fluid" src="<?php echo($image['url']); ?>" alt="<?php echo($image['alt']); ?>"/>
                <?php } ?>
                <?php if ($style == 'carousel') { ?>
					<div class="slides">
						<?php foreach ($carousel as $slide) { ?>
							<div class="slide"><img class="img-fluid" src="<?php echo($slide['image']['url']); ?>" alt="<?php echo($slide['image']['alt']); ?>"/></div>
						<?php } ?>
					</div>
					<div class="dots"></div>
                <?php } ?>
                <?php if ($style == 'video') { ?>
                    <div class="video-container <?php echo($video['settings']['style']); ?>">
                        <?php if ($video['settings']['style'] == 'oembed') { ?>
                            <?php echo($video['oembed']); ?>
                        <?php } else if ($video['settings']['style'] == 'lightbox') { ?>
                            <a rel="lightbox" data-lightbox-type="iframe" href="<?php echo($video['url']); ?>"><img class="img-fluid" src="<?php echo($video['image']['url']); ?>" alt="<?php echo($video['image']['alt']); ?>"/></a>
                            <div class="play-button"><a rel="lightbox" data-lightbox-type="iframe" href="<?php echo($video['url']); ?>"><img class="img-fluid" src="<?php bloginfo('stylesheet_directory'); ?>/images/icon_play.png" alt="Play"/></a></div>
                        <?php } ?>
                    </div>
                <?php } ?>
            <?php if (!$fullwidth || $style == 'text') { ?>
            </div>
			<?php if ($settings['include_caption'] && isset($block['fullimage_caption'])) { ?>
				<div class="col-12 caption-container"><p class="caption"><?php echo($block['fullimage_caption']); ?></p></div>
			<?php } ?>
        </div>
    </div><?php } ?>
</div>
<?php } ?>
<!-- END FULL IMAGE --> 
