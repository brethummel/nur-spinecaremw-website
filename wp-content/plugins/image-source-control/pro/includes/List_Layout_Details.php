<?php

namespace ISC\Pro;

use ISC\Plugin;
use ISC_Log;

/**
 * Handle rendering of the per-page list layout in <details> style
 */
class List_Layout_Details {

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

		// validate settings on save
		add_filter( 'isc_settings_on_save_after_validation', [ $this, 'validate_settings_on_save' ], 10, 2 );

		// From here on, the option needs to be enabled for the hooks to work.
		$options = Plugin::get_options();
		if ( empty( $options['list_layout']['details'] ) ) {
			return;
		}

		// change the main tag of the image source box to <details>
		add_filter(
			'isc_image_list_box_tag',
			function ( $tag ) {
				return 'details';
			}
		);

		// change the layout of the per-page list
		add_filter( 'isc_render_image_source_box', [ $this, 'render_image_source_box' ], 10, 4 );

		// add style to the frontend
		add_action( 'wp_head', [ $this, 'style' ] );
	}

	/**
	 * Override the image source box content
	 *
	 * @param string $list_box           HTML of the source box.
	 * @param string $content            content of the source box, i.e., list of sources.
	 * @param string $headline           headline of the source box.
	 * @param bool   $create_placeholder if true, create a placeholder.
	 */
	public function render_image_source_box( $list_box, $content, $headline, $create_placeholder ) {
		$headline = $headline ?? __( 'image sources', 'image-source-control-isc' );

		ob_start();
		require ISCPATH . 'pro/public/views/image-source-box-details.php';

		ISC_Log::log( 'creating image source box as a collapsed list' );

		return ob_get_clean();
	}

	/**
	 * Add Pro options when saving the settings page
	 *
	 * @param array $options sanitized options.
	 * @param array $input_options options as they come from the settings page.
	 * @return array sanitized options
	 */
	public function validate_settings_on_save( $options, $input_options ) {
		$options['list_layout']['details'] = ! empty( ( $input_options['list_layout'] ?? [] )['details'] ?? false );

		return $options;
	}

	/**
	 * Style in wp_head frontend
	 */
	public function style() {
		wp_register_style( 'isc-list-details', false, [], ISCVERSION );
		wp_enqueue_style( 'isc-list-details' );
		wp_add_inline_style( 'isc-list-details', 'summary.isc_image_list_title{cursor:pointer;}' );
	}
}
