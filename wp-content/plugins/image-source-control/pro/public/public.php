<?php

use ISC\Image_Sources\Image_Sources;
use ISC\Plugin;

/**
 * Class ISC_Pro_Public
 *
 * Public-facing functions of ISC Pro
 */
class ISC_Pro_Public {

	/**
	 * ISC_Pro_Public constructor
	 */
	public function __construct() {
		if ( defined( 'ISC_DISABLE_PRO' ) && ISC_DISABLE_PRO ) {
			return;
		}

		add_action( 'wp', [ $this, 'register_hooks' ] );
	}

	/**
	 * Register hooks
	 */
	public function register_hooks() {

		// make sure we are really in the frontend and ISC is allowed to load
		if ( is_admin() || ! ISC_Public::can_load_image_sources() || wp_doing_ajax() || defined( 'REST_REQUEST' ) || wp_is_json_request() ) {
			return;
		}

		$options = Plugin::get_options();

		// handle which image are considered in the source list or overlay
		$list_included_images    = isset( $options['list_included_images'] ) ? $options['list_included_images'] : false;
		$overlay_included_images = ( ISC_Public::captions_enabled() && isset( $options['overlay_included_images'] ) ) ? $options['overlay_included_images'] : false;

		/**
		 * Register output buffering to catch the whole HTML, including head and body.
		 * using `get_header` and `wp_print_footer_scripts` ensures that
		 * use `wp_head` and `wp_footer` if mostly catching `body` or as fallback
		 */
		if ( in_array( $list_included_images, [ 'body_img', 'body_urls' ], true )
			|| $overlay_included_images === 'body_img'
		) {

			$this->register_start_output_buffering();

			add_action( 'wp_print_footer_scripts', [ $this, 'stop_output_buffering' ], 99999 );
			// fallback if wp_print_footer_scripts() is missing
			add_action( 'wp_footer', [ $this, 'stop_output_buffering_fallback' ], 99999 );
			// disable the basic content index if the list is using the body_img or body_urls option since we run the index later
			// without this, the Per-page list would be empty on the first page load after saving the post, which could lead to wrongly cached versions
			if ( in_array( $list_included_images, [ 'body_img', 'body_urls' ], true ) ) {
				add_filter( 'isc_update_indexes_in_the_content', '__return_false' );
			}

			// Compatibility with the reader mode in the official AMP plugin
			// and the AMPforWP, since they don’t use the typical WordPress hooks, and we need to use their alternatives
			add_action( 'amp_post_template_footer', [ $this, 'stop_output_buffering' ], 99999 );
		} elseif ( ! empty( $options['global_list_indexed_images'] ) ) { // if Global List is using an option that needs indexing all image positions, not just in content
			$this->register_start_output_buffering();
			// disable the basic content index
			add_filter( 'isc_update_indexes_in_the_content', '__return_false' );

			// stop output buffering for indexing
			add_action( 'wp_print_footer_scripts', [ $this, 'stop_output_buffering_for_index' ], 99999 );
			add_action( 'wp_footer', [ $this, 'stop_output_buffering_for_index_fallback' ], 99999 );
		}

		// render the source string with URLs
		add_filter( 'isc_public_source_url_html', [ $this, 'render_source_url_html' ], 10, 3 );

		switch ( $list_included_images ) {
			case 'body_img': // jump to next
			case 'body_urls':
				// override output of the source list placed automatically or manually to create an empty image source placeholder
				add_action( 'isc_sources_list_override_output', [ $this, 'add_source_list_to_content_placeholder' ], 99999 );
				break;
			default:
		}

		// handle which images are included in the Global list
		add_filter( 'isc_global_list_get_attachment_arguments', [ $this, 'global_list_included_images' ], 10, 2 );

		// load standard source option
		add_filter( 'isc_use_standard_source_for_attachment', [ $this, 'use_standard_source_by_default' ], 20, 2 );

		// disable file extension check. This addresses a case in which external images didn’t use a file extension
		add_filter( 'isc_allow_empty_file_extension', '__return_true' );

		// handle source overlay
		if ( $overlay_included_images === 'body_img' ) {
			// disable the default overlay filter from the base plugin
			add_filter( 'isc_public_add_source_captions_to_content', '__return_false' );
			// minify the content that is used when looking for captions
			// disabled for now since it breaks when the minified code is not identical with the code on the actual page
			// add_filter( 'isc_public_caption_regex_content', array( 'ISC_Pro_Model', 'remove_line_breaks' ) );
		}

		// use a custom global list if the columns were customized
		if ( array_key_exists( 'global_list_included_data', $options ) && $options['global_list_included_data'] !== [] ) {
			add_filter( 'isc_public_global_list_view_path', '__return_null' );
			add_action( 'isc_public_global_list_after', [ $this, 'display_global_list' ], 10, 7 );
		}

		// add the `data-isc-source-text` attribute to HTML tags with image URLs in the inline styles. Needs to run before add_caption_from_isc_images_attribute()
		add_filter( 'isc_public_caption_regex_content', [ $this, 'add_captions_for_inline_styles' ], 9 );
		add_filter( 'isc_public_caption_regex_content', [ $this, 'add_captions_to_style_blocks' ], 9 );
		// convert the `data-isc-images` attribute into <span> tags
		add_filter( 'isc_public_caption_regex_content', [ $this, 'add_caption_from_isc_images_attribute' ], 10 );

		// load Custom Attribute Processor
		$this->load_custom_attribute_processor();

		// remove the overlay from images with the `isc-disable-overlay` class
		add_filter( 'isc_extract_images_from_html', [ $this, 'remove_overlay_from_isc_disable_overlay_class' ], 10 );
		// find valid image URLs in other places than the `src` attribute
		add_filter( 'isc_extract_images_from_html', [ $this, 'find_more_valid_image_urls' ], 10 );

		// add the `data-isc-image` attributes to the page list
		add_filter( 'isc_filter_image_ids_from_content', [ $this, 'get_image_ids_from_isc_images_attribute' ], 10, 2 );
		add_filter( 'isc_filter_any_image_ids_from_content', [ $this, 'get_image_ids_from_isc_images_attribute' ], 10, 2 );
	}

