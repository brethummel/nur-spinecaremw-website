<?php

function acf_bkgnd_choices_colors( $field ) {
    $field['choices'] = array(
		'bkgnd-white|light' => 'White',
		'bkgnd-tertiary|light' => 'Pearl',
		'bkgnd-light|light' => 'Soft White',
		'bkgnd-light2|light' => 'Cream',
		'bkgnd-primary|dark' => 'Bright Orange',
		'bkgnd-grad-orange|dark' => 'Orange Gradient'
	);
	
	if ($field['key'] == 'field_643609ebea9c8') { // section block
		$field['choices']['image'] = 'Image';
	}
	
	if ($field['key'] == 'field_60d3a88eed232') { // text block
		$field['choices']['columns'] = 'Split Backgrounds';
	}
	
	// if ($field['key'] == 'field_610c27e5e072f') { // hero block
	// 	$field['choices']['image'] = "Image";
	// 	// $field['choices']['video'] = "Video";
	// }

    return $field;
}

function acf_bkgnd_choices_greys( $field ) {
    $field['choices'] = array(
		'bkgnd-white|light' => 'White',
		'bkgnd-light|light' => 'Soft White',
		'bkgnd-light2|light' => 'Cream'
	);
    return $field;
}

function acf_button_choices_colors( $field ) {
    $field['choices'] = array(
		'primary' => 'Gradient Outline'
	);
    return $field;
}

function acf_rule_choices_colors( $field ) {
    $field['choices'] = array(
		'light' => 'Soft White',
		'primary' => 'Bright Orange',
		'secondary' => 'Tangerine'
	);
    return $field;
}


// ======================================= //
//              MASTER FIELDS              //
// ======================================= // 

// Button Colors
add_filter('acf/load_field/key=field_60d3c6bbff86a', 'acf_button_choices_colors'); // Settings : Background



// ======================================= //
//            MILD (CORE) BLOCKS           //
// ======================================= // 

// Accordion
add_filter('acf/load_field/key=field_6137c069cb56d', 'acf_bkgnd_choices_colors'); // Settings : Background

// Bio
add_filter('acf/load_field/key=field_610c472909076', 'acf_bkgnd_choices_colors'); // Settings : Background

// Buttons
add_filter('acf/load_field/key=field_614cb4452911b', 'acf_bkgnd_choices_colors'); // Settings : Background

// Contact Form
add_filter('acf/load_field/key=field_61326efe8ad38', 'acf_bkgnd_choices_colors'); // Settings : Background

// Full Image
add_filter('acf/load_field/key=field_610c42acd8ab0', 'acf_bkgnd_choices_colors'); // Settings : Background

// Legal
add_filter('acf/load_field/key=field_610c426e9c0a2', 'acf_bkgnd_choices_colors'); // Settings : Background

// People Grid
add_filter('acf/load_field/key=field_6130f79cbf296', 'acf_bkgnd_choices_greys'); // Settings : Background

// Posts
add_filter('acf/load_field/key=field_621ffc5465975', 'acf_bkgnd_choices_colors'); // Settings : Background

// Rule
add_filter('acf/load_field/key=field_614b9cf7267dd', 'acf_bkgnd_choices_greys'); // Settings : Background
add_filter('acf/load_field/key=field_614b9d97b610a', 'acf_rule_choices_colors'); // Settings : Rule Color

// Strip
add_filter('acf/load_field/key=field_61451828202a0', 'acf_bkgnd_choices_colors'); // Settings : Background

// Testimonials
add_filter('acf/load_field/key=field_60d65566d6dec', 'acf_bkgnd_choices_colors'); // Settings : Background

// Text
add_filter('acf/load_field/key=field_60d3a88eed232', 'acf_bkgnd_choices_colors'); // Settings : Background
add_filter('acf/load_field/key=field_618d7dcf02b72', 'acf_bkgnd_choices_colors'); // Columns : Backgrounds : Column 1
add_filter('acf/load_field/key=field_618d7e5002b73', 'acf_bkgnd_choices_colors'); // Columns : Backgrounds : Column 2
add_filter('acf/load_field/key=field_665783d1e0a28', 'acf_bkgnd_choices_colors'); // Columns : Backgrounds : New Column 1
add_filter('acf/load_field/key=field_66578445e0a2a', 'acf_bkgnd_choices_colors'); // Columns : Backgrounds : New Column 2

// Text + Image
add_filter('acf/load_field/key=field_60d6209fcb6c9', 'acf_bkgnd_choices_colors'); // Settings : Background

// Tiles
add_filter('acf/load_field/key=field_610c4d141012d', 'acf_bkgnd_choices_colors'); // Settings : Background



// ======================================= //
//              MEDIUM BLOCKS              //
// ======================================= //

// Audio Player
add_filter('acf/load_field/key=field_6261d1d951228', 'acf_bkgnd_choices_colors'); // Settings : Background
add_filter('acf/load_field/key=field_6262f91f092b0', 'acf_button_choices_colors'); // Settings : Background

// Gallery
add_filter('acf/load_field/key=field_61ae4e5eb8419', 'acf_bkgnd_choices_colors'); // Settings : Background

// Logo Grid
add_filter('acf/load_field/key=field_61c49c43bbb97', 'acf_bkgnd_choices_colors'); // Settings : Background

// Resources
add_filter('acf/load_field/key=field_61bfbed0c964e', 'acf_bkgnd_choices_colors'); // Settings : Background

// Section
add_filter('acf/load_field/key=field_643609ebea9c8', 'acf_bkgnd_choices_colors'); // Settings : Background

// Ticker
add_filter('acf/load_field/key=field_634dc212ae515', 'acf_bkgnd_choices_colors'); // Settings : Background




// ======================================= //
//               SPICY BLOCKS              //
// ======================================= // 

// Related
add_filter('acf/load_field/key=field_61b3d89c48a6b', 'acf_bkgnd_choices_colors'); // Settings : Background




// ======================================= //
//              CUSTOM BLOCKS              //
// ======================================= // 

// Event Info
add_filter('acf/load_field/key=field_66a9032521b1c', 'acf_bkgnd_choices_greys'); // Settings : Background

// Tabs
add_filter('acf/load_field/key=field_66f6e94bcdbec', 'acf_bkgnd_choices_colors'); // Settings : Background

// Tabs
add_filter('acf/load_field/key=field_669803122c533', 'acf_bkgnd_choices_colors'); // Settings : Background

?>