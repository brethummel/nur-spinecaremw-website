<?php 

function spr_post_scripts() {
	$themeurl = get_template_directory_uri();
	$themedir = get_template_directory();
	wp_register_script('block_posts_ajax.js', $themeurl . '/partials/core/mild/block_posts/block_posts_ajax.js', array('jquery'), '1.1.2', true);
	$script_data_array = array(
		'ajaxurl' => admin_url('admin-ajax.php'),
		'security' => wp_create_nonce('spr_load_more_posts'),
	);
	wp_localize_script('block_posts_ajax.js', 'posts', $script_data_array);
	wp_enqueue_script('block_posts_ajax.js');
}
add_action('wp_enqueue_scripts', 'spr_post_scripts');


function spr_load_posts_by_ajax_callback() {

	check_ajax_referer('spr_load_more_posts', 'security');
	$posttype = explode(', ', $_POST['post-type']);
	$template = $_POST['template'];
	$fields = $_POST['fields'];

	// $cats = explode(', ', $_POST['cats']);
	// $catlist = [];
	// foreach ($cats as $cat) {
	// 	$cat = get_category_by_slug($cat);
	// 	$catlist[] = $cat->term_id;
	// }
	// $catlist = implode(', ', $catlist);

	set_query_var('date', $_POST['date']);
	set_query_var('excerpt', $_POST['excerpt']);
	set_query_var('columns', $_POST['columns']);
	set_query_var('filters', explode(', ', $_POST['filters']));

	$args = array(
		'post_type' => $posttype,
		'post_status' => 'publish',
		'orderby' => $_POST['orderby'],
		'order' => $_POST['order'],
		'paged' => $_POST['page'],
		'posts_per_page' => $_POST['posts_per_page'],
		'cat' => $_POST['cats'],
		'tag_id' => $_POST['tags'],
		'post_parent' => 0
	);
	$results = get_posts($args);

	if ($results) {
		if ($template == 'list') {
			foreach ($results as $result) {
				$id = $result->ID;
				$title = $result->post_title;
				// echo($title . '<br/>');
				$fields = get_fields($id);
				$searchfields = explode(', ', $_POST['searchfields']);
				// print_r($searchfields);
				$search = '';
				foreach ($fields as $f => $field) {
					if (in_array($f, $searchfields)) {
						if (is_array($field)) {
							$search .= implode(' ', $field) . ' ';
						} else {
							$field = str_replace(array("\r", "\n"), ' ', $field);
							$search .= $field . ' ';
						}
					}
				}
				echo('<li data-search="' . strtolower($search) . '" data-title="' . $title . '" data-id="' . $id . '"><a href="' . get_post_permalink($id) . '">' . $title . '</a><span class="content-match">(content&nbsp;match)</span></li>');
			}
		} else {
			foreach ($results as $result) {
				get_template_part('partials/custom/_templates/_posts/' . $template, null, $result);
			}
		}
	}

	wp_die();

}
add_action('wp_ajax_spr_load_posts_by_ajax', 'spr_load_posts_by_ajax_callback');
add_action('wp_ajax_nopriv_spr_load_posts_by_ajax', 'spr_load_posts_by_ajax_callback');


?>