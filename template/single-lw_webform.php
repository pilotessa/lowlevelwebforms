<?php
/**
 * Form Template: {title}
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
			<input type="submit" />
			</p>
		</form>
<?php
}
