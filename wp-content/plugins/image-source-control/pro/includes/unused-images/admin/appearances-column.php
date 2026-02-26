<?php

namespace ISC\Pro\Unused_Images\Admin;

use ISC\Plugin;
use ISC\Pro\Unused_Images_List_Table;

/**
 * Render the appearance column in the Media Library
 */
class Appearances_Column {

	/**
	 * Register hooks
	 * Initialized by ISC\Pro\Unused_Images in the "init" hook
	 *
	 * @return void
	 */
	public function __construct() {
		if ( ! Plugin::is_module_enabled( 'unused_images' ) ) {
			return;
		}

		// add a new "Appearances" column to the Media > Library > List (wp-admin/upload.php?mode=list)
		add_filter( 'manage_media_columns', [ $this, 'add_appearances_column_head' ] );
		// add column content
		add_filter( 'manage_media_custom_column', [ $this, 'add_appearances_column_content' ], 10, 2 );
	}


	/**
	 * Add heading for the Appearances column in the attachment list
	 *
	 * @param array $columns array with existing columns.
	 *
	 * @return array $new_columns
	 */
	public function add_appearances_column_head( $columns ): array {
		$new_columns = [];
		if ( is_array( $columns ) ) {
			// place the column directly after the title column
			foreach ( $columns as $key => $value ) {
				$new_columns[ $key ] = $value;
				if ( 'title' === $key ) {
					$new_columns['isc_appearances'] = __( 'Appearances', 'image-source-control-isc' );
				}
			}
		} else {
			$new_columns['isc_appearances'] = __( 'Appearances', 'image-source-control-isc' );
		}

		return $new_columns;
	}

	/**
	 * Display Appearances column content
	 *
	 * @param string $column_name name of the column.
	 * @param int    $att_id      attachment ID.
	 */
	public function add_appearances_column_content( string $column_name, int $att_id ) {
		if ( 'isc_appearances' !== $column_name || ! \ISC\Media_Type_Checker::should_process_attachment( $att_id ) ) {
			return;
		}

		Appearances_List::render( $att_id );

		// Output the row actions.
		$this->render_row_actions( $att_id );
	}

	/**
	 * Helper function to build row actions HTML.
	 *
	 * @param int $att_id attachment ID.
	 *
	 * @return void
	 */
	private function render_row_actions( int $att_id ) {
		$actions = [
			'isc_appearances' => sprintf(
				'<a href="%s"><span class="dashicons dashicons-search"></span> %s</a>',
				esc_url( Unused_Images_List_Table::get_attachment_id_url( $att_id ) ),
				esc_html__( 'Deep Check', 'image-source-control-isc' )
			),
		];

		$action_count = count( $actions );
		$i            = 0;

		?>
		<div class="row-actions">
			<?php

			foreach ( $actions as $action => $link ) {
				++$i;
				$sep = ( $i === $action_count ) ? '' : ' | ';
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				printf( "<span class='%s'>%s%s</span>", $action, $link, $sep );
			}

			?>
		</div>
		<?php
	}
}
