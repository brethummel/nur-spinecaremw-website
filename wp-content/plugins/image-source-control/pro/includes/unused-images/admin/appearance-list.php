<?php

namespace ISC\Pro\Unused_Images\Admin;

use ISC\Plugin;
use ISC\Pro\Indexer\Indexer;
use ISC\Pro\Indexer\Index_Table;

/**
 * Render the content of the Appearances list in the appropriate column in the Media Library
 * and the list in the media details.
 * and the Appearances column in the Unused Images list
 */
class Appearances_List {

	/**
	 * Render a complete list that considers:
	 * - results from the database search for image IDs
	 * - results from the isc_index table from the frontend indexer
	 * - results from the image source index stored as post meta for images and posts
	 *
	 * @param int   $image_id post ID of the attachment.
	 * @param array $elements Enable certain elements. Disabled by default.
	 *                        - 'details' shows the details list.
	 *                        - 'checks' shows the check indicators.
	 *
	 * @return void
	 */
	public static function render( int $image_id, array $elements = [] ) {

		// get Indexer results
		$indexed_posts = self::get_indexer_posts( $image_id );

		// get database search results
		$database_results    = self::get_database_results( $image_id );
		$database_last_check = (int) get_post_meta( $image_id, 'isc_possible_usages_last_check', true );

		// get posts from the image sources index if the module is enabled
		$image_sources_index = self::get_image_sources_index( $image_id );

		// render the combined results
		$combined_results = self::combine_results(
			$indexed_posts,
			$database_results,
			$image_sources_index
		);

		if ( $combined_results ) {
			self::render_combined_results( $combined_results );
		} elseif ( ! $database_last_check ) {
				self::render_note( 'unchecked' );
		} else {
			self::render_note( 'unused' );
		}

		$options = Plugin::get_options();
		if ( ! empty( $options['unused_images']['appearances_details'] ) && in_array( 'details', $elements, true ) ) {
			self::render_details_list( $indexed_posts, $database_results, $database_last_check, $image_sources_index );
		}

		if ( in_array( 'checks', $elements, true ) ) {
			self::render_check_indicators( $image_id, $indexed_posts, $database_last_check, Indexer::is_indexer_expired() );
		}
	}

	/**
	 * Render quick notes
	 *
	 * @param string $note index of the note.
	 *
	 * @return void
	 */
	public static function render_note( string $note ) {
		$notes = [
			'unused'     => esc_html__( 'unused', 'image-source-control-isc' ),
			'unchecked'  => esc_html__( 'unchecked', 'image-source-control-isc' ),
			'no_results' => esc_html__( 'no results', 'image-source-control-isc' ),
		];

		if ( ! array_key_exists( $note, $notes ) ) {
			return;
		}

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		printf( '&mdash; %s &mdash;', $notes[ $note ] );
	}

	/**
	 * Return true if there is a last deep check
	 *
	 * @param int $image_id post ID of the attachment.
	 *
	 * @return bool
	 */
	public static function has_last_deep_check_results( int $image_id ): bool {
		return ! empty( get_post_meta( $image_id, 'isc_possible_usages', true ) );
	}

	/**
	 * Return a formatted array of the indexed posts for a given image ID.
	 * from the isc_index table
	 *
	 * @param int $image_id post ID of the attachment.
	 *
	 * @return array
	 */
	public static function get_indexer_posts( int $image_id ): array {
		$index_table   = new Index_Table();
		$indexed_posts = $index_table->get_by_attachment_id( $image_id );

		if ( count( $indexed_posts ) ) {
			foreach ( $indexed_posts as $post_id => $data ) {
				$indexed_posts[ $post_id ] = (object) [
					'ID'         => $post_id,
					'post_title' => get_the_title( $post_id ),
					'post_type'  => get_post_type( $post_id ),
				];
			}
		}

		return $indexed_posts;
	}

