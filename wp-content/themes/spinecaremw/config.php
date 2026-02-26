<?php

// ======================================= //
//              QUICK CONFIG               //
// ======================================= // 

$usemenus = false; // enables admin menu management (not fully implemented)
$multilocs = true; // true modifies Contact Info fields to allow multiple locations
$sitebanner = true; // true adds Site Banner fields to Contact Info page
$leadattribution = true; // true enables lead attribution: see section below
$allowsvgs = true; // true enables upload of svg files into media library
$layerslider = false; // true enables layerslider type in block_hero

$GLOBALS['posttypes'] = array(
	// 'page' => array('blocks', array('image', 'title', 'excerpt'), 'relationships', 'taxonomy'), // excerpt = manually entered
	// 'page' => array('blocks', array('image', 'title', 'snippet'), 'relationships', 'taxonomy'), // snippet = first xx chars of text
	// 'page' => array('blocks', 'post_title', 'relationships', 'taxonomy'), // uses the existing post title
	'page' => array('blocks', array('image', 'title'), 'relationships'),
	'event' => array('blocks', array('image', 'title', 'excerpt'), 'relationships'),
	'patients' => array('blocks', array('image', 'title', 'excerpt'), 'relationships'),
	'provider' => array('blocks', 'post_title', 'relationships'),
	'post' => array('blocks', array('image', 'title'), 'relationships'),
	'resource' => array('post_title', 'relationships'),
	// 'article' => array('blocks', array('image', 'title', 'excerpt'), 'relationships'),
	// 'news' => array(array('image', 'title', 'excerpt'), 'relationships')
	// add additional post types here
);

$peopleposts = array( // add post type for people and add functionality to block_peoplegrid to populate from them
	'use' => true,
	'department' => false, // adds additional field after Title
	'department_title' => 'Department', // field title for department field
	'slug' => 'provider',
	'singular' => 'Provider',
	'plural' => 'Providers',
	'icon' => 'dashicons-businessman'
);
$GLOBALS['peopleposts'] = $peopleposts;

$GLOBALS['produrl'] = 'nuraclinics.com';
$GLOBALS['stageurl'] = 'nurastage.wpenginepowered.com';
$GLOBALS['stageuser'] = '';
$GLOBALS['stagepass'] = '';


// ======================================= //
//            LEAD ATTRIBUTION             //
// ======================================= // 

$GLOBALS['leadattribution'] = $leadattribution;

