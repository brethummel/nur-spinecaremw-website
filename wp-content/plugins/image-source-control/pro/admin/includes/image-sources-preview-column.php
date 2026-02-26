<?php

namespace ISC\Pro\Admin;

use ISC\Image_Sources\Renderer\Image_Source_String;

/**
 * Add a column with a preview of the image sources to the Media library list view
 */
class Image_Sources_Preview_Column {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'init', [ $this, 'register_hooks' ] );
	}

	/**
	 * Register hooks
	 *
	 * @return void
	 */
	public function register_hooks(): void {
		if ( ! \ISC\Plugin::is_module_enabled( 'image_sources' ) ) {
			return;
		}

		// add new columns to the Media > Library > List (wp-admin/upload.php?mode=list)
		// add columns
		add_filter(
			'manage_media_columns',
			[
				$this,
				'add_list_columns_head',
			]
		);
		// add column content
		add_filter(
			'manage_media_custom_column',
			[
				$this,
				'add_preview',
			],
			10,
			2
		);
	}

	/**
	 * Add heading for extra column of attachment list
	 *
	 * @param array $columns array with existing columns.
	 * @return array $new_columns
	 */
	public function add_list_columns_head( $columns ) {
		$new_columns         = [];
		$column_head_content = __( 'Image Source', 'image-source-control-isc' ) . '<span class="dashicons dashicons-hidden isc-admin-list-view-column-hide hidden"></span>';

		if ( is_array( $columns ) ) {
			// place the column directly after the title column
			foreach ( $columns as $key => $value ) {
				$new_columns[ $key ] = $value;
				if ( 'title' === $key ) {
					$new_columns['isc_preview'] = $column_head_content;
				}
			}
		} else {
			$new_columns['isc_preview'] = $column_head_content;
		}

		return $new_columns;
	}

	/**
	 * Display the image source preview
	 *
	 * @param string $column_name name of the column.
	 * @param int    $att_id attachment ID.
	 */
	public function add_preview( $column_name, $att_id ) {
		if ( 'isc_preview' !== $column_name ) {
			return;
		}

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo Image_Source_String::get( $att_id );
	}
}