	/**
	 * Get last check date in the correct time format
	 *
	 * @param int $image_id post ID of the attachment.
	 *
	 * @return string
	 */
	public static function get_last_deep_check_date( int $image_id ): string {
		$last_check = get_post_meta( $image_id, 'isc_possible_usages_last_check', true );

		if ( $last_check ) {
			return human_time_diff( $last_check, time() );
		} else {
			return '';
		}
	}

	/**
	 * Return the post information from the index stored in the isc_image_posts meta
	 * in an expected format
	 *
	 * @param int $image_id post ID of the attachment.
	 *
	 * @return array
	 */
	public static function get_image_sources_index( int $image_id ) {
		if ( ! Plugin::is_module_enabled( 'image_sources' ) ) {
			return [];
		}

		$posts_formatted = [];

		$posts = get_post_meta( $image_id, 'isc_image_posts', true );
		if ( is_array( $posts ) && $posts !== [] ) {
			foreach ( $posts as $post_id ) {
				$posts_formatted[] = (object) [
					'ID'         => $post_id,
					'post_title' => get_the_title( $post_id ),
					'post_type'  => get_post_type( $post_id ),
				];
			}
		}

		return $posts_formatted ?? [];
	}

	/**
	 * Return results from the database search for image IDs
	 *
	 * @param int $image_id post ID of the attachment.
	 *
	 * @return array
	 */
	public static function get_database_results( int $image_id ): array {
		$database_results = get_post_meta( $image_id, 'isc_possible_usages', true );
		if ( ! is_array( $database_results ) ) {
			return [];
		}

		// load data for post IDs
		if ( array_key_exists( 'posts', $database_results ) && is_array( $database_results['posts'] ) ) {
			$posts = [];
			foreach ( $database_results['posts'] as $data ) {
				$posts[] = (object) [
					'ID'         => $data->ID,
					'post_title' => get_the_title( $data->ID ),
					'post_type'  => get_post_type( $data->ID ),
				];
			}
			$database_results['posts'] = $posts;
		}

		// load data for user IDs
		if ( array_key_exists( 'usermetas', $database_results ) && is_array( $database_results['usermetas'] ) ) {
			$user_ids = [];
			foreach ( $database_results['usermetas'] as $data ) {
				$user_ids[] = (int) $data->user_id;
			}

			$metas = []; // Initialize the metas array here

			// Only proceed with fetching user data if there are user IDs
			if ( ! empty( $user_ids ) ) {
				// Get unique user IDs to avoid redundant queries.
				$unique_user_ids = array_unique( $user_ids );

				// Fetch all necessary user data in a single query.
				$users_data = get_users(
					[
						'include' => $unique_user_ids,
						'fields'  => [ 'ID', 'display_name' ], // Only fetch necessary fields
					]
				);

				// Map user IDs to their display names for quick lookup.
				$users_map = [];
				foreach ( $users_data as $user ) {
					$users_map[ $user->ID ] = $user->display_name;
				}

				// Populate the metas array using the pre-fetched user data
				foreach ( $database_results['usermetas'] as $data ) {
					$user_id = (int) $data->user_id;
					// Use the pre-fetched display name, or fallback to the user ID if not found.
					$user_name = $users_map[ $user_id ] ?? $user_id;

					$metas[] = (object) [
						'user_id'   => $user_id,
						'meta_key'  => $data->meta_key,
						'user_name' => $user_name,
					];
				}
			}
			// Assign the (potentially empty or populated) metas array back to database_results
			$database_results['usermetas'] = $metas;
		}

		return $database_results;
	}

	/**
	 * Render combined results
	 *
	 * @param array $combined_results Combined results containing posts, postmetas, and options.
	 *
	 * @return void
	 */
	public static function render_combined_results( array $combined_results ) {
		echo "<div class='isc-appearances-list-combined'>";

		if ( ! empty( $combined_results['posts'] ) ) {
			$posts = $combined_results['posts'];
			include ISCPATH . 'pro/admin/templates/unused-images/appearance-list/results-in-post-content.php';
		}

		if ( ! empty( $combined_results['postmetas'] ) ) {
			$postmetas = $combined_results['postmetas'];
			include ISCPATH . 'pro/admin/templates/unused-images/appearance-list/results-in-postmeta.php';
		}

		if ( ! empty( $combined_results['options'] ) ) {
			$options = $combined_results['options'];
			include ISCPATH . 'pro/admin/templates/unused-images/appearance-list/results-in-options.php';
		}

		if ( ! empty( $combined_results['usermetas'] ) ) {
			$usermetas = $combined_results['usermetas'];
			include ISCPATH . 'pro/admin/templates/unused-images/appearance-list/results-in-usermeta.php';
		}

		echo '</div>';
	}

