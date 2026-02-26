<?php
/*
Partial Name: block_posts
*/
?>

<!-- BEGIN POSTS -->
<?php $block = $args; 
    $settings = $block['posts_settings'];
	$display = $block['posts_config'];
	$return = $block['posts_return'];
    $classes = ''; 
    if ($block['posts_display']['custom_class'] !== null) { 
        $class = $block['posts_display']['custom_class'];
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

	$template = 'posts_singlet-default';
	if (isset($display['post_template'])) {
		$template = $display['post_template'];
	} elseif (isset($settings['template'])) { // backwards compatability
		$template = $settings['template'];
	}

	$filterbarcats = false;
	if (isset($settings['filter_bar']) && $settings['filter_bar']) {
		$filterbarcats = $block['posts_filters']['filter_by'];
	} elseif (isset($settings['use_filters']) && $settings['use_filters']) { // backwards compatability
		$filterbarcats = $block['posts_filters']['filter_by'];
	}

	$tags = false;
	if (($display['display'] == 'tags' || $display['display'] == 'both') && isset($return['tags']) && $return['tags']) {
		$tags = $return['tags'];
	} elseif (isset($settings['use_tags']) && $settings['use_tags']) {
		$tags = $block['posts_filters']['display_only'];
	}

	$cats = false;
	if (($display['display'] == 'cats' || $display['display'] == 'both') && isset($return['categories']) && $return['categories']) {
		$cats = $return['categories'];
	}

	$columns = 3;
	if (isset($settings['columns'])) {
		$columns = $settings['columns'];
	} elseif (isset($display['columns'])) { // backwards compatability
		$columns = $display['columns'];
	}

	$orderby = 'date';
	if (isset($display['orderby'])) {
		$orderby = $display['orderby'];
	}

	$order = 'DESC';
	if (isset($display['order'])) {
		$order = $display['order'];
	}
		
	$args = array(
		'post_type' => $display['post_types'],
		'post_status' => 'publish',
		'orderby' => $orderby,
		'order' => $order,
		'paged' => 1,
		'posts_per_page' => $settings['per_page'],
		'post_parent' => 0
	);
		
	if (isset($_GET['t'])) {
		$tag = $_GET['t'];
		$taglist = [$tag];
		$taglist = implode(', ', $taglist);
		$args['tag_id'] = $taglist;
	} elseif ($tags) {
		$taglist = implode(', ', $tags);
		$args['tag_id'] = $taglist;
	}
	// echo('<pre>'); print_r($taglist); echo('</pre>');
		
	if (isset($_GET['c'])) {
		$cat = get_category_by_slug($_GET['c']);
		$catlist = [$cat->term_id];
		$catlist = implode(', ', $catlist);
		$args['cat'] = $catlist;
	} elseif ($cats) {
		$catlist = implode(', ', $cats);
		$args['cat'] = $catlist;
	} 
	// echo('<pre style="color: white; z-index: 2; position: relative;">'); print_r($args); echo('</pre>');

	if (isset($_GET['sc'])) {
		$searchterms = explode(',', $_GET['sc']);
		$matchids = explode(',', $_GET['mi']);
		$args['posts_per_page'] = -1;
		$args['post__in'] = $matchids;
		$args['orderby'] = 'post__in';
		unset($args['order']);
	}

	$results = get_posts($args);

	// echo('<pre>'); print_r($results); echo('</pre>');

	$behavior = 'scroll';
	if (isset($block['posts_behavior']['behavior'])) {
		$behavior = $block['posts_behavior']['behavior'];
	}
	$classes .= ' ' . $behavior;

	$sidebar = false;
	if (isset($block['posts_settings']['sidebar'])) {
		$sidebar = $block['posts_settings']['sidebar'];
	}
	$classes .= ' sidebar';

	$displayblock = true;
	if (isset($block['posts_display']['display_block']) && $block['posts_display']['display_block'] != true) {
		$displayblock = false;
	}

?>
<?php if ($displayblock) { ?>
<?php
	set_query_var('date', '0');
	set_query_var('excerpt', '1');
	set_query_var('columns', $columns);
	set_query_var('filters', $filterbarcats);
?>
<?php $mypath = substr(dirname(__FILE__), strpos(dirname(__FILE__), '/wp-content')); ?>
<script type="text/javascript" src="<?php bloginfo('url'); ?><?php echo($mypath); ?>/block_posts.js"></script>
<div id="posts-<?php echo(bin2hex(random_bytes(4))); ?>" class="block-posts block padded<?php echo($classes); ?>">
    <div class="container">
        <div class="row">
			<?php $allposts = $block['posts_behavior']['posts_page']; ?>
        	<?php if (isset($_GET['t'])) { ?>
        		<div class="col-12 active-tags">
        			<?php $taglist = explode(',', $taglist); $activetags = ''; ?>
        			<?php foreach ($taglist as $t => $tag) {
						$tag = get_tag($tag);
						if ($behavior == 'fixed') {
							$link = $allposts . '?t=' . $tag->term_id;
						} else {
							$link = get_the_permalink() . '?t=' . $tag->term_id;
						}
        				$activetags .= '<a href="' . $link . '">#' . $tag->slug . '</a>';
        				if ($t < (count($taglist) - 1)) {
        					$activetags .= ' or ';
        				}
        			} ?>
        			<h3><span class="count"><?php echo(count($results)); ?></span> tagged <?php echo($activetags); ?><span class="reset">|&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?php the_permalink(); ?>">reset</a></span></h3>
        		</div>
        	<?php } ?>
        	<?php if (isset($_GET['sc'])) { ?>
        		<div class="col-12 active-tags">
        			<h3><?php echo(count($matchids)); ?> results: <?php echo(implode(' ', $searchterms)); ?><span class="reset">|&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?php the_permalink(); ?>">reset</a></span></h3>
        		</div>
        	<?php } ?>
			<div class="col-12 no-results<?php if (!$results) { ?> show<?php } ?>"><p>Sorry, no results were found.</p></div>
			<?php if ($sidebar && (in_array('search', $block['posts_filters']['include']) || in_array('tags', $block['posts_filters']['include']))) { ?>
				<div class="sidebar col-12 col-md-4 col-lg-3 order-2 order-md-3">
					<?php if (in_array('search', $block['posts_filters']['include'])) { ?>
						<div class="search-bar" data-searchfields="<?php echo($block['posts_filters']['search_fields']); ?>">
							<span class="icon"></span><input type="search" id="search" name="search-bar"<?php if (isset($searchterms)) { ?> value="<?php echo(implode(' ', $searchterms)); ?><?php } ?>"/>
							<div class="search-results">
								<div class="loading"><img src="<?php bloginfo('stylesheet_directory'); ?>/images/spinner_white.gif" alt="loading..."/></div>
								<ul>
								</ul>
								<?php if ($behavior == 'fixed') {
									$url = $allposts . '?sc=';
								} else {
									$url = get_the_permalink() . '?sc=';
								} ?>
								<p class="no-results">No results were found.</p>
								<p class="all-results"><a href="<?php echo($url); ?>">View All</a></p>
							</div>
						</div>
					<?php } ?>
					<?php if (in_array('tags', $block['posts_filters']['include'])) { ?>
					<div class="tag-cloud">
						<?php $cloud = get_tags_in_use($display['post_types'], $catlist, 'id'); ?>
						<?php if (count($cloud) > 0) { ?>
							<h4><?php echo($block['posts_filters']['tags_list_title']); ?></h4>
							<ul class="tags">
								<?php foreach ($cloud as $tag) { ?>
									<?php $tag = get_tag($tag); ?>
									<?php if ($behavior == 'fixed') {
										$link = $allposts . '?t=' . $tag->term_id;
									} else {
										$link = get_the_permalink() . '?t=' . $tag->term_id;
									} ?>
									<li <?php if (isset($_GET['t']) && in_array($tag->term_id, $taglist)) { ?>class="current" <?php } ?>data-id="<?php echo($tag->term_id); ?>" data-slug="<?php echo($tag->slug); ?>"><a href="<?php echo($link); ?>"><?php echo($tag->name); ?></a></li>
								<?php } ?>
								<?php if (is_array($taglist)) {
									$taglist = implode(',', $taglist);
								} ?>
							</ul>
						<?php } ?>
					</div>
					<?php } ?>
				</div>
			<?php } ?>
			<div class="posts-container<?php if ($sidebar) { ?> col-12 col-md-8 col-lg-9 order-3 order-md-2<?php } ?><?php if ($columns == 1) { ?> no-flex<?php } ?>" data-per-page="<?php echo($settings['per_page']); ?>" data-post-type="<?php echo(implode(', ', $display['post_types'])); ?>" data-template="<?php echo($template); ?>" data-date="0" data-excerpt="1" data-columns="<?php echo($columns); ?>" data-cats="<?php echo($catlist); ?>"  data-tags="<?php echo($taglist); ?>" data-orderby="<?php echo($orderby); ?>" data-order="<?php echo($order); ?>"<?php if ($filterbarcats) { ?> data-filters="<?php echo(implode(', ', $filterbarcats)); ?>"<?php } ?> data-current-page="1"<?php if (isset($_GET['sc'])) { ?> data-searchterms="<?php echo(implode(',', $searchterms)); ?>"<?php } ?>>
				<?php if ($results) { ?>
					<?php foreach ($results as $result) { 
						get_template_part('partials/custom/_templates/_posts/' . $template, null, $result);
						$catmap = get_query_var('catmap');
					} ?>
				<?php } else {
					get_template_part('partials/custom/_templates/_posts/' . $template, null, null);
					$catmap = get_query_var('catmap');
				} ?>
				<?php if ($behavior == 'scroll') { ?>
					<div class="col-12 load-more<?php if (!$results) { ?> hide<?php } ?>"><p><img src="<?php bloginfo('stylesheet_directory'); ?>/images/spinner_<?php echo(substr($background[0], 6)); ?>.gif" alt="loading..."/></p></div>
				<?php } elseif ($behavior == 'fixed') { ?>
					<div class="col-12 view-all"><a href="<?php echo($allposts); ?>"><?php echo($block['posts_behavior']['cta']); ?></a></div>
				<?php } ?>
			</div>
			<?php if ($filterbarcats) { ?>
				<?php if (isset($_GET['c'])) { 
					$active = array($_GET['c']);
					$viewing = 'some';
				} else { 
					$active = [];
					foreach ($filterbarcats as $filter) {
						$cat = get_category($filter);
						$active[] = $cat->slug;
					}
					$viewing = 'all';
				} ?>
				<div class="col-12 order-first filter-row">
					<?php if ($viewing == 'all') { 
						$vhead = "All Resources";
					} else {
						$vhead = $catmap[$active[0]]['filter'];
					} ?>
					<h2 class="viewing"><?php echo($vhead); ?></h2>
					<div class="filters" data-active="<?php echo(implode(', ', $active)); ?>" data-viewing="<?php echo($viewing); ?>">
						<ul><?php foreach ($filterbarcats as $filter) {
							$cat = get_category($filter); ?>
							<li id="<?php echo($cat->slug); ?>" class="<?php if (in_array($cat->slug, $active) && $viewing != 'all') { echo(' active'); } ?>" data-cat="<?php echo($filter); ?>"><?php echo($catmap[$cat->slug]['filter']); ?></li>
						<?php } ?></ul>
					</div>
				</div>
			<?php } ?>
        </div>
    </div>
</div>
<?php } ?>
<!-- END POSTS --> 