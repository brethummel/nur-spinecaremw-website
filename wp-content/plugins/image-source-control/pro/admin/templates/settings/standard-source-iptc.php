<?php
/**
 * Render the IPTC setting for the standard source option
 *
 * @var string $standard_source value of the Standard Source option
 * @var string $standard_source_tag value of the Standard Source IPTC tag option
 */
?>
<br/>
<label>
	<input type="radio" name="isc_options[standard_source]" value="iptc" <?php checked( $standard_source, 'iptc' ); ?> />
	<?php esc_html_e( 'IPTC meta data', 'image-source-control-isc' ); ?>
</label>
<p class="description">
<?php
esc_html_e( 'Use the selected IPTC image meta data.', 'image-source-control-isc' );
?>
	<label>
		<input type="radio" name="isc_options[standard_source_iptc_tag]" value="credit" <?php checked( 'credit', $standard_source_tag ); ?>/><code>credit</code>
	</label>
	<label>
		<input type="radio" name="isc_options[standard_source_iptc_tag]" value="copyright" <?php checked( 'copyright', $standard_source_tag ); ?>/><code>copyright</code>
	</label>
</p>