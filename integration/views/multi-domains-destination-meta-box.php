<?php
global $multi_dm;

$domains = get_site_option( 'md_domains' );
$is_subdomain_install = is_subdomain_install();

$the_domain = '';
if ( count( $domains ) > 1 ) {
    $primary = wp_list_filter( $domains, array( 'domain_name' => DOMAIN_CURRENT_SITE ) );
    $else = wp_list_filter( $domains, array( 'domain_name' => DOMAIN_CURRENT_SITE ), 'NOT' );

    $domains = array_merge( $primary, $else );

    $super_admin = is_super_admin();
    $show_restricted_domains = $multi_dm->show_restricted_domains();
    $posted_domain = isset( $_POST['domain'] ) ? $_POST['domain'] : '';

    $the_domain = '<select id="domain" name="domain">';
    foreach ( $domains as $_domain ) {
        if ( $super_admin || ( $_domain['domain_status'] == 'restricted' && $show_restricted_domains ) || $_domain['domain_status'] != 'private' ) {
            $title = $is_subdomain_install ? '.' . $_domain['domain_name'] : $_domain['domain_name'] . '/';
            $the_domain .= '<option value="' . $_domain['domain_name'] . '" ' . selected( $_domain['domain_name'], $posted_domain, false ) . '>' . $title . '</option>';
        }
    }
    $the_domain .= '</select>';
} else {
    $the_domain = '<span class="cloner-subdomain">' . $domains[0]['domain_name'] . '<input type="hidden" name="domain" value="' . $domains[0]['domain_name'] . '"></span>';
}

?>

<div class="cloner-clone-option" id="cloner-create-wrap">

    <p>
        <label for="cloner-create">
            <input type="radio" name="cloner-clone-selection" value="create_md" id="cloner-create" class="clone_clone_option"/>
            <?php _e( 'Erstelle eine neue Webseite', 'psource-cloner' ); ?>
        </label>
    </p>

    <?php if ( ! $is_subdomain_install ): ?>
        <?php echo $the_domain; ?><br/>
    <?php endif; ?>

    <input id="blog_create" name="blog_create" type="text" class="regular-text" title="<?php esc_attr_e( 'Domain' ) ?>" placeholder="<?php echo esc_attr( __( 'Gib hier Deinen Webseiten-Namen ein...', 'psource-cloner' ) ); ?>"/><br/>

    <?php if ( $is_subdomain_install ): ?>
        <?php echo $the_domain; ?>
    <?php endif; ?>
    <p class="description"><?php _e( 'Nur Kleinbuchstaben (a-z) und Zahlen erlaubt.' ); ?></p>


</div>

<div class="cloner-clone-option" id="cloner-replace-wrap">
    <p>
        <label for="cloner-replace">
            <input type="radio" name="cloner-clone-selection" value="replace" id="cloner-replace" class="clone_clone_option"/>
            <?php _e( 'Ersetze vorhandene Webseite', 'psource-cloner' ); ?>
        </label>
    </p>
    <?php if ( ! is_subdomain_install() ): ?>
        <br/>
    <?php endif; ?>
    <input name="blog_replace_autocomplete" type="text" class="regular-text ui-autocomplete-input" title="<?php esc_attr_e( 'Domain' ) ?>" placeholder="<?php echo esc_attr( __( 'Beginne mit dem Schreiben, um eine vorhandene Webseite zu suchen' ) ); ?>"/><br/>
    <span class="description"><?php _e( 'Leer lassen, um zur Hauptseite zu klonen', 'psource-cloner' ); ?></span>
    <input name="blog_replace" type="hidden" value=""/>
</div>
<div class="clear"></div>

<?php do_action( 'psource_cloner_destination_meta_box' );
