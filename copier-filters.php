<?php

add_action( 'psource_copier-copy-options', 'psource_cloner_set_blog_privacy' );
function psource_cloner_set_blog_privacy( $source_blog_id ) {
	$option = get_option( 'copier-pending', array() );
	if ( isset( $option['blog_public'] ) ) {
		update_option( 'blog_public', $option['blog_public'] );
	}
}