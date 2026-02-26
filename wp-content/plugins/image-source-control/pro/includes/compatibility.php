<?php
/**
 * Automatically initialize all compatibility modules.
 *
 * This code scans the `includes/compatibility/` directory,
 * derives the class name from the filename, and instantiates it.
 * This avoids having to manually add each new compatibility class.
 */

$compatibility_path      = __DIR__ . '/compatibility/';
$compatibility_namespace = 'ISC\\Pro\\Compatibility\\';

// Use glob() to find all .php files in the directory.
$compatibility_files = glob( $compatibility_path . '*.php' );

if ( $compatibility_files ) {
	foreach ( $compatibility_files as $file ) {
		// Get the filename without the .php extension to derive the class name.
		$class_name = basename( $file, '.php' );

		// Construct the fully qualified class name with its namespace.
		$full_class_name = $compatibility_namespace . $class_name;

		// Check if the class exists before trying to instantiate it.
		if ( class_exists( $full_class_name ) ) {
			new $full_class_name();
		} else {
			error_log( "ISC Pro Compatibility Autoloader: Class $full_class_name does not exist in file $file." );
		}
	}
}
