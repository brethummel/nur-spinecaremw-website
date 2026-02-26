<?php

namespace ISC\Pro;

/**
 * Class Unused_Images_List_Table
 *
 * This class is used to render the list of unused images in the WordPress admin area.
 * It extends the WP_List_Table class provided by WordPress.
 */
class Unused_Images_List_Table extends \WP_List_Table {

	/**
	 * Number of entries per page
	 */
	const PER_PAGE = 100;

	/**
	 * Option name for the total number of items
	 */
	const TOTAL_ITEMS_OPTION_NAME = 'isc_unused_images_total_items';

	/**
	 * Total items
	 *
	 * @var int
	 */
	private $total_items = 0;

	/**
	 * Constructor
	 *
	 * Sets up the list table.
	 */
	public function __construct() {
		parent::__construct(
			[
				'singular' => __( 'Unused Image', 'image-source-control-isc' ),
				'plural'   => __( 'Unused Images', 'image-source-control-isc' ),
				'ajax'     => false,
			]
		);
	}

	/**
	 * Prepares the items for the list
	 *
	 * This method sets up the items to be displayed in the list table.
	 * It also sets up pagination and column headers.
	 *
	 * @return void
	 */
	public function prepare_items() {
		// process bulk actions
		$this->process_bulk_action();

		$this->_column_headers = [ $this->get_columns(), [], $this->get_sortable_columns() ];

		$this->items = $this->get_items();
		$total_items = $this->total_items;

		$this->set_pagination_args(
			[
				'total_items' => $total_items,
				'per_page'    => self::get_per_page(),
				'total_pages' => ceil( $total_items / self::get_per_page() ),
			]
		);
	}

