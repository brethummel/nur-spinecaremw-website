<?php
/*
Partial Name: block_resources
*/
?>

<!-- BEGIN RESOURCES -->
<?php $block = $args; 
    $settings = $block['resources_settings'];
    $classes = '';
    if (isset($settings['background'])) { 
        $background = explode("|", $settings['background']);
        foreach ($background as $value) {
            $classes .= ' ' . $value;
        }
    }
    if ($block['resources_display']['custom_class'] !== null) { 
        $class = $block['resources_display']['custom_class'];
		if (strlen($class) > 0) {
			$classes .= ' ' . $class;
		}
	} 
    if (isset($settings['style'])) { 
        $style = $settings['style'];
        $classes .= ' ' . $style;
    }
	$columns = 3;
	if (isset($settings['columns'])) {
		$columns = $settings['columns'];
	}
	$classes .= ' columns-' . $columns;
	if ($columns == 4) {
		$cardclasses = 'col-12 col-md-6 col-lg-3';
	} elseif ($columns == 3) {
		$cardclasses = 'col-12 col-md-6 col-lg-4';
	} elseif ($columns == 2) {
		$cardclasses = 'col-12 col-md-6';
	} elseif ($columns == 1) {
		$cardclasses = 'col-12';
	}
	$resources = $block['resources_resources'];
	$displayblock = true;
	if (isset($block['resources_display']['display_block']) && $block['resources_display']['display_block'] != true) {
		$displayblock = false;
	}
?>
<?php if ($displayblock) { ?>
<div class="block-resources block padded<?php echo($classes); ?>">
    <div class="container">
        <div class="row">
        	<?php if ($style == 'bullets') { ?>
				<div class="col-12">
					<div class="content-container">
						<ul class="resources">
							<?php foreach ($resources as $resource) { 
								$type = $resource['settings']['type']; ?>
								<li class="<?php echo($type); ?>">
									<?php if ($type == "vid") { ?>
										<a href="<?php echo($resource['resource']['url']); ?>" rel="lightbox" data-lightbox-type="iframe"><?php echo($resource['settings']['link_text']); ?></a>
									<?php } else if ($type == "url") { ?>
										<?php if ($resource['resource']['link_type'] == "external") { ?>
											<a href="<?php echo($resource['resource']['url']); ?>" target="_blank"><?php echo($resource['settings']['link_text']); ?></a>
										<?php } elseif ($resource['resource']['link_type'] == "internal") { ?>
											<a href="<?php echo($resource['resource']['page']); ?>"><?php echo($resource['settings']['link_text']); ?></a>
										<?php } ?>
									<?php } else { ?>
										<a href="<?php echo($resource['resource']['file']['url']); ?>" target="_blank"><?php echo($resource['settings']['link_text']); ?></a>
									<?php } ?>
								</li>
							<?php } ?>
						</ul>
					</div>
				</div>
			<?php } ?>
        	<?php if ($style == 'cards') { ?>
				<?php foreach ($resources as $resource) { 
					$type = $resource['settings']['type']; ?>
	                <div class="tile <?php echo($cardclasses); ?>">
	                    <div class="card-container">
	                    	<div class="icon <?php echo($type); ?>">
								<?php if ($type == "vid") { ?>
									<a href="<?php echo($resource['resource']['url']); ?>" rel="lightbox" data-lightbox-type="iframe"><img class="img-fluid" src="<?php echo get_template_directory_uri(); ?>/images/icon_video.png" alt="<?php echo($resource['settings']['link_text']); ?>"/></a>
								<?php } else if ($type == "url") { ?>
									<?php $url = $resource['resource']['url']; ?>
									<a href="<?php echo($url); ?>"<?php if ($resource['resource']['link_type'] == "external") { ?> target="_blank"<?php } ?>><img class="img-fluid" src="<?php echo get_template_directory_uri(); ?>/images/icon_url.png" alt="<?php echo($resource['settings']['link_text']); ?>"/></a>
								<?php } else { ?>
									<?php $icon = 'icon_' . $type . '.png'; ?>
									<a href="<?php echo($resource['resource']['file']['url']); ?>" target="_blank"><img class="img-fluid" src="<?php echo get_template_directory_uri(); ?>/images/<?php echo($icon); ?>" alt="<?php echo($resource['settings']['link_text']); ?>"/></a>
								<?php } ?>
							</div>
		                    <div class="text">
		                    	<p>
									<?php if ($type == "vid") { ?>
										<a href="<?php echo($resource['resource']['url']); ?>" rel="lightbox" data-lightbox-type="iframe"><?php echo($resource['settings']['link_text']); ?></a>
									<?php } else if ($type == "url") { ?>
										<?php if ($resource['resource']['link_type'] == "external") { ?>
											<a href="<?php echo($resource['resource']['url']); ?>" target="_blank"><?php echo($resource['settings']['link_text']); ?></a>
										<?php } elseif ($resource['resource']['link_type'] == "internal") { ?>
											<a href="<?php echo($resource['resource']['page']); ?>"><?php echo($resource['settings']['link_text']); ?></a>
										<?php } ?>
									<?php } else { ?>
										<a href="<?php echo($resource['resource']['file']['url']); ?>" target="_blank"><?php echo($resource['settings']['link_text']); ?></a>
									<?php } ?>
								</p>
		                    </div>
	                    </div>
	                </div>
				<?php } ?>
			<?php } ?>
        </div>
    </div>
</div>
<?php } ?>
<!-- END RESOURCES --> 