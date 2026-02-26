<?php
/**
 * Render the option to enable support for Elementor background images.
 *
 * @var bool $enable_elementor_background_images
 */
?>
<label>
	<input type="checkbox" name="isc_options[elementor_background_images]" value="1" <?php checked( $enable_elementor_background_images ); ?>>
	<?php esc_html_e( 'Enable support for Elementor background images.', 'image-source-control-isc' ); ?>
</label> <a href="<?php echo esc_url( ISC\Admin_Utils::get_isc_localized_website_url( 'documentation/elementor-image-captions/', 'dokumentation/bildquellen-elementor/', 'elementor' ) ); ?>" target="_blank"><?php esc_html_e( 'Manual', 'image-source-control-isc' ); ?></a>