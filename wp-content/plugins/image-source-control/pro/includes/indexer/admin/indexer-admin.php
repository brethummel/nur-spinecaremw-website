<?php

namespace ISC\Pro\Indexer;

use ISC\Helpers;
use ISC\Plugin;

/**
 * Admin interface of the Indexer
 */
class Indexer_Admin {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'admin_menu', [ $this, 'register_page' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
		add_action( 'wp_ajax_isc_get_indexer_total', [ $this, 'ajax_get_indexer_total' ] );
		add_action( 'wp_ajax_isc_get_indexer_batch', [ $this, 'ajax_get_indexer_batch' ] );
		add_action( 'wp_ajax_isc_run_indexer', [ $this, 'ajax_run_indexer' ] );
		add_action( 'wp_ajax_isc_cleanup_indexer', [ $this, 'ajax_cleanup_indexer' ] );
		add_action( 'wp_ajax_isc_get_indexer_status', [ $this, 'ajax_get_indexer_status' ] );
		add_action( 'isc_admin_sources_after_post_index_section', [ $this, 'after_post_index_section' ] );
	}

	/**
	 * Register the admin page
	 *
	 * @return void
	 */
	public function register_page() {
		add_submenu_page(
			'options.php',
			__( 'Indexer', 'image-source-control-isc' ),
			__( 'Indexer', 'image-source-control-isc' ),
			'manage_options',
			'isc-indexer',
			[ $this, 'render_page' ]
		);

		// add page header
		add_filter(
			'isc_admin_pages',
			function ( $pages ) {
				$pages[] = 'admin_page_isc-indexer';
				return $pages;
			}
		);
	}

	/**
	 * Render the indexer page
	 *
	 * @return void
	 */
	public function render_page() {
		// Total pages: all published posts of public post types
		$indexer     = new Index_Run();
		$total_pages = $indexer->get_total_content_count( 'all' );

		// Not indexed pages
		$not_indexed_pages = count( $indexer->get_unindexed_post_ids() );

		include_once ISCPATH . 'pro/admin/templates/indexer/page.php';
	}

	/**
	 * Return true if the option to execute as admin is enabled
	 */
	public function is_execute_as_admin_enabled(): bool {
		$options = Plugin::get_options();
		return ! empty( $options['unused_images']['execute_as_admin'] );
	}

	/**
	 * Enqueue scripts
	 *
	 * @param string $hook The current admin page.
	 *
	 * @return void
	 */
	public function enqueue_scripts( string $hook ) {
		if ( $hook !== 'admin_page_isc-indexer' ) {
			return;
		}

		Helpers::enqueue_script( 'isc-indexer-js', 'pro/admin/assets/js/indexer.js' );
		wp_enqueue_style( 'isc-indexer-css', ISCBASEURL . 'pro/admin/assets/css/indexer.css', [], ISCVERSION );

		// Localize script with translatable strings
		wp_localize_script(
			'isc-indexer-js',
			'iscIndexerL10n',
			[
				'error'            => __( 'Error', 'image-source-control-isc' ),
				'indexingComplete' => __( 'Indexing complete.', 'image-source-control-isc' ),
				'processingPages'  => __( 'Processing %1$s of %2$s pages', 'image-source-control-isc' ),
				'processedSummary' => __( 'Processed %1$s pages and found a total of %2$s images.', 'image-source-control-isc' ),
				'postTypes'        => $this->get_post_type_labels(),
			]
		);
	}

	/**
	 * Get post type labels from WordPress core
	 *
	 * @return array Array of post type labels
	 */
	private function get_post_type_labels() {
		$post_types = get_post_types( [ 'public' => true ], 'objects' );
		$labels     = [];

		foreach ( $post_types as $post_type => $post_type_object ) {
			$labels[ $post_type ] = $post_type_object->labels->singular_name;
		}

		return $labels;
	}

	/**
	 * AJAX handler to get the total count of public content
	 *
	 * @return void
	 */
	public function ajax_get_indexer_total() {
		check_ajax_referer( 'isc-admin-ajax-nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( 'Insufficient permissions.' );
		}

		$indexing_mode = isset( $_POST['indexing_mode'] ) ? sanitize_text_field( wp_unslash( $_POST['indexing_mode'] ) ) : 'all';

		// Validate indexing mode
		if ( ! in_array( $indexing_mode, [ 'all', 'unindexed' ], true ) ) {
			$indexing_mode = 'all';
		}

		$indexer = new Index_Run();
		$total   = $indexer->get_total_content_count( $indexing_mode );

		wp_send_json_success( [ 'total' => $total ] );
	}

