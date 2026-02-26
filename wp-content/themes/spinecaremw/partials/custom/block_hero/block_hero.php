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
        if ($style == 'page-title') {
            $titleclasses = ' col-12';
        } elseif ($style == 'marquee') {
            $titleclasses = ' col-12 col-md-8 col-lg-6';
        }
    }
    if ($style == 'marquee') {
    	$image = $block['hero_content']['image'];
    	$imageanchor = $image['anchor'];
        $classes .= ' image ' . $imageanchor;
    }
    if ($block['hero_display']['custom_class'] !== null) { 
        $class = $block['hero_display']['custom_class'];
		if (strlen($class) > 0) {
			$classes .= ' ' . $class;
		}
	}
    if (isset($settings['include_button']) && $settings['include_button']) {
        $buttons = $block['hero_content']['buttons'];
    }
    $content = $block['hero_content']['content'];
    if (isset($content['title']) && strlen($content['title']) > 0) {
        $title = $content['title'];
    } else {
        $title = get_the_title();
    }
    if (isset($content['eyebrow']) && strlen($content['eyebrow']) > 0) {
        $classes .= ' has-eyebrow';
    }
    if (isset($content['subhead']) && strlen($content['subhead']) > 0) {
        $classes .= ' has-subhead';
    }
	$displayblock = true;
	if (isset($block['hero_display']['display_block']) && $block['hero_display']['display_block'] != true) {
		$displayblock = false;
	}
?>
<?php if ($displayblock) { ?>
<div class="block-hero block<?php echo($classes); ?>"<?php if ($style == 'marquee' && isset($image)) { ?> style="background-image: url(<?php echo($image['image']['url']); ?>)"<?php } ?>>
    <div class="title-container">
		<div class="container">
			<div class="row">
				<div class="title<?php echo($titleclasses); ?>">
					<div class="text-container">
						<?php if (isset($content['eyebrow']) && strlen($content['eyebrow']) > 0) { ?><h1><?php echo($content['eyebrow']); ?></h1><?php } ?>
						<h2><?php echo($title); ?></h2>
						<?php if (isset($content['subhead']) && strlen($content['subhead']) > 0) { ?><p class="subhead"><?php echo($content['subhead']); ?></p><?php } ?>
						<?php if (isset($content['content']) && strlen($content['content']) > 0) { ?><div class="paragraph"><?php echo($content['content']); ?></div><?php } ?>
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
</div>
<?php } ?>
<!-- END HERO --> 
