<?php

namespace ISC\Pro\Admin;

use ISC\Image_Sources\Renderer\Image_Source_String;
use ISC_Model;

/**
 * Add a column to bulk edit image sources to the Media library list view
 */
class Image_Sources_Form_Column {

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
				'add_edit_fields',
			],
			10,
			2
		);
		// AJAX call to store the information
		add_action( 'wp_ajax_isc-update-attachment', [ $this, 'update_attachment' ] );

		// add custom CSS to the footer
		add_action( 'admin_footer', [ $this, 'add_custom_footer_code' ] );
	}

	/**
	 * Add heading for extra column of attachment list
	 *
	 * @param array $columns array with existing columns.
	 * @return array $new_columns
	 */
	public function add_list_columns_head( $columns ) {
		// If the current user doesn't have the required capability, return the original columns.
		if ( ! current_user_can( 'edit_posts' ) ) {
			return $columns;
		}

		$new_columns         = [];
		$column_head_content = __( 'Image Source Form', 'image-source-control-isc' ) . '<span class="dashicons dashicons-hidden isc-admin-list-view-column-hide hidden"></span>';

		if ( is_array( $columns ) ) {
			// place the column directly after the title column
			foreach ( $columns as $key => $value ) {
				$new_columns[ $key ] = $value;
				if ( 'title' === $key ) {
					$new_columns['isc_fields'] = $column_head_content;
				}
			}
		} else {
			$new_columns['isc_fields'] = $column_head_content;
		}

		return $new_columns;
	}

	/**
	 * Display image source edit fields
	 *
	 * @param string $column_name name of the column.
	 * @param int    $att_id attachment ID.
	 */
	public function add_edit_fields( $column_name, $att_id ) {
		if ( 'isc_fields' !== $column_name || ! \ISC\Media_Type_Checker::should_process_attachment( $att_id ) ) {
			return;
		}

		// Check if the current user can edit this specific attachment.
		if ( ! current_user_can( 'edit_post', $att_id ) ) {
			return;
		}

		$text         = \ISC\Image_Sources\Image_Sources::get_image_source_text_raw( $att_id );
		$url          = \ISC\Image_Sources\Image_Sources::get_image_source_url( $att_id );
		$use_standard = \ISC\Standard_Source::use_standard_source( $att_id );

		// add input field for license, if enabled
		$options                     = \ISC\Plugin::get_options();
		$licenses                    = \ISC\Image_Sources\Utils::licences_text_to_array( $options['licences'] );
		$licenses_enabled            = ! empty( $options['enable_licences'] ) ? true : false;
		$selected_license            = \ISC\Image_Sources\Image_Sources::get_image_license( $att_id );
		$use_standard_by_default     = ! empty( $options['use_standard_source_by_default'] );
		$use_standard_as_placeholder = $use_standard || $use_standard_by_default;
		$standard_source_text        = \ISC\Standard_Source::standard_source_is( 'exclude' ) ? '(' . _x( 'not displayed', 'Placeholder for the source input field if an image without a source is excluded from showing an attribution in the frontend', 'image-source-control-isc' ) . ')' : \ISC\Standard_Source::get_standard_source_text_for_attachment( $att_id );

		require dirname( __DIR__ ) . '/templates/column-image-sources-form-fields.php';
	}

	/**
	 * Update attachment information using AJAX
	 */
	public function update_attachment() {
		check_ajax_referer( 'isc-admin-ajax-nonce', 'nonce' );

		if ( empty( $_REQUEST['att_id'] ) || empty( $_REQUEST['field'] ) || ! isset( $_REQUEST['value'] ) ) {
			die( 'Missing information' );
		}

		$att_id = intval( wp_unslash( $_REQUEST['att_id'] ) );

		if ( ! current_user_can( 'edit_post', $att_id ) ) {
			die( 'Wrong capabilities' );
		}

		// validate the post type
		if ( 'attachment' !== get_post_type( $att_id ) ) {
			die( 'Wrong post type' );
		}

		$value = trim( $_REQUEST['value'] );

		// store new information
		switch ( $_REQUEST['field'] ) {
			// save image source text
			case 'isc-source':
				ISC_Model::save_field( $att_id, 'isc_image_source', sanitize_text_field( $value ) );
				break;
			// save image source URL
			case 'isc-source-url':
				ISC_Model::save_field( $att_id, 'isc_image_source_url', sanitize_text_field( $value ) );
				break;
			// save if standard source is going to be used
			case 'isc-standard':
				$value = $value === 'true' || $value === true;
				ISC_Model::save_field( $att_id, 'isc_image_source_own', $value );
				break;
			// save license
			case 'isc-source-license':
				ISC_Model::save_field( $att_id, 'isc_image_licence', sanitize_text_field( $value ) );
				break;
		}

		// return the new image source for preview
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo Image_Source_String::get( $att_id );

		die();
	}

	/**
	 * Custom CSS for the footer of the attachment list page
	 * used under Media > Library > Mode=List
	 *
	 * Custom JavaScript to store the values automatically
	 */
	public function add_custom_footer_code() {
		$screen = get_current_screen();
		if ( empty( $screen->id ) || $screen->id !== 'upload' ) {
			return;
		}
		?><style>
			/**
			 * added by ISC Pro > /pro/admin/includes/columns.php
			 */
			.column-isc_fields { position: relative; top: 0; left: 0; }
			.column-isc_fields input[type="text"],
			.column-isc_fields input[type="url"],
			.column-isc_fields select { width: 100%; }
			.column-isc_fields .settings-save-status { position: absolute; top: 0; right: 0; }
		</style>
		<script>
			/**
			 * Prevent ISC form fields from being added to the main filter URL.
			 */
			document.addEventListener('DOMContentLoaded', function() {
				// Find the main filter form used by WordPress Media Library.
				const filterForm = document.getElementById('posts-filter');

				if (filterForm) {
					filterForm.addEventListener('submit', function(event) {
						const clickedElement = event.submitter;
						const filterButtonIds = ['post-query-submit', 'search-submit'];

						// Only disable if a filter/bulk action button was likely clicked.
						if (clickedElement && filterButtonIds.includes(clickedElement.id)) {
							const iscFormFields = filterForm.querySelectorAll(
								'.column-isc_fields input, .column-isc_fields select'
							);

							iscFormFields.forEach(function(field) {
								field.disabled = true;
							});
						}
					});
				} else {
					console.warn('ISC: Could not find the Media Library filter form (#posts-filter).');
				}
			});
		</script>
		<?php
	}
}


