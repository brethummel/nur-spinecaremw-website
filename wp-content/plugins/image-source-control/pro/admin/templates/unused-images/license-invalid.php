<?php
/**
 * Show a note if the license is invalid
 *
 * @var int $attachment_count number of attachments without association to a post
 */
?>
<div class="notice-error error">
	<p>
		<?php
		if ( $attachment_count > 2 ) {
			printf(
				// translators: %d is the number of images without a known position
				esc_html__( '%d images without a known position.', 'image-source-control-isc' ),
				(int) $attachment_count
			);
		}
		?>
		<?php
		printf(
			// translators: %s marks the opening and closing link tag to the settings page
			esc_html__( 'Please %1$sactivate your license%2$s to see the list.', 'image-source-control-isc' ),
			'<a href="' . esc_url( admin_url( 'options-general.php?page=isc-settings' ) ) . '">',
			'</a>'
		);
		?>
	</p>
</div>
