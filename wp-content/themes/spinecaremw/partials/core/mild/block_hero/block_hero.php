<?php
/*
Partial Name: block_hero
*/
?>

<!-- BEGIN HERO -->
<?php $block = $args; 
	$settings = $block['hero_settings'];
    $classes = ''; 
    if (isset($settings['style'])) { 
        $style = $settings['style'];
        $classes .= ' ' . $style;
    }
    if ($block['hero_display']['custom_class'] !== null) { 
        $class = $block['hero_display']['custom_class'];
		if (strlen($class) > 0) {
			$classes .= ' ' . $class;
		}
	}
    if ($style != 'carousel') {
        if ($style == "marquee") {
            $background = $settings['background'];
            if ($background == "video") {
                $video = $block['hero_content']['video'];
                $tint = $video['tint'];
            } elseif ($background == "image") {
                $image = $block['hero_content']['image'];
                $tint = $image['tint'];
				$anchor = $image['anchor'];
        		$classes .= ' ' . $anchor;
            }
        } elseif ($style == "page") {
            $background = 'image';
            $image = $block['hero_content']['image'];
            $tint = $image['tint'];
			$anchor = $image['anchor'];
			$classes .= ' ' . $anchor;
        }
        $classes .= ' ' . $background;
        if (isset($settings['orientation'])) { $classes .= ' ' . $settings['orientation']; }
        $content = $block['hero_content']['content'];
        if (isset($settings['include_button']) && $settings['include_button']) {
            $buttons = $block['hero_content']['buttons'];
        }
    } else { // style == 'carousel'
        $slides = $block['hero_slides'];
    }
	$displayblock = true;
	if (isset($block['hero_display']['display_block']) && $block['hero_display']['display_block'] != true) {
		$displayblock = false;
	}
?>
<?php if ($displayblock) { ?>
<?php $mypath = substr(dirname(__FILE__), strpos(dirname(__FILE__), '/wp-content')); ?>
<?php if ($style == 'carousel') { ?><script type="text/javascript" src="<?php bloginfo('url'); ?><?php echo($mypath); ?>/block_hero.js"></script><?php } ?>
<div class="block-hero block<?php echo($classes); ?>"<?php if (isset($background) && $background == 'image') { ?> style="background-image: url(<?php echo($image['image']['url']); ?>)"<?php } ?>>
    <?php if ($style != 'carousel') { ?>
        <?php if (isset($tint) && $tint) { ?><div class="tinter"></div><?php } ?>
        <?php if (isset($background) && $background == "video") { ?>
            <div class="video-container d-flex h-100 flex-column">
                <video playsinline autoplay muted loop poster="<?php echo($video['poster']['url']); ?>" id="bkgnd-video">
                    <source src="<?php echo($video['mp4']['url']); ?>" type="video/mp4">
                    <source src="<?php echo($video['webm']['url']); ?>" type="video/webm">
                </video>
            </div>
        <?php } ?>
        <div class="title-container d-flex h-100 flex-column">
			<div class="container">
				<div class="row flex-grow-1">
					<div class="col-12 col-md-8 col-lg-6 title">
						<div class="text-container">
							<h1><?php echo($content['title']); ?></h1>
							<?php if (isset($content['subhead']) && strlen($content['subhead']) > 0) { ?><p><?php echo($content['subhead']); ?></p><?php } ?>
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
    <?php } else { // style == 'carousel' ?>
        <div class="slides">
            <?php foreach ($slides as $slide) { ?>
                <?php 
                    $settings = $slide['settings'];
                    $classes = ''; 
                    $image = $slide['image'];
                    $tint = $image['tint'];
                    if (isset($settings['align_text'])) { $classes .= ' ' . $settings['align_text']; }
                    $content = $slide['content'];
                    if (isset($settings['include_button']) && $settings['include_button']) {
                        $button = $slide['button'];
                    }
                ?>
                <div class="slide<?php echo($classes); ?>" style="background-image: url(<?php echo($image['image']['url']); ?>)">
                    <?php if (isset($tint) && $tint) { ?><div class="tinter"></div><?php } ?>
                    <div class="container title-container d-flex h-100 flex-column">
                        <div class="row flex-grow-1">
                            <div class="col-12 col-md-8 col-lg-6 title">
                                <div class="text-container">
                                    <h1><?php echo($content['title']); ?></h1>
                                    <?php if (isset($content['subhead']) && strlen($content['subhead']) > 0) { ?><p><?php echo($content['subhead']); ?></p><?php } ?>
                                    <?php if (isset($button)) { ?>
                                    <div class="buttons">
                                        <?php get_template_part('partials/custom/master_fields/buttons', null, $button); ?>
                                    </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
        <div class="dots"></div>
    <?php } ?>
</div>
<?php } ?>
<!-- END HERO --> 