	/**
	 * Register output buffering hooks
	 *
	 * @return void
	 */
	public function register_start_output_buffering(): void {
		add_action( 'get_header', [ $this, 'start_output_buffering' ], 99999 );
		// fallback if get_header() is missing
		add_action( 'wp_head', [ $this, 'start_output_buffering_fallback' ], 1 );
		// Compatibility with the reader mode in the official AMP plugin
		// and the AMPforWP, since they don’t use the typical WordPress hooks, and we need to use their alternatives
		add_action( 'amp_post_template_head', [ $this, 'start_output_buffering' ], 1 );
	}

	/**
	 * Start output buffering
	 */
	public function start_output_buffering() {
		$current_action = current_action();
		ISC_Log::log( "start output buffer in $current_action" );

		ob_start( [ $this, 'handle_output_buffering' ] );

		if ( ! defined( 'ISC_BUFFERING_START' ) ) {
			define( 'ISC_BUFFERING_START', true );
		}
	}

	/**
	 * Handle output buffering
	 * The main purpose is to have a handle that we can identify later.
	 *
	 * @param string $page_content buffered content.
	 *
	 * @return string
	 */
	public function handle_output_buffering( string $page_content ): string {
		return $page_content;
	}

	/**
	 * Start output buffering a bit later when get_header() is not available
	 * this is the case with Oxygen page builder
	 */
	public function start_output_buffering_fallback() {
		if ( did_action( 'get_header' ) ) {
			return;
		}

		$this->start_output_buffering();
	}

