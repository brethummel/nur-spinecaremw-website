<?php
/**
 * A list of users in whose meta data an image was found
 *
 * @var object[] $usermetas List of user meta records.
 */

?>
<ul>
	<?php foreach ( $usermetas as $meta ) : ?>
		<li>
			<?php esc_html_e( 'User meta key', 'image-source-control-isc' ); ?>:
			<?php echo esc_html( $meta->meta_key ); ?>,
			<?php
			$edit_user_link = get_edit_user_link( $meta->user_id );
			if ( $edit_user_link ) :
				?>
					<a href="<?php echo esc_url( $edit_user_link ); ?>" target="_blank"><?php echo esc_html( $meta->user_name ); ?></a>
			<?php else : ?>
					(<?php echo (int) $meta->user_id; ?>)
			<?php endif; ?>
		</li>
		<?php
	endforeach;
	?>
</ul>
