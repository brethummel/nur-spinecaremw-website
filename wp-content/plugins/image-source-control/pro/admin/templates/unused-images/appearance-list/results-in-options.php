<?php
/**
 * A list of options in which option_value an image was found
 *
 * @var object $options list of WP_Post objects
 */
?>
<ul>
	<?php foreach ( $options as $option ) : ?>
		<li>
			<?php esc_html_e( 'Option', 'image-source-control-isc' ); ?>:
			<?php
			echo esc_html( $option->option_name );
			if ( isset( $option->search_type ) && $option->search_type === 'id' ) :
				?>
			(<code title="<?php esc_html_e( 'Attachment ID', 'image-source-control-isc' ); ?>">ID</code>)
				<?php
			endif;
			?>
		</li>
	<?php endforeach; ?>
</ul>

