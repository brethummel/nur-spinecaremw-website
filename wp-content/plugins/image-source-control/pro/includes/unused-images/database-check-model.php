<?php

namespace ISC\Pro\Unused_Images;

use ISC\Plugin;

/**
 * Search for images in the database
 */
class Database_Check_Model {
	/**
	 * Options to ignore
	 */
	const IGNORED_OPTIONS = [
		'code_snippets_settings', // https://wordpress.org/plugins/code-snippets/, no attachment IDs included
		'cron',
		'elementor_remote_info_library', // https://wordpress.org/plugins/elementor/, IDs are not for local resources so they can be ignored
		'elementor_submissions_db_version', // https://wordpress.org/plugins/elementor/
		'elementor_notes_db_version', // https://wordpress.org/plugins/elementor/
		'essential-dashboard', // Essential grid, no attachment IDs included
		'ez-toc-post-content-core-level', // https://wordpress.org/plugins/easy-table-of-contents/, contains outdated data on my site; the option is also (no longer?) part of that plugin
		'icl_sitepress_settings', // WPML, no attachment IDs included
		'isc_storage',
		'loco_settings', // https://wordpress.org/plugins/loco-translate/, no attachment IDs included
		'strcpv_visits_by_page', // https://wordpress.org/plugins/page-visits-counter-lite/
		'thread_comments_depth', // not an attachment ID
		'widget_recent-posts', // not an attachment ID
		'widget_recent-comments', // not an attachment ID
		'woocommerce_inbox_variant_assignment', // https://wordpress.org/plugins/woocommerce/, not an attachment ID
		'wp_mail_smtp_migration_version', // https://wordpress.org/plugins/wp-mail-smtp/, not an attachment ID
		'/newsletter_backup_.*/', // https://www.thenewsletterplugin.com/, not relevant
	];

	/**
	 * Post Meta Keys to ignore
	 */
	const IGNORED_POST_META_KEYS = [
		'isc_possible_usages',
		'isc_post_images',
	];

	/**
	 * AJAX call to check the database for usages of a given image
	 *
	 * @param int $image_id ID of the image to check.
	 *
	 * @return bool
	 */
	public function search( $image_id ): bool {
		if ( ! $image_id ) {
			return false;
		}

		$model         = new \ISC_Model();
		$search_string = $model->get_base_file_url( $image_id );
		if ( ! $search_string ) {
			delete_post_meta( $image_id, 'isc_possible_usages' );
			delete_post_meta( $image_id, 'isc_possible_usages_last_check' );
			return false;
		}

		// remove the extension from the file URL
		// this makes sure that we also find image files with suffixes
		// original file path: https://example.com/image.png
		// search string: https://example.com/image
		// finds also "https://example.com/image-150x150.png" in the database
		$search_string = str_replace( '.' . pathinfo( $search_string, PATHINFO_EXTENSION ), '', $search_string );

		$results = [];

		// search the string in wp_posts’ post_content
		$posts = $this->search_in_content( $search_string, $image_id );
		if ( $posts ) {
			$results['posts'] = $posts;
		}

		// search the string in wp_postmeta’s meta_value
		$postmetas = $this->search_filepath_in_postmeta( $search_string, $image_id );
		if ( $postmetas ) {
			$results['postmetas'] = $postmetas;
		}

		// search the string and ID in wp_options’s option_value
		$options = $this->search_in_options( $search_string, $image_id );
		if ( $options ) {
			$results['options'] = $options;
		}

		// search the string and ID in wp_usermeta
		$usermetas = $this->search_filepath_in_user_meta( $search_string );
		if ( $usermetas ) {
			$results['usermetas'] = $usermetas;
		}

		// store the results for later use
		update_post_meta( $image_id, 'isc_possible_usages', $results );
		update_post_meta( $image_id, 'isc_possible_usages_last_check', time() );

		return true;
	}

