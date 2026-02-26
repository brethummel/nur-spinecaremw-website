<?php
/**
 * Render the License Key setting
 *
 * @var string $license_key ISC license key.
 * @var string $license_status
 * @var string $expiry_date
 * @var string $error_text
 * @var string $expired
 */
?>
<input type="text" id='license-key' name="license-key" placeholder="<?php esc_html_e( 'License key', 'image-source-control-isc' ); ?>"
	   value="<?php echo esc_attr( $license_key ); ?>"
		<?php
		if ( 'valid' === $license_status && ! $expired ) :
			?>
		readonly="readonly"<?php endif; ?>/>
<button type="button" id="license-activate"
		class="button button-<?php echo ( 'valid' === $license_status && ! $expired ) ? 'secondary' : 'primary'; ?>">
		<?php
		echo ( 'valid' === $license_status && ! $expired ) ? esc_html__( 'Update license', 'image-source-control-isc' ) : esc_html__( 'Activate License', 'image-source-control-isc' );
		?>
	</button>
<button type="button" id="license-deactivate" class="button button-secondary"
	<?php
	if ( 'valid' !== $license_status ) {
		echo ' style="display: none;" ';
	}
	?>
	><?php esc_html_e( 'Deactivate license', 'image-source-control-isc' ); ?></button>
<input type="hidden" id="isc-licenses-ajax-referrer" value="<?php echo esc_attr( wp_create_nonce( 'isc_ajax_license_nonce' ) ); ?>"/>
<p id="license-activate-error"><?php echo wp_kses_post( $error_text ); ?></p>
<?php
