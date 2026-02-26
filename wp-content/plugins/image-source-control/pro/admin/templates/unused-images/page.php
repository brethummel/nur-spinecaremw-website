<?php
/**
 * Render view with unused images
 *
 * @var object[] $attachments list of attachments without association to a post
 * @var array[] $views list of views
 * @var bool $indexer_expired true if the indexer is considered to be outdated, e.g. 7 days
 * @var \ISC\Pro\Unused_Images_List_Table $unused_images_list_table
 */

// warn if indexer is outdated
if ( $indexer_expired ) :
	?>
	<div class="notice notice-error">
		<p>
			<?php
			printf(
			// translators: %s is a time difference string, e.g., "2 hours"
				esc_html__( 'The indexer is outdated. The last check was more than %s days ago.', 'image-source-control-isc' ),
				esc_html( \ISC\Pro\Indexer\Indexer::MAX_DAYS_SINCE_LAST_CHECK )
			);
			?>
			<a class="button button-primary" href="<?php echo esc_url( admin_url( 'options.php?page=isc-indexer' ) ); ?>"><?php esc_html_e( 'Run indexer', 'image-source-control-isc' ); ?></a>
		</p>
	</div>
	<?php
else :
	?><p>
	<a class="button button-secondary" href="<?php echo esc_url( admin_url( 'options.php?page=isc-indexer' ) ); ?>"><?php esc_html_e( 'Run indexer', 'image-source-control-isc' ); ?></a>
</p>
	<?php
endif;
?>
	<div id="isc-table-unused-images-page" class="wrap">
		<div class='subsubsub'>
			<span class="dashicons dashicons-filter"></span>
			<?php
			foreach ( $views as $view ) :
				?>
				<a class="button <?php echo esc_attr( $view['class'] ); ?>" href="<?php echo esc_url( $view['url'] ); ?>"><?php echo esc_html( $view['label'] ); ?></a>
				<?php
			endforeach;
			?>
		</div>
		<?php
		$unused_images_list_table->views();
		?>
		<form id="unused-images-form" method="post">
		<?php
		$unused_images_list_table->display();
		?>
		</form>
	</div>
<?php