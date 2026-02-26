<?php
namespace ISC\Pro;

use ISC\Plugin;
use ISC\Pro\Indexer\Indexer;
use ISC\Helpers;
use ISC\Pro\Unused_Images\Admin\Appearances_Column;
use ISC\Pro\Unused_Images\Admin\Appearances_List;

/**
 * Logic to handle unused images
 */
class Unused_Images extends \ISC\Unused_Images {

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
		if ( ! Plugin::is_module_enabled( 'unused_images' ) ) {
			return;
		}

		new Appearances_Column();

		add_action( 'admin_init', [ $this, 'settings_init' ] );
		add_action( 'admin_menu', [ $this, 'add_menu_items' ] );
		add_filter(
			'isc_admin_pages',
			function ( $pages ) {
				$pages[] = 'media_page_isc-unused-images';
				return $pages;
			}
		);

		// extend the media library details with the Appearances list
		add_filter( 'attachment_fields_to_edit', [ $this, 'add_appearance_list_to_media_details' ], 10, 2 );

		// add relevant scripts
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );

		// AJAX call to store the information
		add_action( 'wp_ajax_isc-unused-images-deep-check', [ $this, 'check_database' ] );
	}

	/**
	 * Initialize settings
	 */
	public function settings_init() {
		new \ISC\Settings\Sections\Unused_Images();
	}

	/**
	 * Add menu items
	 */
	public function add_menu_items() {
		// add submenu page to the Media options
		add_submenu_page(
			'upload.php',
			esc_html__( 'Unused Images', 'image-source-control-isc' ),
			esc_html__( 'Unused Images', 'image-source-control-isc' ),
			'manage_options',
			'isc-unused-images',
			[ $this, 'render_sources_page' ]
		);
	}

	/**
	 * Enqueue scripts and styles
	 */
	public function enqueue_scripts() {
		$screen = get_current_screen();
		if ( empty( $screen->id ) || $screen->id !== 'media_page_isc-unused-images' ) {
			return;
		}

		// add the script
		Helpers::enqueue_script( 'isc_pro_unused_images', 'pro/admin/assets/js/unused-images.js' );

		// add style
		wp_enqueue_style( 'isc_pro_unused_images_css', ISCBASEURL . 'pro/admin/assets/css/unused-images.css', [], ISCVERSION );
	}

	/**
	 * Add Appearances list to the Media details page
	 *
	 * @param array  $form_fields Attachment form fields.
	 * @param object $post        WP_Post object.
	 *
	 * @return array
	 */
	public function add_appearance_list_to_media_details( array $form_fields, object $post ): array {
		ob_start();
		Appearances_List::render( $post->ID );
		require_once ISCPATH . 'pro/admin/templates/unused-images/deep-check-link.php';

		// add a list of posts the image is used in
		$form_fields['isc_image_usage'] = [
			'label' => __( 'Appearances', 'image-source-control-isc' ),
			'input' => 'html',
			'html'  => ob_get_clean(),
		];

		return $form_fields;
	}

	/**
	 * Missing sources page callback
	 */
	public function render_sources_page() {
		if ( ! \ISC\Pro\Admin\License::is_valid() ) {
			$attachments      = self::get_unused_attachments();
			$attachment_count = count( $attachments );
			require_once ISCPATH . '/pro/admin/templates/unused-images/license-invalid.php';
		} else {
			require_once ISCPATH . 'pro/includes/unused-images/admin/list-table.php';
			$unused_images_list_table = new Unused_Images_List_Table();
			$unused_images_list_table->prepare_items();
			$views           = $unused_images_list_table->get_views();
			$indexer_expired = Indexer::is_indexer_expired();

			require_once ISCPATH . '/pro/admin/templates/unused-images/page.php';
		}
	}

	/**
	 * Get all attachments that are not used
	 * We are using a custom query since WP_Query is not flexible enough
	 *
	 * @param array $args arguments for the query.
	 *
	 * @return array|object|null query results objects or post IDs.
	 */
	public static function get_unused_attachments( array $args = [] ) {
		global $wpdb;

		$offset        = isset( $args['offset'] ) ? (int) $args['offset'] : 0;
		$limit         = isset( $args['limit'] ) ? (int) $args['limit'] : self::ESTIMATE_LIMIT;
		$filter        = $args['filter'] ?? '';
		$attachment_id = isset( $args['attachment_id'] ) ? (int) $args['attachment_id'] : 0;

		$index_table = $wpdb->prefix . 'isc_index';

		/**
		 * If the image ID was not found in the isc_index table, the image is not known to ISC.
		 *
		 * Base filter query - check if attachment is not in the index table and not used as featured image
		 * `_thumbnail_id` is the post meta key for the featured image of a post. Image IDs in here are considered used images
		 *     though we cannot be 100% sure if the theme makes use of them
		 */
		$filter_query = "AND NOT EXISTS (
            SELECT 1 FROM {$index_table} idx
            WHERE idx.attachment_id = p.ID
        )
        AND NOT EXISTS (
            SELECT 1 FROM {$wpdb->postmeta} featured
            WHERE featured.meta_value = p.ID
            AND featured.meta_key = '_thumbnail_id'
        )";

		switch ( $filter ) {
			case 'unchecked':
				$filter_query .= "AND NOT EXISTS (
			        SELECT 1 FROM {$wpdb->postmeta} possible_usages
			        WHERE possible_usages.post_id = p.ID
			        AND possible_usages.meta_key = 'isc_possible_usages'
			    )";
				break;
			case 'unused':
				$filter_query .= "AND EXISTS (
			        SELECT 1 FROM {$wpdb->postmeta} possible_usages
			        WHERE possible_usages.post_id = p.ID
			        AND possible_usages.meta_key = 'isc_possible_usages'
			        AND possible_usages.meta_value = 'a:0:{}'
			    )";
				break;
		}

		// override filter query if an attachment ID is given
		if ( $attachment_id ) {
			$filter_query = "AND p.ID = $attachment_id";
		}

		// Add image type check if images_only is enabled
		if ( \ISC\Media_Type_Checker::enabled_images_only_option() ) {
			$filter_query .= " AND p.post_mime_type LIKE 'image/%'";
		}

		// Attachment IDs that are considered used
		if ( ! $attachment_id ) {
			$dynamic_excludes = self::get_definitely_used_image_ids();

			if ( ! empty( $dynamic_excludes ) ) {
				$placeholders  = implode( ',', array_fill( 0, count( $dynamic_excludes ), '%d' ) );
				$filter_query .= $wpdb->prepare(
					// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared,WordPress.DB.PreparedSQLPlaceholders
					" AND p.ID NOT IN ($placeholders)",
					...$dynamic_excludes
				);
			}
		}

		/**
		 * We are not considering `post_parent` relevant here, since an image might have been uploaded to a post once, but no longer be used in there
		 */
		$query = "SELECT p.*, attachment_meta.meta_value as metadata
			    FROM {$wpdb->posts} p
			    LEFT JOIN {$wpdb->postmeta} attachment_meta ON attachment_meta.post_id = p.ID AND attachment_meta.meta_key = '_wp_attachment_metadata'
			    WHERE p.post_type = 'attachment'
			    $filter_query
			    LIMIT %d, %d";

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
		return $wpdb->get_results(
			$wpdb->prepare(
			// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
				$query,
				$offset,
				$limit
			)
		);
	}

	/**
	 * Get the post to which a given image was uploaded
	 *
	 * @param int $image_id post ID of the attachment.
	 *
	 * @return array|null|\WP_Post
	 */
	public static function get_uploaded_to_post( int $image_id ) {
		$parent_post_id = wp_get_post_parent_id( $image_id );

		if ( ! $parent_post_id ) {
			return null;
		}

		return get_post( $parent_post_id );
	}

	/**
	 * AJAX call to check the database for usages of a given image
	 */
	public function check_database() {
		check_ajax_referer( 'isc-admin-ajax-nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			die( 'Wrong capabilities' );
		}

		if ( empty( $_REQUEST['image_id'] ) ) {
			die( 'Missing information' );
		}

		$image_id = (int) $_REQUEST['image_id'];

		if ( ! $image_id ) {
			die( 'Image ID invalid' );
		}

		$model         = new \ISC_Model();
		$search_string = $model->get_base_file_url( $image_id );
		if ( ! $search_string ) {
			delete_post_meta( $image_id, 'isc_possible_usages' );
			delete_post_meta( $image_id, 'isc_possible_usages_last_check' );
			die( esc_html__( 'No image URL found.', 'image-source-control-isc' ) );
		}

		// perform the search
		( new \ISC\Pro\Unused_Images\Database_Check_Model() )->search( $image_id );

		// render the results
		Appearances_List::render( $image_id, [ 'checks', 'details' ] );

		die();
	}

	/**
	 * Return an array of attachment IDs that are considered used
	 * E.g., site_icon, etc.
	 *
	 * @return int[]
	 */
	protected static function get_definitely_used_image_ids(): array {
		$ids = [];

		// site_icon is the Icon for the site
		$site_icon = get_option( 'site_icon' );
		if ( $site_icon && is_numeric( $site_icon ) ) {
			$ids[] = (int) $site_icon;
		}

		/**
		 * Filter the list of attachment IDs that are considered used
		 *
		 * @param int[] $ids List of attachment IDs.
		 */
		$ids = (array) apply_filters( 'isc_unused_images_ids_considered_used', $ids );

		// cleanup and unique
		return array_filter( array_unique( array_map( 'absint', $ids ) ) );
	}
}
