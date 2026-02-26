<?php
/**
 * Render content on the Post Index section on the Tools page
 */

?>
<a class="button button-primary" href="<?php echo esc_url( admin_url( 'options.php?page=isc-indexer' ) ); ?>"><?php esc_html_e( 'Run indexer', 'image-source-control-isc' ); ?></a>
<p class="description"><?php esc_html_e( 'Find images in the content of all published pages.', 'image-source-control-isc' ); ?></p>
