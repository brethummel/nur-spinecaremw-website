<?php
/**
 * The posts that ISC already found an image in
 *
 * @var int[] $image_posts_ids Post IDs
 */

foreach ( $image_posts_ids as $_post_id ) :
	// skip revisions
	if ( wp_is_post_revision( $_post_id ) ) {
		continue;
	}

	// get the post type readable title
	$_post_type        = get_post_type( $_post_id );
	$_post_type_object = get_post_type_object( $_post_type );
	$_post_type_title  = $_post_type_object->labels->singular_name ?? $_post_type;

	?>
	<p class="isc-found-in-image-posts-meta">
		<a href="<?php echo esc_url( get_permalink( $_post_id ) ); ?>" target="_blank"><?php echo esc_html( get_the_title( $_post_id ) ); ?></a>
		(<?php echo esc_html( $_post_type_title ); ?>)
	</p>
	<?php
endforeach;
