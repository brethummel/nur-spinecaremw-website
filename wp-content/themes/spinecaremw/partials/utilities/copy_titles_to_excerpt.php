<?php

require_once("../../../../../wp-load.php");

$themeurl = get_template_directory_uri();
$themedir = get_template_directory();

$posttypes = $GLOBALS['posttypes'];

if (!isset($_GET['pt'])) { ?>
	<h2>post titles --> excerpt title</h2>
	<p>Which post type would you like to apply this to?</p>
	<form action="#">
		<select style="font-size: 16px;" name="pt" id="post-types">
			<option value="">--- select ---</option>
			<?php foreach ($posttypes as $t => $type) { ?>
				<option value="<?php echo($t); ?>"><?php echo(ucwords($t)); ?></option>
			<?php } ?>
		</select>
		<input type="submit" value="Go!"/>
	</form>
<?php } else { 
		
$args = array(
	'post_type' => $_GET['pt'],
	'posts_per_page' => -1,
);

$posts = get_posts($args);


?>
	<h2>copying titles for post type = <?php echo($_GET['pt']);?>...</h2>
	<?php foreach ($posts as $p => $post) {

		$id = $post->ID;
		$title = $post->post_title;

		// if ($p == 1) {
			if (get_field('excerpt_title') == '' || get_field('excerpt_title') == 'Test Post') {
				update_field('field_5ea063fcc6680', $title, $id);
				echo('<pre>'); echo($id . ': title changed to "' . get_field('excerpt_title') . '"...'); echo('</pre>');
			} else {
				echo('<pre>'); echo($id . ': existing title "' . get_field('excerpt_title') . '" retained...'); echo('</pre>');
			}
		// }
	} ?>
<?php }


?>