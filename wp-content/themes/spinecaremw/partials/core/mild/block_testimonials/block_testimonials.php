<?php
/*
Partial Name: block_testimonials
*/
?>

<!-- BEGIN TESTIMONIALS -->
<?php $block = $args; 
    $settings = $block['testimonials_settings'];
    $classes = ''; 
    if (isset($settings['style'])) {
        $style = $settings['style'];
        $classes .= ' ' . $style;
    }
    if ($block['testimonials_display']['custom_class'] !== null) { 
        $class = $block['testimonials_display']['custom_class'];
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
    $quotes = $block['testimonials_slides'];
	$speed = 5000;
	if (isset($settings['speed'])) {
		$speed = $settings['speed'];
	}
	$arrows = 'false';
	if ($settings['display_arrows']) {
		$arrows = 'true';
	}
	$displayblock = true;
	if (isset($block['testimonials_display']['display_block']) && $block['testimonials_display']['display_block'] != true) {
		$displayblock = false;
	}
?>
<?php if ($displayblock) { ?>
<?php if (!isset($GLOBALS['testimonials_exists'])) { ?>
	<?php $mypath = substr(dirname(__FILE__), strpos(dirname(__FILE__), '/wp-content')); ?>
	<script type="text/javascript" src="<?php bloginfo('url'); ?><?php echo($mypath); ?>/block_testimonials.js"></script>
	<?php $GLOBALS['testimonials_exists'] = true; ?>
<?php } ?>
<div class="block-testimonials block padded<?php echo($classes); ?>" data-speed="<?php echo($speed); ?>" data-arrows="<?php echo($arrows); ?>">
    <div class="container">
		<div class="row justify-content-center">
			<div class="col-12<?php if ($style != 'image') { ?> col-lg-8<?php } ?> slides-container">
				<div class="slides">
					<?php foreach ($quotes as $quote) { ?>
					<div class="slide">
						<div class="row justify-content-center">
							<?php if ($style == 'image') { 
								$quoteclasses = 'col-12 col-md-8'; 
								if ($quote['image']['orientation'] == "right") { $quoteclasses .= ' order-md-first'; } ?>
								<div class="image-col col-12 col-md-4 col-lg-3<?php if ($quote['image']['orientation'] == "right") { echo(' order-md-last'); } ?>"><img class="img-fluid" src="<?php echo($quote['image']['image']['url']); ?>" /></div>
							<?php } else { $quoteclasses = 'col-12'; } ?>
							<div class="text-col <?php echo($quoteclasses); ?>">
								<?php if ($style == 'logo') { ?>
									<div class="logo"><img src="<?php echo($quote['logo']['logo']['url']); ?>" /></div>
								<?php } ?>
								<div class="quote"><?php echo($quote['quote']); ?></div>
								<p class="attribution">
									<span class="name"><?php echo($quote['attribution']['name']); ?></span><?php if (isset($quote['attribution']['title'])) { ?>, <span class="title"><?php echo($quote['attribution']['title']); ?></span><?php } ?><br/><?php if (isset($quote['attribution']['company'])) { ?><span class="company"><?php echo($quote['attribution']['company']); ?></span><?php } ?>
								</p>
								<?php $showbutton = true; ?>
								<?php if (isset($quote['show_button'])) { $showbutton = $quote['show_button']; } ?>
								<?php if (isset($settings['include_button']) && $settings['include_button'] && $showbutton) {
									$buttons = $quote['button']; ?>
									<div class="buttons">
										<?php get_template_part('partials/custom/master_fields/buttons', null, $buttons); ?>
									</div>
								<?php } ?>
							</div>
						</div>
					</div>
					<?php } ?>
				</div>
			</div>
		</div>
		<?php if ($arrows == 'true') { ?>
			<div class="arrows">
				<div class="prev"></div>
				<?php if (count($quotes) > 1 && count($quotes) < 10) { ?>
					<div class="dots"></div>
				<?php } ?>
				<div class="next"></div>
			</div>
		<?php } ?>
    </div>
</div>
<?php } ?>
<!-- END TESTIMONIALS --> 
