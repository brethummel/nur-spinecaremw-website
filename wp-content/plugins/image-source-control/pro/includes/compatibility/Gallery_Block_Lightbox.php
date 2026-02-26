<?php

namespace ISC\Pro\Compatibility;

/**
 * Provide compatibility with the plugin Lightbox for Gallery & Image Block
 * https://wordpress.org/plugins/gallery-block-lightbox/
 */
class Gallery_Block_Lightbox {

	/**
	 * Constructor method for the class.
	 *
	 * @return void
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
		// Check if the function from the Lightbox plugin exists
		if ( is_admin() || ! function_exists( '\Gallery_Block_Lightbox\register_assets' ) ) {
			return;
		}

		if ( ! \ISC\Plugin::is_module_enabled( 'image_sources' ) ) {
			return;
		}

		// add the "no-lightbox" class to image source links
		add_filter( 'isc_public_source_url_html_classes', [ $this, 'add_no_lightbox_class' ] );
	}

	/**
	 * Add the "no-lightbox" class to image source links
	 *
	 * @link https://github.com/goaround/gallery-block-lightbox/issues/13
	 *
	 * @param string[] $classes list of classes.
	 *
	 * @return string[]
	 */
	public function add_no_lightbox_class( array $classes ): array {
		$classes[] = 'no-lightbox';
		return $classes;
	}
}