if ($leadattribution) {
	
	// how long until attribution cookie should expire?
	
	//                 (yy * ddd * hh * mm * ss)
	$cookie_lifespan = (10 * 365 * 24 * 60 * 60);
	$GLOBALS['cookie_lifespan'] = $cookie_lifespan;

	// LEAD ATTRIBUTION
	acf_add_local_field_group(array(
		'key' => 'group_62675f4b67bf2',
		'title' => 'Default Lead Attribution',
		'fields' => array(
			array(
				'key' => 'field_626760773a0f2',
				'label' => 'Note',
				'name' => '',
				'type' => 'message',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'message' => 'Enter the campaign and source values that <strong>new, direct-traffic leads</strong> will be attributed to by default if no other values are specified elsewhere.',
				'new_lines' => 'wpautop',
				'esc_html' => 0,
			),
			array(
				'key' => 'field_62675f563a0ef',
				'label' => 'Lead Attribution',
				'name' => 'attribution',
				'type' => 'group',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'layout' => 'table',
				'sub_fields' => array(
					array(
						'key' => 'field_62695359ce7fd',
						'label' => 'Set Campaign To',
						'name' => 'campaign_setting',
						'type' => 'select',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'choices' => array(
							'slug' => 'Permalink',
							'value' => 'Global Value',
						),
						'default_value' => 'slug',
						'allow_null' => 0,
						'multiple' => 0,
						'ui' => 0,
						'return_format' => 'value',
						'ajax' => 0,
						'placeholder' => '',
					),
					array(
						'key' => 'field_62695574ce7fe',
						'label' => 'Campaign',
						'name' => 'campaign_permalink',
						'type' => 'text',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => array(
							array(
								array(
									'field' => 'field_62695359ce7fd',
									'operator' => '==',
									'value' => 'slug',
								),
							),
						),
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'default_value' => 'Ex: ' . $GLOBALS['produrl'] . ' (home)',
						'placeholder' => '',
						'prepend' => '',
						'append' => '',
						'readonly' => 1,
						'maxlength' => '',
					),
					array(
						'key' => 'field_6267605b3a0f0',
						'label' => 'Campaign',
						'name' => 'campaign',
						'type' => 'text',
						'instructions' => '',
						'required' => 1,
						'conditional_logic' => array(
							array(
								array(
									'field' => 'field_62695359ce7fd',
									'operator' => '==',
									'value' => 'value',
								),
							),
						),
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'default_value' => '',
						'placeholder' => '',
						'prepend' => '',
						'append' => '',
						'maxlength' => '',
					),
					array(
						'key' => 'field_626760693a0f1',
						'label' => 'Source',
						'name' => 'source',
						'type' => 'text',
						'instructions' => '',
						'required' => 1,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'default_value' => 'Website (direct traffic)',
						'placeholder' => '',
						'prepend' => '',
						'append' => '',
						'maxlength' => '',
					),
					array(
						'key' => 'field_62676093a0pq3',
						'label' => 'Medium',
						'name' => 'medium',
						'type' => 'text',
						'instructions' => '',
						'required' => 1,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'default_value' => 'website',
						'placeholder' => '',
						'prepend' => '',
						'append' => '',
						'maxlength' => '',
					),
					array(
						'key' => 'field_626ab4b7583ca',
						'label' => 'Attribution Model',
						'name' => 'model',
						'type' => 'select',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'choices' => array(
							'first' => 'First Source',
							'last' => 'Last Source',
							'last-non-direct' => 'Last Non-Direct Source',
						),
						'default_value' => 'last-non-direct',
						'allow_null' => 0,
						'multiple' => 0,
						'ui' => 0,
						'return_format' => 'value',
						'ajax' => 0,
						'placeholder' => '',
					),
				),
			),
		),
		'location' => array(
			array(
				array(
					'param' => 'options_page',
					'operator' => '==',
					'value' => 'acf-options-global-info',
				),
			),
		),
		'menu_order' => 25,
		'position' => 'acf_after_title',
		'style' => 'default',
		'label_placement' => 'top',
		'instruction_placement' => 'label',
		'hide_on_screen' => '',
		'active' => true,
		'description' => '',
		'show_in_rest' => 0,
	));
	// To complete the loop on lead attribution, you must have a lead form that has a source
	// and campaign field in it, then specify the element IDs of their corresponding input
	// fieds using $attribution_fieldmap below.
	
	$attribution_fieldmap = array(
		'campaign_field' => 'field[154]',  // specify form field ID for campaign field
		'source_field' => 'field[155]',  // specify form field ID for source field
		'attrsrc_field' => 'field[156]',  // specify form field ID for source field
		'visits_field' => array(
			'field' => 'field[157]',
			'delimiter' => ' | ',
		),  // specify form field ID for page visits field
	);
	$GLOBALS['attribution_fieldmap'] = $attribution_fieldmap;
	
}


// ======================================= //
//          ADD FORMATS DROPDOWN           //
// ======================================= // 

$mcestyles = false; // show custom styles dropdown in WYSIWYG editor

$styleformats = array(
	array(  
		'title' => 'Intro Text',  
		'inline' => 'span',  
		'classes' => 'intro',
		'wrapper' => true,
	),
);
$GLOBALS['styleformats'] = $styleformats;


// ======================================= //
//             ADD THEME FONTS             //
// ======================================= // 

function spr_add_theme_fonts($themeurl) {
    wp_enqueue_style('nunito', 'https://fonts.googleapis.com/css2?family=Nunito+Sans:ital,opsz,wght@0,6..12,200..1000;1,6..12,200..1000&display=swap');
    wp_enqueue_style('myriad', 'https://use.typekit.net/gyk4ngd.css');
    wp_enqueue_style('typicons', $themeurl . '/fonts/Typicons/typicons.min.css');
    wp_enqueue_style('microns', $themeurl . '/fonts/Microns/microns.css');
}


