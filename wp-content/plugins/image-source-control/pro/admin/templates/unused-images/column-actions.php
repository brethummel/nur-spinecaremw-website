<?php
/**
 * Render the content of the actions column
 *
 * @var int $item_id attachment ID
 */

?>
<button type="button" class="button isc-button-deep-check" data-image-id="<?php echo esc_attr( $item_id ); ?>"><?php esc_html_e( 'Deep Check', 'image-source-control-isc' ); ?></button>