	/**
	 * Stop output buffering
	 */
	public function stop_output_buffering() {
		if ( ! defined( 'ISC_BUFFERING_START' ) ) {
			ISC_Log::log( 'return from stop_output_buffering() because ISC_BUFFERING_START is not defined.' );
			return;
		}

		// get full buffer stack
		$all          = ob_get_status( true );
		$count        = count( $all );
		$target_index = null;

		foreach ( $all as $i => $buffer ) {
			if ( strpos( $buffer['name'], 'handle_output_buffering' ) !== false ) {
				$target_index = $i;
				break;
			}
		}

		// if our buffer handle is not found, bail out
		if ( $target_index === null ) {
			ISC_Log::log( 'ISC buffer handle_output_buffering not found in buffer stack.' );
			return;
		}

		// flush all buffers above our ISC‐callback, with error logging
		for ( $i = $count - 1; $i > $target_index; $i-- ) {
			if ( ! @ob_end_flush() ) {
				ISC_Log::log( "Failed to flush buffer at index {$i}" );
			}
		}

		// verify that our buffer is now on top
		$remaining = ob_get_status( true );
		$top       = end( $remaining );
		if ( strpos( $top['name'], 'handle_output_buffering' ) === false ) {
			ISC_Log::log( 'Unexpected buffer on top after flushing intermediates: ' . ( $top['name'] ?? 'n/a' ) );
			return;
		}

		// now retrieve our ISC buffer contents
		$page_content = ob_get_clean();
		if ( $page_content === false || $page_content === '' ) {
			ISC_Log::log( 'ob_get_clean failed or returned empty content.' );
			return;
		}

		$current_action = current_action();
		ISC_Log::log( "stop output buffer in $current_action" );

		$content = $page_content;

		\ISC\Indexer::update_indexes( $content );

		$options = Plugin::get_options();

		// maybe add source captions
		if ( $options['overlay_included_images'] === 'body_img' && ISC_Public::captions_enabled() ) {
			ISC_Log::log( 'Pro: initiate source overlays for the whole body' );
			$page_content = ISC_Public::get_instance()->add_source_captions_to_content( $page_content );
		}

		if ( $options['list_included_images'] === '' || ! in_array( $options['list_included_images'], [ 'body_img', 'body_urls' ], true ) ) {
			// phpcs:ignore
			echo $page_content;
			return;
		}

		// look for images in the body.
		if ( isset( $options['list_included_images'] ) && 'body_urls' === $options['list_included_images'] ) {
			$image_ids = ISC_Pro_Model::get_ids_from_any_image_url( $content );
		} else {
			/**
			 * The AMP for WP plugin, and only that one, keeps `amp-img` tags in the code so we are adding that to the filtered tags
			 * This also only concerns the body_img option
			 */
			if ( function_exists( 'ampforwp_is_amp_endpoint' ) && ampforwp_is_amp_endpoint() ) {
				add_filter(
					'isc_filter_image_ids_tags',
					function ( $tags ) {
						$tags[] = 'amp-img';
						return $tags;
					}
				);
			}
			$image_ids = ISC_Model::filter_image_ids( $content );
		}

		ISC_Log::log( sprintf( 'found %d image IDs', count( $image_ids ) ) );

		// build the source list
		$source_list     = self::render_image_source_list( $image_ids );
		$source_list_tag = apply_filters( 'isc_image_list_box_tag', 'div' );

		// place image source list
		/**
		 * Search for `.isc_image_list_box` in the content
		 *
		 * .*? is an ungreedy version of .* and needed or otherwise it would break pages with multiple source lists
		 * /s means to also look for linebreaks so that we find lists that already contain sources (e.g., from the original post)
		 */
		$page_content = preg_replace( '/\<' . $source_list_tag . ' class="isc_image_list_box"\>.*?\<\/' . $source_list_tag . '\>/s', $source_list, $page_content, 1 );

		// phpcs:ignore
		echo $page_content;
	}

	/**
	 * Stop output buffering a bit later when wp_print_footer_scripts() is not available
	 * no case reported yet
	 */
	public function stop_output_buffering_fallback() {
		if ( did_action( 'wp_print_footer_scripts' ) ) {
			return;
		}

		$this->stop_output_buffering();
	}

	/**
	 * Stop output buffering for running the page index
	 */
	public function stop_output_buffering_for_index() {
		if ( ! defined( 'ISC_BUFFERING_START' ) ) {
			ISC_Log::log( 'return from stop_output_buffering() because ISC_BUFFERING_START is not defined.' );
			return;
		}
		$this->check_buffer_level();

		$page_content = ob_get_clean();

		$current_action = current_action();
		ISC_Log::log( "stop output buffer in $current_action" );

		$content = $page_content;

		\ISC\Indexer::update_indexes( $content );

		// phpcs:ignore
		echo $page_content;
	}

	/**
	 * Stop output buffering a bit later when wp_print_footer_scripts() is not available
	 * no case reported yet
	 */
	public function stop_output_buffering_for_index_fallback() {
		if ( did_action( 'wp_print_footer_scripts' ) ) {
			return;
		}

		$this->stop_output_buffering_for_index();
	}

	/**
	 * Add an empty sources list as a placeholder to the content when the appropriate option is enabled
	 */
	public function add_source_list_to_content_placeholder() {
		ISC_Log::log( 'Pro: adding empty list placeholder to content' );
		return self::render_empty_source_list();
	}

	/**
	 * Render an empty source list
	 *
	 * @return string
	 */
	public static function render_empty_source_list() {
		return ISC_Public::get_instance()->render_image_source_box( '', true );
	}


	/**
	 * Create sources list.
	 *
	 * @param array $image_ids attachment IDs.
	 *
	 * @return string
	 *
	 * @todo add some basic function in the core plugin.
	 */
	public static function render_image_source_list( $image_ids = [] ) {
		ISC_Log::log( 'start Pro source list' );

		$return = '';
		if ( ! empty( $image_ids ) ) {
			$exclude_standard = ISC\Standard_Source::standard_source_is( 'exclude' );

			$atts = [];
			foreach ( $image_ids as $attachment_id => $attachment_array ) {
				$image_uses_standard_source = ISC\Standard_Source::use_standard_source( $attachment_id );
				$source                     = Image_Sources::get_image_source_text_raw( $attachment_id );

				// check if source of own images can be displayed. The code is overly complex on purpose to include the log output.
				if ( ( ! $image_uses_standard_source && $source === '' ) || ( $image_uses_standard_source && $exclude_standard ) ) {
					if ( $image_uses_standard_source && $exclude_standard ) {
						ISC_Log::log( 'skipped because standard sources are excluded for image ' . $attachment_id );
					} else {
						ISC_Log::log( 'skipped because of empty source for image ' . $attachment_id );
					}
					unset( $atts[ $attachment_id ] );
				} else {
					$atts[ $attachment_id ]['title']  = Image_Sources::get_image_title( $attachment_id );
					$atts[ $attachment_id ]['source'] = ISC\Image_Sources\Renderer\Image_Source_String::get( $attachment_id );
					if ( ! $atts[ $attachment_id ]['source'] ) {
						ISC_Log::log( sprintf( 'image %d: skipped because of empty standard source', $attachment_id ) );
						unset( $atts[ $attachment_id ] );
					}
				}
			}

			$return = ISC_Public::get_instance()->render_attachments( $atts );
		}

		return $return;
	}

