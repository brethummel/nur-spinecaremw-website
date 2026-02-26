<?php
/**
 * Render the WP Caption setting for the standard source option
 *
 * @var string $standard_source value of the Standard Source option
 */
?>
<br/>
<label>
	<input type="radio" name="isc_options[standard_source]" value="wp_caption" <?php checked( $standard_source, 'wp_caption' ); ?> />
	<?php esc_html_e( 'Caption', 'image-source-control-isc' ); ?>
</label>
<p class="description">
<?php
esc_html_e( 'Use the caption entered in the media library.', 'image-source-control-isc' );
?>
</p>