	/**
	 * Search for the string and the attachment ID in post content
	 *
	 * @param string $search_string search string.
	 * @param int    $image_id post ID of the attachment.
	 *
	 * @return array|object|null list of posts that contain the string or the attachment ID in their content
	 */
	private function search_in_content( string $search_string, int $image_id ) {
		$result1 = $this->search_filepath_in_post_content( $search_string );
		$result2 = $this->search_attachment_id_in_content( $image_id );
		return array_merge( $result1, $result2 );
	}

	/**
	 * Search for a string in the post_content of all posts
	 * except for revisions
	 *
	 * @param string $search_string string to search for.
	 * @return array|object|null list of posts that contain the string
	 */
	public function search_filepath_in_post_content( $search_string ) {
		global $wpdb;
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
		return $wpdb->get_results( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type != 'revision' AND post_status != 'trash' AND post_content LIKE %s", '%' . $search_string . '%' ) );
	}

	/**
	 * Search for the attachment ID in post content
	 * It could be part of a shortcode (e.g., WP Bakery) there
	 *
	 * @param int $attachment_id attachment ID.
	 *
	 * @return array|object|null list of posts that contain the attachment ID
	 */
	public function search_attachment_id_in_content( int $attachment_id ) {
		$options = Plugin::get_options();

		if ( empty( $options['unused_images']['deep_checks'] ) || ! in_array( 'ID in content', $options['unused_images']['deep_checks'], true ) ) {
			return [];
		}

		global $wpdb;

		$query = $wpdb->prepare(
			"SELECT ID
				FROM $wpdb->posts
				WHERE post_type != 'revision'
				  AND post_status != 'trash'
				  AND (
					  post_content LIKE %s
					  OR post_content LIKE %s
				  )",
			'%image="' . $attachment_id . '"%',
			'%picture="' . $attachment_id . '"%'
		);

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared
		$results = $wpdb->get_results( $query );

		// add search_type=id to the results
		foreach ( $results as $result ) {
			$result->search_type = 'id';
		}

		return $results;
	}

	/**
	 * Search for a string in the meta_value of all postmeta, excluding specific keys.
	 *
	 * @param string $search_string string to search for.
	 * @param int    $image_id post ID of the attachment.
	 *
	 * @return array|object|null list of post IDs and keys that contain the string
	 */
	public function search_filepath_in_postmeta( $search_string, $image_id ) {
		global $wpdb;

		$excluded_meta_keys = apply_filters( 'isc_unused_images_ignored_post_meta_keys', $this::IGNORED_POST_META_KEYS );

		if ( ! is_array( $excluded_meta_keys ) ) {
			$excluded_meta_keys = [];
		}

		// Base SQL query parts
		$sql_select = 'SELECT postmeta.post_id, postmeta.meta_key';
		$sql_from   = "FROM {$wpdb->postmeta} AS postmeta LEFT JOIN {$wpdb->posts} AS posts ON posts.ID = postmeta.post_id";
		$sql_where  = "WHERE posts.post_type != 'revision'";

		// Prepare arguments for the query dynamically
		$query_args = [];

		// Add the NOT IN clause safely if there are keys to exclude
		if ( ! empty( $excluded_meta_keys ) ) {
			// Create the correct number of %s placeholders
			$placeholders = implode( ', ', array_fill( 0, count( $excluded_meta_keys ), '%s' ) );
			$sql_where   .= " AND postmeta.meta_key NOT IN ( {$placeholders} )";
			// Add the keys themselves to the beginning of the arguments array
			$query_args = array_merge( $query_args, $excluded_meta_keys );
		}

		// Add the post_id exclusion
		$sql_where   .= ' AND postmeta.post_id != %d';
		$query_args[] = (int) $image_id;

		// Add the meta_value LIKE clause
		$sql_where .= ' AND postmeta.meta_value LIKE %s';
		// Use esc_like for safety with LIKE comparisons
		$query_args[] = '%' . $wpdb->esc_like( $search_string ) . '%';

		// Combine the SQL parts
		$sql = "{$sql_select} {$sql_from} {$sql_where}";

		// Prepare the final query with all arguments
		$prepared_sql = $wpdb->prepare( $sql, $query_args );

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
		return $wpdb->get_results( $prepared_sql );
	}

	/**
	 * Search for a string in the option_value of all options
	 *
	 * @param string $search_string string to search for.
	 *
	 * @return array|object|null list of option names that contain the string
	 */
	public function search_filepath_in_options( $search_string ) {
		global $wpdb;
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
		return $wpdb->get_results( $wpdb->prepare( "SELECT option_name FROM $wpdb->options WHERE option_value LIKE %s", '%' . $search_string . '%' ) );
	}

	/**
	 * Search for the attachment ID in the option_value of all options
	 *
	 * @param int $attachment_id attachment ID.
	 *
	 * @return array|object|null list of option names that contain the attachment ID
	 */
	public function search_attachment_id_in_options( int $attachment_id ) {
		global $wpdb;
		$attachment_id     = (int) $attachment_id; // Ensure it's an integer
		$serialized_format = ';i:' . $attachment_id . ';'; // Serialized format

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
		$results = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT option_name, option_value FROM $wpdb->options WHERE option_value = %s OR option_value LIKE %s",
				$attachment_id,
				'%' . $wpdb->esc_like( $serialized_format ) . '%'
			)
		);

