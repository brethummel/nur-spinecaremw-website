<?php

namespace ISC\Pro;

use ISC\Image_Sources\Renderer\Caption;

/**
 * Custom Attribute Processor class to convert image URLs in specific HTML elements to ISC attributes and outputs.
 * e.g.
 * <span data-bgsrc="https://example.com/image.jpg"></span>
 * will be converted to
 * <span data-bgsrc="https://example.com/image.jpg" data-isc-source-text="Image Source"></span>
 * the `data-isc-images` attribute will be added if the overlay is enabled.
 * <span data-bgsrc="https://example.com/image.jpg" data-isc-source-text="Image Source" data-isc-images"123"></span>
 */
class Custom_Attribute_Processor {

	/**
	 * The regex pattern to match the elements.
	 *
	 * @var string
	 */
	private $regex_pattern;

	/**
	 * Replaced group key in the matches of the pattern
	 *
	 * @var int
	 */
	private $replaced_match_index = 0;

	/**
	 * Group key in the matches of the pattern with the URL
	 *
	 * @var int
	 */
	private $url_match_index;

	/**
	 * Whether to enable the overlay attribute.
	 *
	 * @var bool
	 */
	private $enable_overlay = false;

	/**
	 * Additional arguments.
	 *
	 * Unused, but could include something like "included_strings" or "excluded_strings" for more tests that would not be possible in the regex.
	 *
	 * @var array
	 */
	private $args = [];

	/**
	 * Constructor method for the class. Set the variables.
	 *
	 * @param string   $regex_pattern        The regex pattern to match the elements.
	 * @param int      $replaced_match_index The index of the replaced group in the matches of the pattern.
	 * @param int|bool $url_match_index      The index of the URL group in the matches of the pattern.
	 * @param bool     $enable_overlay       Whether to enable the overlay attribute.
	 * @param array    $args                 Additional arguments.
	 */
	public function __construct( string $regex_pattern = '', int $replaced_match_index = 0, $url_match_index = null, bool $enable_overlay = null, array $args = [] ) {

		// Stop if the pattern is not provided
		if ( $regex_pattern !== '' ) {
			$this->regex_pattern = $regex_pattern;
		} else {
			\ISC_Log::log( 'Custom_Attribute_Processor: No regex pattern provided.' );
			return;
		}

		// Stop if the URL match index is not provided
		if ( null !== $url_match_index ) {
			$this->url_match_index = $url_match_index;
		} else {
			\ISC_Log::log( 'Custom_Attribute_Processor: No URL match index provided.' );
			return;
		}

		// Set the replaced match index if provided
		if ( null !== $replaced_match_index ) {
			$this->replaced_match_index = $replaced_match_index;
		}

		// Set the enable overlay if provided
		if ( null !== $enable_overlay ) {
			$this->enable_overlay = $enable_overlay;
		}

		// Set the additional arguments
		$this->args = $args;

		add_filter( 'isc_public_caption_regex_content', [ $this, 'process_html_content' ], 9 );
	}

	/**
	 * Processes the HTML content and adds attributes based on the regex pattern.
	 *
	 * @param string $html The HTML content to process.
	 *
	 * @return string The modified HTML content.
	 */
	public function process_html_content( string $html ): string {
		$count = preg_match_all( $this->regex_pattern, $html, $matches, PREG_SET_ORDER );

		if ( false === $count ) {
			return $html;
		}

		$replaced = [];

		foreach ( $matches as $_match ) {
			$old_content = $_match[ $this->replaced_match_index ];
			$hash        = md5( $old_content );
			$url         = $_match[ $this->url_match_index ];

			// Skip repeating elements
			if ( in_array( $hash, $replaced, true ) ) {
				continue;
			} else {
				$replaced[] = $hash;
			}

			// Skip if the full match contains "data-isc-source-text" already, which is possible if multiple processors are used.
			if ( false !== strpos( $_match[0], 'data-isc-source-text' ) ) {
				\ISC_Log::log( sprintf( 'Custom_Attribute_Processor: Skipping element with URL %s due to existing data-isc-source-text attribute.', $url ) );
				continue;
			}

			// Skip if the full match the "wp-image-" class string since that is handled already for images
			if ( false !== strpos( $_match[0], 'wp-image-' ) && false !== strpos( $_match[0], '<img' ) ) {
				\ISC_Log::log( sprintf( 'Custom_Attribute_Processor: Skipping img tag with URL %s due to existing wp-image- attribute.', $url ) );
				continue;
			}

			$image_id = (int) \ISC_Model::get_image_by_url( $url );

			$source_string = Caption::get( $image_id, [], [ 'styled' => false ] );
			if ( ! $source_string ) {
				continue;
			}

			$new_content = ' data-isc-source-text="' . esc_attr( $source_string ) . '"';

			/*
			 * If overlay is enabled, add the overlay attribute as well.
			 * This will be converted into a visible caption overlay by the ISC plugin.
			 */
			if ( $this->enable_overlay ) {
				$new_content .= ' data-isc-images="' . esc_attr( $image_id ) . '"';
			}

			// Replace the old content with the new one
			$html = str_replace(
				$old_content, // The full matched attribute string
				$old_content . $new_content, // Append new attributes
				$html
			);
		}

		return $html;
	}
}
