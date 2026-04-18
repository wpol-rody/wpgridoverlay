<?php
/**
 * Admin settings page.
 *
 * @package GridOverlay
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers and renders the plugin settings page.
 */
class WPGO_Settings_Page {
	/**
	 * Option name used by the Settings API.
	 *
	 * @var string
	 */
	const OPTION_NAME = 'wpgo_settings';

	/**
	 * Settings group name.
	 *
	 * @var string
	 */
	const OPTION_GROUP = 'wpgo_settings_group';

	/**
	 * Register WordPress hooks.
	 */
	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'add_options_page' ) );
		add_action( 'admin_init', array( __CLASS__, 'register_settings' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_admin_assets' ) );
	}

	/**
	 * Add the settings page below Settings.
	 */
	public static function add_options_page() {
		add_options_page(
			__( 'Grid Overlay', 'grid-overlay' ),
			__( 'Grid Overlay', 'grid-overlay' ),
			'manage_options',
			'grid-overlay',
			array( __CLASS__, 'render_page' )
		);
	}

	/**
	 * Enqueue admin assets only on the plugin settings page.
	 *
	 * @param string $hook_suffix Current admin page hook suffix.
	 */
	public static function enqueue_admin_assets( $hook_suffix ) {
		if ( 'settings_page_grid-overlay' !== $hook_suffix ) {
			return;
		}

		wp_enqueue_style(
			'wpgo-admin',
			WPGO_PLUGIN_URL . 'assets/admin.css',
			array(),
			WPGO_VERSION
		);
	}

	/**
	 * Register settings, sections, and fields.
	 */
	public static function register_settings() {
		register_setting(
			self::OPTION_GROUP,
			self::OPTION_NAME,
			array(
				'type'              => 'array',
				'sanitize_callback' => array( __CLASS__, 'sanitize_settings' ),
				'default'           => self::get_default_settings(),
			)
		);

		add_settings_section(
			'wpgo_overlay_section',
			__( 'Overlay settings', 'grid-overlay' ),
			array( __CLASS__, 'render_section_description' ),
			'grid-overlay'
		);

		add_settings_field(
			'enabled',
			__( 'Enable overlay', 'grid-overlay' ),
			array( __CLASS__, 'render_enabled_field' ),
			'grid-overlay',
			'wpgo_overlay_section'
		);

		add_settings_field(
			'overlay_color',
			__( 'Overlay color', 'grid-overlay' ),
			array( __CLASS__, 'render_overlay_color_field' ),
			'grid-overlay',
			'wpgo_overlay_section'
		);

		add_settings_field(
			'overlay_opacity',
			__( 'Overlay opacity', 'grid-overlay' ),
			array( __CLASS__, 'render_overlay_opacity_field' ),
			'grid-overlay',
			'wpgo_overlay_section'
		);

		foreach ( self::get_groups() as $group_key => $group_label ) {
			$section_id = 'wpgo_' . $group_key . '_section';

			add_settings_section(
				$section_id,
				esc_html( $group_label ),
				'__return_empty_string',
				'grid-overlay'
			);

			foreach ( self::get_number_fields( $group_key ) as $field_key => $field ) {
				add_settings_field(
					$group_key . '_' . $field_key,
					esc_html( $field['label'] ),
					array( __CLASS__, 'render_number_field' ),
					'grid-overlay',
					$section_id,
					array(
						'group_key' => $group_key,
						'field_key' => $field_key,
						'field'     => $field,
					)
				);
			}
		}
	}

	/**
	 * Sanitize settings before saving.
	 *
	 * @param array $input Raw option input.
	 * @return array
	 */
	public static function sanitize_settings( $input ) {
		$input  = is_array( $input ) ? $input : array();
		$output = self::get_default_settings();

		$output['enabled'] = ! empty( $input['enabled'] ) ? 1 : 0;
		$overlay_color     = isset( $input['overlay_color'] ) ? sanitize_hex_color( $input['overlay_color'] ) : '';

		if ( ! empty( $overlay_color ) ) {
			$output['overlay_color'] = $overlay_color;
		}

		$output['overlay_opacity'] = isset( $input['overlay_opacity'] ) ? absint( $input['overlay_opacity'] ) : $output['overlay_opacity'];

		if ( $output['overlay_opacity'] > 100 ) {
			$output['overlay_opacity'] = 100;
		}

		foreach ( self::get_groups() as $group_key => $group_label ) {
			foreach ( self::get_number_fields( $group_key ) as $field_key => $field ) {
				$value = isset( $input[ $group_key ][ $field_key ] ) ? absint( $input[ $group_key ][ $field_key ] ) : $output[ $group_key ][ $field_key ];

				if ( $value < $field['min'] ) {
					$value = $field['min'];
				}

				$output[ $group_key ][ $field_key ] = $value;
			}
		}

		return $output;
	}

	/**
	 * Render the settings page.
	 */
	public static function render_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to access this page.', 'grid-overlay' ) );
		}
		?>
		<div class="wrap wpgo-settings">
			<h1><?php echo esc_html__( 'Grid Overlay', 'grid-overlay' ); ?></h1>
			<form action="options.php" method="post">
				<?php
				settings_fields( self::OPTION_GROUP );
				do_settings_sections( 'grid-overlay' );
				submit_button( __( 'Save settings', 'grid-overlay' ) );
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * Render the settings section description.
	 */
	public static function render_section_description() {
		echo '<p>' . esc_html__( 'Configure the development grid overlay behavior.', 'grid-overlay' ) . '</p>';
	}

	/**
	 * Render the enabled checkbox field.
	 */
	public static function render_enabled_field() {
		$settings = self::get_settings();
		?>
		<label for="wpgo-enabled">
			<input
				type="checkbox"
				id="wpgo-enabled"
				name="<?php echo esc_attr( self::OPTION_NAME ); ?>[enabled]"
				value="1"
				<?php checked( 1, $settings['enabled'] ); ?>
			/>
			<?php echo esc_html__( 'Enable the grid overlay.', 'grid-overlay' ); ?>
		</label>
		<?php
	}

	/**
	 * Render the overlay color field.
	 */
	public static function render_overlay_color_field() {
		$settings = self::get_settings();
		?>
		<input
			type="color"
			id="wpgo-overlay-color"
			name="<?php echo esc_attr( self::OPTION_NAME ); ?>[overlay_color]"
			value="<?php echo esc_attr( $settings['overlay_color'] ); ?>"
		/>
		<?php
	}

	/**
	 * Render the overlay opacity field.
	 */
	public static function render_overlay_opacity_field() {
		$settings = self::get_settings();
		?>
		<input
			type="number"
			id="wpgo-overlay-opacity"
			name="<?php echo esc_attr( self::OPTION_NAME ); ?>[overlay_opacity]"
			value="<?php echo esc_attr( $settings['overlay_opacity'] ); ?>"
			min="0"
			max="100"
			step="1"
		/>
		<span><?php echo esc_html__( '%', 'grid-overlay' ); ?></span>
		<?php
	}

	/**
	 * Render a number setting field.
	 *
	 * @param array $args Field arguments from the Settings API.
	 */
	public static function render_number_field( $args ) {
		$settings  = self::get_settings();
		$group_key = sanitize_key( $args['group_key'] );
		$field_key = sanitize_key( $args['field_key'] );
		$field     = $args['field'];
		$field_id  = 'wpgo-' . $group_key . '-' . $field_key;
		$value     = isset( $settings[ $group_key ][ $field_key ] ) ? absint( $settings[ $group_key ][ $field_key ] ) : 0;
		?>
		<input
			type="number"
			id="<?php echo esc_attr( $field_id ); ?>"
			name="<?php echo esc_attr( self::OPTION_NAME ); ?>[<?php echo esc_attr( $group_key ); ?>][<?php echo esc_attr( $field_key ); ?>]"
			value="<?php echo esc_attr( $value ); ?>"
			min="<?php echo esc_attr( $field['min'] ); ?>"
			step="<?php echo esc_attr( $field['step'] ); ?>"
		/>
		<?php
	}

	/**
	 * Get saved settings merged with defaults.
	 *
	 * @return array
	 */
	public static function get_settings() {
		$settings = get_option( self::OPTION_NAME, array() );
		$settings = is_array( $settings ) ? $settings : array();
		$settings = array_replace_recursive( self::get_default_settings(), $settings );

		if ( empty( $settings['desktop']['container_width'] ) ) {
			$settings['desktop']['container_width'] = self::get_desktop_container_width_default();
		}

		return $settings;
	}

	/**
	 * Get default settings.
	 *
	 * @return array
	 */
	private static function get_default_settings() {
		$settings = array(
			'enabled'         => 0,
			'overlay_color'   => '#ff0000',
			'overlay_opacity' => 30,
		);

		foreach ( self::get_groups() as $group_key => $group_label ) {
			$settings[ $group_key ] = array();

			foreach ( self::get_number_fields( $group_key ) as $field_key => $field ) {
				$settings[ $group_key ][ $field_key ] = $field['min'];
			}
		}

		$settings['desktop']['container_width'] = self::get_desktop_container_width_default();

		return $settings;
	}

	/**
	 * Get the default desktop container width.
	 *
	 * @return int
	 */
	private static function get_desktop_container_width_default() {
		return 1170;
	}

	/**
	 * Get responsive setting groups.
	 *
	 * @return array
	 */
	private static function get_groups() {
		return array(
			'desktop' => __( 'Desktop', 'grid-overlay' ),
			'tablet'  => __( 'Tablet', 'grid-overlay' ),
			'mobile'  => __( 'Mobile', 'grid-overlay' ),
		);
	}

	/**
	 * Get number fields shown for each responsive group.
	 *
	 * @param string $group_key Responsive group key.
	 * @return array
	 */
	private static function get_number_fields( $group_key ) {
		$fields = array(
			'columns' => array(
				'label' => __( 'Aantal banen', 'grid-overlay' ),
				'min'   => 1,
				'step'  => 1,
			),
			'gap'     => array(
				'label' => __( 'Gap', 'grid-overlay' ),
				'min'   => 0,
				'step'  => 1,
			),
			'spacing' => array(
				'label' => __( 'Spacing zijkanten', 'grid-overlay' ),
				'min'   => 0,
				'step'  => 1,
			),
		);

		if ( 'desktop' === $group_key ) {
			return array_merge(
				array(
					'container_width' => array(
						'label' => __( 'Breedte container', 'grid-overlay' ),
						'min'   => 0,
						'step'  => 1,
					),
				),
				$fields
			);
		}

		return $fields;
	}
}