	/**
	 * Change which images are included in the Global list
	 * Pro comes with the option to include only images that have an explicit source or use the "Use standard source" option
	 *
	 * @param array $args                 arguments of the post query used to load the attachments for the global list.
	 * @param array $shortcode_attributes attributes of the global list shortcode.
	 */
	public function global_list_included_images( array $args, array $shortcode_attributes ) {
		$options = Plugin::get_options();

		if ( isset( $options['global_list_included_images'] )
			&& $options['global_list_included_images'] === 'with_sources' ) {
			// phpcs:ignore
			$args['meta_query'] = [
				'relation' => 'OR',
				// image source is empty
				[
					'key'     => 'isc_image_source',
					'value'   => '',
					'compare' => '!=',
				],
				// and does not belong to an author
				[
					'key'     => 'isc_image_source_own',
					'value'   => '1',
					'compare' => '=',
				],
			];
		}

		// remove the pagination if the global list is rendered as a simple list
		if ( array_key_exists( 'style', $shortcode_attributes )
			&& $shortcode_attributes['style'] === 'list' ) {
			$args['posts_per_page'] = -1; // -1 means no limit
		}

		return $args;
	}

	/**
	 * Checks if the "Show standard source by default" option is enabled
	 * and if so, show standard source to all images that are missing explicit sources in the frontend
	 *
	 * @param bool $use_standard_source current value.
	 * @param int  $attachment_id attachment ID.
	 *
	 * @return bool true if standard source should be used
	 */
	public function use_standard_source_by_default( bool $use_standard_source, int $attachment_id ) {

		if ( is_admin() ) {
			return $use_standard_source;
		}

		$options = Plugin::get_options();

		// get image source text
		$text = ISC_Public::get_instance()->get_image_source_text_raw( $attachment_id );

		/**
		 * Use standard if appropriate option is enabled and source text is empty
		 * otherwise, return the current value
		 */
		return empty( $text ) && ! empty( $options['use_standard_source_by_default'] ) ? true : $use_standard_source;
	}

	/**
	 * Override the template of the Global List with a more dynamic version
	 *
	 * @param array   $shortcode_attributes attributes of the global list shortcode.
	 * @param array[] $attachments image source information.
	 * @param int     $up_limit total page count.
	 * @param string  $before_links optional html to display before pagination links.
	 * @param string  $after_links optional html to display after pagination links.
	 * @param string  $prev_text text for the previous page link.
	 * @param string  $next_text text for the next page link.
	 */
	public function display_global_list( $shortcode_attributes, $attachments, $up_limit, $before_links, $after_links, $prev_text, $next_text ) {
		if ( ! is_array( $attachments ) || $attachments === [] ) {
			return;
		}
		$options          = Plugin::get_options();
		$included_columns = ! empty( $options['global_list_included_data'] ) ? $options['global_list_included_data'] : [];

		if ( array_key_exists( 'style', $shortcode_attributes )
			&& $shortcode_attributes['style'] === 'list' ) {
			$lines = $this->prepare_global_list_as_simple_list( $attachments, $options, $included_columns );
			require ISCPATH . 'pro/public/views/global-list-list-view.php';
			// no pagination for the list
		} else {
			// otherwise, we render a table
			require ISCPATH . 'pro/public/views/global-list.php';
			\ISC\Image_Sources\Renderer\Global_List::pagination_links( $up_limit, $before_links, $after_links, $prev_text, $next_text );
		}
	}

