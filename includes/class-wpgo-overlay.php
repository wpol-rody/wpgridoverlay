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
		add_action( 'admin_bar_menu', array( __CLASS__, 'add_admin_bar_toggle' ), 100 );
		add_action( 'admin_post_wpgo_toggle_overlay', array( __CLASS__, 'toggle_overlay' ) );
		add_action( 'wp_ajax_wpgo_toggle_overlay', array( __CLASS__, 'ajax_toggle_overlay' ) );
	}

	/**
	 * Enqueue front-end overlay assets.
	 */
	public static function enqueue_assets() {
		if ( ! self::is_enabled() && ! self::can_toggle_from_admin_bar() ) {
			return;
		}

		wp_enqueue_style(
			'wpgo-overlay',
			WPGO_PLUGIN_URL . 'assets/overlay.css',
			array(),
			WPGO_VERSION
		);

		if ( ! self::can_toggle_from_admin_bar() ) {
			return;
		}

		$is_enabled = self::is_enabled();

		wp_enqueue_script(
			'wpgo-overlay-toggle',
			WPGO_PLUGIN_URL . 'assets/overlay-toggle.js',
			array(),
			WPGO_VERSION,
			true
		);

		wp_localize_script(
			'wpgo-overlay-toggle',
			'wpgoOverlayToggle',
			array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'wpgo_toggle_overlay' ),
				'enabled' => $is_enabled,
				'labels'  => self::get_toggle_labels( $is_enabled ),
			)
		);
	}

	/**
	 * Render the overlay markup.
	 */
	public static function render_overlay() {
		if ( ! self::is_enabled() && ! self::can_toggle_from_admin_bar() ) {
			return;
		}

		$class_name = self::is_enabled() ? 'wpgo-overlay' : 'wpgo-overlay wpgo-overlay--hidden';

		?>
		<div class="<?php echo esc_attr( $class_name ); ?>" style="<?php echo esc_attr( self::get_style_attribute() ); ?>" aria-hidden="true">
			<div class="wpgo-overlay__grid"></div>
		</div>
		<?php
	}

	/**
	 * Add a front-end admin bar item to toggle the overlay.
	 *
	 * @param WP_Admin_Bar $wp_admin_bar Admin bar instance.
	 */
	public static function add_admin_bar_toggle( $wp_admin_bar ) {
		if ( is_admin() || ! is_admin_bar_showing() || ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$is_enabled  = self::is_enabled();
		$labels      = self::get_toggle_labels( $is_enabled );
		$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '/';
		$redirect    = home_url( $request_uri );

		$url = wp_nonce_url(
			add_query_arg(
				array(
					'action'   => 'wpgo_toggle_overlay',
					'redirect' => rawurlencode( $redirect ),
				),
				admin_url( 'admin-post.php' )
			),
			'wpgo_toggle_overlay'
		);

		$wp_admin_bar->add_node(
			array(
				'id'    => 'wpgo-toggle-overlay',
				'title' => $labels['title'],
				'href'  => $url,
				'meta'  => array(
					'title' => $labels['metaTitle'],
				),
			)
		);
	}

	/**
	 * Toggle the saved overlay enabled state from the admin bar.
	 */
	public static function toggle_overlay() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to do this.', 'gridly-design-overlay' ) );
		}

		check_admin_referer( 'wpgo_toggle_overlay' );

		self::toggle_saved_overlay();

		$redirect = isset( $_GET['redirect'] )
			? esc_url_raw( rawurldecode( wp_unslash( $_GET['redirect'] ) ) )
			: home_url( '/' );

		wp_safe_redirect( $redirect );
		exit;
	}

	/**
	 * Toggle the saved overlay enabled state through AJAX.
	 */
	public static function ajax_toggle_overlay() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error(
				array(
					'message' => __( 'You do not have permission to do this.', 'gridly-design-overlay' ),
				),
				403
			);
		}

		check_ajax_referer( 'wpgo_toggle_overlay', 'nonce' );

		$is_enabled = self::toggle_saved_overlay();

		wp_send_json_success(
			array(
				'enabled' => $is_enabled,
				'labels'  => self::get_toggle_labels( $is_enabled ),
			)
		);
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
	 * Determine whether the current request can toggle the overlay from the admin bar.
	 *
	 * @return bool
	 */
	private static function can_toggle_from_admin_bar() {
		return ! is_admin() && is_admin_bar_showing() && current_user_can( 'manage_options' );
	}

	/**
	 * Toggle the saved overlay state.
	 *
	 * @return bool
	 */
	private static function toggle_saved_overlay() {
		$settings            = WPGO_Settings_Page::get_settings();
		$settings['enabled'] = empty( $settings['enabled'] ) ? 1 : 0;

		update_option( WPGO_Settings_Page::OPTION_NAME, $settings );

		return ! empty( $settings['enabled'] );
	}

	/**
	 * Get the admin bar labels for the current overlay state.
	 *
	 * @param bool $is_enabled Whether the overlay is enabled.
	 * @return array
	 */
	private static function get_toggle_labels( $is_enabled ) {
		return array(
			'title'     => $is_enabled ? __( 'Grid uitzetten', 'gridly-design-overlay' ) : __( 'Grid aanzetten', 'gridly-design-overlay' ),
			'metaTitle' => $is_enabled ? __( 'Grid overlay uitzetten', 'gridly-design-overlay' ) : __( 'Grid overlay aanzetten', 'gridly-design-overlay' ),
		);
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
