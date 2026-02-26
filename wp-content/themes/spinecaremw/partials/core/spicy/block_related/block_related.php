<?php
/*
Partial Name: block_related
*/
?>

<!-- BEGIN RELATED -->
<?php $block = $args; 
    $settings = $block['related_settings'];
    $classes = '';
	// echo('<pre>'); print_r($block); echo('</pre>');
    if ($block['related_display']['custom_class'] !== null) { 
        $class = $block['related_display']['custom_class'];
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

	$display = $block['related_config'];

	$template = 'none';
	if (isset($settings['post_template'])) {
		$template = $settings['post_template'];
	}

	$excerpt = true;
	if (!$display['excerpt']) {
		$excerpt = $display['excerpt'];
	}
	$displayblock = true;
	if (isset($block['related_display']['display_block']) && $block['related_display']['display_block'] != true) {
		$displayblock = false;
	}
	$columns = 4;
	if (isset($settings['columns'])) {
		$columns = $settings['columns'];
	} else {
		$columns = $display['columns']; // backwards compatibility
	}
	if ($columns == 4) {
		$articleclasses = 'col-12 col-md-6 col-lg-3';
	} elseif ($columns == 3) {
		$articleclasses = 'col-12 col-md-6 col-lg-4';
	} elseif ($columns == 2) {
		$articleclasses = 'col-12 col-md-6';
	} elseif ($columns == 1) {
		$articleclasses = 'col-12';
	}

	$displayin = 'rows';
	if (isset($settings['display_in'])) {
		$displayin = $settings['display_in'];
	}

	if ($displayin == 'carousel') { 
		$carouseldata = '';
		$carouseldata .= ' data-speed="7000"';
		$carouseldata .= ' data-columns="' . $columns . '"';
		$arrows = 'true';
		$carouseldata .= ' data-arrows="false"';
		$classes .= ' carousel';
	}
	// echo('<pre style="color: white; z-index: 1; position: relative;">'); print_r($carouseldata); echo('</pre>');
?>
<?php if ($displayblock) { ?>
<?php
	set_query_var('date', '0');
	set_query_var('excerpt', '1');
	set_query_var('columns', $columns);
	set_query_var('filters', $filterbarcats);
	set_query_var('displayin', $displayin);
?>
<?php
$related = $block['related_content'];
			
if ($settings['backfill']) {

	$backfillsettings = $block['related_backfill'];

	$posttypes = $GLOBALS['posttypes'];
	$backfillmode = 'pick';
	if (isset($backfillsettings['mode'])) {
		$backfillmode = $backfillsettings['mode'];
	} elseif (isset($settings['mode'])) {
		$backfillmode = $settings['mode']; // backwards compatability
	}
	
	if (isset($settings['total'])) {
		$totalitems = $settings['total'];
	} else {
		$totalitems = $backfillsettings['total']; // backwards compatibility
	}
	// $useposts = array_keys($backfillsettings['post_type']);
	$useposts = $backfillsettings['post_type'];
	
	global $post;
	if ($backfillmode == 'ancestor') {
		$ancestorurl = $block['related_ancestor']['child_of'];
		$ancestorid = url_to_postid($ancestorurl);
		$ancestors = get_post_ancestors($post->ID);
		if (array_search($ancestorid, $ancestors) > 0) {
			$pageid = $ancestors[array_search($ancestorid, $ancestors) - 1];
		} else {
			// if ancestorid is not an ancestor of the current page, fall back to the categories of this page
			$backfillmode = 'page';
			$pageid = $post->ID;
		}
	} else {
		$pageid = $post->ID;
	}
	$sort = $backfillsettings['sort'];

	$args = array(
		'numberposts' => -1,
		'post_type' => $useposts,
		'orderby' => 'date',
		'order' => 'DESC',
		'exclude' => array(get_the_id()),
		'tax_query' => array(
			'relation' => 'OR'
		)
	);

	if ($backfillmode == 'pick') {
		$categories = $backfillsettings['categories'];
		$tags = $backfillsettings['tags'];
		if ($categories) {
			$catlist = [];
			foreach ($categories as $category) {
				$catlist[] = $category->term_id;
			}
			$catlist = implode(', ', $catlist);
			// $args['cat'] = $catlist;
			$args['tax_query'][] = array(
				'taxonomy' => 'category',
				'field' => 'term_id',
				'terms' => $catlist,
				'include_children' => false
			);
		}

		if ($tags) {
			$taglist = [];
			foreach ($tags as $tag) {
				$taglist[] = $tag->term_id;
			}
			$taglist = implode(', ', $taglist);
			// $args['tag_id'] = $taglist;
			$args['tax_query'][] = array(
				'taxonomy' => 'post_tag',
				'field' => 'term_id',
				'terms' => $taglist
			);
		}
	} elseif ($backfillmode == 'page' || $backfillmode == 'ancestor') {
		$catlist = wp_get_post_categories($pageid);
		if ($catlist) {
			//$args['category__in'] = $catlist;
			$args['tax_query'][] = array(
				'taxonomy' => 'category',
				'field' => 'term_id',
				'terms' => $catlist,
				'include_children' => false
			);
		}
		$tagobjects = wp_get_post_tags($pageid);
		$taglist = [];
		foreach ($tags as $tag) {
			$taglist[] = $tag->term_id;
		}
		if ($taglist) {
			// $args['tag__in'] = $taglist;
			$args['tax_query'][] = array(
				'taxonomy' => 'post_tag',
				'field' => 'term_id',
				'terms' => $taglist
			);
		}
		
	}

	$backfill = get_posts($args);
	if ($sort == 'random') {
		shuffle($backfill);
	} elseif ($sort == 'weighted') {
		foreach($backfill as $o => $object) {
			$catsnum = count(wp_get_post_categories($object->ID));
			$tagsnum = count(wp_get_post_tags($object->ID));
			$backfill[$o]->termsnum = $catsnum + $tagsnum;
		}
		function cmp($a, $b) {
			return strcmp($a->termsnum, $b->termsnum);
		}
		usort($backfill, "cmp");
	}
	
//	echo('<pre>');
//	var_dump($backfill);
//	echo('</pre>');
	
	if (is_array($related)) {
		$itemsnum = count($related);
	} else {
		$related = [];
		$itemsnum = 0;
	}
	foreach($backfill as $k => $filler) {
		$found = false;
		foreach($related as $a => $article) {
			if ($filler->ID == $article->ID) {
				$found = true;
			}
		}
		if (!$found && $itemsnum < $totalitems) {
			$itemsnum ++;
			$related[] = $filler;
		}
	}

}
?>
<?php if ($displayin == 'carousel') { ?>
	<?php $mypath = substr(dirname(__FILE__), strpos(dirname(__FILE__), '/wp-content')); ?>
	<script type="text/javascript" src="<?php bloginfo('url'); ?><?php echo($mypath); ?>/block_related.js"></script>
<?php } ?>
<div class="block-related block padded<?php echo($classes); ?>"<?php if ($displayin == 'carousel') { echo($carouseldata); } ?>>
    <div class="container">
        <div class="row">
			<?php if (strlen($block['related_title']) > 0) { ?>
				<div class="col-12 title-container"><h2><?php echo($block['related_title']); ?></h2></div>
			<?php } ?>
			<div class="articles-container<?php if ($displayin == 'carousel') { ?> slides<?php } ?>">
            <?php foreach ($related as $page) { 
            	if ($template != 'none') {
            		get_template_part('partials/custom/_templates/_posts/' . $template, null, $page);
            	} else { ?>
					<div class="article-container <?php echo($articleclasses); ?>">
						<div class="article">
							<div class="image-container">
								<a href="<?php echo(get_permalink($page->ID)); ?>">
								<img src="<?php echo(get_field('excerpt_image', $page->ID)['url']); ?>"/></a>
							</div>
	                    	<div class="text-container">
								<h4><a href="<?php echo(get_permalink($page->ID)); ?>"><?php echo(get_field('excerpt_title',$page->ID)); ?></a></h4>
								<?php if ($excerpt) { ?><p><?php echo(get_field('excerpt_excerpt', $page->ID)); ?></p><?php } ?>
							</div>
							<div class="buttons-container">
								<a class="button primary small" href="<?php echo(get_permalink($page->ID)); ?>">Read more</a>
							</div>
						</div>
					</div>
            	<?php } ?>
            <?php } ?>
			</div>
        </div>
		<?php if ($arrows == 'true') { ?>
			<div class="arrows">
				<div class="prev"></div>
				<div class="dots"></div>
				<div class="next"></div>
			</div>
		<?php } ?>
    </div>
</div>
<?php } ?>
<!-- END RELATED --> 
