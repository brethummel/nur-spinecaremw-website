<?php
/**
 * The post to which an image was uploaded
 *
 * @var WP_Post $post WP_Post object
 * @var string $post_type_name Post type name
 */
?>
<p>
	<?php esc_html_e( 'Uploaded to', 'image-source-control-isc' ); ?>:
	<a href="<?php echo esc_url( get_permalink( $post->ID ) ); ?>" target="_blank"><?php echo esc_html( $post->post_title ); ?></a>
	(<?php echo esc_html( $post_type_name ); ?>)
</p>

