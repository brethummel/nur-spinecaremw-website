<?php

use ISC\Pro\Admin\License;
use ISC\Helpers;

/**
 * Class ISC_Pro_Admin
 *
 * WP Admin related functions of ISC Pro
 */
class ISC_Pro_Admin {
	/**
	 * ISC_Pro_Admin constructor
	 */
	public function __construct() {
		if ( defined( 'ISC_DISABLE_PRO' ) && ISC_DISABLE_PRO ) {
			return;
		}

		add_action( 'plugins_loaded', [ $this, 'plugins_loaded' ] );

		new License();
		new ISC\Pro\Admin\Image_Sources_Columns();
		new ISC\Pro\Admin\Image_Sources_Form_Column();
		new ISC\Pro\Admin\Image_Sources_Preview_Column();
		new ISC_Pro_Admin_Storage_Form();
	}

	/**
	 * Register hooks
	 */
	public function plugins_loaded() {

		// enable the additional sources options
		add_filter( 'isc_plugin_options_modules', [ $this, 'remove_pro_flag' ] );
		add_filter( 'isc_list_included_images_options', [ $this, 'remove_pro_flag' ] );
		add_filter( 'isc_overlay_included_images_options', [ $this, 'remove_pro_flag' ] );
		add_filter( 'isc_overlay_advanced_included_images_options', [ $this, 'remove_pro_flag' ] );
		add_filter( 'isc_global_list_included_images_options', [ $this, 'remove_pro_flag' ] );
		add_filter( 'isc_caption_style_options', [ $this, 'remove_pro_flag' ] );

		// enable global list colum options
		add_filter( 'isc_global_list_included_data_options', [ $this, 'remove_pro_flag' ] );

		// load additional option templates
		add_action( 'isc_admin_settings_template_after_standard_source', [ $this, 'render_default_standard_source_option' ] );

		// manipulate settings on save
		add_filter( 'isc_settings_on_save_after_validation', [ $this, 'validate_settings_on_save' ], 10, 2 );

		// adjust ISC source form fields in the Media library (not the block editor)
		add_filter( 'isc_admin_attachment_form_fields', [ $this, 'extend_isc_fields' ], 10, 3 );

		// add relevant scripts
		add_action( 'admin_enqueue_scripts', [ $this, 'add_admin_scripts' ] );

		// change default media thumbnail for external images
		add_filter( 'wp_get_attachment_image_src', [ $this, 'wp_get_attachment_image_src_thumbnail' ], 10, 2 );

		// match pro settings sections with their modules
		add_filter( 'isc_settings_plugin_modules_related_sections', [ $this, 'extend_plugin_modules' ] );
	}

	/**
	 * Remove the "is_pro" flag from option arrays.
	 *
	 * @param array[] $options any multidimentional options array where top level arrays need the "is_pro" key set to false.
	 * @return array[]
	 */
	public function remove_pro_flag( array $options ) {
		// set "is_pro" to true
		foreach ( $options as $_key => $_options ) {
			if ( ! empty( $_options['is_pro'] ) ) {
				$options[ $_key ]['is_pro'] = false;
			}
		}

		return $options;
	}

	/**
	 * Show an option under Settings > Miscellaneous Settings > Standard source to show standard source if no other source is given
	 */
	public function render_default_standard_source_option() {
		$options                        = \ISC\Plugin::get_options();
		$use_standard_source_by_default = ! empty( $options['use_standard_source_by_default'] );

		require_once 'templates/settings/show-standard-source.php';
	}

	/**
	 * Add Pro options when saving the settings page
	 *
	 * @param array $options sanitized options.
	 * @param array $input_options options as they come from the settings page.
	 * @return array sanitized options
	 */
	public function validate_settings_on_save( $options, $input_options ) {

		// add the Show Standard Source option
		$options['use_standard_source_by_default'] = ! empty( $input_options['use_standard_source_by_default'] );

		// Per-page list > Layout > Collapsed
		$options['list_layout']['details'] = ! empty( ( $options['list_layout'] ?? [] )['details'] ?? false );

		$options['global_list_included_data'] = [];
		if ( isset( $input_options['global_list_included_data'] ) && is_array( $input_options['global_list_included_data'] ) ) {
			foreach ( $input_options['global_list_included_data'] as $column ) {
				$options['global_list_included_data'][] = esc_attr( $column );
			}
		}

		// clear all indices if the global index option was changed
		$previous_options = \ISC\Plugin::get_options();
		if ( empty( $previous_options['global_list_indexed_images'] ) !== empty( $input_options['global_list_indexed_images'] ) ) {
			\ISC\Indexer::clear_index();
		}
		$options['global_list_indexed_images'] = ! empty( $input_options['global_list_indexed_images'] );

		// add the IPTC standard source tag
		$options['standard_source_iptc_tag'] = esc_attr( $input_options['standard_source_iptc_tag'] );

		return $options;
	}

	/**
	 * Add additional descriptions to the ISC form fields
	 *
	 * @param array  $form_fields Attachment form fields.
	 * @param object $post        WP_Post object.
	 * @param array  $options     ISC options.
	 *
	 * @return array
	 */
	public function extend_isc_fields( array $form_fields, object $post, array $options ) {
		// add hint to the "Standard Source" field when the "Default Standard Source" is enabled
		if ( ! empty( $options['use_standard_source_by_default'] ) ) {
			$form_fields['isc_image_source_own']['helps'] .= '<br/>' .
															__( 'Your current settings show the standard source by default when the individual image source is missing.', 'image-source-control-isc' );
		}

		return $form_fields;
	}

	/**
	 * Add scripts to ISC-related pages
	 */
	public function add_admin_scripts() {
		$screen = get_current_screen();
		if ( isset( $screen->id ) && $screen->id === 'settings_page_isc-settings' ) {
			Helpers::enqueue_script( 'isc_pro_settings_script', 'pro/admin/assets/js/settings.js' );
		}
	}

	/**
	 * Manipulate wp_get_attachment_image_src to change the thumbnail URL
	 *
	 * @param array|false $image Array of image data, or boolean false if no image is available.
	 * @param int         $attachment_id Image attachment ID.
	 */
	public function wp_get_attachment_image_src_thumbnail( $image, $attachment_id ) {

		// change the image thumbnail URL if the attachment is an external image
		if ( get_post_meta( $attachment_id, 'isc_imported_into_library', true ) ) {
			// convert false to array
			if ( $image === false ) {
				$image = [];
			}

			$image[0] = ISCBASEURL . '/public/assets/images/isc-icon-gray.svg';
			$image[1] = 256; // width
			$image[2] = 256; // height
		}

		return $image;
	}

	/**
	 * Extend the plugin modules with Pro features
	 *
	 * @param array $modules Existing modules configuration.
	 * @return array Modified modules configuration
	 */
	public function extend_plugin_modules( $modules ): array {
		// Add or modify modules
		$modules['unused_images'] = [
			'isc_settings_section_unused_images',
		];

		return $modules;
	}
}
