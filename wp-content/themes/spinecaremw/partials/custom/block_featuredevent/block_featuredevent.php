	<?php
/*
Partial Name: block_hero
*/
?>

<!-- BEGIN HERO -->
<?php $block = $args; 
	$settings = $block['featuredevent_settings'];
    $classes = ''; 
    $style = 'page-title';
    $classes .= ' ' . $style;
    if ($block['featuredevent_display']['custom_class'] !== null) { 
        $class = $block['featuredevent_display']['custom_class'];
		if (strlen($class) > 0) {
			$classes .= ' ' . $class;
		}
	}
    if (isset($block['featuredevent_title']) && strlen($block['featuredevent_title']) > 0) {
        $title = $block['featuredevent_title'];
    } else {
    	$title = "no title";
    }

	$eventid = $block['featuredevent_event']['pick'];
    $blocks = get_field('content_blocks', $eventid);
    foreach ($blocks as $block) {
		if ($block['acf_fc_layout'] == 'block_eventinfo') {
			$eventinfo = $block['eventinfo_eventinfo'];
			$eventdate = DateTime::createFromFormat('M j', $eventinfo['date']);
			$date_now = date('Y-m-d H:i:s');
			if ($eventinfo['date'] != '') {
				if ($eventdate->format('Y-m-d H:i:s') <= $date_now) { // date is in the past
					$mycats = get_the_category($eventid);
					$catlist = [];
					foreach ($mycats as $cat) {
						$catlist[] = $cat->term_id;
					}
					$catlist = implode(', ', $catlist);

					// get fallback event 
					$args = array(
						'post_type' => 'event',
						'post_status' => 'publish',
						'orderby' => 'rand',
						'paged' => 1,
						'posts_per_page' => 1,
						'exclude' => array($eventid),
						'post_parent' => 0,
						'cat' => $catlist
					);

					$event = get_posts($args);
					$eventid = $event[0]->ID;
					$eventinfo = array(
						'date' => '',
						'time' => ''
					);
					// echo('<pre>'); print_r($event[0]->ID); echo('</pre>');

				}
			}
			$myimage = get_field('excerpt_image', $eventid)['url'];
			$mytitle = get_field('excerpt_title', $eventid);
			$myexcerpt = '<p>' . get_field('excerpt_excerpt', $eventid) . '</p>';
			$link = get_permalink($eventid);
			// echo('<pre>'); print_r($myexcerpt); echo('</pre>');
		}
    }
	// echo('<pre>'); print_r($event); echo('</pre>');



	$displayblock = true;
	if (isset($block['featuredevent_display']['display_block']) && $block['featuredevent_display']['display_block'] != true) {
		$displayblock = false;
	}
?>
<?php if ($displayblock) { ?>
<div class="block-hero block-featuredevent block<?php echo($classes); ?>">
    <div class="title-container">
		<div class="container">
			<div class="row">
				<div class="title col-12">
					<div class="text-container">
						<h1><?php echo($title); ?></h1>
					</div>
				</div>
			</div>
		</div>
    </div>
    <div class="posts-container no-flex">
	    <div class="container">
	        <div class="row">
				<div class="post-container col-12 event">
					<div class="post col-12">
						<?php if (isset($myimage) && $myimage) { ?>
							<div class="image-container">
								<a href="<?php echo($link); ?>"<?php if (isset($target)) { ?> target="<?php echo($target); ?>"<?php } ?>>
								<div class="image" style="background-image: url(<?php echo($myimage); ?>);"></div>
								</a>
							</div>
						<?php } ?>
						<div class="text-container">
							<?php if ($eventinfo['date']) { ?>
								<p class="date"><?php echo($eventinfo['date']); ?>, <?php echo($eventinfo['time']); ?></p>
							<?php } else { ?>
								<p class="webinar">On-Demand</p>
							<?php } ?>
							<?php if ($mytitle) { ?>
								<h4><a href="<?php echo($link); ?>"<?php if (isset($target)) { ?> target="<?php echo($target); ?>"<?php } ?>><?php echo($mytitle); ?></a></h4>
							<?php } ?>
							<?php if ($myexcerpt) { ?><?php echo($myexcerpt); ?><?php } ?>
							<p class="read-more"><a class="button medium primary" href="<?php echo($link); ?>"<?php if (isset($target)) { ?> target="<?php echo($target); ?>"<?php } ?>></a></p>
						</div>
					</div>
				</div>
	        </div>
	    </div>
	</div>
</div>
<?php } ?>
<!-- END HERO --> 
