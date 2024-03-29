<?php

add_action( 'psource_cloner_clone_site_screen', 'cloner_multi_domains_tweak_destination_meta_box' );
function cloner_multi_domains_tweak_destination_meta_box() {
    if ( ! class_exists( 'multi_domain' ) )
        return;
    remove_meta_box( 'cloner-destination', 'cloner', 'normal' );
    add_meta_box( 'cloner-destination', __( 'Ziel', 'psource-cloner'), 'cloner_multi_domains_destination_meta_box', 'cloner', 'normal' );
}

function cloner_multi_domains_destination_meta_box() {
    include_once( PSOURCE_CLONER_PLUGIN_DIR . 'integration/views/multi-domains-destination-meta-box.php' );
}

add_filter( 'psource_cloner_pre_clone_actions_switch_default', 'cloner_multi_domains_process_clone_site_form', 10, 6 );

/**
 * @param $result
 * @param $selection
 * @param $blog_title_selection
 * @param $new_blog_title
 * @param $blog_id
 * @param $blog_details
 * @return array|bool|WP_Error
 */
function cloner_multi_domains_process_clone_site_form( $result, $selection, $blog_title_selection, $new_blog_title, $blog_id, $blog_details ) {
    global $wpdb, $current_site;
    if ( $selection === 'create_md' ) {
        // Checking if Der Blog existiert bereits
        // Sanitize the domain/subfolder
        $blog = ! empty( $_REQUEST['blog_create'] ) ? $_REQUEST['blog_create'] : false;

        if ( ! $blog ) {
            return new WP_Error( 'source_blog_not_exist', __( 'Bitte gib einen Webseiten-Namen ein', 'psource-cloner' ) );
        }

        $domain = $_REQUEST['domain'];

        if ( empty( $domain ) ) {
            return new WP_Error( 'source_blog_not_exist', __( 'Bitte gib einen Webseiten-Namen ein', 'psource-cloner' ) );
        }

        $all_domains = get_site_option( 'md_domains' );
        $search_domain_results = wp_list_filter( $all_domains, array( 'domain_name' => $domain ) );

        if ( empty( $search_domain_results ) ) {
            return new WP_Error( 'source_blog_not_exist', __( 'Fehlende oder ungültige Webseiten-Adresse.', 'psource-cloner' ) );
        }


        $subdomain = '';
        if ( preg_match( '|^([a-zA-Z0-9-])+$|', $blog ) )
            $subdomain = strtolower( $blog );

        if ( empty( $subdomain ) ) {
            return new WP_Error( 'source_blog_not_exist', __( 'Fehlende oder ungültige Webseiten-Adresse.', 'psource-cloner' ) );
        }

        $full_address = '';

        // Check if the blog exists
        if ( is_subdomain_install() ) {
            $new_domain = $subdomain . '.' . $domain;
            $new_path = '';
            $blog_exists = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $wpdb->blogs WHERE domain LIKE %s", '%' . $new_domain . '%' ) );
        }
        else {
            $new_domain = $domain;
            $new_path = $current_site->path . trailingslashit( $subdomain );
            $blog_exists = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $wpdb->blogs WHERE domain LIKE %s AND path = %s", '%' . $new_domain . '%', $new_path . '/' ) );
        }

        if ( ! empty( $blog_exists ) ) {
            return new WP_Error( 'blog_already_exists', __( 'Der Blog existiert bereits', 'psource-cloner' ) );
        }


        if ( 'clone' == $blog_title_selection ) {
            $new_blog_title = $blog_details->blogname;
        }


        return array(
            'new_blog_title' => $new_blog_title,
            'new_domain' => $new_domain,
            'new_path' => $new_path
        );
    }

    return false;
}

if ( ! is_subdomain_install() ) {
    add_filter( 'cloner_autocomplete_sites', 'cloner_multi_domains_autocomplete_sites' );
    function cloner_multi_domains_autocomplete_sites( $sites ) {
        global $wpdb;
        foreach ( $sites as $key => $site ) {
            $domain = $wpdb->get_var( $wpdb->prepare( "SELECT domain FROM $wpdb->blogs WHERE blog_id = %d", $site['blog_id'] ) );
            $sites[ $key ]['blog_name'] = $site['blog_name'] . " ( $domain )";
        }
        return $sites;
    }

}


