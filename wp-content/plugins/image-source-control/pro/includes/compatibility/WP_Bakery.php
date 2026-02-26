<?php

namespace ISC\Pro\Compatibility;

use ISC\Plugin;
use ISC_Log;
use ISC\Image_Sources\Renderer\Caption;

/**
 * Provide compatibility with the WP Bakery page builder (previously known as Visual Composer)
 */
class WP_Bakery {

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
		// enable only, if the WP Bakery plugin is enabled
		if ( ! defined( 'WPB_VC_VERSION' ) || ! Plugin::is_module_enabled( 'image_sources' ) ) {
			return;
		}

		// add the source markup to HTML tags with background images added by WP Bakery. Needs to run before add_caption_from_isc_images_attribute()
		add_filter( 'isc_public_caption_regex_content', [ $this, 'add_source_text_for_background_image_css' ], 9 );
	}

	/**
	 * Add the source markup to HTML tags with background images added by WP Bakery.
	 * 1. Looking for any HTML tag with a specific ID in a class
	 * 2. Looking for that class in wp_postmeta
	 * 3. Extract image URLs from that meta information
	 *
	 * @param string $html The HTML content to process.
	 *
	 * @return string The modified HTML content.
	 */
	public function add_source_text_for_background_image_css( string $html ): string {

		// only run if the overlay is enabled for WP Bakery background images
		if ( ! $this->show_wp_bakery_background_overlay() ) {
			return $html;
		}

		/**
		 * Match groups:
		 * 0 - Full match: the full DIV tag if it contains a class with a specific ID.
		 * 1 - Full match without the closing ">" so that we can inject more attributes into the starting DIV tag
		 * 2 - The class ID.
		 */
		$pattern = '#(<div[\x20|\x9|\xD|\xA]+[^>]*class="[^"]*(vc_custom_\d+)[^>]*)\/?>#i';
		$count   = preg_match_all( $pattern, $html, $matches, PREG_SET_ORDER );

		ISC_Log::log( 'WP_Bakery:add_source_text_for_background_image_css(): number of containers with class ID found: ' . $count );

		if ( $count === false ) {
			return $html;
		}

		$replaced = [];
		$post_id  = get_the_ID();

		foreach ( $matches as $_match ) {
			$hash = md5( $_match[2] ); // $_match[2] is the class with the ID
			if ( in_array( $hash, $replaced, true ) ) {
				ISC_Log::log( 'WP_Bakery:add_source_text_for_background_image_css() skipped a repeating element' );
				continue;
			} else {
				$replaced[] = $hash;
			}

			// look for the ID in wp_postmeta
			$image_id = self::get_image_id_from_postmeta( $_match[2], $post_id );

			if ( ! $image_id ) {
				continue;
			}

			$source_string = Caption::get( $image_id );
			if ( ! $source_string ) {
				continue;
			}

			$old_content = $_match[1];

			// the `data-isc-images` attribute is added to the element.
			// the attribute is automatically converted into a caption text added within the background element.
			$new_content = ' data-isc-images="' . esc_attr( $image_id ) . '"';

			// add the main ISC class to the element to render the overlay
			$content_with_new_classes = str_replace(
				$_match[2],
				$_match[2] . ' isc-source',
				$old_content
			);

			$html = str_replace(
				$old_content,
				$content_with_new_classes . $new_content,
				$html
			);
		}

		return $html;
	}

	/**
	 * Get the image ID from the postmeta table.
	 *
	 * @param string $class_id The class ID to search for.
	 * @param int    $post_id  The post ID to search in.
	 */
	private static function get_image_id_from_postmeta( string $class_id, int $post_id ): int {
		// search wp_postmeta for the key `_wpb_shortcodes_custom_css` with the class ID as part of the value
		global $wpdb;
		$class_id = esc_sql( $class_id );

		if ( $post_id ) {
			$query = "SELECT post_id, meta_value FROM $wpdb->postmeta WHERE post_id = $post_id AND meta_key = '_wpb_shortcodes_custom_css' AND meta_value LIKE '%$class_id%' LIMIT 1";
		} else {
			$query = "SELECT post_id, meta_value FROM $wpdb->postmeta WHERE meta_key = '_wpb_shortcodes_custom_css' AND meta_value LIKE '%$class_id%' LIMIT 1";
		}

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.NoCaching
		$results = $wpdb->get_results( $query );

		if ( ! $results ) {
			return 0;
		}

		$content = $results[0]->meta_value;

		if ( empty( $content ) ) {
			return 0;
		}

		/**
		 * The searched post meta blocks could contain CSS for multiple containers
		 * so the pattern needs to be more specific to only find image URLs after the class ID
		 * depending on the complexity, WP Bakery uses `background` or `background-image` with `url()` not necessarily being the first property
		 */
		$pattern = '#' . $class_id . '{[^}]*background[^;]*url\(([^)]*)\?id=(\d*)#is';

		/**
		 * Match groups
		 * 0 - full match (including first char)
		 * 1 - image URL without image ID
		 * 2 - image ID
		 */
		preg_match_all( $pattern, $content, $matches, PREG_SET_ORDER );

		if ( ! count( $matches ) ) {
			return 0;
		}

		$image_id = (int) $matches[0][2];

		ISC_Log::log( sprintf( 'WP_Bakery:get_image_id_from_postmeta found image ID "%s" for URL %s', $image_id, $matches[0][1] ) );

		// return only the first image URL
		return $image_id;
	}

	/**
	 * Return true if the overlay source should be displayed for background images added by the WP Bakery plugin.
	 *
	 * @return bool
	 */
	public function show_wp_bakery_background_overlay(): bool {
		$options = Plugin::get_options();
		return ! empty( $options['display_type'] )
			&& is_array( $options['display_type'] )
			&& in_array( 'overlay', $options['display_type'], true )
			&& array_key_exists( 'overlay_included_advanced', $options )
			&& in_array( 'wp_bakery_background_overlay', $options['overlay_included_advanced'], true );
	}
}
