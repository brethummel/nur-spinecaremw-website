<?php
/*
Partial Name: block_eventinfo
*/
?>

<!-- BEGIN EVENT INFO -->
<?php $block = $args; 
    $settings = $block['eventinfo_settings'];
    $imagecol = $block['eventinfo_contenttable']['imagecol'];
    $contentcol = $block['eventinfo_contenttable']['contentcol'];
    $classes = ''; 
	$imageclasses = ''; 
    if (isset($settings['background'])) { 
        $background = explode("|", $settings['background']);
        foreach ($background as $value) {
            $classes .= ' ' . $value;
        }
    }
    $style = 'image';
    $classes .= ' ' . $style;
    $image = $imagecol['image'];
    $imagestyle = $image['style'];
    $classes .= ' ' . $imagestyle;		
	$anchor = 'middle';
	if (isset($image['anchor'])) { $anchor = $image['anchor']; }
	$imageclasses = ' ' . $anchor;

	$split = 'equal';
	$classes .= ' ' . $split;
	$columnstyles = array('col-12 col-lg-6', 'col-12 col-lg-6');
	$columnpercents = array('fifty', 'fifty');

    if ($block['eventinfo_display']['custom_class'] !== null) { 
        $class = $block['eventinfo_display']['custom_class'];
		if (strlen($class) > 0) {
			$classes .= ' ' . $class;
		}
	}

    $orientation = 'right';
    $classes .= ' image-' . $orientation; 
	$imageclasses .= ' ' . $columnstyles[1];
	$fullimageclass .= ' ' . $columnpercents[1];
	$textclasses = $columnstyles[0];

	$displayblock = true;
	if (isset($block['eventinfo_display']['display_block']) && $block['eventinfo_display']['display_block'] != true) {
		$displayblock = false;
	}
?>
<?php if ($displayblock) { ?>
<?php $mypath = substr(dirname(__FILE__), strpos(dirname(__FILE__), '/wp-content')); ?>
<div class="block-eventinfo block padded<?php echo($classes); ?>">
    <div class="container">
        <div class="row">
            <div class="image-col<?php echo($imageclasses); ?> <?php echo($imagestyle); ?><?php if ($orientation == 'right') { ?> order-lg-last<?php } ?>">
                <div class="image-container">
					<img class="img-fluid" src="<?php echo($imagecol['image']['image']['url']); ?>" alt="<?php echo($imagecol['image']['image']['alt']); ?>"/>
				</div>
				<?php if ($settings['captions'] && isset($imagecol['image']['caption'])) { ?><p class="caption"><?php echo($imagecol['image']['caption']); ?></p><?php } ?>
            </div>
            <div class="text-col <?php echo($textclasses); ?><?php if ($orientation == 'right') { ?> order-lg-first<?php } ?> vert-center">
				<div class="content-container">
					<?php if (isset($block['eventinfo_eventinfo']['date']) && $block['eventinfo_eventinfo']['time']) { ?>
						<p class="date-time"><?php echo($block['eventinfo_eventinfo']['date']); ?>,&nbsp;<?php echo($block['eventinfo_eventinfo']['time']); ?> CST</p>
					<?php } ?>
					<?php echo($contentcol['content']); ?>
				</div>
            </div>
        </div>
    </div>
</div>
<?php } ?>
<!-- END EVENT INFO --> 
