<?php get_header(); ?>

<!-------------------------------- BEGIN SINGLE CONTENT ------------------------------>
<?php

if (have_posts()) : the_post();

	// If you want to append blocks from another page, create a $blocks array with
	// a key for 'before' and 'after', with the page to pull from and the blocks you
	// want to include, index begins with 1, not 0 in this case

//	$blocks = array(
//		'before' => array(
//			'ID' => 100, // page ID
//			'blocks' => $afterblocks // which blocks (index begins with 1, not 0)
//		),
//		'after' => array(
//			'ID' => 100, // page ID
//			'blocks' => $afterblocks // which blocks (index begins with 1, not 0)
//		),
//	);

	if ($post->post_status == 'publish' || current_user_can('edit_posts') || $_GET['preview'] == 'true') {
		if (isset($blocks['before']) || isset($blocks['after'])) {
			get_template_part('partials/_blocks', null, $blocks);
		} else {
			get_template_part('partials/_blocks', null, null);
		}
	} else {
		require('404.php');
	}

else :

	require('404.php');

endif;

?>
<!-------------------------------- END SINGLE CONTENT --------------------------------> 

<?php get_footer(); ?>