<?php
/**
 * Render setting to enable Image source overlay
 *
 * @var array $deep_checks deep check related options.
 */

?>
<div>
	<label>
		<input type="checkbox" name="isc_options[unused_images][deep_checks][]" id="isc-settings-unused-images-deep-checks" value="ID in content" <?php checked( in_array( 'ID in content', $deep_checks, true ) ); ?> />
		<?php
		esc_html_e( 'Search attachment IDs in content.', 'image-source-control-isc' );
		?>
	</label>
</div>
<?php
