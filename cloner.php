<?php
/*
Plugin Name: Cloner
Plugin URI: https://n3rds.work/piestingtal_source/cloner-klone-inhalte-in-multisite-per-mausklick/
Description: Klone Webseiten in einer Multisite-Netzwerkinstallation
Author: WMS N@W
Author URI: https://n3rds.work
Version: 1.8.4
Network: true
Text Domain: psource-cloner
Domain Path: lang
*/

/*
Copyright 2016-2021 WMS N@W (https://n3rds.work)
Author – DerN3rd


This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License (Version 2 – GPLv2) as published by
the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
*/

require 'psource/psource-plugin-update/plugin-update-checker.php';
$MyUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
	'https://n3rds.work//wp-update-server/?action=get_metadata&slug=cloner', 
	__FILE__, 
	'cloner' 
);

class PSOURCE_Cloner {

	private static $instance = null;

	public static function get_instance() {
		if ( ! self::$instance )
			self::$instance = new self();

		return self::$instance;
	}

	public function __construct() {
		$this->set_constants();
		$this->includes();

		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );
		add_action( 'init', array( $this, 'init_plugin' ) );
		add_action( 'init', array( $this, 'maybe_upgrade' ) );

		add_action( 'network_admin_notices', array( $this, 'display_installation_admin_notice' ) );

		add_filter( 'copier_set_copier_args', array( $this, 'set_copier_args' ), 10, 3 );

		add_action( 'admin_bar_menu', array( $this, 'add_admin_bar_link' ), 40 );

		if ( is_network_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {
			require_once( PSOURCE_CLONER_PLUGIN_DIR . 'admin/cloner-admin-settings.php' );
			add_action( 'plugins_loaded', array( 'PSOURCE_Cloner_Admin_Settings', 'get_instance' ) );

			require_once( PSOURCE_CLONER_PLUGIN_DIR . 'admin/cloner-admin-clone-site.php' );
			add_action( 'plugins_loaded', array( 'PSOURCE_Cloner_Admin_Clone_Site', 'get_instance' ) );
		}

		if ( defined( 'DOING_AJAX' ) && DOING_AJAX )
			require_once( PSOURCE_CLONER_PLUGIN_DIR . 'admin/ajax.php' );

	}

	

	private function set_constants() {
		if ( ! defined( 'PSOURCE_CLONER_PLUGIN_DIR' ) )
			define( 'PSOURCE_CLONER_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

		if ( ! defined( 'PSOURCE_CLONER_PLUGIN_URL' ) )
			define( 'PSOURCE_CLONER_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

		if ( ! defined( 'PSOURCE_CLONER_LANG_DOMAIN' ) )
			define( 'PSOURCE_CLONER_LANG_DOMAIN', 'psource-cloner' );

		 //Define the same language domain for the copier classes.
		if ( ! defined( 'PSOURCE_COPIER_LANG_DOMAIN' ) )
			define( 'PSOURCE_COPIER_LANG_DOMAIN', 'psource-cloner' );

		if ( ! defined( 'PSOURCE_CLONER_VERSION' ) )
			define( 'PSOURCE_CLONER_VERSION', '1.8.4' );
	}

	private function includes() {
		include_once( PSOURCE_CLONER_PLUGIN_DIR . 'integration/integration.php' );
		include_once( PSOURCE_CLONER_PLUGIN_DIR . 'copier/copier.php' );
		include_once( PSOURCE_CLONER_PLUGIN_DIR . 'copier-filters.php' );
		include_once( PSOURCE_CLONER_PLUGIN_DIR . 'helpers/general.php' );
		include_once( PSOURCE_CLONER_PLUGIN_DIR . 'helpers/settings.php' );
	}

	public function load_plugin_textdomain() {
		$domain = PSOURCE_CLONER_LANG_DOMAIN;
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, false, basename( PSOURCE_CLONER_PLUGIN_DIR ) . '/lang/' );
	}

	public function init_plugin() {
		if ( is_network_admin() && isset( $_GET['cloner_dismiss_install_notice'] ) ) {
			update_site_option( 'psource_cloner_installation_notice_done', true );
		}
	}

	public function display_installation_admin_notice() {

		if ( is_super_admin() && ! get_site_option( 'psource_cloner_installation_notice_done' ) ) {
			$dismiss_url = add_query_arg( 'cloner_dismiss_install_notice', 'true' );
			?>
				<div class="updated">
					<p class="alignleft"><?php printf( __( 'Cloner wurde erfolgreich installiert und kann in <a href="%s">Einstellungen &raquo; Cloner</a> konfiguriert werden', 'psource-cloner' ), network_admin_url( 'settings.php?page=cloner' ) ); ?></p>
					<p class="alignright"><a href="<?php echo esc_url( $dismiss_url ); ?>" class="button-secondary"><?php _e( 'Verwerfen', 'psource-cloner' ); ?></a></p>
					<div class="clear"></div>
				</div>
			<?php
		}
	}


	/**
	 * Remove arguments from copier based on Cloner Settings
	 * 
	 * @param type $args 
	 * @return type
	 */
	public function set_copier_args( $option, $destination_blog_id, $args ) {

		if ( ! empty( $args ) ) {
			// We don't want to mess with New Blog Templates
			return $option;
		}

		$settings = psource_cloner_get_settings();

		$to_copy = $option['to_copy'];
		foreach ( $to_copy as $to_copy_option => $value ) {
			if ( ! in_array( $to_copy_option, $settings['to_copy'] ) && $to_copy_option != 'widgets' && $to_copy_option != 'nav_menu_item' )
				unset( $option['to_copy'][ $to_copy_option ] );
		}

		return $option;
	}


	public function maybe_upgrade() {
		$current_version_saved = get_site_option( 'psource_cloner_version', '1.1' );

		if ( PSOURCE_CLONER_VERSION === $current_version_saved)
			return;

		if ( version_compare( $current_version_saved, '1.2', '<' ) ) {
			$settings = psource_cloner_get_settings();
			$settings['to_copy'][] = 'cpts';
			psource_cloner_update_settings( $settings );
		}

		update_site_option( 'psource_cloner_version', PSOURCE_CLONER_VERSION );
	}

	/**
	 * Add a "Clone Site" link in admin bar for Super Admins
	 */
	public function add_admin_bar_link() {
		global $wp_admin_bar;

		if ( ! current_user_can( 'manage_network' ) )
			return;

		if ( is_network_admin() )
			return;

		if ( ! cloner_is_blog_clonable( get_current_blog_id() ) )
			return;

		$url = network_admin_url( 'index.php' );
		$url = add_query_arg(
			array(
				'page' => 'clone_site',
				'blog_id' => get_current_blog_id()
			),
			$url
		);

		$wp_admin_bar->add_menu( array(
			'parent' => 'site-name',
			'id'     => 'clone-site',
			'title'  => __( 'Webseite klonen', 'psource-cloner' ),
			'href'   => $url,
		) );
	}

}

function psource_cloner() {
	return PSOURCE_Cloner::get_instance();
}

psource_cloner();
