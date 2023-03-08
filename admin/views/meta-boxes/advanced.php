<p><?php _e( 'Du hast Dich entschieden, den Hauptblog zu klonen. Bitte <strong>wähle die Tabellen aus, die geklont werden sollen</strong>. Beachte dass reine Netzwerktabellen viel Platz und viel Zeit zum Klonen beanspruchen können.', 'psource-cloner' ); ?></p>
<div id="additional-tables" style="display:none">
	<select id="additional-tables-selector">
		<?php foreach ( $additional_tables as $table ): ?>
			<?php
                $table_name = $table['name'];
                $value = $table['prefix.name'];
            ?>
            <option value="<?php echo $value; ?>"><?php echo $table_name; ?></option>
		<?php endforeach; ?>
	</select>
</div>
<ul id="additional-tables-checkboxes">
	<?php $i = 0; ?>
	<?php foreach ( $additional_tables as $table ): ?>
		<?php if ( $i % 3 == 0 ): ?>
			<br class="clear"/>
		<?php endif; ?>

		<?php
            $table_name = $table['name'];
            $value = $table['prefix.name'];

            $checked = in_array( $value, $additional_tables_previous_selection );
        ?>
        <li>
        	<input type="checkbox" name="additional_tables[]" id="table-<?php echo $value; ?>" <?php checked( $checked ); ?> value="<?php echo $value; ?>" />
        	<label for="table-<?php echo $value; ?>"><?php echo $table_name; ?></label><br/>
        </li>
		<?php $i++; ?>
	<?php endforeach; ?>
</ul>
<div class="clear"></div>

	