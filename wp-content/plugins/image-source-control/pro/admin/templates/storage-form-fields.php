<?php
/**
 * Content of the ISC storage table under Media > Image Sources > Additional images
 * showing edit fields for sources
 *
 * @var string $image_key         Encoded image URL so that it can be used as a key
 */
?>
<span class="settings-save-status" role="status">
	<span class="spinner"></span>
</span>
<button class="button button-secondary" type="button" name="isc-move-to-media-library" data-img-key="<?php echo esc_attr( $image_key ); ?>"><?php esc_html_e( 'Manage in the media library', 'image-source-control-isc' ); ?></button>
