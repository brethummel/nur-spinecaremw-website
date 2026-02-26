<?php

namespace ISC\Pro\Indexer;

use ISC_Log;

/**
 * Manage the isc_index table
 */
class Index_Table {

	/**
	 * Schema version - increment when changing table structure
	 *
	 * @var string
	 */
	private $schema_version = '1.0';

	/**
	 * Option name for storing table info
	 *
	 * @var string
	 */
	private $option_name = 'isc_index_table_version';

	/**
	 * Table name
	 *
	 * @var string
	 */
	private $table_name;

	/**
	 * WordPress Database Object
	 *
	 * @var \wpdb
	 */
	private $wpdb;

	/**
	 * Valid position values
	 *
	 * @var array
	 */
	private $valid_positions = [
		'thumbnail',
		'content',
		'body',
	];

	/**
	 * Static cache for the oldest entry date (string format).
	 * Stores null if no entries or if strtotime() conversion failed.
	 *
	 * @var string|null
	 */
	private static $cached_oldest_string_result = null;

	/**
	 * Static cache for the oldest entry date (timestamp format).
	 * Stores null if no entries or if string conversion failed.
	 *
	 * @var int|null
	 */
	private static $cached_oldest_timestamp_result = null;

	/**
	 * Flag to indicate if the oldest entry date cache has been populated.
	 * This is necessary to distinguish between an unpopulated cache and a cache
	 * that holds a null result (e.g., table is empty).
	 *
	 * @var bool
	 */
	private static $is_oldest_entry_date_cached = false;


	/**
	 * Constructor
	 */
	public function __construct() {
		global $wpdb;
		$this->wpdb       = $wpdb;
		$this->table_name = $wpdb->prefix . 'isc_index';

		// Ensure the table exists upon instantiation
		$this->ensure_table_exists();
	}

	/**
	 * Check if the table exists and create it if necessary
	 *
	 * @return bool True, if the table exists or was created successfully
	 */
	public function ensure_table_exists(): bool {
		// Check if we've already created the table in this version
		$stored_version = get_option( $this->option_name );

		if ( $stored_version === $this->schema_version ) {
			// Table exists and is at current version
			return true;
		}

		// If no version stored or version mismatch, check if table physically exists
		if ( ! $this->table_exists() ) {
			// Table doesn't exist, create it
			$created = $this->create_table();

			if ( $created ) {
				// Store the current schema version
				update_option( $this->option_name, $this->schema_version );
			}

			return $created;
		} elseif ( $stored_version !== $this->schema_version ) {
			// Table exists but schema version is different - update might be needed
			$this->maybe_update_schema( $stored_version );

			// Update the stored version
			update_option( $this->option_name, $this->schema_version );
		}

		return true;
	}

	/**
	 * Check if the table exists
	 *
	 * @return bool
	 */
	private function table_exists(): bool {
		$table = $this->wpdb->get_var(
			$this->wpdb->prepare(
				'SHOW TABLES LIKE %s',
				$this->table_name
			)
		);

		return $table === $this->table_name;
	}

	/**
	 * Create the table
	 *
	 * @return bool true, if the table was created successfully
	 */
	private function create_table(): bool {
		$charset_collate = $this->wpdb->get_charset_collate();

		$sql = "CREATE TABLE {$this->table_name} (
            ID bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            post_id bigint(20) unsigned NOT NULL,
            attachment_id bigint(20) unsigned NOT NULL,
            position varchar(50) NOT NULL DEFAULT 'content',
            last_checked datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (ID),
            KEY post_id (post_id),
            KEY attachment_id (attachment_id)
        ) $charset_collate;";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		$result = dbDelta( $sql );

