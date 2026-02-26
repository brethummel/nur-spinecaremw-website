<?php
/**
 * Render the details section of the appearances list.
 *
 * @var array $indexed_posts       Posts found by the indexer.
 * @var array $database_results    Results from the database search.
 * @var int   $database_last_check Timestamp of the last deep check.
 * @var array $image_sources_index Posts from the image sources index.
 */

?>
<details class="isc-appearances-list">
		<summary><?php esc_html_e( 'Details', 'image-source-control-isc' ); ?></summary>
		<h4><?php esc_html_e( 'Content check', 'image-source-control-isc' ); ?></h4>
		<?php if ( $indexed_posts ) : ?>
				<?php $posts = $indexed_posts; ?>
				<?php include ISCPATH . 'pro/admin/templates/unused-images/appearance-list/results-in-post-content.php'; ?>
		<?php else : ?>
				<?php \ISC\Pro\Unused_Images\Admin\Appearances_List::render_note( 'no_results' ); ?>
		<?php endif; ?>

		<h4><?php esc_html_e( 'Database check', 'image-source-control-isc' ); ?></h4>
		<?php if ( ! $database_last_check ) : ?>
				<?php \ISC\Pro\Unused_Images\Admin\Appearances_List::render_note( 'unchecked' ); ?>
				<?php $database_results = []; ?>
		<?php elseif ( ! empty( $database_results ) && ! empty( $database_results['posts'] ) ) : ?>
				<?php $posts = $database_results['posts']; ?>
				<?php include ISCPATH . 'pro/admin/templates/unused-images/appearance-list/results-in-post-content.php'; ?>
		<?php elseif ( empty( $database_results ) ) : ?>
				<?php \ISC\Pro\Unused_Images\Admin\Appearances_List::render_note( 'no_results' ); ?>
		<?php endif; ?>

		<?php if ( ! empty( $database_results['postmetas'] ) ) : ?>
				<?php $postmetas = $database_results['postmetas']; ?>
				<?php include ISCPATH . 'pro/admin/templates/unused-images/appearance-list/results-in-postmeta.php'; ?>
		<?php endif; ?>

		<?php if ( ! empty( $database_results['options'] ) ) : ?>
				<?php $options = $database_results['options']; ?>
				<?php include ISCPATH . 'pro/admin/templates/unused-images/appearance-list/results-in-options.php'; ?>
		<?php endif; ?>

		<?php if ( ! empty( $database_results['usermetas'] ) ) : ?>
			<?php $usermetas = $database_results['usermetas']; ?>
			<?php include ISCPATH . 'pro/admin/templates/unused-images/appearance-list/results-in-usermeta.php'; ?>
		<?php endif; ?>

		<?php if ( $image_sources_index && \ISC\Plugin::is_module_enabled( 'image_sources' ) ) : ?>
				<h4><?php esc_html_e( 'Post Index', 'image-source-control-isc' ); ?> (<?php esc_html_e( 'Image Sources', 'image-source-control-isc' ); ?>)</h4>
				<?php $posts = $image_sources_index; ?>
				<?php include ISCPATH . 'pro/admin/templates/unused-images/appearance-list/results-in-post-content.php'; ?>
		<?php endif; ?>
</details>
