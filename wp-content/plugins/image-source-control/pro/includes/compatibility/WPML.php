<?php

namespace ISC\Pro\Compatibility;

class WPML {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'init', [ $this, 'register_hooks' ] );

		// hide the admin language switcher from WPML on our pages. Pro-pages only.
		add_filter( 'wpml_show_admin_language_switcher', [ $this, 'disable_wpml_admin_lang_switcher' ] );
	}

	/**
	 * Register hooks
	 */
	public function register_hooks(): void {
		if ( ! self::is_installed() ) {
			return;
		}
		add_filter( 'isc_indexer_all_content_urls', [ $this, 'filter_all_content_urls_wpml' ] );
	}

	/**
	 * Return true if WPML is installed
	 */
	public static function is_installed() {
		return class_exists( 'SitePress' );
	}

	/**
	 * Disable the WPML language switcher on ISC pages
	 *
	 * @param bool $state current state.
	 *
	 * @return bool
	 */
	public function disable_wpml_admin_lang_switcher( $state ): bool {
		// needs to run before plugins_loaded with prio 1, so our own `is_isc_page` function is not available here
		global $pagenow;

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( ! isset( $_GET['page'] ) ) {
			return $state;
		}

		// indexer page
		if (
			$pagenow === 'options.php'
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended
			&& in_array( $_GET['page'], [ 'isc-indexer' ], true )
		) {
			$state = false;
		}

		return $state;
	}

	/**
	 * Returns the original post ID in the default language.
	 *
	 * @param int|null $post_id Optional. Post ID to get the original for. Defaults to current post.
	 *
	 * @return int|null The original post ID, or null if not available.
	 */
	public static function get_original_post_id( $post_id = null ) {
		if ( null === $post_id ) {
			$post_id = get_the_ID();
		}

		if ( ! $post_id || ! function_exists( 'wpml_get_default_language' ) || ! function_exists( 'icl_object_id' ) ) {
			return $post_id;
		}

		$original_post_id = apply_filters( 'wpml_object_id', $post_id, get_post_type( $post_id ), false, self::get_post_language( $post_id ) );

		return $original_post_id ?: $post_id;
	}

	/**
	 * Return the post language
	 *
	 * @param int $post_id Post ID
	 * @return string Language code
	 */
	public static function get_post_language( int $post_id ): string {
		$language_details = apply_filters( 'wpml_post_language_details', NULL, $post_id ) ?? '';

		return $language_details['language_code'] ?? '';
	}

	/**
	 * Filters the content URL list to provide language-specific permalinks via WPML.
	 *
	 * @param array $urls The default list of content URLs.
	 * @return array Modified list of content URLs, one per post per language.
	 */
	public function filter_all_content_urls_wpml( array $urls ): array {
		$translated_urls = [];

		$original_lang = apply_filters( 'wpml_current_language', null );
		$languages     = apply_filters( 'wpml_active_languages', null );

		$post_types = get_post_types( [ 'public' => true ] );

		foreach ( $languages as $lang_code => $language ) {
			do_action( 'wpml_switch_language', $lang_code );

			foreach ( $post_types as $post_type ) {
				$posts = get_posts( [
					                    'post_type'        => $post_type,
					                    'post_status'      => 'publish',
					                    'posts_per_page'   => -1,
					                    'fields'           => 'ids',
					                    'suppress_filters' => false,
				                    ] );

				foreach ( $posts as $post_id ) {
					$translated_urls[] = [
						'id'       => $post_id,
						'type'     => $post_type,
						'lang'     => $lang_code,
						'url'      => get_permalink( $post_id ),
					];
				}
			}
		}

		// Restore original language context
		do_action( 'wpml_switch_language', $original_lang );

		return $translated_urls;
	}

}
