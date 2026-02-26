<?php
/**
 * Render the option to enable support for Divi background images.
 *
 * @var bool $enable_divi_background_images
 */

?>
<label>
	<input type="checkbox" name="isc_options[divi_background_images]" value="1" <?php checked( $enable_divi_background_images ); ?>>
	<?php
	printf(
		// translators: %s is the name of the theme or page builder, e.g. Divi.
		esc_html__( 'Enable support for %s background images.', 'image-source-control-isc' ),
		'Divi'
	);
	?>
</label>