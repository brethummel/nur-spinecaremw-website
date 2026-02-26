<?php

namespace ISC\Pro\Indexer;

/**
 * Indexer
 *
 * This class is responsible for indexing all published content and extracting image URLs
 */
class Index_Run {

	/**
	 * Default batch size for fetching URLs
	 *
	 * @var int
	 */
	public int $url_batch_size = 100;

	/**
	 * Start or resume indexing process for a single URL
	 *
	 * @param array $url_data URL data for the item to process (id, url).
	 * @param bool  $execute_as_admin Whether to execute requests as admin user.
	 *
	 * @return array
	 */
	public function index_single_item( array $url_data, bool $execute_as_admin = false ): array {
		if ( empty( $url_data['id'] ) || empty( $url_data['url'] ) ) {
			return [
				'error' => 'Invalid URL data provided.',
			];
		}

		$post_id = (int) $url_data['id'];
		$url     = $url_data['url'];

		if ( $post_id <= 0 ) {
			return [
				'error' => 'Invalid post ID.',
			];
		}

		// we call the frontend here to cause indexing
		$this->fetch_content( $url, $execute_as_admin );

		$index_table  = new \ISC\Pro\Indexer\Index_Table();
		$post_images  = $index_table->get_by_post_id( $post_id );
		$images_count = count( $post_images );

		return [
			'id'           => $post_id,
			'title'        => get_the_title( $post_id ),
			'url'          => $url,
			'post_type'    => get_post_type( $post_id ),
			'images_count' => $images_count,
		];
	}

	/**
	 * Get the total count of public content
	 *
	 * @param string $indexing_mode The indexing mode: 'all' or 'unindexed'
	 * @return int
	 */
	public function get_total_content_count( string $indexing_mode = 'all' ): int {
		if ( 'unindexed' === $indexing_mode ) {
			return count( $this->get_unindexed_post_ids() );
		}

		$count      = 0;
		$post_types = get_post_types( [ 'public' => true ] );

		foreach ( $post_types as $post_type ) {
			$num_posts = wp_count_posts( $post_type );
			// Sum published posts count
			$count += $num_posts->publish;
		}

		return $count;
	}

	/**
	 * Get an array with post IDs that haven’t been indexed yet
	 *
	 * @return array unindexed post IDs
	 */
	public function get_unindexed_post_ids(): array {
		global $wpdb;

		$post_types              = get_post_types( [ 'public' => true ] );
		$post_types_placeholders = implode( ',', array_fill( 0, count( $post_types ), '%s' ) );

		$query = $wpdb->prepare(
			"SELECT p.ID
			FROM {$wpdb->posts} p
			LEFT JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id AND pm.meta_key = %s
			WHERE p.post_type IN ($post_types_placeholders)
			AND p.post_status = 'publish'
			AND pm.post_id IS NULL
			",
			array_merge( [ Indexer::LAST_INDEX_META_KEY ], $post_types )
		);

		$results = $wpdb->get_col( $query );

		return $results ? array_map( 'intval', $results ) : [];
	}

	/**
	 * Get a batch of public content URLs to index
	 *
	 * @param int    $offset Offset to start from.
	 * @param int    $batch_size Number of items to retrieve.
	 * @param string $indexing_mode The indexing mode: 'all' or 'unindexed'
	 *
	 * @return array Array of URL data (id, url)
	 */
	public function get_content_urls_batch( int $offset = 0, int $batch_size = null, string $indexing_mode = 'all' ): array {
		if ( 'unindexed' === $indexing_mode ) {
			return $this->get_unindexed_content_urls_batch( $offset, $batch_size );
		}

		return $this->get_all_content_urls_batch( $offset, $batch_size );
	}

	/**
	 * Get a batch of all public content URLs to index
	 *
	 * @param int $offset Offset to start from.
	 * @param int $batch_size Number of items to retrieve.
	 *
	 * @return array Array of URL data (id, url)
	 */
	private function get_all_content_urls_batch( int $offset = 0, int $batch_size = null ): array {
		$urls = [];

		if ( $batch_size === null ) {
			$batch_size = $this->url_batch_size;
		}

		$post_types = get_post_types( [ 'public' => true ], 'names' );

		$posts = get_posts(
			[
				'post_type'        => $post_types,
				'post_status'      => 'publish',
				'numberposts'      => $batch_size, // Use numberposts for get_posts limit
				'offset'           => $offset,
				'fields'           => 'ids', // Get only IDs initially
				'orderby'          => 'ID',
				'order'            => 'ASC',
				'suppress_filters' => true, // Avoid potential conflicts
			]
		);

		foreach ( $posts as $post_id ) {
			$urls[] = [
				'id'  => $post_id,
				'url' => get_permalink( $post_id ),
			];
		}
		return $urls;
	}

