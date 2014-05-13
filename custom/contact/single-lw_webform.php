<?php
if ( ! empty( $lw_webform_errors ) ) {
	echo '<p>' . implode( '<br />', $lw_webform_errors ) . '</p>';
}
if ( ! empty( $lw_webform_messages ) ) {
	echo '<p>' . implode( '', $lw_webform_messages ) . '</p>';
} else {
?>
		<form method="post">
			<p>
			<label for="name"><em>*</em><?php _e( 'Name', 'lowlevelwebforms' ); ?></label>
			<input id="name" name="name" type="text" class="required" value="<?php echo esc_attr( $lw_webform_values[ 'name' ] ); ?>" />
			</p>
			<p>
			<label for="email"><em>*</em><?php _e( 'Email', 'lowlevelwebforms' ); ?></label>
			<input id="email" name="email" type="email" class="required email" value="<?php echo esc_attr( $lw_webform_values[ 'email' ] ); ?>" />
			</p>
			<p>
			<label for="comments"><em>&nbsp;</em><?php _e( 'Comments', 'lowlevelwebforms' ); ?></label>
			<textarea id="comments" name="comments"><?php echo esc_textarea( $de_webform_values[ 'comments' ] ); ?></textarea>
			</p>
			<p><em>*</em><span class="legend"><?php _e( 'Required field', 'lowlevelwebforms' ); ?></span>
			</p>
			<p>
			<input id="submit" name="submit" value="<?php _e( 'Send', 'lowlevelwebforms' ); ?>" type="submit">
			</p>
		</form>
<?php
}
