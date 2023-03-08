<h3><?php _e( 'Site Title', 'psource-cloner' ); ?></h3>
<p><input id="cloner-clone-blog-title" type="radio" checked name="cloner_blog_title" value="clone" /> <label for="cloner-clone-blog-title"><?php _e( 'Blog-Titel klonen', 'psource-cloner' ); ?></label></p>
<p><input id="cloner-keep-blog-title" type="radio" name="cloner_blog_title" value="keep" /> <label for="cloner-keep-blog-title"><?php _e( 'Behalte den Zielblogtitel', 'psource-cloner' ); ?></label></p>
<p><input id="cloner-replace-blog-title" type="radio" name="cloner_blog_title" value="replace" /> 
	<label for="cloner-replace-blog-title">
		<?php _e( 'Blog-Titel überschreiben mit', 'psource-cloner' ); ?> 
		<input type="text" name="replace_blog_title" value=""/>
	</label>
	
</p>

<h3><?php _e( 'Suchmaschinensichtbarkeit' ); ?></h3>
<label for="blog_public">
	<input name="cloner_blog_public" type="checkbox" id="blog_public" <?php checked( ! $blog_public ); ?>>
	<?php _e( 'Halte Suchmaschinen davon ab, die geklonte Webseite zu indizieren', 'psource-cloner' ); ?>
</label>

