<?php
/**
 * Front-end grid overlay.
 *
 * @package GridOverlay
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Renders the front-end grid overlay.
 */
class WPGO_Overlay {
	/**
	 * Register WordPress hooks.
	 */
	public static function init() {
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ) );
		add_action( 'wp_footer', array( __CLASS__, 'render_overlay' ) );
	}

	/**
	 * Enqueue front-end overlay assets.
	 */
	public static function enqueue_assets() {
		if ( ! self::is_enabled() ) {
			return;
		}

		wp_enqueue_style(
			'wpgo-overlay',
			WPGO_PLUGIN_URL . 'assets/overlay.css',
			array(),
			WPGO_VERSION
		);
	}

	/**
	 * Render the overlay markup.
	 */
	public static function render_overlay() {
		if ( ! self::is_enabled() ) {
			return;
		}

		?>
		<div class="wpgo-overlay" style="<?php echo esc_attr( self::get_style_attribute() ); ?>" aria-hidden="true">
			<div class="wpgo-overlay__grid"></div>
		</div>
		<?php
	}

	/**
	 * Determine whether the overlay should render.
	 *
	 * @return bool
	 */
	private static function is_enabled() {
		$settings = WPGO_Settings_Page::get_settings();

		return ! empty( $settings['enabled'] );
	}

	/**
	 * Build the CSS custom properties for the overlay.
	 *
	 * @return string
	 */
	private static function get_style_attribute() {
		$settings = WPGO_Settings_Page::get_settings();
		$rgb      = self::hex_to_rgb( $settings['overlay_color'] );
		$opacity  = min( 100, absint( $settings['overlay_opacity'] ) ) / 100;

		$properties = array(
			'--wpgo-overlay-fill'      => sprintf( 'rgba(%d, %d, %d, %.2F)', $rgb['red'], $rgb['green'], $rgb['blue'], $opacity ),
			'--wpgo-desktop-container' => absint( $settings['desktop']['container_width'] ) . 'px',
			'--wpgo-desktop-columns'   => absint( $settings['desktop']['columns'] ),
			'--wpgo-desktop-gap'       => absint( $settings['desktop']['gap'] ) . 'px',
			'--wpgo-desktop-spacing'   => absint( $settings['desktop']['spacing'] ) . 'px',
			'--wpgo-tablet-columns'    => absint( $settings['tablet']['columns'] ),
			'--wpgo-tablet-gap'        => absint( $settings['tablet']['gap'] ) . 'px',
			'--wpgo-tablet-spacing'    => absint( $settings['tablet']['spacing'] ) . 'px',
			'--wpgo-mobile-columns'    => absint( $settings['mobile']['columns'] ),
			'--wpgo-mobile-gap'        => absint( $settings['mobile']['gap'] ) . 'px',
			'--wpgo-mobile-spacing'    => absint( $settings['mobile']['spacing'] ) . 'px',
		);

		$styles = '';

		foreach ( $properties as $name => $value ) {
			$styles .= $name . ':' . $value . ';';
		}

		return $styles;
	}

	/**
	 * Convert a sanitized hex color to RGB.
	 *
	 * @param string $hex_color Hex color value.
	 * @return array
	 */
	private static function hex_to_rgb( $hex_color ) {
		$hex_color = sanitize_hex_color( $hex_color );

		if ( empty( $hex_color ) ) {
			$hex_color = '#ff0000';
		}

		$hex_color = ltrim( $hex_color, '#' );

		if ( 3 === strlen( $hex_color ) ) {
			$hex_color = $hex_color[0] . $hex_color[0] . $hex_color[1] . $hex_color[1] . $hex_color[2] . $hex_color[2];
		}

		return array(
			'red'   => hexdec( substr( $hex_color, 0, 2 ) ),
			'green' => hexdec( substr( $hex_color, 2, 2 ) ),
			'blue'  => hexdec( substr( $hex_color, 4, 2 ) ),
		);
	}
}
