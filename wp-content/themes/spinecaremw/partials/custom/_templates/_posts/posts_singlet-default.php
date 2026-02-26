<?php
/*
Template Name: Default
Partial Name: singlet_post-default
*/
?>
<?php $result = $args; 
	$date = get_query_var('date');
	$excerpt = get_query_var('excerpt');
	$columns = get_query_var('columns');
	$filters = get_query_var('filters');
	$displayin = get_query_var('displayin');
	$classes = '';
	if (isset($displayin) && $displayin == 'carousel') {
		$classes .= ' col-12';
	} else {
		if ($columns == 4) {
			$classes .= ' col-12 col-md-6 col-lg-3';
		} elseif ($columns == 3) {
			$classes .= ' col-12 col-md-6 col-lg-4';
		} elseif ($columns == 2) {
			$classes .= ' col-12 col-md-6';
		} elseif ($columns == 1) {
			$classes .= ' col-12';
		}
	}

	$type = $GLOBALS['posttypes'][get_post_type($result->ID)];
	// foreach ($GLOBALS['posttypes'] as $t => $type) {
	if (array_key_exists('page', $GLOBALS['posttypes'])) {
		if (in_array('excerpts', $type)) { // backwards compatability
			// echo('<pre>'); echo('I am in the "excerpts" conditional!<br/>'); echo('</pre>');
			$method = 'excerpts';
			$myimage = get_field('excerpt_image', $result->ID)['url'];
			$mytitle = get_field('excerpt_title', $result->ID);
			$myexcerpt = '<p>' . get_field('excerpt_excerpt', $result->ID) . '</p>';
		} elseif (in_array('post_title', $type)) {
			// echo('<pre>'); echo('I am in the "post_title" conditional!<br/>'); echo('</pre>');
			$method = 'post_title';
			$myimage = false;
			$mytitle = get_the_title($result->ID);
			$myexcerpt = false;
		} else {
			// echo('<pre>'); echo('I am in the "array" conditional!<br/>'); echo('</pre>');
			foreach ($type as $term) {
				if (gettype($term) == 'array') {
					$method = 'array';
					if (in_array('image', $term) || in_array('title', $term) || in_array('excerpt', $term) || in_array('snippet', $term)) {
						if (in_array('image', $term)) {
							$myimage = get_field('excerpt_image', $result->ID)['url'];
						} else {
							$myimage = false;
						}
						if (in_array('title', $term)) {
							$mytitle = get_field('excerpt_title', $result->ID);
							$mycats = get_the_category($result->ID);
							foreach ($mycats as $cat) {
								if ($cat->slug == '5-minutes-with') {
									$myjobtitle = get_field('excerpt_jobtitle', $result->ID);
									$mytitle .= '<span class="title">' . $myjobtitle . '</span>';
								}
							}
						} else {
							$mytitle = false;
						}
						if (in_array('excerpt', $term)) {
							$myexcerpt = '<p>' . get_field('excerpt_excerpt', $result->ID) . '</p>';
						} elseif (in_array('snippet', $term)) {
							$blocks = get_field('content_blocks', $result->ID);
							// echo('<pre>'); print_r($blocks); echo('</pre>');
							$snippet = '';
							foreach ($blocks as $block) {
								if ($block['acf_fc_layout'] == 'block_text') {
									$snippet .= $block['text_content'] . $block['text_content2'];
								} elseif ($block['acf_fc_layout'] == 'block_textimage') {
									$snippet .= $block['textimage_content'];
								}
							}
							$snippet = preg_replace("/\<h1(.*)\>(.*)\<\/h1\>/","", $snippet);
							$snippet = preg_replace("/\<h2(.*)\>(.*)\<\/h2\>/","", $snippet);
							$snippet = preg_replace("/\<h3(.*)\>(.*)\<\/h3\>/","", $snippet);
							$snippet = preg_replace("/\<h4(.*)\>(.*)\<\/h4\>/","", $snippet);
							$snippet = preg_replace("/\<h5(.*)\>(.*)\<\/h5\>/","", $snippet);
							$snippet = preg_replace("/\<h6(.*)\>(.*)\<\/h6\>/","", $snippet);
							$snippet = str_replace('em>', 'p>', $snippet);
							$snippet = implode(' ', array_slice(explode(' ', substr($snippet, 0, 500)), 0, 44));
							$myexcerpt = '<p class="snippet">' . strip_tags($snippet) . '</p>';
						} else {
							$myexcerpt = false;
						}
					}
				}
			}
		}
	} else { // backwards compatability
		// echo('<pre>'); echo('I am in the "else" backwards compatability conditional!<br/>'); echo('</pre>');
		$postobj = get_post_type_object($type);
		$usetypes[$type] = $postobj->labels->name;
		$method = 'post_title';
		$myimage = get_field('excerpt_image', $result->ID)['url'];
		$mytitle = get_field('excerpt_title', $result->ID);
		$myexcerpt = '<p>' . get_field('excerpt_excerpt', $result->ID) . '</p>';
	}

	// echo('<pre>'); echo('myimage = ' . $myimage . '<br/>'); echo('</pre>');
	// echo('<pre>'); echo('mytitle = ' . $mytitle . '<br/>'); echo('</pre>');
	// echo('<pre>'); echo('myexcerpt = ' . $myexcerpt . '<br/>'); echo('</pre>');
	
	if ($result && $filters) {
		$catids = wp_get_post_categories($result->ID); 
		$cats = '';
		foreach ($catids as $i => $id) {
			$cat = get_category($id);
			$cats .= $cat->slug . ',';
			if ($i == 0) { $mycat = $cat->slug; } 
		} 
		$cats = rtrim($cats, ', '); 
	}
	
	$catmap = array(
		'blog' => array('id' => '3', 'label' => 'Blog Post', 'filter' => 'Blog Posts'),
		'news' => array('id' => '4', 'label' => 'In the News', 'filter' => 'News'),
		'video' => array('id' => '5', 'label' => 'Video', 'filter' => 'Videos'),
		'download' => array('id' => '6', 'label' => 'Download', 'filter' => 'Downloads'),
		'solutions' => array('id' => '10', 'label' => 'Solution Brief', 'filter' => 'Solution Briefs'),
	);
	set_query_var('catmap', $catmap);