	/**
	 * Prepare the Global List as a simple list view
	 *
	 * - remove duplicate lines
	 * - order alphabetically
	 *
	 * @param array    $attachments attachment information.
	 * @param array    $options plugin options.
	 * @param string[] $included_columns columns to include in the list.
	 *
	 * @return array
	 */
	public function prepare_global_list_as_simple_list( array $attachments, array $options, array $included_columns ): array {
		$lines = [];

		foreach ( $attachments as $id => $data ) {
			$source = ISC\Image_Sources\Renderer\Image_Source_String::get( $id );
			$line   = [];
			if ( $options['thumbnail_in_list'] ) :
				ob_start();
					\ISC\Image_Sources\Renderer\Global_List::render_global_list_thumbnail( $id );
				$line[] = ob_get_clean();
			endif;
			if ( in_array( 'attachment_id', $included_columns, true ) ) :
				$line[] = esc_html( $id );
			endif;
			if ( in_array( 'title', $included_columns, true ) ) :
				$line[] = wp_filter_post_kses( $data['title'] );
			endif;
			if ( in_array( 'posts', $included_columns, true ) ) :
				$line[] = $data['posts'];
			endif;
			if ( in_array( 'source', $included_columns, true ) ) :
				$line[] = $source;
			endif;

			// combine into a single line
			$lines[] = implode( ' | ', $line );
		}

		// remove duplicate lines
		$lines = array_unique( $lines );

		// order alphabetically
		sort( $lines );

		return $lines;
	}

	/**
	 * If an HTML element in the content has the `data-isc-images` attribute
	 * this function generates the overlay caption for them and adds the <span class="isc-source-text"> element within the original HTML element
	 *
	 * This function is more or less an alternative version of ISC_Public::add_source_captions_to_content()
	 *
	 * @param string $content website content.
	 * @return string parsed website content.
	 */
	public function add_caption_from_isc_images_attribute( string $content ): string {

		ISC_Log::log( 'data-isc-images to caption: start creating source overlays' );

		/**
		 * 0 – full match of the HTML tag that has the attribute
		 * 1 – HTML tag name, e.g., "span" or "a"
		 * 2 - value of data-isc-images
		 */
		$pattern = '#<(\w+)[^>]+data-isc-images="([^"]*)[^>]*>#';
		$count   = preg_match_all( $pattern, $content, $matches, PREG_SET_ORDER );

		ISC_Log::log( 'data-isc-images to caption: embedded image IDs found: ' . $count );

		if ( false === $count ) {
			return $content;
		}

		// gather elements already replaced to prevent duplicate sources, see GitHub #105
		$replaced = [];
		foreach ( $matches as $match ) {
			$old_content = $match[0];
			$id          = (int) $match[2];
			$hash        = md5( $old_content );
			if ( in_array( $hash, $replaced, true ) ) {
				ISC_Log::log( sprintf( 'data-isc-images to caption: skipped image %d because it appears multiple times', $id ) );
				continue;
			} else {
				$replaced[] = $hash;
			}

			ISC_Log::log( sprintf( 'data-isc-images to caption: found ID "%d" in the image tag', $id ) );

			// if the container element is a link, don’t render links in the source string
			$args = [];
			if ( $match[1] === 'a' ) {
				$args['disable-links'] = true;
			}

			// don’t display empty sources
			$caption_string = ISC\Image_Sources\Renderer\Caption::get( $id, [], $args );
			if ( ! $caption_string ) {
				ISC_Log::log( sprintf( 'data-isc-images to caption: skipped empty sources string for ID "%d"', $id ) );
				continue;
			}

			$new_content = $old_content . $caption_string;
			$content     = str_replace( $old_content, $new_content, $content );
		}
		ISC_Log::log( 'data-isc-images to caption: number of unique images found: ' . count( $replaced ) );

		return $content;
	}

	/**
	 * Return the image IDs from the `data-isc-images` attribute
	 *
	 * @param string[] $sources image sources with image ids => image src URI.
	 * @param string   $content any HTML document.
	 *
	 * @return string[] image sources with image ids => image src URI
	 */
	public function get_image_ids_from_isc_images_attribute( array $sources, string $content ): array {

		ISC_Log::log( 'ID from data-isc-images: start looking for the data-isc-images attribute' );

		/**
		 * 0 – full match
		 * 1 - value of data-isc-images
		 */
		$pattern = '#data-isc-images="([^"]*)#';
		$count   = preg_match_all( $pattern, $content, $matches, PREG_SET_ORDER );

		ISC_Log::log( 'ID from data-isc-images: embedded image IDs found: ' . $count );

		if ( false === $count ) {
			return $sources;
		}

		foreach ( $matches as $match ) {
			$id = (int) $match[1];
			if ( ! array_key_exists( $id, $sources ) ) {
				$sources[ $id ] = '';
			}
			ISC_Log::log( sprintf( 'ID from data-isc-images: found ID "%s" in the image tag', $id ) );
		}

		return $sources;
	}

