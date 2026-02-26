<?php
/*
Partial Name: block_contactform
*/
?>

<!-- BEGIN CONTACT FORM -->
<?php $block = $args; 
    $settings = $block['contactform_settings'];
    $classes = ''; 
    if (isset($settings['style'])) {
        $style = $settings['style'];
        $classes .= ' ' . $style;
        if ($style == 'info-map' || $style == 'info') {
        	$classes .= ' form';
        }
    }
    if ($block['contactform_display']['custom_class'] !== null) { 
        $class = $block['contactform_display']['custom_class'];
		if (strlen($class) > 0) {
			$classes .= ' ' . $class;
		}
		if (($style == 'map' || $style == 'info-map') && isset($block['contactform_map']['settings']['orientation'])) {
			$classes .= ' map-' . $block['contactform_map']['settings']['orientation'];
			if ($style == 'map' && $block['contactform_map']['settings']['fill_column']) {
				$classes .= ' fill-column ';
			}
		}
    }
    if (isset($settings['background'])) { 
        $background = explode("|", $settings['background']);
        foreach ($background as $value) {
            $classes .= ' ' . $value;
        }
    }
    $formtype = $settings['form_type'];
    $classes .= ' ' . $formtype;
    if ($formtype == 'code') {
    	$code = $block['contactform_code'];
    	if (str_contains($code, 'cognito')) {
    		$classes .= ' cognito';
    	}
    }
	$leadattribution = $GLOBALS['leadattribution'];
	if (isset($leadattribution) && $leadattribution) { 
		$attribution_fieldmap = $block['contactform_attrfieldmap']['fields'];
		$attribution_fields = ' data-campaign-field="' . $attribution_fieldmap['campaign'] . '"';
		$attribution_fields .= ' data-source-field="' . $attribution_fieldmap['source'] . '"';
		$attribution_fields .= ' data-medium-field="' . $attribution_fieldmap['medium'] . '"';
		$attribution_fields .= ' data-attrsrc-field="' . $attribution_fieldmap['attrsrc'] . '"';
		$attribution_fields .= ' data-visits-field="' . $attribution_fieldmap['visits'] . '"';
	}
	$displayblock = true;
	if (isset($block['contactform_display']['display_block']) && $block['contactform_display']['display_block'] != true) {
		$displayblock = false;
	}
