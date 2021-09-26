<?php

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) )
	exit ();

delete_site_option( 'psource_cloner_installation_notice_done' );
delete_site_option( 'psource_cloner_settings' );
delete_site_option( 'cloner_main_site_tables_selected' );
