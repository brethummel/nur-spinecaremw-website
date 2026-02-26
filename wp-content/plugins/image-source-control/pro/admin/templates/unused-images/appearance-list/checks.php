<?php
/**
 * Check indicators for the unused images appearances list.
 *
 * @var bool $has_db_check Whether the database check is available.
 * @var string $indexer_url URL to the indexer page.
 * @var string $time_diff Time difference since the last check.
 */

?>
<div class="isc-appearances-checks">
		<span class="isc-check-indicator isc-check-indexer" title="<?php esc_attr_e( 'Content check', 'image-source-control-isc' ); ?>">
				<?php if ( $indexer_url ) : ?>
						<a href="<?php echo esc_url( $indexer_url ); ?>" target="_blank">
				<?php endif; ?>
				<span class="dashicons dashicons-text-page"></span>
				<span class="dashicons <?php echo ! $indexer_url ? 'dashicons-yes' : 'dashicons-no-alt'; ?>" title="<?php esc_attr_e( 'Content check', 'image-source-control-isc' ); ?>"></span>
				<?php
				if ( $indexer_url ) :
					?>
					</a><?php endif; ?>
		</span>
		<span class="isc-check-indicator isc-check-database" title="<?php esc_attr_e( 'Database check', 'image-source-control-isc' ); ?>">
				<a href="#">
					<span class="dashicons dashicons-database"></span>
					<span class="dashicons <?php echo $has_db_check ? 'dashicons-yes' : 'dashicons-no-alt'; ?>" title="<?php esc_attr_e( 'Database check', 'image-source-control-isc' ); ?>"></span>
				</a>
		</span>
	<?php if ( $time_diff ) : ?>
		<span class="isc-table-unused-images-last-check">
		<?php
		printf(
		// translators: %s is a time difference string, e.g., "2 hours"
			esc_html__( 'Checked %s ago', 'image-source-control-isc' ),
			esc_html( $time_diff )
		);
		?>
	</span>
	<?php endif; ?>
</div>