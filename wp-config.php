<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'wordpress_db' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', '' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         ':-ig@x)c @RMvpt@PjP]FqH>`)rG{;W7+8W DpKxO>1W=J4_vX82``P&++|c,!#w' );
define( 'SECURE_AUTH_KEY',  '5.@wDM2~y[~XDsWpwF_c2O2*FuqQn{yl!UQC3,zq0R1Uy@ ~]lcRG~q,[E?bm_4z' );
define( 'LOGGED_IN_KEY',    'w0~C_bdQ0_0g je0QE;4}`qgvrY&(Wz({*{[B-~9alI`t|YSkUiw5bu)h4HW8;-w' );
define( 'NONCE_KEY',        '.aeV|4%`@kCHN?h5?4qhj.:Q(3.*I J&AX^t[s@e]OPY^ZRO5xZPq`dnE~7YVqpB' );
define( 'AUTH_SALT',        '@8U8kB&DO3=G;0W#0GfVqhMC]wPNL(^+)TD`Z=l1DV31piZ4w12)hHHsU)%WhSC1' );
define( 'SECURE_AUTH_SALT', '*g,nWR v-+b_=MpaRNfGP!k^B$w|@;6c!(q^Urk1WvY~W4?f!-get`+-Qw,^Xl_>' );
define( 'LOGGED_IN_SALT',   '~g#q1@a3^tv{bGuhpXH~a(`ORtOWVQ]lU3e]v_uk%RftT5Oy5t9v{?9F|:A/#J8{' );
define( 'NONCE_SALT',       'x`2`K!@[_L#_1;HK>G$<az?#[Q^X@ywN/*Qcj<=6h]&FYTsNc1_D^+x5S(onK~pM' );

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Sets up WordPress vars and included files. */
require_once( ABSPATH . 'wp-settings.php' );
