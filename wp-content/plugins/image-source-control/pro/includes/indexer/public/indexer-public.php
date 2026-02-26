<?php

namespace ISC\Pro\Indexer;

use ISC\Plugin;
use ISC_Pro_Model;

/**
 * Frontend logic for Unused Images
 */
class Indexer_Public {

	/**
	 * Seconds after which an index is considered to be expired
	 */
	const EXPIRATION_PERIOD = 7 * DAY_IN_SECONDS;

	/**
	 * Unused_Images_Public constructor.
	 */
	public function __construct() {
		add_action( 'init', [ $this, 'register_hooks' ] );
	}

	/**
	 * Load all hooks
	 */
	public function register_hooks() {
		if ( ! Plugin::is_module_enabled( 'unused_images' ) ) {
			return;
		}

		// index the featured image
		add_action( 'wp', [ $this, 'index_featured_image_on_view' ] );
		// index images in the content
		add_filter( 'the_content', [ $this, 'index_content' ], PHP_INT_MAX );
	}

	/**
	 * Index the featured image when a single page is opened
	 *
	 * @return void
	 */
	public function index_featured_image_on_view() {
		if ( ! is_singular() ) {
			return;
		}

		$post_id = get_the_ID();

		// don’t index if the index isn’t expired and this isn’t the bot running
		if ( ! $this->index_for_post_expired( $post_id ) && ! \ISC\Indexer::is_index_bot() ) {
			return;
		}

		// Get the featured image ID
		$featured_image_id = get_post_thumbnail_id( $post_id );

		$index_table = new Index_Table();

		// If no featured image, delete any existing entry for 'thumbnail'
		if ( ! $featured_image_id ) {
			$index_table->delete_by_post_id( $post_id, 'thumbnail' );
			return;
		}

		// Update the index table
		$index_table->insert_or_update( $post_id, $featured_image_id, 'thumbnail' );
	}

	/**
	 * Index used images in the content
	 * Even though the `isc_after_update_indexes` action hook gives us access to the post content,
	 * this could also be the <body> content, depending on the Image Sources settings
	 *
	 * @param string $content post content.
	 *
	 * @return string
	 */
	public function index_content( $content = '' ) {

		if ( ! $this->is_indexable_page() ) {
			return $content;
		}

		$post_id = get_the_ID();

		// don’t index if the index isn’t expired and this isn’t the bot running
		if ( ! $this->index_for_post_expired( $post_id ) && ! \ISC\Indexer::is_index_bot() ) {
			return $content;
		}

		$index_table = new Index_Table();
		$image_ids   = ISC_Pro_Model::get_ids_from_any_image_url( $content );

		// If this is a global list page, we don't want to index the content,
		// but in case this happened in the past, we delete the index.
		// we also set the last index timestamp to the current time to prevent more index calls for a while
		if ( \ISC\Indexer::is_global_list_page( $content ) ) {
			$index_table->delete_by_post_id( $post_id, 'content' );
			update_post_meta( $post_id, Indexer::LAST_INDEX_META_KEY, time() );
			return $content;
		}

		// If no images are found, remove all content images from the database, if there were some previously
		// and update the last index timestamp and return
		if ( empty( $image_ids ) ) {
			$index_table->delete_by_post_id( $post_id, 'content' );
			update_post_meta( $post_id, Indexer::LAST_INDEX_META_KEY, time() );
			return $content;
		}

		// prepare data for index table
		$data = [];
		foreach ( $image_ids as $image_id => $image_url ) {
			$data[] = [
				'attachment_id' => $image_id,
				'position'      => 'content',
			];
		}

		$index_table->bulk_update_by_post_id( $post_id, $data, 'content' );

		// Update the last index timestamp
		update_post_meta( $post_id, Indexer::LAST_INDEX_META_KEY, time() );

		return $content;
	}

	/**
	 * Check if this is an indexable page
	 *
	 * @returns bool
	 */
	public function is_indexable_page(): bool {

		$post_id = get_the_ID();

		if ( ! $post_id || ! is_singular() ) {
			return false;
		}

		// return if this is not the main query or within the loop
		if ( ! \ISC_Public::is_main_loop() ) {
			return false;
		}

		if ( ! \ISC\Indexer::can_index_the_page() ) {
			return false;
		}

		return true;
	}

	/**
	 * Checks if the index for a post is expired or if it needs to be indexed
	 *
	 * @param int $post_id The post ID to check.
	 * @return bool True if expired or needs indexing, false otherwise
	 */
	public function index_for_post_expired( int $post_id ): bool {
		// Check the post meta for last index time
		$last_index_time = get_post_meta( $post_id, Indexer::LAST_INDEX_META_KEY, true );

		// If no last index time or it's expired, we need to index
		if ( empty( $last_index_time ) ) {
			return true;
		}

		// Check if the difference between current time and last index is greater than the expiration period
		return ( time() - intval( $last_index_time ) ) > self::EXPIRATION_PERIOD;
	}
}