	/**
	 * Manipulate the source URL HTML.
	 * relevant, when the source URL is a comma separated list of URLs
	 *
	 * @param string   $markup HTML.
	 * @param int      $id attachment ID.
	 * @param string[] $metadata attachment metadata.
	 *
	 * @return string
	 */
	public function render_source_url_html( $markup, $id, $metadata ) {
		$source_parts        = explode( ',', $metadata['source'] );
		$url_ends_with_comma = substr( $metadata['source_url'], -1 ) === ',';
		// remove comma at the end of the URL string
		$metadata['source_url'] = rtrim( $metadata['source_url'], ',' );
		$source_url_parts       = preg_split( '/\s*,\s*(?=http|,)/', $metadata['source_url'] );

		// if the source URL string ends with a comma, we add an empty string to the array
		if ( $url_ends_with_comma ) {
			$source_url_parts[] = '';
		}

		// return early if either array only has one element
		if ( 1 >= count( $source_parts ) || 1 >= count( $source_url_parts ) ) {
			return $markup;
		}

		$linked_sources = [];
		$count          = count( $source_parts );

		for ( $i = 0; $i < $count; $i++ ) {
			if ( isset( $source_url_parts[ $i ] ) ) {
				$url = esc_url( trim( $source_url_parts[ $i ] ) );
				// an invalid URL adds an unlinked source string
				if ( ! filter_var( $url, FILTER_VALIDATE_URL ) ) {
					$linked_sources[] = trim( $source_parts[ $i ] );
				} else {
					$linked_sources[] = sprintf(
						'<a href="%2$s" target="_blank" rel="nofollow">%1$s</a>',
						trim( $source_parts[ $i ] ),
						$url
					);
				}
			} else {
				// additional source strings without a URL
				$linked_sources[] = trim( $source_parts[ $i ] );
			}
		}

		return implode( ', ', $linked_sources );
	}

	/**
	 * Remove the caption from images with the isc-disable-overlay class.
	 * "isc-disable-overlay" can be in any of the tags: <figure>, <a> or <img>
	 *
	 * @param array $matches found images.
	 *
	 * @return array
	 * @uses ISC_Model::extract_images_from_html
	 */
	public function remove_overlay_from_isc_disable_overlay_class( array $matches ): array {

		if ( ! $matches ) {
			return $matches;
		}

		foreach ( $matches as $key => $match ) {
			// if isc-disable-overlay exists in match[0], remove this item from the array
			if ( strpos( $match['full'], 'isc-disable-overlay' ) ) {
				ISC_Log::log( 'isc-disable-overlay: found isc-disable-overlay class, removing image from array' );
				unset( $matches[ $key ] );
			}
		}

		// reset array keys
		return array_values( $matches );
	}

	/**
	 * Find valid image URLs in other places than the `src` attribute
	 * e.g.,
	 * <img src="data:some" data-src="https://example.com/image.jpg">
	 *
	 * @param array $matches found images.
	 *
	 * @return array
	 */
	public function find_more_valid_image_urls( array $matches ): array {
		if ( ! $matches ) {
			return $matches;
		}

		// iterate through matches and try to find valid image URLs if none existed before
		foreach ( $matches as $key => $match ) {
			// check if img_src is a valid URL
			if ( ! empty( $match['img_src'] ) && ! filter_var( $match['img_src'], FILTER_VALIDATE_URL ) ) {
				// extract image URLs
				$urls = \ISC\Image_Sources\Analyze_HTML::extract_image_urls( $match['inner_code'] );
				if ( ! count( $urls ) ) {
					continue;
				}

				// Try each URL until we find one not exclusively in an href
				foreach ( $urls as $url ) {
					// Check if this URL appears in an href attribute
					if ( preg_match( '/href=["\']' . preg_quote( $url, '/' ) . '["\']/', $match['inner_code'] ) ) {
						// Count occurrences of this URL in the HTML
						$count = substr_count( $match['inner_code'], $url );

						// If URL appears only once (in href) and not in other attributes, skip it
						if ( $count <= 1 ) {
							ISC_Log::log( 'Found image URL only in an href attribute and skipped it: ' . $url );
							continue; // Try the next URL
						}
					}

					// Found a valid URL that isn't exclusively in an href
					$matches[ $key ]['img_src'] = $url;
					ISC_Log::log( 'Found valid image URL in inner_code: ' . $url );
					break; // Exit URL loop after finding the first valid URL
				}
			}
		}

		// reset array keys
		return array_values( $matches );
	}

