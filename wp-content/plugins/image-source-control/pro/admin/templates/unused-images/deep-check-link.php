<?php
/**
 * Deep check link template
 *
 * @var WP_Post $post Post object.
 */

?><p><span class="dashicons dashicons-search"></span><a href="<?php echo esc_url( \ISC\Pro\Unused_Images_List_Table::get_attachment_id_url( $post->ID ) ); ?>"><?php esc_html_e( 'Deep Check', 'image-source-control-isc' ); ?></a></p><?php