// ======================================= //
//          ADD ADDTL JS PACKAGES          //
// ======================================= // 

function spr_add_js_packages($themeurl) {

	// lead_attribution.js
	if ($GLOBALS['leadattribution']) {
		wp_register_script('lead_attribution.js', get_template_directory_uri() . '/partials/admin/lead_attribution.js', array('jquery'), '0.4.0', true);
	    wp_enqueue_script('lead_attribution.js');
	}
	
	// lottie-player.js
    // wp_register_script('lottie-player.js', 'https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js', array('jquery'), '0.4.0', true);
    // wp_enqueue_script('lottie-player.js');
	
	// lottie-interactivity.js
	// wp_register_script('lottie-interactivity.js', 'https://unpkg.com/@lottiefiles/lottie-interactivity@latest/dist/lottie-interactivity.min.js', array('jquery'), '0.4.0', true);
    // wp_enqueue_script('lottie-interactivity.js');
	
	// lottie-interactive.js
    // wp_enqueue_script('lottie-interactive.js');
	// wp_register_script('lottie-interactive.js', 'https://unpkg.com/lottie-interactive@latest/dist/lottie-interactive.js', array('jquery'), '0.4.0', true);
	
	// skrollr.js
	// wp_register_script('skrollr.js', 'https://cdnjs.cloudflare.com/ajax/libs/skrollr/0.6.30/skrollr.min.js', array('jquery'), '0.4.0', true);
    // wp_enqueue_script('skrollr.js');
	
	// lax.js
	// wp_register_script('lax.js', 'https://cdn.jsdelivr.net/npm/lax.js', array('jquery'), '2.0.3', true);
    // wp_enqueue_script('lax.js');
	
	// rolly.js
	// wp_register_script('rolly.js', 'https://unpkg.com/rolly.js@0.4.0/dist/rolly.min.js', array('jquery'), '0.4.0', true);
    // wp_enqueue_script('rolly.js');
	// wp_enqueue_style('rolly', 'https://unpkg.com/rolly.js@0.4.0/css/style.css');
}


// ======================================= //
//            CUSTOM SHORTCODES            //
// ======================================= // 

//function shortcode_init(){
//	add_shortcode( 'shortcode-text', 'spr_insert_shortcode-name' );
//}
//add_action('init', 'shortcode_init');

//function spr_insert_shortcode-name() {
//	return '';
//}


// ======================================= //
//                ACF INIT                 //
// ======================================= // 

// ----------- GOOGLE MAPS ----------- //

function spr_acf_setting_init() {
	acf_update_setting( 'google_api_key', '');
}
// add_action( 'acf/init', 'spr_acf_setting_init' );


// --------- SIDEBAR ORDER ---------- //
	
function spr_priority_sidebars_order() {

	global $post;
	global $wp_meta_boxes;

	$posttype = $post->post_type;

	// each item in this array should have a post type as it's key,
	// then in the child array, add the id of each meta box in the
	// order you want them to appear after the Publish meta box.
	$priority_sidebars = array(
		'page' => array (
			'acf-group_663bbd8a7942a',
		)
	);

	if (array_key_exists($posttype, $priority_sidebars)) {

		// echo('<pre>'); echo("I think I'm in excerptposttypes!"); echo('</pre>');
		$sidebar = $wp_meta_boxes[$posttype]['side']['core'];

		$submitdiv = $sidebar['submitdiv'];
		unset($sidebar['submitdiv']);
		$sortedmetas = array($submitdiv);
		
		foreach ($priority_sidebars[$posttype] as $box) {
			if (isset($sidebar[$box])) {
				$thisbox = $sidebar[$box];
				unset($sidebar[$box]);
				$sortedmetas[] = $thisbox;
			}
		}

		foreach($sidebar as $meta) {
			array_push($sortedmetas, $meta);
		}

		$wp_meta_boxes[$posttype]['side']['core'] = $sortedmetas;

	}
}
add_action('acf/add_meta_boxes', 'spr_priority_sidebars_order');


