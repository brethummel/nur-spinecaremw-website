<?php
/**
 * Status table tbody template for the indexer page
 *
 * @var int $total_pages Total number of pages.
 * @var int $not_indexed_pages Number of not indexed pages.
 *
 * @return void
 */

?>
<tr>
	<td><?php esc_html_e( 'Posts', 'image-source-control-isc' ); ?></td>
	<td><?php echo esc_html( $total_pages ); ?></td>
	<td></td>
</tr>
<tr>
	<td><?php esc_html_e( 'Not indexed', 'image-source-control-isc' ); ?></td>
	<td><?php echo esc_html( $not_indexed_pages ); ?></td>
	<td>
		<?php
		if ( $not_indexed_pages !== 0 ) :
			?>
		<button class="button isc-indexer-btn" data-isc-indexer-mode="unindexed">
			<?php esc_html_e( 'Run indexer', 'image-source-control-isc' ); ?>
		</button>
		<?php endif; ?>
	</td>
</tr>