<?php

/**
 * Helper functions that manages the plugin settings
 */

function psource_cloner_get_settings() {
	$defaults = psource_cloner_get_default_settings();
	$settings = get_site_option( 'psource_cloner_settings' );

	if ( ! $settings )
		$settings = array();

	return apply_filters( 'psource_cloner_settings', wp_parse_args( $settings, $defaults ) );
}

function psource_cloner_update_settings( $new_settings ) {
	$settings = psource_cloner_get_settings();

	$settings = wp_parse_args( $new_settings, $settings );

	$to_copy = $settings['to_copy'];
	// the order is important!
	usort( $to_copy, 'psource_cloner_order_settings_array' );
	$settings['to_copy'] = $to_copy;

	update_site_option( 'psource_cloner_settings', $settings );
}

function psource_cloner_order_settings_array( $a, $b ) {
	$keys_order = array_keys( psource_cloner_get_settings_labels() );

	$a_pos = array_search( $a, $keys_order );
	$b_pos = array_search( $b, $keys_order );

	if ( $a_pos === $b_pos )
		return 0;

	return ( $a_pos < $b_pos ) ? -1 : 1;
	
}



function psource_cloner_get_default_settings() {
	return array( 
		'to_copy' => array(
			'settings',
			'widgets',
			'posts',
			'pages',
			'cpts',
			'terms',
			'menus',
			'users',
			'comments',
			'attachment',
			'tables'
		),
		'to_replace' => array() // Let member decide if he needs to replace urls
	);
}

function psource_cloner_get_settings_labels() {
	return array(
		'settings' => __( 'Einstellungen', 'psource-cloner' ),
        'posts' => __( 'Beiträge', 'psource-cloner' ),
        'pages' => __( 'Seiten', 'psource-cloner' ),
        'cpts' => __( 'Benutzerdefinierte Beitragstypen', 'psource-cloner' ),
        'terms' => __( 'Terms', 'psource-cloner' ),
        'menus' => __( 'Menüs', 'psource-cloner' ),
        'users' => __( 'Benutzer', 'psource-cloner' ),
        'comments' => __( 'Kommentare', 'psource-cloner' ),
        'attachment' => __( 'Anhänge', 'psource-cloner' ),
        'tables' => __( 'Benutzerdefinierte Tabellen', 'psource-cloner' )
	);
}