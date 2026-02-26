<?php

namespace ISC\Settings\Sections;

use ISC\Plugin;
use ISC\Settings;

/**
 * Handle Unused Images settings
 */
class Unused_Images extends Settings\Section {

	/**
	 * Add settings section
	 */
	public function add_settings_section() {

		// source in caption
		add_settings_section( 'isc_settings_section_unused_images', __( 'Unused Images', 'image-source-control-isc' ), '__return_false', 'isc_settings_page' );
		add_settings_field( 'unused_images_content_ids', __( 'Deep Check', 'image-source-control-isc' ), [ $this, 'render_field_deep_check' ], 'isc_settings_page', 'isc_settings_section_unused_images' );
		add_settings_field( 'unused_images_execute_as_admin', __( 'Indexer', 'image-source-control-isc' ), [ $this, 'render_field_execute_as_admin' ], 'isc_settings_page', 'isc_settings_section_unused_images' );
		add_settings_field( 'unused_images_appearances_details', __( 'Details', 'image-source-control-isc' ), [ $this, 'render_field_appearances_details' ], 'isc_settings_page', 'isc_settings_section_unused_images' );
	}

	/**
	 * Render Deep Check options
	 */
	public function render_field_deep_check() {
		$options     = $this->get_options();
		$deep_checks = ! empty( $options['unused_images']['deep_checks'] ) ? $options['unused_images']['deep_checks'] : [];
		require_once ISCPATH . '/pro/admin/templates/settings/unused-images/deep-check.php';
	}

	/**
	 * Render option to show more details in the Appearances list
	 */
	public function render_field_appearances_details() {
		$options             = $this->get_options();
		$appearances_details = ! empty( $options['unused_images']['appearances_details'] );
		require_once ISCPATH . '/pro/admin/templates/settings/unused-images/appearances-details.php';
	}

	/**
	 * Render option to execute indexer as admin
	 */
	public function render_field_execute_as_admin() {
		$options          = $this->get_options();
		$execute_as_admin = ! empty( $options['unused_images']['execute_as_admin'] );
		require_once ISCPATH . '/pro/admin/templates/settings/unused-images/execute-as-admin.php';
	}

	/**
	 * Validate settings
	 *
	 * @param array $output output data.
	 * @param array $input  input data.
	 *
	 * @return array $output
	 */
	public function validate_settings( array $output, array $input ): array {
		$output['unused_images']['deep_checks']         = isset( $input['unused_images']['deep_checks'] ) && is_array( $input['unused_images']['deep_checks'] ) ? $input['unused_images']['deep_checks'] : [];
		$output['unused_images']['appearances_details'] = ! empty( $input['unused_images']['appearances_details'] );
		$output['unused_images']['execute_as_admin']    = ! empty( $input['unused_images']['execute_as_admin'] );

		return $output;
	}
}
