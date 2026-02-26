<?php
/**
 * Render the content of the file column
 *
 * @var WP_Post $item post object
 * @var int $filecount number of files
 * @var string $filesize_string file size string
 * @var string $uploaded_string uploaded string
 * @var string $file_path file path
 */

edit_post_link( esc_html( $item->post_title ), '', '', $item->ID );
?>
<br />
<?php echo esc_html( $file_path ); ?>
<br />
<span title="<?php esc_html_e( 'number of files', 'image-source-control-isc' ); ?>">
	<?php echo esc_html( $filecount ) . 'x'; ?>
	<span class="dashicons dashicons-images-alt2" style="color: #999;"></span>
</span>
<?php
if ( $filesize_string ) :
	echo ' ' . esc_html( $filesize_string );
endif;
echo ' ';
?>
<span title="<?php esc_html_e( 'upload date', 'image-source-control-isc' ); ?>">
	<span class="dashicons dashicons-upload" style="color: #999;"></span>
	<?php echo esc_html( $uploaded_string ); ?>
</span>