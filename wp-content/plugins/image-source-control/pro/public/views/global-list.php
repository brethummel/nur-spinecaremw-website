<?php
/**
 * Render the global list of image sources
 *
 * @var array    $options plugin options.
 * @var array    $attachments attachment information.
 * @var string[] $included_columns displayed columns.
 *
 * Added comment `isc_stop_overlay` as a class to the table to suppress overlays within it starting at that point
 * todo: allow overlays to start again after the table
 **/

?>
<div class="isc_all_image_list_box isc_stop_overlay" style="overflow: scroll;">
	<table>
		<thead>
		<?php if ( $options['thumbnail_in_list'] ) : ?>
			<th><?php esc_html_e( 'Thumbnail', 'image-source-control-isc' ); ?></th>
		<?php endif; ?>
		<?php if ( in_array( 'attachment_id', $included_columns, true ) ) : ?>
			<th><?php esc_html_e( 'Attachment ID', 'image-source-control-isc' ); ?></th>
		<?php endif; ?>
		<?php if ( in_array( 'title', $included_columns, true ) ) : ?>
			<th><?php esc_html_e( 'Title', 'image-source-control-isc' ); ?></th>
		<?php endif; ?>
		<?php if ( in_array( 'posts', $included_columns, true ) ) : ?>
			<th><?php esc_html_e( 'Attached to', 'image-source-control-isc' ); ?></th>
		<?php endif; ?>
		<?php if ( in_array( 'source', $included_columns, true ) ) : ?>
			<th><?php esc_html_e( 'Source', 'image-source-control-isc' ); ?></th>
		<?php endif; ?>
		</thead>
		<tbody>
		<?php foreach ( $attachments as $id => $data ) : ?>
			<?php
			$source = ISC\Image_Sources\Renderer\Image_Source_String::get( $id );
			?>
			<tr>
				<?php
				$v_align = '';
				if ( $options['thumbnail_in_list'] ) :
					$v_align = 'style="vertical-align: top;"';
					?><td><?php \ISC\Image_Sources\Renderer\Global_List::render_global_list_thumbnail( $id ); ?></td><?php
				endif; ?>
				<?php if ( in_array( 'attachment_id', $included_columns, true ) ) : ?>
					<td <?php echo $v_align; ?>><?php echo esc_html( $id ); ?></td>
				<?php endif; ?>
				<?php if ( in_array( 'title', $included_columns, true ) ) : ?>
					<td <?php echo $v_align; ?>><?php echo $data['title']; ?></td>
				<?php endif; ?>
				<?php if ( in_array( 'posts', $included_columns, true ) ) : ?>
					<td <?php echo $v_align; ?>><?php echo $data['posts']; ?></td>
				<?php endif; ?>
				<?php if ( in_array( 'source', $included_columns, true ) ) : ?>
					<td <?php echo $v_align; ?>><?php echo $source; ?></td>
				<?php endif; ?>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table></div>