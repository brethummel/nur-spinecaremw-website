<?php

namespace ISC\Pro\Admin;

use ISC\Admin_Utils;

/**
 * Features for all Image Source related columns
 */
class Image_Sources_Columns {

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
		if ( ! \ISC\Plugin::is_module_enabled( 'image_sources' ) ) {
			return;
		}

		// add custom CSS to the footer
		add_action( 'admin_footer', [ $this, 'add_custom_footer_code' ] );
	}

	/**
	 * Custom CSS for the footer of the attachment list page
	 * used under Media > Library > Mode=List
	 *
	 * Custom JavaScript to store the values automatically
	 */
	public function add_custom_footer_code() {
		// bounce if we are not in the list view
		if ( ! Admin_Utils::is_media_library_list_view_page() ) {
			return;
		}
		?><style>
			.isc-admin-list-view-column-hide {
				display: none;
				float: right;
			}
			.table-view-list.media thead th:hover .isc-admin-list-view-column-hide {
				display: inline-block;
				cursor: pointer;
			}
		</style>
		<script>
			<?php
			require_once ISCPATH . 'pro/admin/assets/js/columns.js';
			?>
		</script>
		<?php
	}
}


