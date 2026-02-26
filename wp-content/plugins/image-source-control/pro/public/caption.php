<?php

namespace ISC\Pro;

use ISC\Plugin;

/**
 * Caption-related frontend functionality
 */
class Caption {

	/**
	 * Premium caption style slugs
	 */
	const CAPTION_STYLES = [ 'hover', 'click' ];

	/**
	 * Construct
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
		add_filter( 'isc_caption_apply_default_style', [ $this, 'use_default_caption_style' ], 10, 2 );
		add_filter( 'isc_overlay_html_source', [ $this, 'render_caption_style' ], 10, 2 );

		if ( ! Plugin::is_module_enabled( 'image_sources' ) ) {
			return;
		}

		add_action( 'wp_footer', [ $this, 'add_caption_css' ] );
		add_action( 'isc_public_caption_default_style', [ $this, 'filter_caption_style' ] );
	}

	/**
	 * Disable the default caption style if any premium style is applied
	 *
	 * @param bool $value The return value.
	 *
	 * @return bool
	 */
	public function use_default_caption_style( bool $value ): bool {
		$style = Plugin::get_options()['caption_style'];

		if ( in_array( $style, self::CAPTION_STYLES, true ) ) {
			return false;
		}

		return $value;
	}

	/**
	 * Select a caption style
	 *
	 * @param string $source The source string.
	 * @param int    $image_id The image ID.
	 *
	 * @return string
	 */
	public function render_caption_style( $source, $image_id ) {
		$options = Plugin::get_options();
		$pretext = $options['source_pretext'];

		// iterate through selected style options
		switch ( $options['caption_style'] ) {
			case 'hover':
				// replace pretext icon with pretext markup
				$source = str_replace( $pretext . ' ', '<span class="isc-source-text-icon">' . $pretext . '</span><span>', $source );
				$source = '<span class="isc-source-text">' . $source . '</span></span>';
				break;
			case 'click':
				// use details > summary markup which naturally expands on click
				$source = str_replace( $pretext . ' ', '<summary>' . $pretext . '</summary>', $source );
				$source = '<details class="isc-source-text">' . $source . '</details>';
				break;
			default:
				break;
		}

		return $source;
	}

	/**
	 * Add caption CSS to the page footer
	 */
	public function add_caption_css() {
		$options = Plugin::get_options();

		// iterate through selected style options
		switch ( $options['caption_style'] ) {
			case 'hover':
				// add hover caption CSS
				echo '<style>';
				require_once ISCPATH . 'pro/public/assets/css/caption/hover.css';
				echo '</style>';
				break;
			case 'click':
				// add click caption CSS
				echo '<style>';
				require_once ISCPATH . 'pro/public/assets/css/caption/click.css';
				echo '</style>';
				break;
			default:
				break;
		}
	}

	/**
	 * Filter the style that is dynamically added to the caption using JavaScript
	 *
	 * @param array $caption_style The caption style array.
	 *
	 * @return array
	 */
	public static function filter_caption_style( array $caption_style ): array {
		$options = Plugin::get_options();

		// this caption style is set dynamically to not interfere with the default caption style logic
		// that prevents captions from jumping into position when being loaded above the image
		if ( $options['caption_style'] === 'click' ) {
			$caption_style['display'] = 'flex !important;';
		}

		return $caption_style;
	}
}