?>
<?php if ($displayblock) { ?>
<div class="block-contactform block padded<?php echo($classes); ?>"<?php if ($leadattribution) { echo($attribution_fields); } ?>>
    <div class="container">
        <div class="row">
            <?php if ($style == 'form') { 
                $formclasses = 'col-12'; 
            } else if ($style == 'info' || $style == 'info-map') {
                $formclasses = 'col-12 col-md-7 col-lg-8';
				$infoclasses = 'col-12 col-md-5 col-lg-4';
            } else if ($style == 'map') { 
				if ($block['contactform_map']['settings']['configuration'] == 'equal') {
					$mapclasses = 'col-12 col-md-6 col-lg-6';
					$infoclasses = 'col-12 col-md-6 col-lg-6';
					if ($block['contactform_map']['settings']['orientation'] == 'right') {
						$mapclasses .= ' order-md-last';
						$infoclasses .= ' order-md-first';
					}
				} else if ($block['contactform_map']['settings']['configuration'] == 'two-thirds') {
					if ($block['contactform_map']['settings']['orientation'] == 'right') {
						$mapclasses = 'col-12 col-md-5 col-lg-4 order-md-last';
						$infoclasses = 'col-12 col-md-7 col-lg-8 order-md-first';
					} else {
						$mapclasses = 'col-12 col-md-7 col-lg-8';
						$infoclasses = 'col-12 col-md-5 col-lg-4';
					}
				} else if ($block['contactform_map']['settings']['configuration'] == 'one-third') {
					if ($block['contactform_map']['settings']['orientation'] == 'right') {
						$mapclasses = 'col-12 col-md-7 col-lg-8 order-md-last';
						$infoclasses = 'col-12 col-md-5 col-lg-4 order-md-first';
					} else {
						$mapclasses = 'col-12 col-md-5 col-lg-4';
						$infoclasses = 'col-12 col-md-7 col-lg-8';
					}
				}
			} ?>
			<?php if ($style != 'map') { ?>
            <div class="form-col <?php echo($formclasses); ?>">
                <?php if ($formtype == 'formidable') { 
                    echo(do_shortcode('[formidable id=' . $block['contactform_formidable'] . ']'));
                } else if ($formtype == 'shortcode') {
                    echo(do_shortcode($block['contactform_shortcode']));
                } else if ($formtype == 'code') {
                    echo($code);
                	if (str_contains($code, 'cognito') && (isset($leadattribution) && $leadattribution)) {
                		// echo('<p>This is a cognito form!</p>');
                	}
                } ?>
            </div>
			<?php } ?>
			<?php if ($style == 'map') { ?>
                <div class="map-col <?php echo($mapclasses); ?>">
					<div id="map">
						<iframe loading="lazy" allowfullscreen referrerpolicy="no-referrer-when-downgrade" src="<?php echo($block['contactform_map']['map_url']); ?>"></iframe>
					</div>
				</div>
			<?php } ?>
            <?php if ($style == 'info' || $style == 'info-map' || $style == 'map') { ?>
                <div class="info-col <?php echo($infoclasses); ?>">
                    <div class="info">
						<?php $infotitle = 'Contact Information';
							if (isset($block['contactform_infotitle'])) {
								$infotitle = $block['contactform_infotitle'];
							}							
						?>
                        <h4><?php echo($infotitle); ?></h4>
                        <?php  
                            $company = get_field('company', 'options');
                            $address = get_field('address', 'options');
                            $contact = get_field('contact', 'options');
                            $contactinfo = $block['contactform_info'];
                        ?>
                        <?php if ($contactinfo && count($contactinfo) > 0) {
							foreach ($contactinfo as $info) {
								switch ($info['acf_fc_layout']) {
									case 'phone': ?>
										<?php if (isset($contact['phone'])) { ?>
											<p class="icon phone"><a href="tel:<?php echo($contact['phone']); ?>"><?php echo($contact['phone']); ?></a></p>
										<?php } ?>
									<?php break;
									case 'fax': ?>
										<?php if (isset($contact['fax'])) { ?>
											<p class="icon fax"><?php echo($contact['fax']); ?></p>
										<?php } ?>
									<?php break;
									case 'email': ?>
										<?php if (isset($contact['email'])) { ?>
											<p class="icon email"><a href="mailto:<?php echo($contact['email']); ?>"><?php echo($contact['email']); ?></a></p>
										<?php } ?>
									<?php break;
									case 'address': ?>
										<?php if (isset($address['street_address'])) { ?>
											<p class="icon address"><strong><?php echo($company); ?></strong><br/>
											<?php echo($address['street_address']); ?><br/>
											<?php if($address['street_address2']) { ?><?php echo($address['street_address2']); ?><br/><?php } ?>
											<?php echo($address['city']['city']); ?>, <?php echo($address['city']['state']); ?> <?php echo($address['city']['zip']); ?></p>
										<?php } ?>
									<?php break;
									case 'content': ?>
										<?php echo($info['content']); ?>
									<?php break;
								}
								if(get_row_layout() == 'phone'): ?>
									<p class="icon phone"><a href="tel:<?php echo($contact['phone']); ?>"><?php echo($contact['phone']); ?></a></p>
								<?php elseif( get_row_layout() == 'email' ): ?>
									<p class="icon email"><a href="mailto:<?php echo($contact['email']); ?>"><?php echo($contact['email']); ?></a></p>
								<?php elseif( get_row_layout() == 'address' ): ?>
									<p class="icon address"><strong><?php echo($company); ?></strong><br/>
									<?php echo($address['street_address']); ?><br/>
									<?php if($address['street_address2']) { ?><?php echo($address['street_address2']); ?><br/><?php } ?>
									<?php echo($address['city']['city']); ?>, <?php echo($address['city']['state']); ?> <?php echo($address['city']['zip']); ?></p>
								<?php elseif( get_row_layout() == 'content' ): ?>
									<?php the_sub_field('content'); ?>
								<?php endif;
							} } ?>
                    </div>
					<?php if ($style == 'info-map') { ?>
						<div class="info-map">
							<div id="map">
								<iframe loading="lazy" allowfullscreen referrerpolicy="no-referrer-when-downgrade" src="<?php echo($block['contactform_map']['map_url']); ?>"></iframe>
							</div>
						</div>
					<?php } ?>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
<?php } ?>
<!-- END CONTACT FORM --> 
