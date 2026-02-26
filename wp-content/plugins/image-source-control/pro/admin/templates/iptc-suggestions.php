<?php
/**
 * Render list of image source suggestions
 *
 * @var array $suggestions List of image source suggestions
 */
?>
<div class="isc-source-suggestions">
	<p><?php esc_html_e( 'Suggestions', 'image-source-control-isc' ); ?>: <span class="hidden">(<?php esc_html_e( 'click to use', 'image-source-control-isc' ); ?>)</span></p>
	<?php if ( ! \ISC\Pro\Admin\License::is_valid() ) : ?>
		<p><?php \ISC\Pro\Admin\License::maybe_render_license_not_valid(); ?></p>
	<?php else : ?>
	<ul>
		<?php
		foreach ( $suggestions as $source ) :
			?><li><a><?php echo $source; ?></a></li><?php
		endforeach;
		?>
	</ul>
	<?php endif; ?>
</div>
