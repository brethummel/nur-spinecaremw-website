<?php

namespace ISC\Pro\Compatibility;

use ISC\Plugin;

/**
 * Provide compatibility with Elementor and Elementor Pro
 */
class Elementor {

	/**
	 * Construct an instance of Elementor
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
		if ( ! defined( 'ELEMENTOR_VERSION' ) || ! Plugin::is_module_enabled( 'image_sources' ) ) {
			return;
		}

		add_action( 'elementor/element/after_add_attributes', [ $this, 'add_data_images_attribute' ] );

		// add the setting
		add_action( 'isc_admin_settings_overlay_included_images_after', [ $this, 'render_elementor_option' ] );

		// validate settings on save
		add_filter( 'isc_settings_on_save_after_validation', [ $this, 'validate_settings_on_save' ], 10, 2 );
	}

	/**
	 * Add the elements background image ID into a new data-isc-images attribute to the element container.
	 * Fires after the attributes of the element HTML tag are rendered.
	 *
	 * @param \Elementor\Element_Base $element The element.
	 * @return void
	 */
	public function add_data_images_attribute( $element ) {
		// check if Elementor option is enabled
		$options = Plugin::get_options();
		if ( ! isset( $options['elementor_background_images'] ) || ! $options['elementor_background_images'] ) {
			return;
		}

		$settings         = $element->get_settings_for_display();
		$background_image = [];
		if ( isset( $settings['background_image'] ) ) { // containers use the key `background_image`; this also applies to lazy loaded background images
			$background_image = $settings['background_image'];
		} elseif ( isset( $settings['_background_image'] ) ) { // paragraphs use this
			$background_image = $settings['_background_image'];
		} elseif ( isset( $settings['bg_image'] ) ) { // the Call To Action widget uses `bg_image`
			$background_image = $settings['bg_image'];
		}

		// one could filter images from the library by checking the "source" attribute of the background image settings,
		// but it is only added for some widgets, not all of them
		if ( empty( $background_image['id'] ) || empty( $background_image['url'] ) ) {
			return;
		}

		$attachment_id = $background_image['id'];

		$element->add_render_attribute( '_wrapper', 'data-isc-images', $attachment_id );
	}

	/**
	 * Show an option under Settings > Miscellaneous Settings to enable Elementor compatibility
	 */
	public function render_elementor_option() {
		$options                            = Plugin::get_options();
		$enable_elementor_background_images = ! empty( $options['elementor_background_images'] );

		require_once ISCPATH . 'pro/admin/templates/settings/elementor.php';
	}

	/**
	 * Add Pro options when saving the settings page
	 *
	 * @param array $options sanitized options.
	 * @param array $input_options options as they come from the settings page.
	 * @return array sanitized options
	 */
	public function validate_settings_on_save( $options, $input_options ) {
		$options['elementor_background_images'] = ! empty( $input_options['elementor_background_images'] );

		return $options;
	}
}