// ======================================= //
//             ADD SVG SUPPORT             //
// ======================================= // 

if ($allowsvgs) {
	add_filter( 'wp_check_filetype_and_ext', function($data, $file, $filename, $mimes) {
		global $wp_version;
		if ( $wp_version !== '4.7.1' ) {
			return $data;
		}
		$filetype = wp_check_filetype( $filename, $mimes );
		return [
			'ext'             => $filetype['ext'],
			'type'            => $filetype['type'],
			'proper_filename' => $data['proper_filename']
		];
	}, 10, 4 );

	function spr_mime_types( $mimes ){
		$mimes['svg'] = 'image/svg+xml';
		return $mimes;
	}
	add_filter( 'upload_mimes', 'spr_mime_types' );

	function spr_fix_svg() {
		echo '';
	}
	add_action( 'admin_head', 'spr_fix_svg' );
}


// ======================================= //
//                USE BLOCKS               //
// ======================================= //

$useblocks = array(

	// ----------- MILD BLOCKS ----------- //
	
	'block_accordion' => true,
	'block_anchor' => true,
	'block_bio' => true,
	'block_buttons' => true,
	'block_contactform' => true,
	'block_fullimage' => true,
	'block_hero' => true,
	'block_legal' => true,
	'block_peoplegrid' => true,
	'block_posts' => true,
	'block_published' => false,
	'block_pullquote' => false,
	'block_rule' => false,
	'block_share' => false,
	'block_strip' => true,
	'block_testimonials' => false,
	'block_text' => true,
	'block_textimage' => true,
	'block_tiles' => true,

	// ---------- MEDIUM BLOCKS ---------- //

	'block_audioplayer' => false,
	'block_gallery' => true,
	'block_layerslider' => false,
	'block_logogrid' => false,
	'block_photostrip' => false,
	'block_resources' => true,
	'block_ticker' => false,
	
	// ---------- SPICY BLOCKS ----------- //

	'block_map' => false,
	'block_related' => true,

);

$GLOBALS['useblocks'] = $useblocks;
	

// ======================================= //
//          ACF ADD CUSTOM SOCIAL          //
// ======================================= //

