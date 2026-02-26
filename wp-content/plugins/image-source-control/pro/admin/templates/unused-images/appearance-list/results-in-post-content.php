<?php
/**
 * A list of posts in which an image was found
 *
 * @var object $posts list of WP_Post objects
 */

?>
<ul>
	<?php foreach ( $posts as $_post ) : ?>
		<?php $_post_type_object = get_post_type_object( $_post->post_type ); ?>
		<li>
			<a href="<?php echo esc_url( get_permalink( $_post->ID ) ); ?>" target="_blank"><?php echo esc_html( $_post->post_title ); ?></a>
			<?php
			echo '(';
			echo esc_html( $_post_type_object->labels->singular_name ?? $_post->post_type );
			if ( isset( $_post->search_type ) && $_post->search_type === 'id' ) :
				?>
				, <code title="<?php esc_html_e( 'Attachment ID', 'image-source-control-isc' ); ?>">ID</code>
				<?php
			endif;
			echo ')';
			?>
		</li>
	<?php endforeach; ?>
</ul>

