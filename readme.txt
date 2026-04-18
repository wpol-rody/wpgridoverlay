=== Grid Overlay ===
Contributors: rodyvdkar
Tags: development, developer-tools, grid, layout, responsive
Requires at least: 6.0
Tested up to: 6.9
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Adds a responsive grid overlay to help check WordPress layouts during development.

== Description ==

Grid Overlay adds a front-end grid overlay that helps developers and site builders check responsive layout alignment while working on WordPress sites.

The plugin includes a settings page under Settings where administrators can configure:

* Overlay visibility.
* Overlay color.
* Overlay opacity.
* Desktop grid container width.
* Desktop, tablet, and mobile columns.
* Desktop, tablet, and mobile gaps.
* Desktop, tablet, and mobile side spacing.

Tablet settings apply from 480px through 1024px. Mobile settings apply up to 479px.

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/grid-overlay/` directory, or install the plugin through the WordPress plugins screen.
2. Activate the plugin through the Plugins screen in WordPress.
3. Go to Settings > Grid Overlay to configure the overlay.

== Frequently Asked Questions ==

= Who is this plugin for? =

This plugin is intended for developers and site builders who want a simple visual grid overlay while checking responsive layouts.

= Does the overlay affect visitors' ability to click the page? =

No. The overlay uses `pointer-events: none`, so it does not block normal page interaction.

= Can I configure different grid values per device size? =

Yes. The settings page includes separate desktop, tablet, and mobile grid values.

== Changelog ==

= 1.0.0 =

* Initial release.