	/**
	 * Get a batch of unindexed content URLs to index
	 *
	 * @param int $offset Offset to start from.
	 * @param int $batch_size Number of items to retrieve.
	 *
	 * @return array Array of URL data (id, url)
	 */
	private function get_unindexed_content_urls_batch( int $offset = 0, int $batch_size = null ): array {
		$urls = [];

		if ( $batch_size === null ) {
			$batch_size = $this->url_batch_size;
		}

		$unindexed_ids = $this->get_unindexed_post_ids();

		// Apply offset and limit to the unindexed IDs
		$batch_ids = array_slice( $unindexed_ids, $offset, $batch_size );

		foreach ( $batch_ids as $post_id ) {
			$urls[] = [
				'id'  => $post_id,
				'url' => get_permalink( $post_id ),
			];
		}

		return $urls;
	}


	/**
	 * Fetch a single URL
	 * causes ISC\Indexer:update_indexes() to run
	 *
	 * @param string $url URL to fetch.
	 * @param bool   $execute_as_admin Whether to execute requests as admin user.
	 *
	 * @return string|false
	 */
	public function fetch_content( string $url, bool $execute_as_admin = false ) {
		// Append a cache-buster query parameter to force a fresh load.
		$url = add_query_arg( 'isc-indexer-cache-buster', time(), esc_url_raw( $url ) );

		$site_url_parts = wp_parse_url( home_url() );
		$request_parts  = wp_parse_url( $url );

		if ( ! is_array( $request_parts ) ) {
			return false;
		}

		$args = [
			'timeout'    => 30,
			'sslverify'  => $execute_as_admin, // Only verify SSL when executing as admin; otherwise set to "false" to avoid issues with self-signed certs
			'user-agent' => 'ISC Index Bot',
			'headers'    => [
				'Cache-Control' => 'no-cache, no-store, must-revalidate',
				'Pragma'        => 'no-cache',
				'Expires'       => '0',
			],
		];

		// Add admin authentication cookies if requested
		if ( $execute_as_admin ) {
			if (
				! is_array( $site_url_parts ) ||
				empty( $site_url_parts['host'] ) ||
				empty( $request_parts['host'] ) ||
				! hash_equals( $site_url_parts['host'], $request_parts['host'] )
			) {
				// Refusing to send admin cookies to foreign host
				return false;
			}

			if ( ! empty( $site_url_parts['scheme'] ) && ! empty( $request_parts['scheme'] ) && $site_url_parts['scheme'] !== $request_parts['scheme'] ) {
				// Refusing to send admin cookies to mismatched scheme
				return false;
			}

			$cookies = $this->get_admin_auth_cookies();
			if ( ! empty( $cookies ) ) {
				$args['cookies'] = $cookies;
			}
		}

		$response = wp_remote_get( $url, $args );

		if ( is_wp_error( $response ) ) {
			// Log error without exposing sensitive cookie data
			error_log( 'ISC Indexer: Failed to fetch URL ' . $url . '. Error: ' . $response->get_error_message() );
			return false;
		}

		$response_code = wp_remote_retrieve_response_code( $response );
		if ( $response_code !== 200 ) {
			// Log non-200 response without exposing sensitive data
			error_log( 'ISC Indexer: Non-200 response for URL ' . $url . '. Response code: ' . $response_code );
		}

		return wp_remote_retrieve_body( $response );
	}

	/**
	 * Get WordPress authentication cookies for the current admin user
	 *
	 * @return array Array of WP_Http_Cookie objects
	 */
	private function get_admin_auth_cookies(): array {
		$cookies = [];

		$home_url = home_url();
		$parts    = wp_parse_url( $home_url );

		if ( ! is_array( $parts ) || empty( $parts['host'] ) ) {
			return $cookies;
		}

		$domain = $parts['host'];
		$path   = ! empty( $parts['path'] ) ? $parts['path'] : '/';

		$auth_cookie_prefixes = [
			'wordpress_logged_in_',
			'wordpress_sec_',
		];

		foreach ( $_COOKIE as $name => $value ) {
			foreach ( $auth_cookie_prefixes as $prefix ) {
				if ( strpos( $name, $prefix ) === 0 ) {
					$cookies[] = new \WP_Http_Cookie(
						[
							'name'   => $name,
							'value'  => $value,
							'domain' => $domain,
							'path'   => $path,
						]
					);
					break;
				}
			}
		}

		return $cookies;
	}
}
