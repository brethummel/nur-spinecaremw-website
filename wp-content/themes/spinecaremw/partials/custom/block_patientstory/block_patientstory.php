<?php
/*
Partial Name: block_patientstory
*/
?>

<!-- BEGIN HERO -->
<?php $block = $args; 
    $classes = ''; 
    if ($block['patientstory_display']['custom_class'] !== null) { 
        $class = $block['patientstory_display']['custom_class'];
		if (strlen($class) > 0) {
			$classes .= ' ' . $class;
		}
	}
	$displayblock = true;
	if (isset($block['patientstory_display']['display_block']) && $block['patientstory_display']['display_block'] != true) {
		$displayblock = false;
	}
?>
<?php if ($displayblock) { ?>
<div class="block-patientstory block padded <?php echo($classes); ?>">
	<div class="container">
		<div class="row">
			<div class="image-col col-12 col-lg-5">
					<?php $image = $block['patientstory_content']['image']; ?>
				<div class="image-container"><img class="img-fluid" src="<?php echo($image['image']['url']); ?>" alt="<?php echo($image['image']['alt']); ?>"/></div>
			</div> 
			<div class="content-col col-12 col-lg-7">
				<div class="content-container">
					<?php $content = $block['patientstory_content']['content']; ?>
					<h5>Real people, real stories</h5>
					<h4><?php echo($content['quote']); ?></h4>
					<?php echo($content['content']); ?>
					<?php if (isset($block['patientstory_content']['story'])) { ?>
						<a href="<?php echo($block['patientstory_content']['story']); ?>">Read More</a>
					<?php } ?>
				</div>
			</div>
		</div>
	</div>
</div>
<?php } ?>
<!-- END HERO --> 
