<?php

namespace ISC\Pro\Compatibility;

use ISC\Plugin;

/**
 * Provide compatibility with JetEngine
 * https://crocoblock.com/plugins/jetengine/
 */
class Jet_Engine {

	/**
	 * Constructor to initialize hooks
	 *
	 * @return void
	 */
	public function __construct() {
		add_action( 'plugins_loaded', [ $this, 'register_hooks' ] );
	}

	/**
	 * Register hooks
	 *
	 * @return void
	 */
	public function register_hooks() {
		// enable only, if the JetEngine plugin is enabled
		if ( ! class_exists( 'Jet_Engine', false ) || ! Plugin::is_module_enabled( 'image_sources' ) ) {
			return;
		}

		// JetEngine Listings – add ISC markup to images in the AJAX-loaded content; this uses a traditional WordPress AJAX call
		add_filter( 'jet-engine/ajax/listing_load_more/response', [ $this, 'add_isc_markup_to_ajax_content' ] );

		// JetEngine Smart Filters – render ISC markup in the custom API endpoint for grid filters
		add_filter( 'jet-smart-filters/render/ajax/data', [ $this, 'add_isc_markup_to_jetengine_smart_filters_renderer' ] );

		// Register custom selectors for the MutationObserver in the ISC front data
		add_filter( 'isc_public_caption_script_options', [ $this, 'register_mutation_observer_updated_containers' ] );
	}

	/**
	 * Add ISC markup to images in the AJAX-loaded content
	 *
	 * @param array $response The response from the AJAX call.
	 *
	 * @return array The modified response with ISC markup added.
	 */
	public function add_isc_markup_to_ajax_content( array $response ): array {
		$response['html'] = \ISC_Public::get_instance()->add_source_captions_to_content( $response['html'] );

		return $response;
	}

	/**
	 * Add ISC markup to images in the JetEngine Smart Filters Renderer
	 * See Jet_Smart_Filters_Render::ajax_apply_filters()
	 *
	 * @param array $args The response from the AJAX call.
	 *
	 * @return array The modified response with ISC markup added.
	 */
	public function add_isc_markup_to_jetengine_smart_filters_renderer( array $args ): array {
		$args['content'] = \ISC_Public::get_instance()->add_source_captions_to_content( $args['content'] );

		return $args;
	}

	/**
	 * Add custom selectors to the ISC front data for the MutationObserver.
	 *
	 * This function hooks into the 'isc_public_caption_script_options' filter
	 * provided by the Image Source Control plugin.
	 *
	 * @param array $front_data The data array being localized for the ISC captions.js script.
	 *
	 * @return array The modified $front_data array.
	 */
	public function register_mutation_observer_updated_containers( array $front_data ): array {

		if ( ! isset( $front_data['observe_elements_selectors'] ) || ! is_array( $front_data['observe_elements_selectors'] ) ) {
			$front_data['observe_elements_selectors'] = [];
		}

		// Grid with or without JetEngine Smart Filters. A deeper selector is not possible because the smart filter overrides it completely.
		$front_data['observe_elements_selectors'][] = '.elementor-widget-jet-listing-grid';

		return $front_data;
	}
}