	/**
	 * Include overlay information and markup to HTML elements that use image URLs in a style attribute, e.g., background image
	 *
	 * @param string $html original HTML.
	 * @return string
	 */
	public function add_captions_for_inline_styles( string $html ): string {
		$options                  = Plugin::get_options();
		$load_overlay_text_inline = ! empty( $options['overlay_included_advanced'] ) && in_array( 'inline_style_data', $options['overlay_included_advanced'], true );
		$show_overlay_text        = ! empty( $options['overlay_included_advanced'] ) && in_array( 'inline_style_show', $options['overlay_included_advanced'], true );
		if ( ! $load_overlay_text_inline && ! $show_overlay_text ) {
			return $html;
		}

		/**
		 * Split content where `isc_stop_overlay` is found to not display overlays starting there
		 */
		if ( strpos( $html, 'isc_stop_overlay' ) ) {
			list( $html, $html_after ) = explode( 'isc_stop_overlay', $html, 2 );
		} else {
			$html_after = '';
		}

		$types = implode( '|', Image_Sources::get_instance()->allowed_extensions );

		/**
		 * Match groups:
		 * 0 - Full match: the full HTML tag with a style attribute that includes an image URL. Without the final >
		 * 1 - Image URL inside `url()`.
		 * 2 - File extension. Not needed
		 *
		 * Key points:
		 * This regular expression matches HTML tags with inline styles that include a `url()` with an image file extension. It can handle various tag types and attribute orders.
		 * It's case-insensitive (`i` modifier), which means it will match attribute and tag names in any combination of uppercase and lowercase.
		 * It does not capture any whitespace or other characters inside the `url()`. It specifically targets `url()`s that contain an image file extension.
		 *
		 * Potential issues:
		 * This regular expression assumes well-formed HTML and might not correctly handle some types of malformed HTML.
		 * It assumes that the `url()` in the style attribute contains an image URL. If it contains a URL that doesn't end with a recognized image file extension, it won't be matched.
		 * It won't capture `url()`s that are not part of a `style` attribute or that are part of a different attribute.
		 * It doesn't handle whitespace or line breaks in the style attribute. If the style attribute includes line breaks, they won't be captured correctly.
		 */
		$pattern = '#<[^>]+style=["\'][^"\']*url\(([^)]+\.(' . $types . '))[^>]*#i';
		$count   = preg_match_all( $pattern, $html, $matches, PREG_SET_ORDER );

		ISC_Log::log( 'Pro: add_captions_for_inline_styles() number of images found: ' . $count );

		if ( false === $count ) {
			return $html . $html_after;
		}

		// gather elements already replaced to prevent duplicate sources, see GitHub #105
		$replaced = [];

		foreach ( $matches as $key => $_match ) {
			// WordPress 6.4 started to escape the single quotes in url() for background images in groups inline styles using &#039;.
			// if found, we remove it before we handle the URL
			$_match[1] = str_replace( '&#039;', '', $_match[1] );
			// WordPress 6.9 started to escape the single quotes in url() for background images in groups inline styles using &apos;.
			$_match[1] = str_replace( '&apos;', '', $_match[1] );

			$hash = md5( $_match[1] ); // $_match[1] is the image URL
			if ( in_array( $hash, $replaced, true ) ) {
				ISC_Log::log( 'Pro: add_captions_for_inline_styles() skipped a repeating element' );
				continue;
			} else {
				$replaced[] = $hash;
			}

			$image_id = (int) ISC_Model::get_image_by_url( $_match[1] );
			ISC_Log::log( sprintf( 'Pro: add_captions_for_inline_styles() found ID for image URL "%s": "%s"', $_match[1], $image_id ) );

			$source_string = ISC\Image_Sources\Renderer\Caption::get( $image_id, [], [ 'styled' => false ] );
			if ( ! $source_string ) {
				continue;
			}

			$old_content = $_match[0];
			$new_content = '';
			if ( $load_overlay_text_inline ) {
				$new_content .= ' data-isc-source-text="' . esc_attr( $source_string ) . '"';
			}

			/**
			 * Add_caption_from_isc_images_attribute() is later converting the data-isc-images attribute to a caption
			 */
			if ( $show_overlay_text ) {
				$new_content .= ' data-isc-images="' . esc_attr( $image_id ) . '"';
			}

			$html = str_replace(
				$old_content,
				$old_content . $new_content,
				$html
			);
		}
		ISC_Log::log( 'Pro: add_captions_for_inline_styles() number of unique image URLs replaced: ' . count( $replaced ) );

		/**
		 * Attach follow content back
		 */
		return $html . $html_after;
	}

