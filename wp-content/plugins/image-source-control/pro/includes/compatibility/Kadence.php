<?php

namespace ISC\Pro\Compatibility;

use ISC\Plugin;
use ISC_Log;

/**
 * Provide compatibility with Kadence Products:
 * - Kadence Blocks, esp., Gallery block
 * - Kadence Related Content Carousel
 */
class Kadence {

	/**
	 * Construct an instance of Kadence
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
		// enable only, if the Kadence Related Content Carousel or Kadence Blocks plugin is enabled
		if ( ( ! defined( 'KTRC_VERSION' ) && ! defined( 'KADENCE_BLOCKS_VERSION' ) )
		|| ! Plugin::is_module_enabled( 'image_sources' ) ) {
			return;
		}

		// adjust the general regular expression to also search for DIVs between the image and the link tag.
		add_filter( 'isc_public_caption_regex', [ $this, 'public_caption_regex' ] );
		// filter the matches from the regular expression to apply some fixes.
		add_filter( 'isc_extract_images_from_html', [ $this, 'filter_matches' ], 10, 2 );
	}

	/**
	 * Adjust the general regular expression to also search for DIVs between the image and the link tag.
	 *
	 * @param string $regex The regular expression.
	 *
	 * @return string
	 *
	 * @see ISC_Model::extract_images_from_html()
	 */
	public function public_caption_regex( string $regex ): string {

		ISC_Log::log( 'ISC_Pro_Compatibility_Kadence: overriding the caption regex' );
		/**
		 * Compared to the original regular expression, this one finds an optional DIV tag between the image and the link tag.
		 * the appropriate part of that in the regular expression is this: (\s*<div[^>]*>)*\s*
		 * The DIV tag does not show up in the matches.
		 */
		return '#(?:<figure[^>]*class="([^"]*)"[^>]*>\s*)?((<a[\x20|\x9|\xD|\xA]+[^>]*>)?\s*(?:<div[^>]*>)*\s*(<img[\x20|\x9|\xD|\xA]+[^>]*[^>]*src="(.+)".*\/?>).*(?:\s*</div>)*?(?:\s*</a>)??)[^<]*#isU';
	}

	/**
	 * Filter the matches from the regular expression to apply some fixes.
	 * With the adjusted regular expression, the DIV and A tags are not part of the inner content of the matches, namely match[3]
	 * I was unable to adjust the regular expression to include the DIV tag in the matches, so I’m doing it here manually.
	 *
	 * Since we are overriding the existing matches, we are ignoring the first parameter.
	 *
	 * @param mixed $matches The current matches from the regular expression.
	 * @param mixed $matches_original The matches from the original regular expression.
	 */
	public function filter_matches( $matches, $matches_original ): array {
		if ( ! is_array( $matches_original ) || ! $matches_original ) {
			return $matches_original;
		}

		return array_map(
			function ( $match ) {
				return [
					'full'         => $match[0] ?? '',
					'figure_class' => $match[1] ?? '',
					'inner_code'   => $match[2] ?? '',
					'img_src'      => $match[5] ?? '',
				];
			},
			$matches_original
		);
	}
}