	/**
	 * AJAX handler to get a batch of content URLs
	 *
	 * @return void
	 */
	public function ajax_get_indexer_batch() {
		check_ajax_referer( 'isc-admin-ajax-nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( 'Insufficient permissions.' );
		}

		$indexer       = new Index_Run();
		$offset        = isset( $_POST['offset'] ) ? intval( $_POST['offset'] ) : 0;
		$batch_size    = isset( $_POST['batch_size'] ) ? intval( $_POST['batch_size'] ) : $indexer->url_batch_size;
		$indexing_mode = isset( $_POST['indexing_mode'] ) ? sanitize_text_field( wp_unslash( $_POST['indexing_mode'] ) ) : 'all';

		// Validate indexing mode
		if ( ! in_array( $indexing_mode, [ 'all', 'unindexed' ], true ) ) {
			$indexing_mode = 'all';
		}

		$urls = $indexer->get_content_urls_batch( $offset, $batch_size, $indexing_mode );

		wp_send_json_success( [ 'urls' => $urls ] );
	}

	/**
	 * Run the indexer for a single item via AJAX
	 *
	 * @return void
	 */
	public function ajax_run_indexer() {
		check_ajax_referer( 'isc-admin-ajax-nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( 'Insufficient permissions.' );
		}

		$url_data      = isset( $_POST['url_data'] ) ? json_decode( wp_unslash( $_POST['url_data'] ), true ) : null;
		$global_offset = isset( $_POST['global_offset'] ) ? intval( $_POST['global_offset'] ) : 0;

		if ( empty( $url_data ) || ! is_array( $url_data ) ) {
			wp_send_json_error( 'Invalid URL data for processing.' );
		}

		// on first run with global_offset=0 clear the full storage
		if ( $global_offset === 0 ) {
			\ISC_Storage_Model::clear_storage();
		}

		$indexer = new Index_Run();
		$result  = $indexer->index_single_item( $url_data, $this->is_execute_as_admin_enabled() );

		if ( isset( $result['error'] ) ) {
			wp_send_json_error( $result['error'] );
		} else {
			// Return the result for the single processed item
			wp_send_json_success( $result );
		}
	}

		/**
		 * Clean up old index entries after the indexer finished.
		 *
		 * @return void
		 */
	public function ajax_cleanup_indexer() {
		check_ajax_referer( 'isc-admin-ajax-nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
				wp_send_json_error( 'Insufficient permissions.' );
		}

		// Only allow cleanup when indexing mode is "all"
		$indexing_mode = isset( $_POST['indexing_mode'] ) ? sanitize_text_field( wp_unslash( $_POST['indexing_mode'] ) ) : '';
		if ( $indexing_mode !== 'all' ) {
			wp_send_json_error( 'Cleanup is only allowed when indexing mode is "all".' );
		}

		$start_time = isset( $_POST['start_time'] ) ? intval( $_POST['start_time'] ) : 0;

		$index_table = new Index_Table();
		$deleted     = $index_table->delete_not_updated_since( $start_time );

		wp_send_json_success( [ 'deleted' => $deleted ] );
	}

	/**
	 * Add content to the Post Index section on the Tools page
	 *
	 * @return void
	 */
	public function after_post_index_section() {
		include ISCPATH . 'pro/admin/templates/indexer/post-index-section.php';
	}

	/**
	 * AJAX handler to get current indexer status data
	 *
	 * @return void
	 */
	public function ajax_get_indexer_status() {
		check_ajax_referer( 'isc-admin-ajax-nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( 'Insufficient permissions.' );
		}

		// Total pages: all published posts of public post types
		$indexer     = new Index_Run();
		$total_pages = $indexer->get_total_content_count( 'all' );

		// Not indexed pages
		$not_indexed_pages = count( $indexer->get_unindexed_post_ids() );

		// Render the status table tbody using the template
		ob_start();
		include ISCPATH . 'pro/admin/templates/indexer/status-table-tbody.php';
		$tbody_html = ob_get_clean();

		wp_send_json_success(
			[
				'tbody_html' => $tbody_html,
			]
		);
	}
}