function spr_add_custom_social($field) {
	
	$field['sub_fields'][] = array(
		'key' => 'field_mql3suabwviq1',
		'label' => 'Pinterest',
		'name' => 'pinterest',
		'type' => 'group',
		'instructions' => '',
		'required' => 0,
		'id' => '',
		'class' => '',
		'conditional_logic' => array(
			array(
				array(
					'field' => 'field_5c2ff7e78c7a5',
					'operator' => '==',
					'value' => '1',
				),
			),
		),
		'wrapper' => array(
			'width' => '',
			'class' => '',
			'id' => '',
		),
		'layout' => 'table',
		'sub_fields' => array(
			array(
				'key' => 'field_mql3suabwviq1a',
				'label' => 'Include?',
				'name' => 'pinterest_include',
				'type' => 'true_false',
				'instructions' => '',
				'required' => 0,
				'id' => '',
				'class' => '',
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '20',
					'class' => '',
					'id' => '',
				),
				'message' => '',
				'default_value' => 0,
				'ui' => 1,
				'ui_on_text' => '',
				'ui_off_text' => '',
				'_name' => 'pinterest_include',
				'_valid' => 1
			),
			array(
				'key' => 'field_mql3suabwviq1b',
				'label' => 'Pinterest',
				'name' => 'pinterest_url',
				'type' => 'url',
				'instructions' => '',
				'required' => 1,
				'id' => '',
				'class' => '',
				'conditional_logic' => array(
					array(
						array(
							'field' => 'field_mql3suabwviq1a',
							'operator' => '==',
							'value' => '1',
						),
					),
				),
				'wrapper' => array(
					'width' => '80',
					'class' => '',
					'id' => '',
				),
				'default_value' => 'http://www.pinterest.com/',
				'placeholder' => '',
				'_name' => 'pinterest_url',
				'_valid' => 1
			),
		),
		'_name' => 'pinterest',
		'_valid' => 1
	);
	
	$field['sub_fields'][] = array(
		'key' => 'field_mql3suabwviq2',
		'label' => 'Houzz',
		'name' => 'houzz',
		'type' => 'group',
		'instructions' => '',
		'required' => 0,
		'id' => '',
		'class' => '',
		'conditional_logic' => array(
			array(
				array(
					'field' => 'field_5c2ff7e78c7a5',
					'operator' => '==',
					'value' => '1',
				),
			),
		),
		'wrapper' => array(
			'width' => '',
			'class' => '',
			'id' => '',
		),
		'layout' => 'table',
		'sub_fields' => array(
			array(
				'key' => 'field_mql3suabwviq2a',
				'label' => 'Include?',
				'name' => 'houzz_include',
				'type' => 'true_false',
				'instructions' => '',
				'required' => 0,
				'id' => '',
				'class' => '',
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '20',
					'class' => '',
					'id' => '',
				),
				'message' => '',
				'default_value' => 0,
				'ui' => 1,
				'ui_on_text' => '',
				'ui_off_text' => '',
				'_name' => 'houzz_include',
				'_valid' => 1
			),
			array(
				'key' => 'field_mql3suabwviq2b',
				'label' => 'Houzz',
				'name' => 'houzz_url',
				'type' => 'url',
				'instructions' => '',
				'required' => 1,
				'id' => '',
				'class' => '',
				'conditional_logic' => array(
					array(
						array(
							'field' => 'field_mql3suabwviq2a',
							'operator' => '==',
							'value' => '1',
						),
					),
				),
				'wrapper' => array(
					'width' => '80',
					'class' => '',
					'id' => '',
				),
				'default_value' => 'http://www.houzz.com/',
				'placeholder' => '',
				'_name' => 'houzz_url',
				'_valid' => 1
			),
		),
		'_name' => 'houzz',
		'_valid' => 1
	);
	
	$field['sub_fields'][] = array(
		'key' => 'field_mql3suabwviq3',
		'label' => 'Vimeo',
		'name' => 'vimeo',
		'type' => 'group',
		'instructions' => '',
		'required' => 0,
		'id' => '',
		'class' => '',
		'conditional_logic' => array(
			array(
				array(
					'field' => 'field_5c2ff7e78c7a5',
					'operator' => '==',
					'value' => '1',
				),
			),
		),
		'wrapper' => array(
			'width' => '',
			'class' => '',
			'id' => '',
		),
		'layout' => 'table',
		'sub_fields' => array(
			array(
				'key' => 'field_mql3suabwviq3a',
				'label' => 'Include?',
				'name' => 'vimeo_include',
				'type' => 'true_false',
				'instructions' => '',
				'required' => 0,
				'id' => '',
				'class' => '',
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '20',
					'class' => '',
					'id' => '',
				),
				'message' => '',
				'default_value' => 0,
				'ui' => 1,
				'ui_on_text' => '',
				'ui_off_text' => '',
				'_name' => 'vimeo_include',
				'_valid' => 1
			),
			array(
				'key' => 'field_mql3suabwviq3b',
				'label' => 'Vimeo',
				'name' => 'vimeo_url',
				'type' => 'url',
				'instructions' => '',
				'required' => 1,
				'id' => '',
				'class' => '',
				'conditional_logic' => array(
					array(
						array(
							'field' => 'field_mql3suabwviq3a',
							'operator' => '==',
							'value' => '1',
						),
					),
				),
				'wrapper' => array(
					'width' => '80',
					'class' => '',
					'id' => '',
				),
				'default_value' => 'http://www.vimeo.com/',
				'placeholder' => '',
				'_name' => 'vimeo_url',
				'_valid' => 1
			),
		),
		'_name' => 'vimeo',
		'_valid' => 1
	);
	
	return $field;
	
}
// add_filter('acf/load_field/key=field_5c2fedddafd87', 'spr_add_custom_social', 10, 4);


// --------- CUSTOMIZE CONTACT INFO FIELDS ---------- //

