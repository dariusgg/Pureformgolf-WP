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

// BEGIN EDIT: Increase memory_limit for woo commerce - Kevin Kirchner ---------------------- //
// @see: http://docs.woothemes.com/document/increasing-the-wordpress-memory-limit/
define( 'WP_MEMORY_LIMIT', '256M' );
define( 'WP_MAX_MEMORY_LIMIT', '512M' );
// END EDIT --------------------------------------------------------------------------------- //

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'pure2335090550');

/** MySQL database username */
define('DB_USER', 'pure2335090550');

/** MySQL database password */
define('DB_PASSWORD', 'ltzBZ/7M/!,7j');

/** MySQL hostname */
define('DB_HOST', 'pureform-dev.cckuhc4fpfcy.ap-southeast-2.rds.amazonaws.com');

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
define('AUTH_KEY',         '4Ph8wrtZwUKbIjY790V7');
define('SECURE_AUTH_KEY',  '2XHv9Fk&7ntbBb3TmsEv');
define('LOGGED_IN_KEY',    'B-BTP=cGKA7mT-5x%/1x');
define('NONCE_KEY',        'TXQ(vJ6NO&M2QkzH+XdI');
define('AUTH_SALT',        'WXJBh9sbs56R+MQpUD8J');
define('SECURE_AUTH_SALT', '-Qd_YpLc#4tI&DG*9FFM');
define('LOGGED_IN_SALT',   '3*NA(4&snH  VjhwtyMr');
define('NONCE_SALT',       'z@/@gS+fX)HD5MLtE$c%');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_57a3gztr93_';
define( 'FORCE_SSL_LOGIN', 1 );
define( 'FORCE_SSL_ADMIN', 1 );

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);
//define( 'WP_CACHE', true );
require_once( dirname( __FILE__ ) . '/gd-config.php' );
define( 'FS_METHOD', 'direct');
define('FS_CHMOD_DIR', (0705 & ~ umask()));
define('FS_CHMOD_FILE', (0604 & ~ umask()));


/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
