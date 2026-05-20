<?php
/**
 * Plugin Name: Gridly Design Overlay
 * Description: Adds a responsive grid overlay to help check WordPress layouts during development.
 * Version: 1.0.6
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * Author: Rody van de Kar
 * Text Domain: gridly-design-overlay
 * Domain Path: /languages
 * License: GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 *
 * @package GridOverlay
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'WPGO_VERSION', '1.0.6' );
define( 'WPGO_PLUGIN_FILE', __FILE__ );
define( 'WPGO_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'WPGO_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

require_once WPGO_PLUGIN_DIR . 'includes/class-wpgo-settings-page.php';
require_once WPGO_PLUGIN_DIR . 'includes/class-wpgo-overlay.php';

WPGO_Settings_Page::init();
WPGO_Overlay::init();
