<?php
/*
Partial Name: block_tiles
*/
?>

<!-- BEGIN TILES -->
<?php $block = $args; 
    $settings = $block['tiles_settings'];
    $classes = ''; 
    if (isset($settings['style'])) {
        $style = $settings['style'];
        $classes .= ' ' . $style;
        if ($style == 'columns') {
            if (isset($settings['columns_image'])) {
                $imagetype = $settings['columns_image'];
                $classes .= ' ' . $imagetype;
            }
        } else if ($style == 'cards') {
            if (isset($settings['cards_image'])) {
                $imagetype = $settings['cards_image'];
                $classes .= ' ' . $imagetype;
            }
        }
    }
    if (isset($settings['background'])) { 
        $background = explode("|", $settings['background']);
        foreach ($background as $value) {
            $classes .= ' ' . $value;
        }
    }
    if ($block['tiles_display']['custom_class'] !== null) { 
        $class = $block['tiles_display']['custom_class'];
		if (strlen($class) > 0) {
			$classes .= ' ' . $class;
		}
	}
    $tiles = $block['tiles_tiles'];
	$displayblock = true;
	if (isset($block['tiles_display']['display_block']) && $block['tiles_display']['display_block'] != true) {
		$displayblock = false;
	}
?>
<?php if ($displayblock) { ?>
<div class="block-tiles block padded<?php echo($classes); ?>">
    <div class="container">
        <div class="row justify-content-center">
            <?php if ($style == 'columns') { $tileclasses = 'col-12 col-md-5 col-lg-3 ' . $imagetype; } ?>
            <?php if ($style == 'cards') { $tileclasses = 'col-12 col-md-5 col-lg-4 ' . $imagetype; } ?>
            <?php if (isset($settings['include_button']) && $settings['include_button']) { $tileclasses .= ' button'; } ?>
            <?php foreach ($tiles as $tile) { ?>
                <div class="tile <?php echo($tileclasses); ?>">
					<?php $showbutton = true; ?>
					<?php if (isset($tile['show_button'])) { $showbutton = $tile['show_button']; } ?>
                    <?php if ($style == 'cards') { ?><div class="card-container"><?php } ?>
					<?php if (isset($tile['image']['url'])) { ?>
                    	<div class="image">
							<?php if (isset($settings['include_button']) && $settings['include_button'] && $showbutton) {
								$buttons = $tile['button'];
								$target = '';
								if ($buttons['button']['settings']['type'] == 'page') {
									$destination = $buttons['button']['destination']['page'];
									if ($buttons['button']['destination']['new_tab']) {
										$target = 'target="_blank"';
									}
								} elseif ($buttons['button']['settings']['type'] == 'url') {
									$destination = $buttons['button']['destination']['url'];
									if ($buttons['button']['destination']['new_tab']) {
										$target = ' target="_blank"';
									}
								} elseif ($buttons['button']['settings']['type'] == 'email') {
									$destination = 'mailto:' . $buttons['button']['destination']['email'];
								}
							?><a <?php echo($target); ?> href="<?php echo($destination); ?>"><?php } ?>
							<img class="img-fluid" src="<?php echo($tile['image']['url']); ?>" alt="<?php echo($tile['image']['alt']); ?>" />
							<?php if (isset($settings['include_button']) && $settings['include_button'] && $showbutton) {
							$buttons = $tile['button']; ?></a><?php } ?>
						</div>
					<?php } ?>
                    <div class="text">
                        <h4><?php echo($tile['title']); ?></h4>
                        <?php echo($tile['text']); ?>
                    </div>
                    <?php if (isset($settings['include_button']) && $settings['include_button'] && $showbutton) {
                        $buttons = $tile['button']; ?>
                        <div class="buttons">
                            <?php get_template_part('partials/custom/master_fields/buttons', null, $buttons); ?>
                        </div>
                    <?php } ?>
                    <?php if ($style == 'cards') { ?></div><?php } ?>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
<?php } ?>
<!-- END TILES --> 
