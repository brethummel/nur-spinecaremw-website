<?php
/*
Partial Name: block_alltreatments
*/
?>

<!-- BEGIN ALL TREATMENTS -->
<?php $block = $args; 
    $classes = ''; 
    $settings = $block['alltreatments_settings'];
    if ($block['alltreatments_display']['custom_class'] !== null) { 
        $class = $block['alltreatments_display']['custom_class'];
		if (strlen($class) > 0) {
			$classes .= ' ' . $class;
		}
	}
	$displayblock = true;
	if (isset($block['alltreatments_display']['display_block']) && $block['alltreatments_display']['display_block'] != true) {
		$displayblock = false;
	}
?>
<?php if ($displayblock) { ?>
<?php $mypath = substr(dirname(__FILE__), strpos(dirname(__FILE__), '/wp-content')); ?>
<script type="text/javascript" src="<?php bloginfo('url'); ?><?php echo($mypath); ?>/block_alltreatments.js"></script>
<div class="block-alltreatments block padded <?php echo($classes); ?>">
	<div class="container">
		<div class="row">
			<?php $which = $settings['display']; ?>
			<?php $myid = get_page_by_path($which)->ID; ?>
    		<?php
			    $args = array(
			        'post_type' => 'page',
			        'post_status' => 'publish',
			        'orderby' => 'menu_order',
			        'order' => 'ASC',
			        'numberposts' => -1,
			        'post_parent' => $myid
			    );
			    $categories = get_posts($args);
			    $all = [];
			    foreach ($categories as $i => $cat) {
			    	$mychildren = get_children(array(
			    		'post_parent' => $cat->ID,
				        'post_status' => 'publish',
				        'orderby' => 'menu_order',
				        'order' => 'ASC',
			    	));
			    	$all[$i]['category'] = $cat;
			    	$all[$i]['children'] = $mychildren;
			    	// echo('<pre>'); print_r($mychildren); echo('</pre>');
			    }
			    // echo('<pre>'); print_r($all); echo('</pre>');
    		?>
			<?php foreach ($all as $c => $cat) { ?>
				<div class="category-container col-12 col-md-6 col-lg-4 <?php echo($cat['category']->post_name); ?><?php if ($c == 0) { ?> open<?php } ?>">
					<div class="category">
						<h3><?php echo($cat['category']->post_title); ?></h3>
						<ul class="conditions">
							<?php foreach($cat['children'] as $condition) { ?>
								<li><a href="<?php bloginfo('url'); ?>/<?php echo($me); ?>/<?php echo($cat['category']->post_name); ?>/<?php echo($condition->post_name); ?>/"><?php echo($condition->post_title); ?></a></li>
							<?php } ?>
						</ul>
					</div>
				</div>
			<?php } ?>
		</div>
	</div>
</div>
<?php } ?>
<!-- END EGIN ALL TREATMENTS --> 