		// further cleanup
		$filtered_results = [];
		foreach ( $results as $row ) {
			// remove transient options
			if ( strpos( $row->option_name, '_transient_' ) === 0
				|| strpos( $row->option_name, '_site_transient_' ) === 0 ) {
				continue;
			}

			// remove options where the attachment ID is an array key
			$unserialized = \ISC\Helpers::maybe_unserialize( $row->option_value );
			// remove the option value from the returned array
			unset( $row->option_value );
			if ( is_array( $unserialized ) && \ISC\Helpers::is_value_in_multidimensional_array( $attachment_id, $unserialized ) ) {
				$filtered_results[] = $row;
			} elseif ( ! is_array( $unserialized ) ) {
				$filtered_results[] = $row;
			}

			$row->search_type = 'id';
		}

		return $filtered_results;
	}

	/**
	 * Search for the string and the attachment ID in the option_value of all options
	 *
	 * @param string $search_string search string.
	 * @param int    $image_id post ID of the attachment.
	 *
	 * @return array|object|null list of option names that contain the string or the attachment ID
	 */
	public function search_in_options( string $search_string, int $image_id ) {
		$result1 = $this->search_filepath_in_options( $search_string );
		$result2 = $this->search_attachment_id_in_options( $image_id );
		$options = array_merge( $result1, $result2 );

		// Get the full list of ignored options, including those added via filter.
		$all_ignored_options = apply_filters( 'isc_unused_images_ignored_options', self::IGNORED_OPTIONS );

		// Filter out options that are explicitly ignored or match a regex pattern.
		return array_filter(
			$options,
			function ( $option ) use ( $all_ignored_options ) {
				$option_name = $option->option_name;

				// Check for exact matches first.
				if ( in_array( $option_name, $all_ignored_options, true ) ) {
					return false;
				}

				// Check for regex matches.
				foreach ( $all_ignored_options as $ignored_pattern ) {
					// Check if the pattern is a valid regex literal (starts and ends with / and has at least one character in between, optionally followed by modifiers).
					// Using '#' as delimiter for the detection regex itself to avoid conflicts with '/' in the pattern.
					if ( preg_match( '#^/(.+)/([a-zA-Z]*)$#', $ignored_pattern ) ) {
						// It's a regex literal, now try to match the option name against it.
						// The $ignored_pattern string already contains the delimiters and modifiers.
						if ( preg_match( $ignored_pattern, $option_name ) ) {
							return false;
						}
					}
				}

				return true;
			}
		);
	}

	/**
	 * Search for a string in the meta_value of all usermeta.
	 *
	 * @param string $search_string string to search for.
	 *
	 * @return array|object|null list of user IDs and keys that contain the string
	 */
	public function search_filepath_in_user_meta( $search_string ) {
		global $wpdb;

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
		return $wpdb->get_results(
			$wpdb->prepare(
				"SELECT user_id, meta_key FROM {$wpdb->usermeta} WHERE meta_value LIKE %s",
				'%' . $wpdb->esc_like( $search_string ) . '%'
			)
		);
	}
}
