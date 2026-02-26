<?php

namespace ISC\Pro\Indexer;

class Indexer {

	/**
	 * Post meta key for storing the last index timestamp
	 */
	const LAST_INDEX_META_KEY = 'isc_last_index';

	/**
	 * Max days since last index check
	 *
	 * @var int
	 */
	const MAX_DAYS_SINCE_LAST_CHECK = 7;

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'plugins_loaded', [ $this, 'plugins_loaded' ] );

		if ( is_admin() ) {
			new Indexer_Admin();
		} else {
			new Indexer_Public();
		}
	}

	/**
	 * Load all hooks
	 */
	public function plugins_loaded() {
		add_action( 'delete_attachment', [ $this, 'delete_attachment_index' ] );
		add_action( 'deleted_post', [ $this, 'delete_post_index' ] );
		add_action( 'trashed_post', [ $this, 'delete_post_index' ] );
		add_action( 'wp_insert_post', [ $this, 'remove_post_index_meta' ] );
		add_action( 'post_updated', [ $this, 'remove_post_index_meta' ] );
	}

	/**
	 * Delete index entries after an attachment was deleted
	 *
	 * @param int $post_id WP_Post ID.
	 */
	public function delete_attachment_index( int $post_id ) {
		$index_table = new Index_Table();
		$index_table->delete_by_attachment_id( $post_id );
	}

	/**
	 * Delete index entries after a post was deleted
	 *
	 * @param int $post_id WP_Post ID.
	 */
	public function delete_post_index( int $post_id ) {
		$index_table = new Index_Table();
		$index_table->delete_by_post_id( $post_id );
	}

	/**
	 * Remove the post index metadata to force re-indexing on next visit
	 *
	 * @param int $post_id The post ID to remove index metadata for.
	 * @return bool True if metadata was deleted, false otherwise
	 */
	public function remove_post_index_meta( int $post_id ) {
		return delete_post_meta( $post_id, self::LAST_INDEX_META_KEY );
	}

	/**
	 * Return true if the oldest entries of the indexer are older than the given time
	 *
	 * @return bool True if the indexer is expired, false otherwise.
	 */
	public static function is_indexer_expired(): bool {
		$index_table     = new Index_Table();
		$oldest_entry    = $index_table->get_oldest_entry_date();

		return $oldest_entry < time() - ( self::MAX_DAYS_SINCE_LAST_CHECK * DAY_IN_SECONDS );
	}
}