	/**
	 * Render indicators showing which checks have been performed.
	 *
	 * @param int   $image_id            Attachment ID.
	 * @param array $indexed_posts       Posts found by the indexer.
	 * @param int   $database_last_check Timestamp of the last database check.
	 * @param bool  $indexer_expired     Whether the indexer is expired.
	 *
	 * @return void
	 */
	public static function render_check_indicators( int $image_id, array $indexed_posts, int $database_last_check, $indexer_expired = false ) {

		// set the URL for the indexer page if the index is empty or outdated
		if ( $indexer_expired || empty( $indexed_posts ) ) {
			$indexer_url = admin_url( 'options.php?page=isc-indexer' );
		} else {
			$indexer_url = '';
		}

		$has_db_check = ! empty( $database_last_check );
		$time_diff    = self::get_last_deep_check_date( $image_id );

		include ISCPATH . 'pro/admin/templates/unused-images/appearance-list/checks.php';
	}

	/**
	 * Render the details section of the appearances list.
	 *
	 * @param array $indexed_posts       Posts found by the indexer.
	 * @param array $database_results    Results from the database search.
	 * @param int   $database_last_check Timestamp of the last deep check.
	 * @param array $image_sources_index Posts from the image sources index.
	 *
	 * @return void
	 */
	public static function render_details_list( array $indexed_posts, array $database_results, $database_last_check, array $image_sources_index ) {
		include ISCPATH . 'pro/admin/templates/unused-images/appearance-list/details.php';
	}

	/**
	 * Combine results from different sources into a single array.
	 *
	 * Format:
	 * [posts] => [
	 *   (object) [
	 *    'ID'         => int,
	 *    'post_title' => string,
	 *    'post_type'  => string,
	 *   ],
	 *  [postmeta] => [
	 *   (object) [
	 *   'post_id'  => int,
	 *   'meta_key' => string,
	 *  ],
	 * 'options' => [
	 *  (object) [
	 *   'option_name' => string,
	 *   'search_type' => string, // e.g., ID
	 *  ],
	 * 'usermetas' => [
	 *   (object) [
	 *    'user_id'    => int,
	 *    'meta_key'   => string,
	 *    'user_name'  => string,
	 *   ],
	 *  ]
	 *
	 * @param array $indexed_posts       Indexed posts from the isc_index table.
	 * @param array $database_results    Results from the database search for image IDs.
	 * @param array $image_sources_index Posts from the image sources index.
	 *
	 * @return array Combined results containing posts, postmetas, and options.
	 */
	public static function combine_results( array $indexed_posts, array $database_results, array $image_sources_index ): array {
		$full_list = [];

		// Add database results
		if ( ! empty( $database_results ) ) {
			$full_list = $database_results;
		}

		// Add indexed posts
		if ( ! empty( $indexed_posts ) ) {
			if ( array_key_exists( 'posts', $full_list ) ) {
				$full_list['posts'] = array_values( array_unique( array_merge( $indexed_posts, $full_list['posts'] ), SORT_REGULAR ) );
			} else {
				$full_list['posts'] = $indexed_posts;
			}
		}

		// Add image sources index results
		if ( ! empty( $image_sources_index ) ) {
			if ( array_key_exists( 'posts', $full_list ) ) {
				$full_list['posts'] = array_values( array_unique( array_merge( $image_sources_index, $full_list['posts'] ), SORT_REGULAR ) );
			} else {
				$full_list['posts'] = $image_sources_index;
			}
		}

		return $full_list;
	}
}
