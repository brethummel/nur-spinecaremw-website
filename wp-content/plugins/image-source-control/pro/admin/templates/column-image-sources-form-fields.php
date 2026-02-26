<?php
/**
 * Content of the ISC column under Media > Library > Mode=List
 * showing edit fields for sources
 *
 * @var int    $att_id               attachment ID
 * @var string $text                 source text
 * @var bool   $use_standard         whether to use the standard source
 * @var string $url                  source URL
 * @var bool   $licenses_enabled     whether licenses are enabled
 * @var array  $licenses             list of licenses
 * @var string $selected_license     currently selected license
 * @var string $standard_source_text standard source text, if the image is supposed to use one
 * @var string $use_standard_as_placeholder decide if the placeholder shows by default
 * @var string $use_standard_by_default true if the standard source is used by default, even if not checked for a given image
 */

?>
	<span class="settings-save-status" role="status">
	<span class="spinner"></span>
</span>
	<input type="text" name="isc-source" value="<?php echo esc_attr( $text ); ?>" data-att-id="<?php echo esc_attr( $att_id ); ?>"
	placeholder="<?php echo $use_standard_as_placeholder ? esc_attr( $standard_source_text ) : esc_html__( 'Source', 'image-source-control-isc' ); ?>" data-isc-standard-text="<?php echo esc_attr( $standard_source_text ); ?>"
	/>
	<br>
	<label>
		<input type="checkbox" name="isc-standard" value="1" <?php checked( $use_standard ); ?> data-att-id="<?php echo esc_attr( $att_id ); ?>" data-isc-use-standard-by-default="<?php echo (bool) $use_standard_by_default; ?>"/>
		<?php esc_html_e( 'Use standard', 'image-source-control-isc' ); ?>
	</label>
	<br>
	<input type="text" name="isc-source-url" value="<?php echo esc_attr( $url ); ?>" data-att-id="<?php echo esc_attr( $att_id ); ?>"
	placeholder="<?php esc_html_e( 'URL', 'image-source-control-isc' ); ?>"/>
<?php if ( $licenses_enabled && $licenses ) : ?>
	<br>
	<label><?php esc_html_e( 'License', 'image-source-control-isc' ); ?>
		<br>
		<select name="isc-source-license" data-att-id="<?php echo esc_attr( $att_id ); ?>">
			<option value="">--</option>
			<?php foreach ( $licenses as $_licence_name => $_licence_data ) : ?>
				<option value="<?php echo esc_attr( $_licence_name ); ?>" <?php selected( $selected_license, $_licence_name ); ?>><?php echo esc_html( $_licence_name ); ?></option>
			<?php endforeach; ?>
		</select>
	</label>
	<?php
endif;
