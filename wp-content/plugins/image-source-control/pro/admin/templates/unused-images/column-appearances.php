<?php
/**
 * Render the content of the appearances column
 *
 * @var object $item attachment data
 */

?>
<span class="isc-table-unused-images-deep-check-result">
	<?php ISC\Pro\Unused_Images\Admin\Appearances_List::render( $item->ID, [ 'details', 'checks' ] ); ?>
</span>
<span class="spinner"></span>
