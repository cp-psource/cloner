<?php

/**
 * Based on Tom McFarlin's Plugin Boilerplate https://github.com/tommcfarlin/ClassicPress-Plugin-Boilerplate
 */
class PSOURCE_Cloner_Admin_Settings {

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Slug of the plugin screen.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_screen_hook_suffix = null;

	/**
	 * Initialize the plugin by loading admin scripts & styles and adding a
	 * settings page and menu.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {

		$this->plugin_slug = 'cloner';

		// Add the options page and menu item.
		add_action( 'network_admin_menu', array( $this, 'add_plugin_settings_menu' ) );

		// Add an action link pointing to the options page.
		$plugin_basename = plugin_basename( plugin_dir_path( realpath( dirname( __FILE__ ) ) ) . $this->plugin_slug . '.php' );
		add_filter( 'network_admin_plugin_action_links_' . $plugin_basename, array( $this, 'add_action_links' ) );

		if ( ! defined( 'PSOURCE_CLONER_ASSETS_URL' ) )
			define( 'PSOURCE_CLONER_ASSETS_URL', plugin_dir_url( __FILE__ ) . 'assets' );

	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance )
			self::$instance = new self;

		return self::$instance;
	}


	/**
	 * Register the administration menu for this plugin into the ClassicPress Dashboard menu.
	 *
	 * @since    1.0.0
	 */
	public function add_plugin_settings_menu() {

		$this->plugin_screen_hook_suffix = add_submenu_page(
			'settings.php',
			__( 'Cloner-Einstellungen', 'psource-cloner' ),
			__( 'Cloner', 'psource-cloner' ),
			'manage_network',
			$this->plugin_slug,
			array( $this, 'display_plugin_admin_page' )
		);

		add_action( 'load-' . $this->plugin_screen_hook_suffix, array( $this, 'sanitize_settings_form' ) );

	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function display_plugin_admin_page() {
		$to_copy_labels = psource_cloner_get_settings_labels();
		$to_copy_labels = apply_filters( 'psource_cloner_to_copy_labels_settings', $to_copy_labels );

		$settings = psource_cloner_get_settings();

		$errors = get_settings_errors( 'psource_cloner_settings' );

		$updated = false;
		if ( isset( $_GET['updated'] ) )
			$updated = true;

		extract( $settings );
		
		if( ! isset( $to_replace ) ){
			$to_replace = array();
		}

		include_once( 'views/settings.php' );
	}

	/**
	 * Add settings action link to the plugins page.
	 *
	 * @since    1.0.0
	 */
	public function add_action_links( $links ) {
		$menu_page_url = menu_page_url( $this->plugin_slug, false );

		if ( $menu_page_url ) {
			$links = array_merge(
				array(
					'settings' => '<a href="' . network_admin_url( 'settings.php?page=' . $this->plugin_slug ) . '">' . __( 'Einstellungen', 'psource-cloner' ) . '</a>'
				),
				$links
			);	
		}

		return $links;

	}

	public function sanitize_settings_form() {
		if ( empty( $_POST['submit'] ) )
			return;

		check_admin_referer( 'psource_cloner_settings' );

		if ( empty( $_POST['to_copy'] ) ) {
			add_settings_error( 'psource_cloner_settings', 'empty-settings', __( 'Du musst mindestens eine Option aktivieren', 'psource-cloner' ) );
			return;
		}

		$settings = psource_cloner_get_settings();

		$to_copy = array_keys( $_POST['to_copy'] );
		$settings['to_copy'] = $to_copy;
		$settings['to_replace'] = ( isset( $_POST['to_replace'] ) ) ? array_keys( $_POST['to_replace'] ) : array();

		psource_cloner_update_settings( $settings );

		$redirect = add_query_arg( 
			array( 
				'page' => $this->plugin_slug,
				'updated' => 'true'
			),
			network_admin_url( 'settings.php' )
		);

		wp_redirect( $redirect );
		exit();


	}

}
