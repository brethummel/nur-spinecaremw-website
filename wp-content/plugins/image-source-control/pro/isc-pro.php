<?php
/**
 * Load ISC Pro
 */

const ISCPRO = true; // used by the base plugin
require_once ISCPATH . 'pro/lib/autoload.php';
new ISC\Pro\WP_Caption();
new ISC\Pro\IPTC();
new ISC\Pro\List_Layout_Details();
new ISC\Pro\Indexer\Indexer();

/**
 * Load the correct text domain from the language folder
 */
function isc_pro_load_textdomain() {
	load_plugin_textdomain( 'image-source-control-isc', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	// translation path for block editor code
	add_filter(
		'isc_path_to_languages',
		function () {
			return ISCPATH . 'pro/languages';
		}
	);
}
add_action( 'init', 'isc_pro_load_textdomain' );

if ( is_admin() ) {
	new ISC_Pro_Admin();
	new ISC\Pro\Unused_Images();
} elseif ( ! is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {
	// include frontend functions
	new ISC_Pro_Public();
	new ISC\Pro\Caption();
}

// load compatibility classes
require_once ISCPATH . 'pro/includes/compatibility.php';