?>
<?php if ($result) { ?>
<?php if (isset($_POST['page'])) {
	$pagenum = $_POST['page'];
} else {
	$pagenum = 1;
} ?>

<!-- BEGIN POST SINGLET -->
<div class="post-container<?php echo($classes); ?><?php if ($filters) { echo(' ' . $mycat); }?><?php if ($pagenum > 1) { ?> just-loaded<?php } ?>"<?php if ($filters) { ?> data-cats="<?php echo($cats); ?>" <?php } ?> data-page="<?php echo($pagenum); ?>">
	<div class="post col-12">
		<a href="<?php echo(get_permalink($result->ID)); ?>"></a>
		<?php if (isset($myimage) && $myimage && $columns != 1) { ?>
			<div class="image-container">
				<a href="<?php echo(get_permalink($result->ID)); ?>">
				<div class="image" style="background-image: url(<?php echo($myimage); ?>);"></div>
				</a>
			</div>
		<?php } ?>
		<div class="text-container">
			<?php if ($filters) { ?><p class="type"><?php echo($catmap[$mycat]['label']); ?></p><?php } ?>
			<?php if ($mytitle) { ?>
				<h4><a href="<?php echo(get_permalink($result->ID)); ?>"><?php echo($mytitle); ?></a></h4>
			<?php } ?>
			<?php if ($date) { ?><p class="date"><?php echo(get_the_date('M j, Y', $result->ID)); ?></p><?php } ?>
			<?php if ($excerpt && $myexcerpt) { ?><?php echo($myexcerpt); ?><?php } ?>
			<p class="read-more"><a class="button medium primary" href="<?php echo(get_permalink($result->ID)); ?>">Learn More</a></p>
		</div>
	</div>
</div>
<!-- END POST SINGLET -->
<?php } ?>