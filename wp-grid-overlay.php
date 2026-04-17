<?php
/**
 * Plugin Name: WP Grid Overlay
 * Plugin URI: https://example.com/wp-grid-overlay
 * Description: Adds a development grid overlay for checking WordPress layouts.
 * Version: 0.1.0
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * Author: Rody
 * Author URI: https://example.com
 * Text Domain: wp-grid-overlay
 * Domain Path: /languages
 * License: GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 *
 * @package WPGridOverlay
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'WPGO_VERSION', '0.1.0' );
define( 'WPGO_PLUGIN_FILE', __FILE__ );
define( 'WPGO_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'WPGO_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/**
 * Load the plugin text domain.
 */
function wpgo_load_textdomain() {
	load_plugin_textdomain(
		'wp-grid-overlay',
		false,
		dirname( plugin_basename( WPGO_PLUGIN_FILE ) ) . '/languages'
	);
}
add_action( 'plugins_loaded', 'wpgo_load_textdomain' );
