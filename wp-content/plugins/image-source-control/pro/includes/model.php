<?php
/**
 * Class ISC_Pro_Model
 *
 * Logic to get and store sources
 */
class ISC_Pro_Model {

	/**
	 * Find any image URL in the content
	 * useful to find also image URLs that are used passively like in background-css rules or HTML attributes
	 *
	 * @param string $content any HTML code.
	 * @return array with image ids => image src uri-s
	 */
	public static function get_ids_from_any_image_url( $content = '' ) {
		$srcs = [];

		ISC_Log::log( 'Pro: enter get_ids_from_any_image_url() to look for image IDs within the content' );

		if ( empty( $content ) ) {
			ISC_Log::log( 'Pro: exit get_ids_from_any_image_url() due to missing content' );
			return $srcs;
		}

		// Extract image URLs from content
		$urls = \ISC\Image_Sources\Analyze_HTML::extract_image_urls_from_html_tags( $content );

		if ( ! count( $urls ) ) {
			ISC_Log::log( 'Pro: exit get_ids_from_any_image_url(): no URLs found' );
			return $srcs;
		}

		ISC_Log::log( sprintf( 'Pro: found %d unique URLs', count( $urls ) ) );

		foreach ( $urls as $_url ) {
			ISC_Log::log( sprintf( 'found src "%s"', $_url ) );
			$id = ISC_Model::get_image_by_url( $_url );
			if ( $id ) {
				$srcs[ $id ] = $_url;
			}
		}

		/**
		 * Filter image IDs found in HTML content, or add new ones based on other rules.
		 *
		 * @since 2.9.0
		 *
		 * @param string[] $srcs image sources with image ids => image src uri
		 * @param string $content any HTML document
		 */
		return apply_filters( 'isc_filter_any_image_ids_from_content', $srcs, $content );
	}

	/**
	 * Remove line breaks from text and source code
	 * useful when using a longer regular expression to code that has many line breaks
	 *
	 * @param string $content original content.
	 * @return string content without line breaks.
	 */
	public static function remove_line_breaks( $content ) {
		return preg_replace( "/\r|\n/", '', $content );
	}
}
