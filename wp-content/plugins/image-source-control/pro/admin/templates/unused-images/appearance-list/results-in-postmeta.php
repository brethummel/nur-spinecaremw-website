<?php
/**
 * A list of posts in which postmeta an image was found
 *
 * @var object $postmetas list of WP_Post objects
 */
?>
<ul>
	<?php foreach ( $postmetas as $postmeta ) : ?>
		<li>
			<?php esc_html_e( 'Post meta key', 'image-source-control-isc' ); ?>:
			<?php echo esc_html( $postmeta->meta_key ); ?>,
			<?php
			$edit_post_link = get_edit_post_link( $postmeta->post_id );
			if ( $edit_post_link ) :
				?>
				<a href="<?php echo esc_url( $edit_post_link ); ?>" target="_blank"><?php echo esc_html( get_the_title( $postmeta->post_id ) ); ?></a>
			<?php else : ?>
				(<?php echo (int) $postmeta->post_id; ?>)
			<?php endif; ?>
		</li>
	<?php endforeach; ?>
</ul>

