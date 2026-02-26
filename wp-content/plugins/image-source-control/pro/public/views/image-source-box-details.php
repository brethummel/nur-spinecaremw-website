<?php
/**
 * Render the image source box as a collapsable list
 *
 * @var string $content list of image source or other content.
 * @var string $headline headline for the image list.
 * @var bool   $create_placeholder whether to create a placeholder or not.
 */

?>
<details class="isc_image_list_box"><?php if ( ! $create_placeholder ) : ?>
	<summary class="isc_image_list_title"><?php echo esc_html( $headline ); ?></summary>
	<?php echo $content; ?>
<?php endif; ?></details>
