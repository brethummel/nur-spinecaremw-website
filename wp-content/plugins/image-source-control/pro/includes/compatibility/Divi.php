<?php
namespace ISC\Pro\Compatibility;

use ET_Builder_Element;
use ISC\Plugin;

/**
 * Provide compatibility with the Divi Theme and Builder:
 * https://www.elegantthemes.com/gallery/divi/
 *
 * - add the caption overlay to background images
 */
class Divi {

	/**
	 * Module slugs that support image sources for background images
	 * The list can be extended by other modules, but one should always check if the module layout can handle the source output
	 *
	 * @var string[]
	 */
	public const SUPPORTED_MODULES = [
		'et_pb_row',
		'et_pb_section',
	];

	/**
	 * Constructor
	 */
	public function __construct() {
		// Divi itself uses init on priority 10, so we use 11 to ensure that our hooks are registered after Divi’s
		add_action( 'init', [ $this, 'register_hooks' ], 11 );
	}

	/**
	 * Register hooks
	 *
	 * @return void
	 */
	public function register_hooks(): void {
		// enable only, if Divi exists in general
		if ( ! defined( 'ET_SHORTCODES_VERSION' ) ) {
			return;
		}

		// add the setting
		add_action( 'isc_admin_settings_overlay_included_images_after', [ $this, 'render_option' ] );
		// validate settings on save
		add_filter( 'isc_settings_on_save_after_validation', [ $this, 'validate_settings_on_save' ], 10, 2 );

		// enable only, if the Divi Builder Class we extend exists, which means, we are in the frontend
		// we also look out for the WP_TESTS_DOMAIN constant, which is set in the WordPress test environment
		// and if the appropriate setting is enabled
		if ( ( class_exists( 'ET_Builder_Element', false ) || defined( 'WP_TESTS_DOMAIN' ) ) && Plugin::is_module_enabled( 'image_sources' ) ) {
			$options = Plugin::get_options();
			if ( isset( $options['divi_background_images'] ) && $options['divi_background_images'] ) {
				// add the 'isc-source' class and the 'data-isc-images' attribute to specific Divi modules with a background image
				add_filter( 'et_module_shortcode_output', [ $this, 'add_module_attributes_and_class' ], 10, 3 );
				// also add a class to fullwidth images to prevent layout breaking
				add_filter( 'et_module_shortcode_output', [ $this, 'add_image_fullwidth_class' ], 10, 3 );
				// add custom styles to the front end to ensure that the ISC source captions are displayed correctly
				add_action( 'wp_head', [ $this, 'front_head' ] );
			}
		}
	}

	/**
	 * Add the 'isc-source' class and the 'data-isc-images' attribute to Divi modules with a background image.
	 * This is done on the final output, as there is no native hook for these modifications
	 * in the provided version of the file.
	 *
	 * @param string             $output          The HTML output of the rendered module.
	 * @param string             $render_slug     The slug of the module being rendered.
	 * @param ET_Builder_Element $module_instance The instance of the module.
	 *
	 * @return array|string|string[]
	 */
	public function add_module_attributes_and_class( $output, $render_slug, $module_instance ) {
		// I don’t know why, but the output can be an array in some irrelevant cases (legacy modules?), so we check for that
		if ( is_array( $output ) || empty( $output ) ) {
			return $output;
		}

		// Check if the module slug is supported for background images.
		if ( ! in_array( $render_slug, self::SUPPORTED_MODULES, true ) ) {
			return $output;
		}

		// Check if the 'background_image' property exists and is not empty.
		if ( ! empty( $module_instance->props['background_image'] ) ) {
			$image_url = $module_instance->props['background_image'];
			$image_id  = attachment_url_to_postid( $image_url );

			if ( $image_id ) {
				// This pattern finds the 'class="' string in the opening div tag.
				$pattern = '/class="/';

				// The replacement string prepends the data-attribute and adds the new class
				// right after the opening quote of the class attribute.
				$replacement = 'data-isc-images="' . esc_attr( $image_id ) . '" class="isc-source ';

				// Perform the replacement, limited to 1 occurrence.
				$new_output = preg_replace( $pattern, $replacement, $output, 1 );

				// Only return the new output if the replacement was successful.
				if ( null !== $new_output ) {
					return $new_output;
				}
			}
		}

		// Return the original output if no changes were made.
		return $output;
	}

	/**
	 * Add the isc_image_has_fullwidth class to et_pb_image modules with the "force_fullwidth" property set
	 * This class will then later be used for a custom CSS rule to prevent ISC overlays from breaking the layout
	 *
	 * @param string             $output          The HTML output of the rendered module.
	 * @param string             $render_slug     The slug of the module being rendered.
	 * @param ET_Builder_Element $module_instance The instance of the module.
	 *
	 * @return array|string
	 */
	public function add_image_fullwidth_class( $output, $render_slug, $module_instance ) {
		// I don’t know why, but the output can be an array in some irrelevant cases (legacy modules?), so we check for that
		if ( is_array( $output ) || empty( $output ) ) {
			return $output;
		}

		// Check if we are dealing with the et_pb_image module
		if ( $render_slug !== 'et_pb_image' ) {
			return $output;
		}

		// Check the 'force_fullwidth' property
		if ( ! empty( $module_instance->props['force_fullwidth'] ) && $module_instance->props['force_fullwidth'] === 'on' ) {
			// This pattern finds the 'class="' string in the opening div tag.
			$pattern = '/class="/';

			// Add the new class right after the opening quote of the class attribute.
			$replacement = 'class="isc_image_has_fullwidth ';

			// Perform the replacement, limited to 1 occurrence.
			$new_output = preg_replace( $pattern, $replacement, $output, 1 );

			// Only return the new output if the replacement was successful.
			if ( null !== $new_output ) {
				return $new_output;
			}
		}

		// Return the original output if no changes were made.
		return $output;
	}

	/**
	 * Add custom styles to the front end to ensure that the ISC source captions are displayed correctly.
	 *
	 * @return void
	 */
	public function front_head() {
		// generate the style tag dynamically for all supported modules
		$selectors = array_map(
			function ( $module ) {
				return '.' . $module . '.isc-source';
			},
			self::SUPPORTED_MODULES
		);
		/**
		 * 1. line: make sure the overlay is displayed correctly within specific Divi modules
		 * 2. line: make sure the overlay is displayed correctly within fullwidth image modules in fullwidth sections
		 * 3. line: prevent the overlay breaking the layout of image modules that have force_fullwidth enabled, see add_image_fullwidth_class()
		 */
		?>
		<style>
			<?php echo implode( ', ', $selectors ); ?> { display: inherit; }
			.et_pb_fullwidth_image .isc-source { display: inherit; }
			.isc_image_has_fullwidth.et_pb_image .isc-source { position: revert; display: revert; }
		</style>
		<?php
	}

	/**
	 * Show an option under Settings > Overlay to enable Divi compatibility
	 */
	public function render_option() {
		$options                       = Plugin::get_options();
		$enable_divi_background_images = ! empty( $options['divi_background_images'] );

		require_once ISCPATH . 'pro/admin/templates/settings/divi.php';
	}

	/**
	 * Add Pro options when saving the settings page
	 *
	 * @param array $options sanitized options.
	 * @param array $input_options options as they come from the settings page.
	 * @return array sanitized options
	 */
	public function validate_settings_on_save( $options, $input_options ) {
		$options['divi_background_images'] = ! empty( $input_options['divi_background_images'] );

		return $options;
	}
}
