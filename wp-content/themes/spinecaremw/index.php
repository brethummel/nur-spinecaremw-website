<?php get_header(); ?>

<!-------------------------------- BEGIN CONTENT ------------------------------>
<?php

if (have_posts()) : the_post();

	if ($post->post_status == 'publish' || current_user_can('edit_posts') || $_GET['preview'] == 'true') {

		get_template_part('partials/_blocks', null, null);
		
	} else {
		require('404.php');
	}

else :

	require('404.php');

endif;

?>
<!-------------------------------- END CONTENT --------------------------------> 

<?php get_footer(); ?>