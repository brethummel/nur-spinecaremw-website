<?php
/**
 * Render the global list of image sources as a simple list – instead of a table
 *
 * @var array $lines list content.
 *
 * Added `isc_stop_overlay` as a class to the list to suppress overlays within it starting at that point
 **/

?>
<div class="isc_all_image_list_box isc_stop_overlay">
	<ul>
		<?php foreach ( $lines as $line ) : ?>
		<li><?php echo $line; ?></li>
		<?php endforeach; ?>
	</ul>
</div>