	/**
	 * Include overlay information and markup to style blocks that contain image URLs, e.g., background image
	 *
	 * @param string $html original HTML.
	 * @return string
	 */
	public function add_captions_to_style_blocks( string $html ): string {
		$options                  = Plugin::get_options();
		$load_overlay_text_inline = ! empty( $options['overlay_included_advanced'] ) && in_array( 'style_block_data', $options['overlay_included_advanced'], true );
		$show_overlay_text        = ! empty( $options['overlay_included_advanced'] ) && in_array( 'style_block_show', $options['overlay_included_advanced'], true );
		if ( ! $load_overlay_text_inline && ! $show_overlay_text ) {
			return $html;
		}

		/**
		 * Split content where `isc_stop_overlay` is found to not display overlays starting there
		 */
		if ( strpos( $html, 'isc_stop_overlay' ) ) {
			list( $html, $html_after ) = explode( 'isc_stop_overlay', $html, 2 );
		} else {
			$html_after = '';
		}

		/**
		 * Match groups:
		 * 0 - Full match: the full style block
		 * 1 - Everything after "<style" to allow adding the attribute into the opening tag,
		 *      includes the closing </style> tag so that we can add the source output after it, if the appropriate option is selected
		 * 2 - Image URL surrounded by quotes (which makes the regex a lot simpler)
		 *
		 * Key points:
		 * Only returns the first image URL in url(). Multiple url() information are ignored.
		 * This regular expression matches style tags that include a `url()`. The image file extension is not checked.
		 * It's case-insensitive (`i` modifier), which means it will match attribute and tag names in any combination of uppercase and lowercase.
		 */
		$pattern = '#<style([^<]*url\(([^)]+)[^>]*</style>)#i';
		$count   = preg_match_all( $pattern, $html, $matches, PREG_SET_ORDER );

		ISC_Log::log( 'Pro: add_captions_to_style_blocks() number of images found: ' . $count );

		if ( false === $count ) {
			return $html . $html_after;
		}

		// gather elements already replaced to prevent duplicate sources, see GitHub #105
		$replaced = [];

		foreach ( $matches as $key => $_match ) {
			// the image URL still includes optional quotes, which we are removing here
			$image_url = sanitize_url( $_match[2] );
			$hash      = md5( $image_url ); // $_match[2] is the image URL
			if ( in_array( $hash, $replaced, true ) ) {
				ISC_Log::log( 'Pro: add_captions_to_style_blocks() skipped a repeating element' );
				continue;
			} else {
				$replaced[] = $hash;
			}

			$image_id = (int) ISC_Model::get_image_by_url( $image_url );
			ISC_Log::log( sprintf( 'Pro: add_captions_to_style_blocks() found ID for image URL "%s": "%s"', $image_url, $image_id ) );

			$source_string = ISC\Image_Sources\Renderer\Caption::get( $image_id, [], [ 'styled' => false ] );
			if ( ! $source_string ) {
				continue;
			}

			$old_content    = $_match[1];
			$content_before = '';
			$content_after  = '';
			if ( $load_overlay_text_inline ) {
				$content_before = ' data-isc-source-text="' . esc_attr( $source_string ) . '"';
			}

			if ( $show_overlay_text ) {
				$content_after = ISC\Image_Sources\Renderer\Caption::add_style( $source_string, $image_id );
			}

			$html = str_replace(
				$old_content,
				$content_before . $old_content . $content_after,
				$html
			);
		}
		ISC_Log::log( 'Pro: add_captions_to_style_blocks() number of unique image URLs replaced: ' . count( $replaced ) );

		/**
		 * Attach follow content back
		 */
		return $html . $html_after;
	}

	/**
	 * Load custom attribute processors
	 * See the ISC\Pro\Custom_Attribute_Processor for the main class
	 */
	public function load_custom_attribute_processor() {
		$custom_attribute_processors = apply_filters( 'isc_pro_public_custom_attribute_processors', [] );

		if ( empty( $custom_attribute_processors ) ) {
			return;
		}
		foreach ( $custom_attribute_processors as $filter_args ) {
			// Ensure the arguments array has at least the required pattern and URL match index
			if ( ! is_array( $filter_args ) || count( $filter_args ) < 2 ) {
				continue;
			}

			// Extract arguments or use default values for optional parameters
			$regex_pattern        = $filter_args['regex_pattern'] ?? '';
			$replaced_match_index = $filter_args['replaced_match_index'] ?? 0;
			$url_match_index      = $filter_args['url_match_index'] ?? null;
			$enable_overlay       = $filter_args['enable_overlay'] ?? false;
			$args                 = $filter_args['args'] ?? [];

			// Create a new instance of Custom_Attribute_Processor with dynamic arguments
			new \ISC\Pro\Custom_Attribute_Processor(
				$regex_pattern,
				$replaced_match_index,
				$url_match_index,
				$enable_overlay,
				$args
			);
		}
	}

	/**
	 * Check if the current buffer level and handle are ours.
	 *
	 * @return void
	 */
	public function check_buffer_level(): void {
		/**
		 * Check if the current buffer level and handle are ours.
		 * If not, this could indicate a conflict with another plugin or theme
		 */
		$buffer_status = ob_get_status();
		if ( ! empty( $buffer_status ) && get_class( $this ) . '::handle_output_buffering' !== $buffer_status['name'] ) {
			ISC_Log::log( 'ISC_Pro_Public::stop_output_buffering: the buffer name is not ISC_Pro_Public::handle_output_buffering but ' . $buffer_status['name'] );
		}
	}
}
