<?php

namespace ISC\Pro;

use ISC\Plugin;
use ISC\Standard_Source;

/**
 * Handle support for copyright information in IPTC data
 */
class IPTC {
	/**
	 * Constructor
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
		if ( ! Plugin::is_module_enabled( 'image_sources' ) ) {
			return;
		}

		// frontend
		// load the IPTC copyright text into the standard source
		add_filter( 'isc_standard_source_text_for_attachment', [ $this, 'get_iptc_source_text' ], 10, 2 );

		// admin

		add_action( 'isc_admin_settings_standard_source_options', [ $this, 'render_iptc_standard_source_option' ] );
		// adjust ISC source form fields in the Media library (not the block editor)
		add_filter( 'isc_admin_attachment_form_fields', [ $this, 'extend_isc_fields' ], 10, 3 );
		// add footer scripts
		add_action( 'admin_footer', [ $this, 'admin_footer_scripts' ] );
	}


	/**
	 * Get the IPTC caption text from the image meta data
	 *
	 * @param string $text          current text.
	 * @param int    $attachment_id attachment ID.
	 *
	 * @return string
	 */
	public function get_iptc_source_text( string $text, int $attachment_id ): string {
		// check if the standard source text is set to IPTC meta data
		if ( ! Standard_Source::standard_source_is( 'iptc' ) ) {
			return $text;
		}

		$image_meta = wp_get_attachment_metadata( $attachment_id );

		// define the order of tags to check based on the user's preference
		$tags_to_check = ( self::get_iptc_source_tag() === 'credit' ) ? [ 'credit', 'copyright' ] : [ 'copyright', 'credit' ];

		foreach ( $tags_to_check as $tag ) {
			if ( ! empty( $image_meta['image_meta'][ $tag ] ) ) {
				return $image_meta['image_meta'][ $tag ];
			}
		}

		return '';
	}

	/**
	 * Show an option under Settings > Miscellaneous Settings > Standard source to use IPTC data
	 */
	public function render_iptc_standard_source_option() {
		$standard_source     = Standard_Source::get_standard_source();
		$standard_source_tag = self::get_iptc_source_tag();
		require_once ISCPATH . 'pro/admin/templates/settings/standard-source-iptc.php';
	}

	/**
	 * Get the IPTC source tag
	 *
	 * @return string
	 */
	public static function get_iptc_source_tag(): string {
		$options = Plugin::get_options();
		return ! empty( $options['standard_source_iptc_tag'] ) ? esc_attr( $options['standard_source_iptc_tag'] ) : 'credit';
	}

	/**
	 * Add IPTC suggestions to the ISC source form fields in the Media library
	 *
	 * @param array  $form_fields Attachment form fields.
	 * @param object $post        WP_Post object.
	 * @param array  $options     ISC options.
	 *
	 * @return array
	 */
	public function extend_isc_fields( array $form_fields, object $post, array $options ) {
		$form_fields['isc_image_source']['helps'] = $form_fields['isc_image_source']['helps'] . self::get_source_text_suggestion_list( $post->ID );
		return $form_fields;
	}

	/**
	 * Render a list of image source suggestions
	 * based on image meta data (IPTC)
	 *
	 * @param int $attachment_id ID of the post object.
	 * @return string|void HTML of the suggestion list
	 */
	private static function get_source_text_suggestion_list( $attachment_id ) {
		$suggestions = [];
		$image_meta  = wp_get_attachment_metadata( $attachment_id );

		if ( ! empty( $image_meta['image_meta']['credit'] ) ) {
			$suggestions[] = $image_meta['image_meta']['credit'];
		}
		if ( ! empty( $image_meta['image_meta']['copyright'] ) ) {
			$suggestions[] = $image_meta['image_meta']['copyright'];
		}

		// remove duplicates
		$suggestions = array_unique( $suggestions );

		if ( count( $suggestions ) ) {
			ob_start();
			require_once ISCPATH . 'pro/admin/templates/iptc-suggestions.php';
			return ob_get_clean();
		}
	}

	/**
	 * Add scripts to the admin area
	 */
	public function admin_footer_scripts() {
		$screen = get_current_screen();
		if ( empty( $screen->id ) ) {
			return;
		}

		// handle suggestions on attachment and post edit pages
		if ( in_array( $screen->id, [ 'attachment', 'post' ], true ) ) {
			echo "<script>document.addEventListener('click', function (event) {
			if ( ! event.target.matches( '.isc-source-suggestions li a' ) ) {
				return;
			}
			event.preventDefault();
			// move text to input field
			document.querySelector( '.compat-field-isc_image_source input.text' ).value = event.target.text;
		}, false);</script>";

			echo '<style>.isc-source-suggestions:hover .hidden { display: inline-block; }
				.isc-source-suggestions ul { padding: revert; }
				.isc-source-suggestions li { list-style: disc; }
				.isc-source-suggestions a { cursor: pointer; }
				</style>';
		}
	}
}
