<div class="cloner-clone-option" id="cloner-create-wrap">
	<p>
		<label for="cloner-create">
			<input type="radio" name="cloner-clone-selection" value="create" id="cloner-create" class="clone_clone_option"/> 
			<?php _e( 'Erstelle eine neue Webseite', 'psource-cloner' ); ?>
		</label>
	</p>
	<?php if ( is_subdomain_install() ): ?>
		<input id="blog_create" name="blog_create" type="text" class="regular-text" title="<?php esc_attr_e( 'Domain' ) ?>" placeholder="<?php echo esc_attr( __( 'Gib hier Deinen Webseiten-Namen ein...', 'psource-cloner' ) ); ?>"/><br/>
		<span class="no-break">.<?php echo preg_replace( '|^www\.|', '', $current_site->domain ); ?></span>
	<?php else: ?>
		<?php echo $current_site->domain . $current_site->path ?><br/>
		<input id="blog_create" name="blog_create" class="regular-text" type="text" title="<?php esc_attr_e( 'Domain' ) ?>" placeholder="<?php echo esc_attr( __( 'Gib hier Deinen Webseiten-Namen ein...', 'psource-cloner' ) ); ?>"/>
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

<?php do_action( 'psource_cloner_destination_meta_box' ); ?>