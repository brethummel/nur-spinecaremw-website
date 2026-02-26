<?php
/**
 * Render setting to enable indexer execution as admin
 *
 * @var bool $execute_as_admin execute indexer as admin option.
 */

?>
<div>
	<label>
		<input type="checkbox" name="isc_options[unused_images][execute_as_admin]" id="isc-settings-unused-images-execute-as-admin" value="1" <?php checked( $execute_as_admin ); ?> />
		<?php
		esc_html_e( 'Run as a logged-in user', 'image-source-control-isc' );
		?>
	</label>
	<p class="description">
		<?php esc_html_e( 'When enabled, the indexer will use your admin session to access pages. This allows indexing of pages restricted to logged-in users.', 'image-source-control-isc' ); ?>
		<?php esc_html_e( 'Warning: This will send your admin authentication cookies with the indexer requests.', 'image-source-control-isc' ); ?>
	</p>
</div>
<?php