	/**
	 * Get current filter from the query
	 *
	 * @return string The current filter.
	 */
	public function get_filter() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		return ( ! empty( $_REQUEST['filter'] ) ? sanitize_key( $_REQUEST['filter'] ) : 'all' );
	}

	/**
	 * Get a specific attachment ID from the query
	 *
	 * @return int The attachment ID.
	 */
	public function get_attachment_id(): int {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		return ( ! empty( $_REQUEST['attachment_id'] ) ? (int) $_REQUEST['attachment_id'] : 0 );
	}

	/**
	 * Return custom filters
	 *
	 * @return array[] Rendering the views list.
	 */
	public function get_views(): array {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$current = $this->get_filter();
		$query   = remove_query_arg( 'paged' );

		return [
			'all'       => [
				'label' => __( 'all', 'image-source-control-isc' ),
				'url'   => remove_query_arg( 'filter', $query ),
				'class' => ( $current === 'all' ? 'current' : '' ),
			],
			'unchecked' => [
				'label' => __( 'unchecked', 'image-source-control-isc' ),
				'url'   => add_query_arg( 'filter', 'unchecked', $query ),
				'class' => ( $current === 'unchecked' ? 'current' : '' ),
			],
			'unused'    => [
				'label' => __( 'unused', 'image-source-control-isc' ),
				'url'   => add_query_arg( 'filter', 'unused', $query ),
				'class' => ( $current === 'unused' ? 'current' : '' ),
			],
		];
	}

	/**
	 * Override the parent views() method to prevent default output
	 * we render the views filter in our template.
	 *
	 * @return void
	 */
	public function views() {}

	/**
	 * Get items to display in the table.
	 *
	 * @return object[]
	 */
	public function get_items() {
		// we query one more item per page to see if there is a next page
		$items = Unused_Images::get_unused_attachments(
			[
				'offset'        => $this->get_offset(),
				'limit'         => self::get_per_page() + 1,
				'filter'        => self::get_filter(),
				'attachment_id' => self::get_attachment_id(),
			]
		);

		if ( ! $items ) {
			return [];
		}

		// calculate total items
		$this->calculate_total_items( count( $items ) );

		// return one item less than we queried to match the number of items per page
		return array_slice( $items, 0, self::get_per_page() );
	}

	/**
	 * Get the Per_Page value
	 *
	 * @return int The number of items per page.
	 */
	public static function get_per_page(): int {
		/**
		 * Filter the number of items per page
		 */
		return apply_filters( 'isc_unused_images_per_page', self::PER_PAGE );
	}

	/**
	 * Get the offset
	 *
	 * @return int The offset to use when querying items.
	 */
	private function get_offset() {
		return ( $this->get_pagenum() - 1 ) * self::get_per_page();
	}

	/**
	 * Calculate total number of items
	 *
	 * @param int $current_items_count number of items on the current page.
	 *
	 * @return int The total number of items.
	 */
	public function calculate_total_items( int $current_items_count ) {
		$this->total_items = $this->get_offset() + $current_items_count;

		// we have reached the end of the list (items on it are fewer than the number of items per page)
		if ( $current_items_count <= self::get_per_page() ) {
			update_option( self::TOTAL_ITEMS_OPTION_NAME, $this->total_items, false );
			return $this->total_items;
		}

		// update total items count in the database if it is lower than the actual number of total items
		$stored_total_items = get_option( self::TOTAL_ITEMS_OPTION_NAME, 0 );

		if ( $stored_total_items < $this->total_items ) {
			update_option( self::TOTAL_ITEMS_OPTION_NAME, $this->total_items, false );
		} else {
			$this->total_items = $stored_total_items;
		}

		return $this->total_items;
	}

	/**
	 * Get sortable columns
	 *
	 * @return array An array of column names.
	 */
	public function get_columns() {
		$columns = [
			'cb'          => '<input type="checkbox" />',
			'thumbnail'   => __( 'Thumbnail', 'image-source-control-isc' ),
			'image_title' => __( 'Image', 'image-source-control-isc' ),
			'appearances' => __( 'Appearances', 'image-source-control-isc' ),
			'actions'     => __( 'Actions', 'image-source-control-isc' ),
		];

		return $columns;
	}

	/**
	 * Get bulk actions
	 *
	 * @return array An array of bulk actions.
	 */
	public function get_bulk_actions() {
		return [
			'delete'     => __( 'Delete Permanently', 'image-source-control-isc' ),
			'deep_check' => __( 'Deep Check', 'image-source-control-isc' ),
		];
	}

	/**
	 * Render column content
	 *
	 * @param object $item        attachment data.
	 * @param string $column_name column name.
	 *
	 * @return string|void The content for the column.
	 */
	public function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'thumbnail':
				return $this->render_thumbnail_column( $item );

			case 'image_title':
				return $this->render_image_column( $item );

			case 'appearances':
				return $this->render_appearances_column( $item );

			case 'actions':
				return $this->render_actions_column( $item );
		}
	}

	/**
	 * Add a checkbox column for bulk actions
	 *
	 * @param object $item The item for the current row.
	 *
	 * @return string The checkbox for the current row.
	 */
	public function column_cb( $item ): string {
		return sprintf(
			'<input type="checkbox" name="bulk_edit[]" value="%s" />',
			$item->ID
		);
	}

	/**
	 * Render the thumbnail column
	 *
	 * @param object $item The item for the current row.
	 *
	 * @return string The content for the thumbnail column.
	 */
	private function render_thumbnail_column( $item ) {
		return edit_post_link( wp_get_attachment_image( $item->ID, [ 60, 60 ] ), '', '', $item->ID );
	}

	/**
	 * Render the image details column
	 *
	 * @param object $item The item for the current row.
	 *
	 * @return string The content for the image details column.
	 */
	private function render_image_column( $item ) {
		ob_start();
		$image_information = Unused_Images::analyze_unused_image( $item->metadata );
		$filecount         = $image_information['files'] ? $image_information['files'] : 1;
		$filesize_string   = $image_information['total_size'] ? size_format( $image_information['total_size'] ) : '';
		$uploaded_string   = date_i18n( get_option( 'date_format' ), strtotime( $item->post_date_gmt ) );
		$file_path         = self::get_attachment_relative_path( $item->ID );
		require ISCPATH . 'pro/admin/templates/unused-images/column-image.php';

		return ob_get_clean();
	}

	/**
	 * Render the appearances column
	 *
	 * @param object $item The item for the current row.
	 *
	 * @return string The content for the appearances column.
	 */
	private function render_appearances_column( $item ) {
		ob_start();
		$this->render_uploaded_to_post( $item->ID );
		require ISCPATH . 'pro/admin/templates/unused-images/column-appearances.php';

		return ob_get_clean();
	}

	/**
	 * Render the appearances column
	 *
	 * @param object $item The item for the current row.
	 *
	 * @return string The content for the appearances column.
	 */
	private function render_actions_column( $item ) {
		ob_start();
		$item_id = $item->ID;
		require ISCPATH . 'pro/admin/templates/unused-images/column-actions.php';

		return ob_get_clean();
	}

	/**
	 * Get the post to which a given image was uploaded
	 *
	 * @param int $image_id post ID of the attachment.
	 *
	 * @return void
	 */
	public function render_uploaded_to_post( int $image_id ) {
		$post = Unused_Images::get_uploaded_to_post( $image_id );

		if ( ! $post ) {
			return;
		}

		$post_type_object = get_post_type_object( $post->post_type );
		$post_type_name   = $post_type_object->labels->singular_name ?? $post->post_type;
		include ISCPATH . 'pro/admin/templates/unused-images/uploaded-to-post.php';
	}

	/**
	 * Process bulk actions
	 * "Deep Check" is not handled here because it submits each check incrementally using JavaScript
	 *
	 * @return void
	 */
	public function process_bulk_action() {

		// get the action
		$action = $this->current_action();

		if ( ! $action ) {
			return;
		}

		// security check
		check_admin_referer( 'bulk-' . $this->_args['plural'] );

		switch ( $action ) {
			case 'delete':
				// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
				$ids = isset( $_REQUEST['bulk_edit'] ) ? wp_unslash( $_REQUEST['bulk_edit'] ) : [];

				// delete the attachments
				foreach ( $ids as $id ) {
					// validate $id
					if ( ! is_numeric( $id ) ) {
						continue;
					}
					wp_delete_attachment( $id, true );
				}
				break;

			default:
				// invalid action
				wp_die( 'Invalid action.' );
		}
	}

	/**
	 * Return the URL to list a specific attachment ID
	 *
	 * @param int $attachment_id The attachment ID.
	 *
	 * @return string The URL to list the attachment.
	 */
	public static function get_attachment_id_url( $attachment_id ) {
		$base_url = admin_url( 'upload.php?page=isc-unused-images' );
		return add_query_arg( 'attachment_id', $attachment_id, $base_url );
	}

	/**
	 * Get the attachment's relative path from the uploads directory
	 *
	 * @param int $attachment_id The attachment ID.
	 * @return string The relative path or empty string if not found
	 */
	private static function get_attachment_relative_path( $attachment_id ): string {
		// First try with wp_get_attachment_metadata
		$metadata = wp_get_attachment_metadata( $attachment_id );

		if ( ! empty( $metadata ) && ! empty( $metadata['file'] ) ) {
			return $metadata['file'];
		}

		// Fallback: Use get_attached_file and calculate relative path
		$full_path = get_attached_file( $attachment_id );

		$uploads_dir = wp_upload_dir();
		if ( ! empty( $full_path ) && file_exists( $full_path ) ) {
			$relative_path = str_replace( $uploads_dir['basedir'] . '/', '', $full_path );

			if ( ! empty( $relative_path ) ) {
				return $relative_path;
			}
		}

		// Second fallback: Try to get from guid (less reliable but sometimes works)
		$attachment_url = wp_get_attachment_url( $attachment_id );

		if ( ! empty( $attachment_url ) ) {
			$uploads_url   = $uploads_dir['baseurl'];
			$relative_path = str_replace( $uploads_url . '/', '', $attachment_url );

			// Make sure we're not returning a full URL
			if ( ! empty( $relative_path ) && strpos( $relative_path, 'http' ) !== 0 ) {
				return $relative_path;
			}
		}

		// No valid path found
		return '';
	}
}
