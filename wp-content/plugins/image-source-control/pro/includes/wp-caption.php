<?php

namespace ISC\Pro;

use ISC\Standard_Source;
/**
 * An option to the standard source to use the WordPress Caption as default source.
 */
class WP_Caption {
	/**
	 * Constructor
	 */
	public function __construct() {

		// frontend
		add_filter( 'isc_standard_source_text_for_attachment', [ $this, 'get_caption_text' ], 10, 2 );

		// admin

		add_action( 'isc_admin_settings_standard_source_options', [ $this, 'render_caption_standard_source_option' ] );
	}

	/**
	 * Get the WP caption text
	 *
	 * @param string $text          current text.
	 * @param int    $attachment_id attachment ID.
	 *
	 * @return string
	 */
	public function get_caption_text( string $text, int $attachment_id ): string {
		// check if the standard source text is set to WP caption
		if ( ! Standard_Source::standard_source_is( 'wp_caption' ) ) {
			return $text;
		}

		return get_post_field( 'post_excerpt', $attachment_id );
	}

	/**
	 * Show an option under Settings > Miscellaneous Settings > Standard source to use the WP Caption
	 */
	public function render_caption_standard_source_option() {
		$standard_source = Standard_Source::get_standard_source();
		require_once ISCPATH . 'pro/admin/templates/settings/standard-source-caption.php';
	}
}
