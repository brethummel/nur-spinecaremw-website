<?php
/**
 * Class ISC_Pro_Admin_Storage_Form
 *
 * Edit data of images in the ISC Storage that are not part of the media library
 */
class ISC_Pro_Admin_Storage_Form {

	/**
	 * ISC_Pro_Admin_Storage_Form constructor
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
		// add the form fields under Media > Image Sources > Additional images
		add_filter(
			'isc_admin_sources_storage_table_source_row',
			[
				$this,
				'add_edit_fields',
			]
		);
		// AJAX call to store the information
		add_action( 'wp_ajax_isc-update-storage-image', [ $this, 'update_storage_images' ] );

		// add custom CSS to the footer
		add_action( 'admin_footer', [ $this, 'add_custom_footer_code' ] );
	}

	/**
	 * Display image source edit fields
	 *
	 * @param int $image_url URL of the image.
	 */
	public function add_edit_fields( $image_url ) {

		// encoded image URL so that it can be used as a key in the form fields
		$image_key = base64_encode( $image_url );

		require dirname( __DIR__ ) . '/templates/storage-form-fields.php';
	}

	/**
	 * Update attachment information using AJAX
	 */
	public function update_storage_images() {
		check_ajax_referer( 'isc-admin-ajax-nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			die( 'Wrong capabilities' );
		}

		if ( empty( $_REQUEST['image_key'] ) || empty( $_REQUEST['field'] ) ) {
			die( 'Missing information' );
		}

		// validate if the image is part of the storage
		$image_url     = base64_decode( $_REQUEST['image_key'] );
		$storage_model = new ISC_Storage_Model();
		if ( ! $storage_model->is_image_url_in_storage( $image_url ) ) {
			return;
		}

		// store new information
		switch ( $_REQUEST['field'] ) {
			// move to media library
			// print the link to the edit page
			case 'isc-move-to-media-library':
				$attachment_id = self::create_attachment_from_url( $image_url );
				edit_post_link( esc_html__( 'Edit details', 'image-source-control-isc' ), '', '', $attachment_id );
				break;
		}

		die();
	}

	/**
	 * Custom CSS for the footer of the attachment list page
	 * used under Media > Image Sources > Additional images
	 *
	 * Custom JavaScript to store the values automatically
	 */
	public function add_custom_footer_code() {
		$screen = get_current_screen();
		if ( empty( $screen->id ) || $screen->id !== 'media_page_isc-sources' ) {
			return;
		}
		?>
		<script>
		<?php
		require_once ISCPATH . 'pro/admin/assets/js/storage.js';
		?>
		</script>
		<?php
	}

	/**
	 * Create a new WP attachment from an image URL
	 *
	 * @param string $url URL of the image as used as key in the storage array.
	 * @return null|int attachment ID.
	 */
	public function create_attachment_from_url( $url ) {
		$file_name        = basename( $url );
		$file_type        = wp_check_filetype( $file_name, null );
		$attachment_title = sanitize_file_name( pathinfo( $file_name, PATHINFO_FILENAME ) );

		if ( ! isset( $file_type['type'] ) ) {
			return null;
		}

		$post_info = [
			'guid'           => esc_url( $url ),
			'post_mime_type' => $file_type['type'],
			'post_title'     => $attachment_title,
			'post_content'   => '',
			'post_status'    => 'inherit',
		];

		// Create the attachment
		$attachment_id = wp_insert_attachment( $post_info, false );

		if ( ! $attachment_id ) {
			return null;
		}

		// add the post ID to the storage
		$storage_model = new ISC_Storage_Model();
		$storage_model->update_post_id( $url, $attachment_id );

		// assign the metadata to attachment
		ISC_Model::update_post_meta( $attachment_id, 'isc_imported_into_library', 1 );

		// return post ID or null if element exists
		return $attachment_id;
	}
}