function spr_add_custom_contact_fields() {
	if( function_exists('acf_add_local_field_group') ):
		acf_add_local_field(array(
			'key' => 'field_3ohxl2fg7ulh',
			'label' => 'Hours',
			'name' => 'hours',
			'type' => 'textarea',
			'instructions' => '',
			'required' => 1,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'maxlength' => '',
			'rows' => 3,
			'placeholder' => '',
			'parent' => 'field_618c116acb2cd',
			'new_lines' => 'wpautop',
		), 1);
		acf_add_local_field(array(
			'key' => 'field_sfx8uayw58oz',
			'label' => 'Appointments Number',
			'name' => 'appointments_number',
			'type' => 'text',
			'instructions' => '',
			'required' => 1,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'placeholder' => '',
			'prepend' => '',
			'append' => '',
			'parent' => 'group_5fc94ae7a614c',
			'maxlength' => '',
		), 1);
	endif;
}
add_action('acf/init', 'spr_add_custom_contact_fields', 10);


// --------- CUSTOMIZE BIO FIELDS ---------- //

function spr_add_custom_bio_fields() { // add field
	if( function_exists('acf_add_local_field_group') ):
		acf_add_local_field(array(
			'key' => 'field_662047d80aa9e2',
			'label' => 'Certifications',
			'name' => 'certifications',
			'type' => 'textarea',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'maxlength' => '',
			'rows' => 3,
			'placeholder' => '',
			'parent' => 'field_662047cb0aa9d',
			'new_lines' => 'wpautop',
		));

	endif;
}
add_action('acf/init', 'spr_add_custom_bio_fields', 10);


function spr_sort_bio_fields($field) {
	$subfields = $field['sub_fields'];
	$certifications = $subfields[0];
	unset($subfields[0]);
	array_push($subfields, $certifications);
	$field['sub_fields'] = $subfields;
	return $field;
}
add_filter('acf/prepare_field/key=field_662047cb0aa9d', 'spr_sort_bio_fields');
	

function spr_mod_bio_title_field($value, $post_id, $field) {

	if(have_rows('content_blocks', $post_id)): 
		while(have_rows('content_blocks', $post_id)): the_row();
			if (get_row_layout() == 'block_bio'):
				$certifications = get_sub_field('bio_info')['info']['certifications'];
				$certifications = str_replace('<p>', '<p class="certifications">', $certifications);
				$certifications = str_replace('</p>', '', $certifications);
			endif;
		endwhile;
	endif;

	// $certifications = get_post_meta($post_id, 'content_blocks_0_bio_info_info_certifications', false);

	// $field = $field . '</p><p class="certifications">' . $certifications;

	// return $field . 'sssssss';


	// echo('<pre>'); print_r($field); echo('</pre>');

	return $value . '</p>' . $certifications;
}
add_filter('acf/format_value/key=field_662047d80aa9e', 'spr_mod_bio_title_field', 10, 3);


function spr_highlight_matches($haystack, $needles) {
	$match = false;
	if (gettype($needles) == 'array') {
		$haystackarr = explode(' ', $haystack);
		foreach ($needles as $term) {
			foreach ($haystackarr as $w => $word) {
				$start = -1;
				$wordlc = strtolower($word);
				$start = strpos($wordlc, strtolower($term));
				// echo('<pre>'); echo($start); echo('</pre>');
				if ($start > -1) {
					$match = true;
					$wordfront = substr($word, 0, $start);
					$termmatch = '<strong>' . substr($word, $start, strlen($term)) . '</strong>';
					$wordback = substr($word, $start + strlen($term));
					$word = $wordfront . $termmatch . $wordback;
					$haystackarr[$w] = $word;
				}
			}
			$haystack = implode(' ', $haystackarr);
		}
	}
	return array($match, $haystack);
}

function spr_filter_post_link($post_link, $post) {
	if ($post->post_type == 'post') {
		$settings = get_field('post_settings', $post->ID);
		// echo('<pre>'); print_r($field); echo('</pre>');

		if ($settings['type'] == 'pdf') {
			$post_link = $settings['pdf']['url'] . '" target="_blank';
		} elseif ($settings['type'] == 'url') {
			$post_link = $settings['url'] . '" target="_blank';
		}

	}
	return $post_link;
}
add_filter('post_type_link', 'spr_filter_post_link', 10, 2);

?>