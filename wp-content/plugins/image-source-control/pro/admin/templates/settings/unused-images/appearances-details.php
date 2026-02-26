<?php
/**
 * Render setting to show the details list in the Appearances section
 *
 * @var bool $appearances_details Display the details list or not.
 */

?>
<label>
	<input type="checkbox" name="isc_options[unused_images][appearances_details]" value="1" <?php checked( $appearances_details ); ?> />
	<?php esc_html_e( 'Show more details in the Appearances list.', 'image-source-control-isc' ); ?>
</label>