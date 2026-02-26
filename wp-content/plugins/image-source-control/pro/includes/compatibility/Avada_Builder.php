<?php

namespace ISC\Pro\Compatibility;

use ISC\Plugin;
use ISC_Log;
use ISC_Model;
use ISC\Image_Sources\Renderer\Caption;

/**
 * Provide compatibility with the Avada Builder (previously known as Fusion Builder)
 */
class Avada_Builder {

	/**
	 * Constructor method for the class.
	 *
	 * @return void
	 */
	public function __construct() {
		add_action( 'init', [ $this, 'register_hooks' ] );
	}

	/**
	 * Register hooks for the Avada Builder compatibility.
	 *
	 * @return void
	 */
	public function register_hooks(): void {
		// enable only, if the Avada Builder plugin is enabled
		if ( ! defined( 'FUSION_BUILDER_VERSION' ) || ! Plugin::is_module_enabled( 'image_sources' ) ) {
			return;
		}

		// add the `data-isc-source-text` attribute to HTML tags with image URLs in the inline styles. Needs to run before add_caption_from_isc_images_attribute()
		add_filter( 'isc_public_caption_regex_content', [ $this, 'add_source_text_for_databg_attribute' ], 9 );
	}

	/**
	 * Adds the `data-isc-source-text` attribute to HTML tags with image URLs in the inline styles.
	 *
	 * @param string $html The HTML content to process.
	 *
	 * @return string The modified HTML content.
	 */
	public function add_source_text_for_databg_attribute( string $html ): string {
		/**
		 * Match groups:
		 * 0 - Full match: the full HTML tag with a data-bg attribute that includes an image URL.
		 * 1 - The `data-bg` attribute and value. Needed to later attach the `data-isc-source-text` attribute behind it.
		 * 2 - The value of the `data-bg` attribute.
		 *
		 * Key points:
		 * It's case-insensitive (`i` modifier), which means it will match attribute and tag names in any combination of uppercase and lowercase.
		 * No initial check if the URL in the attribute is actually an image URL.
		 * It doesn't handle whitespace or line breaks in the style attribute. If the attribute includes line breaks, they won't be captured correctly.
		 */
		$pattern = '#<div[\x20|\x9|\xD|\xA]+[^>]*((data-bg|data-preload-img|data-bg-url)="(.+)").*\/?>#isU';
		$count   = preg_match_all( $pattern, $html, $matches, PREG_SET_ORDER );

		ISC_Log::log( 'Avada_Builder:add_source_text_for_databg_attribute(): number of images found: ' . $count );

		if ( false === $count ) {
			return $html;
		}

		$enable_overlay_for_avada_background_images = $this->show_avada_background_overlay();

		$replaced = [];

		foreach ( $matches as $_match ) {
			$hash = md5( $_match[3] ); // $_match[2] is the image URL
			if ( in_array( $hash, $replaced, true ) ) {
				ISC_Log::log( 'Avada_Builder:add_source_text_for_databg_attribute() skipped a repeating element' );
				continue;
			} else {
				$replaced[] = $hash;
			}

			$image_id = (int) ISC_Model::get_image_by_url( $_match[3] );
			ISC_Log::log( sprintf( 'Avada_Builder:add_source_text_for_databg_attribute() found ID for image URL "%s": "%s"', $_match[1], $image_id ) );

			$source_string = Caption::get( $image_id, [], [ 'styled' => false ] );
			if ( ! $source_string ) {
				continue;
			}

			$old_content = $_match[1];
			$new_content = ' data-isc-source-text="' . esc_attr( $source_string ) . '"';

			// if the Avada Builder option is selected in the settings, the `data-isc-images` attribute is added to the element.
			// the attribute is automatically converted into a caption text added within the background element.
			if ( $enable_overlay_for_avada_background_images ) {
				$new_content .= ' data-isc-images="' . esc_attr( $image_id ) . '"';
			}

			$html = str_replace(
				$old_content,
				$old_content . $new_content,
				$html
			);
		}
		ISC_Log::log( 'Avada_Builder:add_source_text_for_databg_attribute(): number of unique image URLs replaced: ' . count( $replaced ) );

		return $html;
	}

	/**
	 * Return true if the overlay source should be displayed when the Avada Builder plugin is active.
	 *
	 * @return bool
	 */
	public function show_avada_background_overlay(): bool {
		$options = Plugin::get_options();
		return array_key_exists( 'overlay_included_advanced', $options ) && false !== array_search( 'avada_background_overlay', $options['overlay_included_advanced'], true );
	}
}
