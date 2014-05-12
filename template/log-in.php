<?php
/**
 * Form Template: Log in
 */

if ( ! empty( $lw_webform_errors ) ) {
	echo '<p>' . implode( '<br />', $lw_webform_errors ) . '</p>';
}
if ( ! empty( $lw_webform_messages ) ) {
	echo '<p>' . implode( '', $lw_webform_messages ) . '</p>';
} else {
?>
		<form method="post">
			<p>
			<label for="email"><em>&nbsp;</em><?php _e( 'Email', 'direct-edit' ); ?></label>
			<input id="email" name="email" type="email" class="required email" value="<?php echo esc_attr( $lw_webform_values[ 'email' ] ); ?>" />
			</p>
			<p>
			<label for="password"><em>&nbsp;</em><?php _e( 'Password', 'direct-edit' ); ?></label>
			<input id="password" name="password" type="password" class="required" />
			</p>
			<p>
			<input id="send" name="send" value="send" type="submit" />
			</p>
		</form>
<?php
}
