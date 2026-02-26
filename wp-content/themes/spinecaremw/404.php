<?php get_header(); ?>

<!-------------------------------- BEGIN 404 CONTENT ------------------------------>
<?php

global $wpdb;
$id404 = $wpdb->get_var($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE post_title LIKE %s AND post_status = %s", '%404%', 'publish'));

if ($id404) {
	global $post;
	$post = get_post($id404, OBJECT);
	setup_postdata($post);
	get_template_part('partials/_blocks', null, null);
} else { ?>
	<div class="block padded">
		<div class="container">
			<div class="row">
				<div class="col-12">
					<h2>Page not found!</h2>
					<p>The page you were looking for appears to have been moved, deleted or simply never existed in the first place.</p><p>Perhaps try the <a href="<?php bloginfo('url'); ?>">home page</a>?</p>
				</div>
			</div>
		</div>
	</div>
<?php }

?>

<!-------------------------------- END 404 CONTENT --------------------------------> 

<?php get_footer(); ?>