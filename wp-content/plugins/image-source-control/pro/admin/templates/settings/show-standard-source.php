<?php
/**
 * Render the option to enable the standard source by default
 *
 * @var bool $use_standard_source_by_default use standard source by default
 */
?><br/>
<label>
	<input type="checkbox" name="isc_options[use_standard_source_by_default]" value="1" <?php checked( $use_standard_source_by_default ); ?>>
<?php esc_html_e( 'Show the standard source for all images that don’t have a source.', 'image-source-control-isc' ); ?>
</label>