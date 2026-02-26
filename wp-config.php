<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'mhs_curious_staging_com');

/** MySQL database username */
define('DB_USER', 'mhscuriousstagin');

/** MySQL database password */
define('DB_PASSWORD', 'Pfa4!d!K');

/** MySQL hostname */
define('DB_HOST', 'mysql.mhs.curious-staging.com');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'w6"A68sisykZYtD%g/IiZSqGw|*FT"PY9PmGo6Bj(JBT1qjWTL)#TO29%%y0o_Po');
define('SECURE_AUTH_KEY',  'XCaLgg!:SG8`Wh$OSf*v9%@D|hCCfr_Tj^jc7/rF:c_S!^;v^w3_:om@G5`nutW+');
define('LOGGED_IN_KEY',    'ObSXrMmvTmiWaU+Hda7)NI"b4E_Tx_7@LpBg*jh!QJokIEAdI_Z5(|Wbk/(lf_qV');
define('NONCE_KEY',        'kFG(?h6ei4Y;Qak?UOzU^U7EzFdglVNX~Nvq6YU07OEfw4b;H#T^;2)xNC$574yL');
define('AUTH_SALT',        'FyMsu9Q5k7(iO0`pmm|xe"&JClh8znRl_K5rc90Y2@L/QMfNcb_ffX:x~b_7rQJx');
define('SECURE_AUTH_SALT', 'M3RhL@MxfWM2cVzi@kigAid4nhpHcU9)bmfh3XTfoAyZRo:TZ_s)vm/aE`x9Qz53');
define('LOGGED_IN_SALT',   'dKza3#f+FI)u:vT2TP*uo$lVduSGO1Q3MI87q?Jl?*1V(Xh/nxvE@"JjWd;x4g$m');
define('NONCE_SALT',       'Br1@sT)xk+!e3cl1P/+/rNkp9:Z+_j9UhgmpCVKqL;!+n:NHk!0~La|$AG8wWREX');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_hd7uy6_';

/**
 * Limits total Post Revisions saved per Post/Page.
 * Change or comment this line out if you would like to increase or remove the limit.
 */
define('WP_POST_REVISIONS',  10);

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define('WPLANG', '');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', true);

/**
 * Removing this could cause issues with your experience in the DreamHost panel
 */

if (isset($_SERVER['HTTP_HOST']) && preg_match("/^(.*)\.dream\.website$/", $_SERVER['HTTP_HOST'])) {
        $proto = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
        define('WP_SITEURL', $proto . '://' . $_SERVER['HTTP_HOST']);
        define('WP_HOME',    $proto . '://' . $_SERVER['HTTP_HOST']);
        define('JETPACK_STAGING_MODE', true);
}


/**
 * Set memory limit on shared
 */
define( 'WP_MEMORY_LIMIT', '512M' );
define( 'WP_MAX_MEMORY_LIMIT', '512M' );


/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
