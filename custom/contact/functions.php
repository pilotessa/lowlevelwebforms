<?php
// Custom webform setup hook
add_action( 'lw_webform_custom_contact_setup', 'lw_custom_contact_create_form' );
// lowlevelwebform hooks
add_action( 'lw_webform_form_setup', 'lw_custom_contact_form_setup' );
add_action( 'lw_webform_form_validate', 'lw_custom_contact_form_validate' );
add_action( 'lw_webform_form_action', 'lw_custom_contact_form_action' );

// Custom webform setup hook
function lw_custom_contact_create_form( $post_name ) {
	if ( $webforms = get_posts( array( 
		'name' => $post_name, 
		'post_type' => 'lw_webform',
		'posts_per_page' => 1
	) ) ) {
		$webform = $webforms[ 0 ];
		
		update_post_meta( $webform->ID, 'lw_success_page', '' );
		update_post_meta( $webform->ID, 'lw_success_message', __( 'We have received your contact request.', 'lowlevelwebforms' ) );
		
		// Admin email
		update_post_meta( $webform->ID, 'lw_use_admin_email', 1 );
		update_post_meta( $webform->ID, 'lw_admin_email_from', get_option( 'admin_email' ) );
		update_post_meta( $webform->ID, 'lw_admin_email_to', get_option( 'admin_email' ) );
		update_post_meta( $webform->ID, 'lw_admin_email_subject', __( 'Contact' ) );
		update_post_meta( $webform->ID, 'lw_admin_email_body_html', 1 );
		update_post_meta( $webform->ID, 'lw_admin_email_body', '
<p>
<table>
<tr><td>' . __( 'name' ) . '</td><td>{name}</td><td></tr>
<tr><td>' . __( 'email' ) . '</td><td><a mailto="{email}">{email}</a></td><td></tr>
<tr><td>' . __( 'comments' ) . '</td><td>{comments}</td><td></tr>
</table>
</p>
		' );
		update_post_meta( $webformPostId, 'lw_admin_attach_uploads', 0 );
	}
}

// lowlevelwebform hooks
function lw_custom_contact_form_setup( $post ) {
}

function lw_custom_contact_form_validate( $post ) {
	global $wpdb;
	global $user_ID;
	global $de_webform_errors;
	global $de_webform_values;

	$lw_webform_values[ 'name' ] = $wpdb->escape( $_POST[ 'name' ] );
	$lw_webform_values[ 'email' ] = $wpdb->escape( $_POST[ 'email' ] );
	$lw_webform_values[ 'comments' ] = $wpdb->escape( $_POST[ 'comments' ] );
	
	if( empty( $lw_webform_values[ 'name' ] ) ) {
		$lw_webform_errors[ 'name' ] = __( 'Name field is empty.', 'lowlevelwebforms' );
	}
	if( empty( $lw_webform_values[ 'email' ] ) || ! filter_var( $lw_webform_values[ 'email' ], FILTER_VALIDATE_EMAIL ) ) {
		$lw_webform_errors[ 'email' ] = __( 'Email field is empty.', 'lowlevelwebforms' );
	}
}

function lw_custom_contact_form_action( $post ) {
}