		return ! empty( $result );
	}

	/**
	 * Update schema if version has changed
	 *
	 * @param string|bool $old_version The old schema version or false if none.
	 * @return bool True if update was successful or not needed
	 */
	private function maybe_update_schema( $old_version ): bool {
		// No updates needed for initial version
		if ( $old_version === false ) {
			return true;
		}

		// Example: If upgrading from version 0.9 to 1.0
		if ( version_compare( $old_version, '1.0', '<' ) ) {
			// Update schema for version 1.0
			$result = $this->wpdb->query(
				"ALTER TABLE {$this->table_name} 
				MODIFY position varchar(50) NOT NULL DEFAULT 'content'"
			);

			return $result !== false;
		}

		return true;
	}

	/**
	 * Check if the database supports transactions
	 *
	 * @return bool True if transactions are supported, false otherwise
	 */
	private function supports_transactions(): bool {
		static $supports_transactions = null;

		// Return cached result if available
		if ( $supports_transactions !== null ) {
			return $supports_transactions;
		}

		// Check if the table supports transactions by inspecting the storage engine
		$table_status = $this->wpdb->get_row(
			$this->wpdb->prepare( 'SHOW TABLE STATUS LIKE %s', $this->table_name )
		);

		if ( $table_status && isset( $table_status->Engine ) ) {
			// InnoDB supports transactions, MyISAM doesn't
			$supports_transactions = 'InnoDB' === $table_status->Engine;
		} else {
			// If we can't determine the engine, assume no transaction support
			$supports_transactions = false;
		}

		return $supports_transactions;
	}

	/**
	 * Remove all entries with a certain post_id and optional position
	 *
	 * @param int    $post_id The post ID.
	 * @param string $position Optional position to filter by position of the image.
	 *
	 * @return int|false Number of deleted rows or false on error
	 */
	public function delete_by_post_id( int $post_id, string $position = '' ) {
		if ( $post_id <= 0 ) {
			return false;
		}

		// validate $position, if given
		if ( ! empty( $position ) && ! $this->is_valid_position( $position ) ) {
			$position = '';
		}

		$where  = [ 'post_id' => $post_id ];
		$format = [ '%d' ];

		if ( ! empty( $position ) ) {
			$where['position'] = $position;
			$format[]          = '%s';
		}

		return $this->wpdb->delete(
			$this->table_name,
			$where,
			$format
		);
	}

	/**
	 * Remove all entries with a certain attachment_id
	 *
	 * @param int $attachment_id The attachment ID.
	 *
	 * @return int|false Number of deleted rows or false on error
	 */
	public function delete_by_attachment_id( int $attachment_id ) {
		// Validate attachment_id
		if ( $attachment_id <= 0 ) {
			return false;
		}

		return $this->wpdb->delete(
			$this->table_name,
			[ 'attachment_id' => $attachment_id ],
			[ '%d' ]
		);
	}

	/**
	 * Bulk insert or update multiple entries
	 *
	 * @param array $entries Array of entries, each containing 'post_id', 'attachment_id', and 'position'.
	 * @return array Array with counts of processed entries
	 */
	public function bulk_insert_or_update( array $entries ): array {
		$count                 = 0;
		$results               = [];
		$supports_transactions = $this->supports_transactions();

		if ( $supports_transactions ) {
			// Start transaction only if supported.
			$this->wpdb->query( 'START TRANSACTION' );
		}

		try {
			foreach ( $entries as $entry ) {
				if ( empty( $entry['post_id'] ) || empty( $entry['attachment_id'] ) ) {
					continue; // Skip invalid entries.
				}

				$post_id       = absint( $entry['post_id'] );
				$attachment_id = absint( $entry['attachment_id'] );

				if ( $post_id <= 0 || $attachment_id <= 0 ) {
					continue;
				}

				$position     = isset( $entry['position'] ) ? sanitize_text_field( $entry['position'] ) : $this->get_default_position();
				$last_checked = $entry['last_checked'] ?? null;

				$result = $this->insert_or_update( $post_id, $attachment_id, $position, $last_checked );
				if ( $result ) {
					++$count;
					$results[] = $result;
				}
			}

			if ( $supports_transactions ) {
				$this->wpdb->query( 'COMMIT' );
			}

			return [
				'count'   => $count,
				'results' => $results,
			];
		} catch ( \Exception $e ) {
			if ( $supports_transactions ) {
				$this->wpdb->query( 'ROLLBACK' );
			}
			return [
				'error' => $e->getMessage(),
				'count' => $count,
			];
		}
	}


	/**
	 * Insert or update an entry
	 *
	 * @param int         $post_id       The post ID.
	 * @param int         $attachment_id The attachment ID.
	 * @param string      $position      The position.
	 * @param string|null $last_checked  Time of the last check (optional).
	 *
	 * @return int|false The ID of the inserted/updated entry or false on error
	 */
	public function insert_or_update( int $post_id, int $attachment_id, string $position, string $last_checked = null ) {
		// Validate IDs to prevent zero or negative values
		if ( $post_id <= 0 || $attachment_id <= 0 ) {
			return false;
		}

		// Validate position
		if ( ! $this->is_valid_position( $position ) ) {
			$position = $this->get_default_position();
		}

		// Set the current time if not specified
		if ( $last_checked === null ) {
			$last_checked = current_time( 'mysql' );
		}

		// Check if an entry already exists
		$existing_id = $this->wpdb->get_var(
			$this->wpdb->prepare( "SELECT ID FROM {$this->table_name} WHERE post_id = %d AND attachment_id = %d AND position = %s", $post_id, $attachment_id, $position )
		);

		$data = [
			'post_id'       => $post_id,
			'attachment_id' => $attachment_id,
			'position'      => $position,
			'last_checked'  => $last_checked,
		];

		$format = [ '%d', '%d', '%s', '%s' ];

		if ( $existing_id ) {
			// Update existing entry
			$this->wpdb->update(
				$this->table_name,
				$data,
				[ 'ID' => $existing_id ],
				$format,
				[ '%d' ]
			);
			return $existing_id;
		} else {
			// Create new entry
			$this->wpdb->insert(
				$this->table_name,
				$data,
				$format
			);
			return $this->wpdb->insert_id;
		}
	}

	/**
	 * Bulk update entries based on a certain post_id.
	 * Entries not included in the $entries array will be deleted.
	 *
	 * @param int    $post_id The post ID.
	 * @param array  $entries Array of entries, each containing 'attachment_id' and 'position'.
	 * @param string $position Optional filter for the position of the image. Relevant for deletion. Otherwise, position should be given in $entries.
	 *
	 * @return array Array with counts of processed entries
	 */
	public function bulk_update_by_post_id( int $post_id, array $entries, string $position = '' ): array {
		if ( $post_id <= 0 ) {
			return [ 'error' => 'Invalid post ID' ];
		}

		// validate $position, if given
		if ( ! empty( $position ) && ! $this->is_valid_position( $position ) ) {
			$position = '';
		}

		$existing_entries     = $this->get_by_post_id( $post_id );
		$existing_attachments = array_column( $existing_entries, 'attachment_id' );

		$new_attachments = array_column( $entries, 'attachment_id' );
		$to_delete       = array_diff( $existing_attachments, $new_attachments );

		$count_added   = 0;
		$count_updated = 0;
		$count_deleted = 0;

		// Check if transactions are supported.
		$supports_transactions = $this->supports_transactions();

		if ( $supports_transactions ) {
			$this->wpdb->query( 'START TRANSACTION' );
		}

		try {
			// Delete entries that are no longer needed, but only, if the position matches.
			if ( ! empty( $to_delete ) ) {
				foreach ( $to_delete as $attachment_id ) {
					$where  = [
						'post_id'       => $post_id,
						'attachment_id' => $attachment_id,
					];
					$format = [ '%d', '%d' ];

					if ( ! empty( $position ) ) {
						$where['position'] = $position;
						$format[]          = '%s';
					}

					$this->wpdb->delete(
						$this->table_name,
						$where,
						$format
					);
					++$count_deleted;
				}
			}

			// Add or update entries.
			foreach ( $entries as $entry ) {
				if ( empty( $entry['attachment_id'] ) ) {
					continue; // Skip invalid entries.
				}

				$attachment_id = absint( $entry['attachment_id'] );
				if ( $attachment_id <= 0 ) {
					continue;
				}

				$entry_position = isset( $entry['position'] ) ? sanitize_text_field( $entry['position'] ) : $this->get_default_position();
				$last_checked   = $entry['last_checked'] ?? null;

				// Check if this is a new entry or an update.
				if ( ! in_array( $attachment_id, $existing_attachments, true ) ) {
					$this->insert_or_update( $post_id, $attachment_id, $entry_position, $last_checked );
					++$count_added;
				} else {
					$this->insert_or_update( $post_id, $attachment_id, $entry_position, $last_checked );
					++$count_updated;
				}
			}

			if ( $supports_transactions ) {
				$this->wpdb->query( 'COMMIT' );
			}

			return [
				'added'   => $count_added,
				'updated' => $count_updated,
				'deleted' => $count_deleted,
				'total'   => $count_added + $count_updated + $count_deleted,
			];
		} catch ( \Exception $e ) {
			if ( $supports_transactions ) {
				$this->wpdb->query( 'ROLLBACK' );
			}
			return [
				'error'   => $e->getMessage(),
				'added'   => $count_added,
				'updated' => $count_updated,
				'deleted' => $count_deleted,
			];
		}
	}


	/**
	 * Get all entries for a specific post
	 *
	 * @param int $post_id The post ID.
	 *
	 * @return array Array of entries
	 */
	/**
	 * Get all entries for a specific post with post_id as array key
	 *
	 * @param int $post_id The post ID.
	 *
	 * @return array Array of entries with post_id as key
	 */
	public function get_by_post_id( int $post_id ): array {
		// Validate post_id
		if ( $post_id <= 0 ) {
			return [];
		}

		$results = $this->wpdb->get_results(
			$this->wpdb->prepare( "SELECT * FROM {$this->table_name} WHERE post_id = %d ORDER BY position", $post_id ),
			ARRAY_A
		);

		// If no results, return empty array
		if ( empty( $results ) ) {
			return [];
		}

		$results_by_post_id = [];

		foreach ( $results as $result ) {
			// If you want to keep all entries with the same post_id as an array under that key
			$results_by_post_id[ $result['attachment_id'] ] = $result;
		}

		return $results_by_post_id;
	}

	/**
	 * Get all entries for a specific attachment
	 *
	 * @param int $attachment_id The attachment ID.
	 *
	 * @return array Array of entries
	 */
	public function get_by_attachment_id( int $attachment_id ): array {
		// Validate attachment_id
		if ( $attachment_id <= 0 ) {
			return [];
		}

		$results = $this->wpdb->get_results(
			$this->wpdb->prepare( "SELECT * FROM {$this->table_name} WHERE attachment_id = %d ORDER BY post_id", $attachment_id ),
			ARRAY_A
		);

		// If no results, return empty array
		if ( empty( $results ) ) {
			return [];
		}

		$results_by_post_id = [];

		foreach ( $results as $result ) {
			// If you want to keep all entries with the same post_id as an array under that key
			$results_by_post_id[ $result['post_id'] ] = $result;
		}

		return $results_by_post_id;
	}

	/**
	 * Update the last_checked timestamp for a specific entry
	 *
	 * @param int $post_id       The post ID.
	 * @param int $attachment_id The attachment ID.
	 *
	 * @return int|false Number of updated rows or false on error
	 */
	public function update_last_checked( int $post_id, int $attachment_id ) {
		// Validate IDs
		if ( $post_id <= 0 || $attachment_id <= 0 ) {
			return false;
		}

		return $this->wpdb->update(
			$this->table_name,
			[ 'last_checked' => current_time( 'mysql' ) ],
			[
				'post_id'       => $post_id,
				'attachment_id' => $attachment_id,
			],
			[ '%s' ],
			[ '%d', '%d' ]
		);
	}

	/**
	 * Find orphaned entries (entries whose post_id or attachment_id no longer exists)
	 *
	 * @return array Array of orphaned entries
	 */
	public function find_orphaned_entries(): array {
		return $this->wpdb->get_results(
			"SELECT p.* FROM {$this->table_name} p
            LEFT JOIN {$this->wpdb->posts} posts ON p.post_id = posts.ID
            LEFT JOIN {$this->wpdb->posts} attachments ON p.attachment_id = attachments.ID
            WHERE posts.ID IS NULL OR attachments.ID IS NULL",
			ARRAY_A
		);
	}

	/**
	 * Delete orphaned entries
	 *
	 * @return int Number of deleted entries
	 */
	public function delete_orphaned_entries() {
		return $this->wpdb->query(
			"DELETE p FROM {$this->table_name} p
            LEFT JOIN {$this->wpdb->posts} posts ON p.post_id = posts.ID
            LEFT JOIN {$this->wpdb->posts} attachments ON p.attachment_id = attachments.ID
            WHERE posts.ID IS NULL OR attachments.ID IS NULL"
		);
	}

	/**
	 * Delete entries with invalid IDs (0 or negative)
	 *
	 * @return int Number of deleted entries
	 */
	public function delete_invalid_entries() {
			return $this->wpdb->query(
				"DELETE FROM {$this->table_name} WHERE post_id <= 0 OR attachment_id <= 0"
			);
	}

		/**
		 * Delete entries that were not updated since a given timestamp.
		 *
		 * @param int $timestamp Unix timestamp to compare against.
		 * @return int Number of deleted entries
		 */
	public function delete_not_updated_since( int $timestamp ) {
		if ( $timestamp <= 0 ) {
			return 0;
		}

		// Convert the timestamp to local time in the same format as current_time('mysql')
		// Add an hour on top to account for potential rounding errors
		$local_datetime = gmdate( 'Y-m-d H:i:s', $timestamp + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS - HOUR_IN_SECONDS ) );

		return $this->wpdb->query(
			$this->wpdb->prepare(
				"DELETE FROM {$this->table_name} WHERE last_checked < %s",
				$local_datetime
			)
		);
	}

	/**
	 * Check if a position value is valid
	 *
	 * @param string $position Position value to check.
	 * @return bool True if valid, false otherwise
	 */
	public function is_valid_position( string $position ): bool {
		return in_array( $position, $this->valid_positions, true );
	}

	/**
	 * Get default position value
	 *
	 * @return string Default position value
	 */
	public function get_default_position(): string {
		return 'content';
	}

	/**
	 * Returns the date of the oldest entry from the isc_index table
	 *
	 * @param string $return_type 'timestamp' for a Unix timestamp, 'string' for a formatted string.
	 * @return int|string|null The oldest date as Unix timestamp or string, null if no entries
	 */
	public function get_oldest_entry_date( string $return_type = 'timestamp' ) {
		// If a value has already been cached
		if ( self::$is_oldest_entry_date_cached ) {
			return 'string' === $return_type ? self::$cached_oldest_string_result : self::$cached_oldest_timestamp_result;
		}

		// If nothing has been cached yet, perform the database query
		$db_result = $this->wpdb->get_var( "SELECT MIN(last_checked) FROM {$this->table_name}" );

		// Cache the string result
		self::$cached_oldest_string_result = empty( $db_result ) ? null : $db_result;

		// Convert and cache the timestamp result, handling potential errors and logging them once.
		self::$cached_oldest_timestamp_result = $this->convert_string_to_timestamp( self::$cached_oldest_string_result );

		self::$is_oldest_entry_date_cached = true;

		// Return the result based on the requested type
		return 'string' === $return_type ? self::$cached_oldest_string_result : self::$cached_oldest_timestamp_result;
	}

	/**
	 * Converts a date string to a Unix timestamp, handling failures and logging them.
	 *
	 * @param string|null $date_string The date string to convert.
	 * @return int|null The Unix timestamp, or null if conversion failed or input was null.
	 */
	private function convert_string_to_timestamp( ?string $date_string ): ?int {
		if ( $date_string === null ) {
			return null;
		}

		$timestamp = strtotime( $date_string );

		if ( $timestamp === false ) {
			// Log the error only when the conversion actually fails.
			ISC_Log::log( 'ISC Dev Helper: Failed to parse date string "' . $date_string . '" from database. Setting timestamp to null.' );
			return null;
		}

		return $timestamp;
	}

	/**
	 * Resets the static cache for get_oldest_entry_date.
	 * Implemented for WP Unit testing purposes.
	 */
	public static function reset_oldest_entry_date_cache() {
		self::$cached_oldest_string_result    = null;
		self::$cached_oldest_timestamp_result = null;
		self::$is_oldest_entry_date_cached    = false;
	}

	/**
	 * Returns the date of the oldest entry for a specific post ID
	 *
	 * @param int    $post_id The post ID to check.
	 * @param string $return_type 'timestamp' for a Unix timestamp, 'string' for a formatted string.
	 * @return int|string|null The oldest date as Unix timestamp or string, null if no entries
	 */
	public function get_oldest_entry_date_by_post_id( int $post_id, string $return_type = 'timestamp' ) {
		// Validate post_id
		if ( $post_id <= 0 ) {
			return null;
		}

		$result = $this->wpdb->get_var(
			$this->wpdb->prepare(
				"SELECT MIN(last_checked) FROM {$this->table_name} WHERE post_id = %d",
				$post_id
			)
		);

		if ( empty( $result ) ) {
			return null;
		}

		if ( $return_type === 'string' ) {
			return $result;
		}

		// Return as Unix timestamp
		return strtotime( $result );
	}
}
