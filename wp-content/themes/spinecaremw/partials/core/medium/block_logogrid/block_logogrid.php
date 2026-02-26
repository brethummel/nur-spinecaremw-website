<?php
/*
Partial Name: block_logogrid
*/
?>

<!-- BEGIN LOGO GRID -->
<?php $block = $args; 
    $settings = $block['logogrid_settings'];
    $classes = '';
    if (isset($settings['background'])) { 
        $background = explode("|", $settings['background']);
        foreach ($background as $value) {
            $classes .= ' ' . $value;
        }
    }
    if (get_sub_field('custom_class') !== null) { 
        $class = get_sub_field('custom_class');
		if (strlen($class) > 0) {
        	$classes .= ' ' . $class;
		}
    }
	$columnnum = intval($settings['columns']);
	$columns = $columnnum;
	if ($columnnum == 5) {
		$columnnum = 'col-lg-fifths';
	} elseif ($columnnum == 8) {
		$columnnum = 'col-lg-eighths';
	} else {
		$columnnum = 'col-lg-' . (12/$columnnum);
	}
	$logoclasses = 'col-6 col-md-3 ' . $columnnum;
    $logos = $block['logogrid_logos'];
?>
<div class="block-logogrid block padded <?php echo('columns-' . $columns); ?><?php echo($classes); ?>">
    <div class="container">
        <div class="row justify-content-center">
			<?php foreach ($logos as $logo) { ?>
				<div class="logo-container <?php echo($logoclasses); ?>">
					<?php $logosettings = $logo['settings'];
						if ($settings['include_label']) {
							$label = $logosettings['label'];
						}
						if ($logosettings['link'] == 'internal') {
							$blank = false;
							$link = $logosettings['page'];
						} elseif ($logosettings['link'] == 'external') {
							$blank = true;
							$link = $logosettings['url'];
						} else {
							unset($link);
						}
					?>
					<div class="logo">
						<?php if (isset($link)) { ?><a href="<?php echo($link); ?>"<?php if ($blank) { ?> target="_blank"<?php } ?>><?php } ?>
							<img src="<?php echo($logo['logo']['url']); ?>" alt="<?php echo($logo['logo']['alt']); ?>">
							<?php if (isset($label)) { ?><p><?php echo($label); ?></p><?php } ?>
						<?php if (isset($link)) { ?></a><?php } ?>
					</div>
				</div>
			<?php } ?>
        </div>
    </div>
</div>
<!-- END LOGO GRID --> 
