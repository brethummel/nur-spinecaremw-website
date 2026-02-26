<?php
/**
 * Render the index bot page
 *
 * @var int $total_pages Total number of pages.
 * @var int $with_images Number of indexed pages with images.
 * @var int $not_indexed_pages Number of not indexed pages.
 *
 * @return void
 */

?>
<div class="wrap">
	<p>
	<?php
	esc_html_e( 'Find images in the content of all published pages.', 'image-source-control-isc' );
	?>
	<a href="<?php echo esc_url( \ISC\Admin_Utils::get_isc_localized_website_url( 'documentation/#unused-images', 'dokumentation/#ungenutzte-bilder', 'index-page' ) ); ?>" target="_blank"><?php esc_html_e( 'Manual', 'image-source-control-isc' ); ?></a>.
	</p>

	<div class="isc-indexer-summary-table" style="margin-bottom: 24px;">
		<table class="widefat isc-indexer-status-table" style="width:auto;">
			<thead>
			<tr>
				<th><?php esc_html_e( 'Posts', 'image-source-control-isc' ); ?></th>
				<th>#</th>
				<th><?php esc_html_e( 'Actions', 'image-source-control-isc' ); ?></th>
			</tr>
			</thead>
			<tbody>
			<?php require ISCPATH . 'pro/admin/templates/indexer/status-table-tbody.php'; ?>
			</tbody>
		</table>
	</div>

	<div class="isc-indexer-controls">
		<?php
		// Check if execute as admin setting is enabled
		if ( $this->is_execute_as_admin_enabled() ) :
			?>
		<div class="notice notice-info inline" style="margin-bottom: 12px;">
			<p>
				<?php esc_html_e( 'Run as a logged-in user', 'image-source-control-isc' ); ?>.
				<a href="<?php echo esc_url( admin_url( 'options-general.php?page=isc-settings#isc_settings_section_unused_images' ) ); ?>"><?php esc_html_e( 'Settings', 'image-source-control-isc' ); ?></a>
			</p>
		</div>
		<?php endif; ?>
		<button class="button button-primary isc-indexer-btn"><?php esc_html_e( 'Start', 'image-source-control-isc' ); ?></button>
		<div id="isc-indexer-progress" class="hidden">
			<!-- Progress stats will be populated by JavaScript -->
			<div class="isc-indexer-progress-stats"></div>
			<progress id="isc-indexer-progress-bar" value="0" max="100"></progress>
			<span id="isc-indexer-status"></span>
		</div>
	</div>

	<!-- Summary will be inserted here when indexing completes -->
	<div id="isc-indexer-results" class="hidden">
		<!-- Summary will be inserted at the top by JavaScript -->

		<!-- Results table with predefined structure -->
		<table id="isc-indexer-results-table" class="wp-list-table widefat fixed striped">
			<thead>
			<tr>
				<th><?php esc_html_e( 'Title', 'image-source-control-isc' ); ?></th>
				<th><?php esc_html_e( 'Post type', 'image-source-control-isc' ); ?></th>
				<th><?php esc_html_e( 'Images', 'image-source-control-isc' ); ?></th>
			</tr>
			</thead>
			<tbody id="isc-indexer-results-tbody">
			<!-- Table rows will be populated by JavaScript -->
			</tbody>
		</table>
	</div>

	<!-- Summary container that will be populated and moved to the top of results -->
	<div id="isc-indexer-summary" class="notice notice-success hidden">
		<p id="isc-indexer-summary-text">
			<!-- Summary text will be populated by JavaScript -->
		</p>
	</div>
</div>