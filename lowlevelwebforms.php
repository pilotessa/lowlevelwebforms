<?php
/*
Plugin Name: Lowlevel Webforms
Version: 1.0
Author: Carlo Roosen, Elena Mukhina
Author URI: http://carloroosen.com/
*/

define( 'LW_VERSION', '1.0' );
define( 'LW_PATH', plugin_dir_path( __FILE__ ) );
define( 'LW_URL', plugin_dir_url( __FILE__ ) );

// Global form variables
global $lw_webform_id;
global $lw_webform_errors;
global $lw_webform_messages;
global $lw_webform_values;
global $lw_webform_search;
global $lw_webform_replace;
global $lw_webform_use_admin_email;
global $lw_webform_use_user_email;
global $lw_webform_success_page;
global $lw_webform_success_message;
global $lw_webform_admin_attachments;
$lw_webform_admin_attachments = array();
global $lw_webform_user_attachments;
$lw_webform_user_attachments = array();

add_action( 'add_meta_boxes', 'lw_webform_add_template_metabox' );
add_action( 'admin_menu', 'lw_webform_plugin_menu' );
add_action( 'init', 'lw_webform_capabilities' );
add_action( 'init', 'lw_webform_create_post_types', 0 );
add_action( 'login_init', 'lw_webform_login_redirect' );
add_action( 'lw_webform_form_setup', 'lw_webform_setup' );
add_action( 'lw_webform_form_validate', 'lw_webform_validate' );
add_action( 'lw_webform_form_action', 'lw_webform_action' );
add_action( 'save_post', 'lw_webform_save_template', 10, 2 );
add_action( 'template_include', 'lw_webform_process', 20 );

add_filter( 'the_content', 'lw_webform_add_the_content' );

function lw_webform_add_template_metabox() {
	add_meta_box( 'lw_webform_general', __( 'General', 'lowlevelwebforms' ), 'lw_webform_general_metabox', 'lw_webform', 'normal', 'core' );
	add_meta_box( 'lw_webform_email_admin', __( 'Admin Email', 'lowlevelwebforms' ), 'lw_webform_email_admin_metabox', 'lw_webform', 'normal', 'core' );
	add_meta_box( 'lw_webform_email_user', __( 'User Email', 'lowlevelwebforms' ), 'lw_webform_email_user_metabox', 'lw_webform', 'normal', 'core' );
	add_meta_box( 'postparentdiv', __( 'Form Template', 'lowlevelwebforms' ), 'lw_webform_template_metabox', 'lw_webform', 'side', 'core' );
}

function lw_webform_general_metabox( $post ) {
	global $wp_post_types;

	$postId = $post->ID;
	
	$successPage = get_post_meta( $postId, 'lw_success_page', true );
	$successMessage = get_post_meta( $postId, 'lw_success_message', true );
	
	echo '<fieldset>';
	echo '<label for="lw_success_page">' . __( 'Success page', 'lowlevelwebforms' ) . '</label>';
	echo '<br />';
	echo '<input type="text" id="lw_success_page" name="lw_success_page" value="' . esc_attr( $successPage ) . '" size="25" />';
	echo '<br />';
	echo '<br />';
	_e( 'or', 'lowlevelwebforms' );
	echo '<br />';
	echo '<br />';
	echo '<label for="lw_success_message">' . __( 'Success message', 'lowlevelwebforms' ) . '</label>';
	echo '<br />';
	echo '<textarea id="lw_success_message" name="lw_success_message" style="width: 100%; height: 100px;">' . esc_textarea( $successMessage ) . '</textarea>';
	echo '</fieldset>';
}

function lw_webform_email_admin_metabox( $post ) {
	$postId = $post->ID;
	$newPage = ( basename( $_SERVER['PHP_SELF'] ) == 'post-new.php' );
	
	$useAdminEmail = get_post_meta( $postId, 'lw_use_admin_email', true );
	$adminEmailFrom = ( $newPage ? get_option( 'admin_email' ) : get_post_meta( $postId, 'lw_admin_email_from', true ) );
	$adminEmailTo = get_post_meta( $postId, 'lw_admin_email_to', true );
	$adminEmailSubject = get_post_meta( $postId, 'lw_admin_email_subject', true );
	$adminEmailBodyHtml = get_post_meta( $postId, 'lw_admin_email_body_html', true );
	$adminEmailBody = get_post_meta( $postId, 'lw_admin_email_body', true );
	$adminAttachUploads = get_post_meta( $postId, 'lw_admin_attach_uploads', true );
	
	echo '<input type="hidden" name="lw_use_admin_email" value="0" />';
	echo '<input type="checkbox" id="lw_use_admin_email" name="lw_use_admin_email" value="1"' . ( $useAdminEmail ? ' checked="checked"' : '' ) . ' />';
	echo ' <label for="lw_use_admin_email">' . __( 'Use admin email', 'lowlevelwebforms' ) . '</label>';
	echo '<br />';
	echo '<br />';
	echo '<label for="lw_admin_email_from">' . __( 'From', 'lowlevelwebforms' ) . '</label>';
	echo '<br />';
	echo '<input type="text" id="lw_admin_email_from" name="lw_admin_email_from" value="' . esc_attr( $adminEmailFrom ) . '" size="25" />';
	echo '<br />';
	echo '<label for="lw_admin_email_to">' . __( 'To', 'lowlevelwebforms' ) . '</label>';
	echo '<br />';
	echo '<input type="text" id="lw_admin_email_to" name="lw_admin_email_to" value="' . esc_attr( $adminEmailTo ) . '" size="25" />';
	echo '<br />';
	echo '<label for="lw_admin_email_subject">' . __( 'Subject', 'lowlevelwebforms' ) . '</label>';
	echo '<br />';
	echo '<input type="text" id="lw_admin_email_subject" name="lw_admin_email_subject" value="' . esc_attr( $adminEmailSubject ) . '" size="25" />';
	echo '<br />';
	echo '<br />';
	echo '<input type="hidden" name="lw_admin_email_body_html" value="0" />';
	echo '<input type="checkbox" id="lw_admin_email_body_html" name="lw_admin_email_body_html" value="1"' . ( $adminEmailBodyHtml ? ' checked="checked"' : '' ) . ' />';
	echo ' <label for="lw_admin_email_body_html">' . __( 'Use html content type', 'lowlevelwebforms' ) . '</label>';
	echo '<br />';
	echo '<br />';
	echo '<label for="lw_admin_email_body">' . __( 'Body', 'lowlevelwebforms' ) . '</label>';
	echo '<br />';
	echo '<textarea id="lw_admin_email_body" name="lw_admin_email_body" style="width: 100%; height: 200px;">' . esc_textarea( $adminEmailBody ) . '</textarea>';
	echo '<br />';
	echo '<br />';
	echo '<input type="hidden" name="lw_admin_attach_uploads" value="0" />';
	echo '<input type="checkbox" id="lw_admin_attach_uploads" name="lw_admin_attach_uploads" value="1"' . ( $adminAttachUploads ? ' checked="checked"' : '' ) . ' />';
	echo ' <label for="lw_admin_attach_uploads">' . __( 'Attach uploads', 'lowlevelwebforms' ) . '</label>';
}

function lw_webform_email_user_metabox( $post ) {
	$postId = $post->ID;
	$newPage = ( basename( $_SERVER['PHP_SELF'] ) == 'post-new.php' );
	
	$useUserEmail = get_post_meta( $postId, 'lw_use_user_email', true );
	$userEmailFrom = ( $newPage ? get_option( 'admin_email' ) : get_post_meta( $postId, 'lw_user_email_from', true ) );
	$userEmailTo = get_post_meta( $postId, 'lw_user_email_to', true );
	$userEmailSubject = get_post_meta( $postId, 'lw_user_email_subject', true );
	$userEmailBodyHtml = get_post_meta( $postId, 'lw_user_email_body_html', true );
	$userEmailBody = get_post_meta( $postId, 'lw_user_email_body', true );
	$userAttachUploads = get_post_meta( $postId, 'lw_user_attach_uploads', true );
	
	echo '<input type="hidden" name="lw_use_user_email" value="0" />';
	echo '<input type="checkbox" id="lw_use_user_email" name="lw_use_user_email" value="1"' . ( $useUserEmail ? ' checked="checked"' : '' ) . ' />';
	echo ' <label for="lw_use_user_email">' . __( 'Use user email', 'lowlevelwebforms' ) . '</label>';
	echo '<br />';
	echo '<br />';
	echo '<label for="lw_user_email_from">' . __( 'From', 'lowlevelwebforms' ) . '</label>';
	echo '<br />';
	echo '<input type="text" id="lw_user_email_from" name="lw_user_email_from" value="' . esc_attr( $userEmailFrom ) . '" size="25" />';
	echo '<br />';
	echo '<label for="lw_user_email_to">' . __( 'To', 'lowlevelwebforms' ) . '</label>';
	echo '<br />';
	echo '<input type="text" id="lw_user_email_to" name="lw_user_email_to" value="' . esc_attr( $userEmailTo ) . '" size="25" />';
	echo '<br />';
	echo '<label for="lw_user_email_subject">' . __( 'Subject', 'lowlevelwebforms' ) . '</label>';
	echo '<br />';
	echo '<input type="text" id="lw_user_email_subject" name="lw_user_email_subject" value="' . esc_attr( $userEmailSubject ) . '" size="25" />';
	echo '<br />';
	echo '<br />';
	echo '<input type="hidden" name="lw_user_email_body_html" value="0" />';
	echo '<input type="checkbox" id="lw_user_email_body_html" name="lw_user_email_body_html" value="1"' . ( $userEmailBodyHtml ? ' checked="checked"' : '' ) . ' />';
	echo ' <label for="lw_user_email_body_html">' . __( 'Use html content type', 'lowlevelwebforms' ) . '</label>';
	echo '<br />';
	echo '<br />';
	echo '<label for="lw_user_email_body">' . __( 'Body', 'lowlevelwebforms' ) . '</label>';
	echo '<br />';
	echo '<textarea id="lw_user_email_body" name="lw_user_email_body" style="width: 100%; height: 200px;">' . esc_textarea( $userEmailBody ) . '</textarea>';
	echo '<br />';
	echo '<br />';
	echo '<input type="hidden" name="lw_user_attach_uploads" value="0" />';
	echo '<input type="checkbox" id="lw_user_attach_uploads" name="lw_user_attach_uploads" value="1"' . ( $userAttachUploads ? ' checked="checked"' : '' ) . ' />';
	echo ' <label for="lw_user_attach_uploads">' . __( 'Attach uploads', 'lowlevelwebforms' ) . '</label>';
}

function lw_webform_template_metabox( $post ) {
	if ( 0 != count( lw_webform_get_templates() ) ) {
		$template = get_post_meta( $post->ID, 'lw_webform_template', true );
		?>
<label class="screen-reader-text" for="lw_webform_template"><?php _e( 'Form Template', 'lowlevelwebforms' ) ?></label><select name="lw_webform_template" id="lw_webform_template">
<option value=""><?php _e( 'Select form template', 'lowlevelwebforms' ); ?></option>
<?php lw_webform_template_dropdown( $template ); ?>
</select>
		<?php
	}
}

function lw_webform_get_templates() {
	$templates = wp_get_theme()->get_files( 'php', 2 );
	$form_templates = array();

	if ( is_array( $templates ) ) {
		$base = array( trailingslashit( get_stylesheet_directory() ), trailingslashit( get_stylesheet_directory() ) );

		foreach ( $templates as $template ) {
			$basename = str_replace( $base, '', $template );
			if ( $basename != 'functions.php' ) {
				// look for templates in 'lowlevelwebforms' folder
				if ( 0 !== strpos( $basename, 'lowlevelwebforms' ) )
					continue;

				$template_data = implode( '', file( $template ) );

				$name = '';
				if ( preg_match( '|Form Template:(.*)$|mi', $template_data, $name ) )
					$name = _cleanup_header_comment($name[ 1 ]);

				if ( !empty( $name ) ) {
					$form_templates[ trim( $name ) ] = $basename;
				}
			}
		}
	}

	return $form_templates;
}

function lw_webform_template_dropdown( $default = '' ) {
	$templates = lw_webform_get_templates();
	ksort( $templates );
	foreach ( array_keys( $templates ) as $template ) {
		if ( $default == $templates[ $template ] )
			$selected = " selected='selected'";
		else
			$selected = '';
		echo "\n\t<option value='".$templates[ $template ]."' $selected>$template</option>";
	}
}

function lw_webform_plugin_menu() {
	global $wpdb;
	global $options;
	global $user_ID;

	if ( basename( $_SERVER['SCRIPT_FILENAME'] ) == 'plugins.php' && isset( $_GET['page'] ) && $_GET['page'] == 'lowlevelwebforms' ) {
		// Check permissions
		if ( ! current_user_can( 'manage_options' ) )  {
			wp_die( __( 'You do not have sufficient permissions to access this page.', 'lowlevelwebforms' ) );
		}

		if ( isset( $_REQUEST['action'] ) && 'create_webform' == $_REQUEST['action'] ) {
			$title = $wpdb->escape( $_POST[ 'custom_webform_name' ] );
			
			$attr = array(
				'post_title' => $title,
				'post_content' => '',
				'post_status' => 'publish',
				'post_date' => date('Y-m-d H:i:s'),
				'post_author' => $user_ID,
				'post_type' => 'lw_webform',
				'post_category' => array( 0 )
			);

			$webform_post_id = wp_insert_post( $attr );
			$webform_post = get_post( $webform_post_id );
			$slug = $webform_post->post_name;
			
			update_post_meta( $webform_post_id, 'lw_webform_template', 'lowlevelwebforms/' . $slug . '.php' );
			update_post_meta( $webform_post_id, 'lw_admin_email_from', get_option( 'admin_email' ) );
			update_post_meta( $webform_post_id, 'lw_user_email_from', get_option( 'admin_email' ) );
			update_post_meta( $webform_post_id, 'lw_success_page', '/' );

			// Check login form template
			$target = get_stylesheet_directory();

			// Create auxiliary dir and copy form template
			if ( ! file_exists( $target . '/lowlevelwebforms' ) ) {
				umask( 0 );
				mkdir( $target . '/lowlevelwebforms', 0777 );
			}
			if ( ! file_exists( $target . '/lowlevelwebforms/' . $slug . '.php' ) ) {
				$template = file_get_contents( LW_PATH . 'template/single-lw_webform.php' );
				$template = str_replace( array( '{title}' ), array( $title ), $template );
				file_put_contents ( $target . '/lowlevelwebforms/' . $slug . '.php', $template );
				chmod( $target . '/lowlevelwebforms/' . $slug . '.php', 0777 );
			}

			wp_redirect( home_url( '/wp-admin/plugins.php?page=lowlevelwebforms&saved=true' ) );
		} elseif ( isset( $_REQUEST['action'] ) && 'create_webform_template' == $_REQUEST['action'] ) {
			$webform = get_post( ( int ) $_REQUEST[ 'webform_id' ] );

			if ( ! empty( $webform ) && $webform->post_type == 'lw_webform' && is_dir( LW_PATH . 'custom/' . $_REQUEST[ 'template_name' ] ) ) {
				$target = get_stylesheet_directory();
				if ( ! file_exists( $target . '/lowlevelwebforms/custom' ) ) {
					umask( 0 );
					mkdir( $target . '/lowlevelwebforms/custom', 0777 );
				}
				if ( file_exists( $target . '/lowlevelwebforms/custom/' . $webform->post_name ) ) {
					lw_rmdir( $target . '/lowlevelwebforms/custom/' . $webform->post_name );
				}
				$result = lw_copy( LW_PATH . 'custom/' . $_REQUEST[ 'template_name' ], $target . '/lowlevelwebforms/custom/' . $webform->post_name );
				if ( ! $result ) {
					@lw_rmdir( $target . '/lowlevelwebforms/custom/' . $webform->post_name );
					wp_redirect( home_url( '/wp-admin/plugins.php?page=lowlevelwebforms&error=create_webform_template' ) );
					die();
				}
				
				// Setup hook
				if ( file_exists( $target . '/lowlevelwebforms/custom/' . $webform->post_name . '/functions.php' ) ) {
					include $target . '/lowlevelwebforms/custom/' . $webform->post_name . '/functions.php';
					do_action( 'lw_webform_custom_' . $_REQUEST[ 'template_name' ] . '_setup', $webform->post_name );
				}
			}

			wp_redirect( home_url( '/wp-admin/plugins.php?page=lowlevelwebforms&saved=true' ) );
		} elseif ( isset( $_REQUEST['action'] ) && 'delete_webform' == $_REQUEST['action'] ) {
			$webform_post_id = ( int ) $_REQUEST[ 'post' ];
			$webform = get_post( $webform_post_id );

			$target = get_stylesheet_directory();
			if ( file_exists( $target . '/lowlevelwebforms/custom/' . $webform->post_name ) ) {
				lw_rmdir( $target . '/lowlevelwebforms/custom/' . $webform->post_name );
			}
			
			if ( $webform_post_id && $webform && $webform->post_type == 'lw_webform' ) {
				wp_delete_post( $webform_post_id, true );
			}

			wp_redirect( home_url( '/wp-admin/plugins.php?page=lowlevelwebforms&saved=true' ) );
		} elseif ( isset( $_REQUEST[ 'action' ] ) && 'lw_options' == $_REQUEST[ 'action' ] ) {
			update_option( 'lw_wp_login_redirect', $_REQUEST[ 'wp_login_redirect' ] );
			
			// Handle login form
			if( $_REQUEST['wp_login_redirect'] ) {
				// Create log in form
				if ( ! get_option( 'lw_login_form' ) || ! get_post( get_option( 'lw_login_form' ) ) || get_post_type( get_option( 'lw_login_form' ) ) != 'lw_webform' ) {
					$args = array(
						'name' => 'log-in',
						'post_type' => 'lw_webform',
						'posts_per_page' => 1
					);
					$p = get_posts( $args );

					if ( count( $p ) ) {
						$loginPost = array_shift( $p );
						$loginPostId = $loginPost->ID;
					} else {
						$attr = array(
							'post_title' => __( 'Log in', 'lowlevelwebforms' ),
							'post_content' => '',
							'post_status' => 'publish',
							'post_date' => date('Y-m-d H:i:s'),
							'post_author' => $user_ID,
							'post_type' => 'lw_webform',
							'post_category' => array( 0 )
						);

						$loginPostId = wp_insert_post( $attr );
						
						update_post_meta( $loginPostId, 'lw_webform_template', 'lowlevelwebforms/log-in.php' );
						update_post_meta( $loginPostId, 'lw_success_page', '/' );
					}
					
					update_option( 'lw_login_form', $loginPostId );
					
					// Check login form template
					$target = get_stylesheet_directory();

					// Create auxiliary dir and copy login form template
					if ( ! file_exists( $target . '/lowlevelwebforms' ) ) {
						umask( 0 );
						mkdir( $target . '/lowlevelwebforms', 0777 );
					}
					if ( ! file_exists( $target . '/lowlevelwebforms/log-in.php' ) ) {
						$template = file_get_contents( LW_PATH . 'template/log-in.php' );
						file_put_contents ( $target . '/lowlevelwebforms/log-in.php', $template );
						chmod( $target . '/lowlevelwebforms/log-in.php', 0777 );
					}
				}
			} else {
				$loginPostId = get_option( 'lw_login_form' );
				
				if ( $loginPostId && get_post( $loginPostId ) && get_post_type( $loginPostId ) == 'lw_webform' ) {
					wp_delete_post( $loginPostId, true );
					delete_option( 'lw_login_form' );
				}
			}
			
			wp_redirect( home_url( '/wp-admin/plugins.php?page=lowlevelwebforms&saved=true' ) );
		}
	}
	
	add_plugins_page( 'Lowlevel Webforms Options', 'Lowlevel Webforms', 'manage_options', 'lowlevelwebforms', 'lw_plugin_page' );
}

function lw_plugin_page() {
	global $wpdb;
	global $options;
	global $user_ID;

	// Check permissions
	if ( ! current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.', 'lowlevelwebforms' ) );
	}

	if ( isset( $_REQUEST['saved'] ) ) {
		echo '<div id="message" class="updated fade"><p><strong> Settings saved.</strong></p></div>';
	} elseif ( isset( $_REQUEST[ 'error' ] ) ) {
		if ( $_REQUEST[ 'error' ] == 'copy_files' ) {
			echo '<div id="message" class="updated fade"><p><strong> Settings could not be saved. Check folder permissions.</strong></p></div>';
		}
	}
	
	$args = array( 'post_type' => 'lw_webform', 'orderby' => 'title', 'posts_per_page' => -1 );
	if ( get_option( 'lw_wp_login_redirect' ) ) {
		$args[ 'exclude' ] = array( get_option( 'lw_login_form' ) );
	}
	$webforms = get_posts( $args );
	?>
	<div class="wrap">
		<div id="icon-themes" class="icon32">
			<br>
		</div>
		<h2>Direct Edit <?php _e( 'Options', 'lowlevelwebforms' ); ?></h2>
		<h3><?php _e( 'custom webforms', 'lowlevelwebforms' ); ?></h3>
		<div class="inside">
			<table border="0">
				<tbody>
					<tr>
						<td style="width: 30px;"><?php _e( 'name', 'lowlevelwebforms' ); ?></td>
						<td><form method="post"><input type="hidden" name="action" value="create_webform" /><input type="text" name="custom_webform_name" id="custom_webform_name" style="width: 240px;" /> <input type="submit" value="create" /></form></td>
					</tr>
					<tr>
						<td></td>
						<td>
							<table border="0">
								<tbody>
									<?php foreach ( $webforms as $webform ) { ?>
									<tr>
										<td><?php echo $webform->post_title; ?></td>
										<td>
											<?php
											if ( is_dir( LW_PATH . '/custom' ) ) {
												$source = LW_PATH . '/custom';
												$d = dir( $source );
												
												$c = 0;
												while ( FALSE !== ( $entry = $d->read() ) ) {
													if ( $entry == '.' || $entry == '..' )
														continue;

													if ( is_dir( "$source/$entry" ) ) {
														$c ++;
													}
												}
												
												if ( $c ) {
													$d->rewind();
													?>
													<form method="post">
														<input type="hidden" name="action" value="create_webform_template" />
														<input type="hidden" name="webform_id" value="<?php echo $webform->ID; ?>" />
														<select name="template_name">
															<?php
																while ( FALSE !== ( $entry = $d->read() ) ) {
																	if ( $entry == '.' || $entry == '..' )
																		continue;

																	if ( is_dir( "$source/$entry" ) ) {
																		?>
																		<option value="<?php echo $entry; ?>"><?php echo $entry; ?></option>
																		<?php
																	}
																}
															?>
														</select>
														<input type="submit" value="create" />
													</form>
													<?php
												}
											}
											?>
										</td>
										<td>
											<input type="button" onclick="location.href='?page=lowlevelwebforms&action=delete_webform&post=<?php echo $webform->ID; ?>'" value="<?php _e( 'remove', 'lowlevelwebforms' ); ?>" />
										</td>
									</tr>
									<?php } ?>
								</tbody>
							</table>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<h3><i><?php _e( 'options', 'lowlevelwebforms' ); ?></i></h3>
		<div class="inside">
			<form method="post">
				<input type="hidden" name="action" value="lw_options" />
				<table border="0">
					<tbody>
						<tr>
							<td><input type="hidden" name="wp_login_redirect" value="" /><label><input type="checkbox" name="wp_login_redirect" value="1"<?php echo ( get_option( 'lw_wp_login_redirect' ) ? ' checked="checked"' : '' ); ?> /> <?php _e( 'wp-login form redirect', 'lowlevelwebforms' ); ?></label></td>
						</tr>
						<tr>
							<td><input type="submit" value="save" /></td>
						</tr>
					</tbody>
				</table>
			</form>
		</div>
	</div>
	<?php
}

function lw_webform_capabilities() {
	// Add dE capabilities
	$admin = get_role( 'administrator' );
	if ( is_object( $admin ) && ! user_can( $admin->ID, 'edit_lw_webform' ) ) {
		$admin->add_cap( 'edit_lw_webform', true );
	}
	if ( is_object( $admin ) && ! user_can( $admin->ID, 'delete_lw_webform' ) ) {
		$admin->add_cap( 'delete_lw_webform', true );
	}
}

function lw_webform_create_post_types() {
	register_post_type( 'lw_webform',
		array(
			'labels' => array(
				'name' => __( 'Webforms', 'lowlevelwebforms' ),
				'singular_name' => __( 'Webform', 'lowlevelwebforms' )
			),
			'public' => true,
			'rewrite' => array( 'slug' => 'webforms', 'lowlevelwebforms' ),
			'supports' => array( 'title', 'editor', 'author', 'page-attributes' ),
			'capabilities' => array(
				'edit_posts' => 'edit_lw_webform',
				'edit_others_posts' => 'edit_lw_webform',
				'publish_posts' => 'publish_posts',
				'read_private_posts' => 'read_private_posts',
				'read' => 'read',
				'delete_posts' => 'delete_lw_webform',
				'delete_private_posts' => 'delete_lw_webform',
				'delete_published_posts' => 'delete_lw_webform',
				'delete_others_posts' =>  'delete_lw_webform',
				'edit_private_posts' => 'edit_lw_webform',
				'edit_published_posts' => 'edit_lw_webform',
				'edit_post' => 'edit_lw_webform',
				'delete_post' => 'delete_lw_webform',
				'read_post' => 'read'
			)
		)
	);
}

function lw_webform_login_redirect() {
	if ( get_option( 'lw_wp_login_redirect' ) ) {
		$action = isset( $_REQUEST['action'] ) ? $_REQUEST['action'] : 'login';
		$redirect_to = ! empty( $_REQUEST['redirect_to'] ) ? $_REQUEST['redirect_to'] : 'wp-login.php?loggedout=true';

		if ( $action != 'logout' ) {
			if ( strpos( lw_get_login_form_permalink(), 'wp-login.php' ) === false ) {
				wp_redirect( lw_get_login_form_permalink() );
				exit;
			}
		}
	}
}

function lw_webform_setup( $post ) {
	global $user_ID;
	global $lw_webform_values;
	
	if ( $post->ID == get_option( 'lw_login_form' ) ) {
		/*
		 * If a user is logged in, he is redirected to the page with his settings
		 */
		if ( $user_ID ) {
			wp_redirect( home_url() );
		}
		
		/*
		 * Form default values
		 */
		$lw_webform_values[ 'email' ] = '';
	}
}

function lw_webform_validate( $post ) {
	global $wpdb;
	global $user_ID;
	global $lw_webform_errors;
	global $lw_webform_values;
	
	if ( $post->ID == get_option( 'lw_login_form' ) ) {
		$lw_webform_values[ 'email' ] = $wpdb->escape( $_POST[ 'email' ] );
		$password = $wpdb->escape( $_POST[ 'password' ] );

		if( empty( $lw_webform_values[ 'email' ] ) || ! filter_var( $lw_webform_values[ 'email' ], FILTER_VALIDATE_EMAIL ) || email_exists( $lw_webform_values[ 'email' ] ) == false )
			$lw_webform_errors[ 'email' ] = __( 'You have no email specified.', 'direct-edit' );
		if( empty( $password ) )
			$lw_webform_errors[ 'password' ] = __( 'You have no password specified.', 'direct-edit' );
	}
}

function lw_webform_action( $post ) {
	global $wpdb;
	global $user_ID;
	global $lw_webform_errors;
	global $lw_webform_values;
	
	if ( $post->ID == get_option( 'lw_login_form' ) ) {
		$email = $lw_webform_values[ 'email' ];
		$password = $wpdb->escape( $_POST[ 'password' ] );
		$user = get_user_by_email( $email );
		$username = $user->user_login;

		$loginData[ 'user_login' ] = $username;  
		$loginData[ 'user_password' ] = $password;  
		$loginData[ 'remember' ] = false;  

		/*
		 * Login attempt
		 */
		$userVerify = wp_signon( $loginData );   

		if ( is_wp_error( $userVerify ) ) {
			$lw_webform_errors[] = $userVerify->get_error_message();
		}
	}
}

function lw_webform_save_template( $post_id, $post ) {
	if ( $post->post_type == 'lw_webform' ) {
		if ( ! current_user_can( 'edit_lw_webform' ) )
			return false;

		if ( basename( $_SERVER['PHP_SELF'] ) == 'post.php' || basename( $_SERVER['PHP_SELF'] ) == 'post-new.php' ) {
			update_post_meta( $post->ID, 'lw_webform_template', $_POST['lw_webform_template'] );
			
			update_post_meta( $post->ID, 'lw_success_page', $_POST['lw_success_page'] );
			update_post_meta( $post->ID, 'lw_success_message', $_POST['lw_success_message'] );
			
			update_post_meta( $post->ID, 'lw_use_admin_email', $_POST['lw_use_admin_email'] );
			update_post_meta( $post->ID, 'lw_admin_email_from', $_POST['lw_admin_email_from'] );
			update_post_meta( $post->ID, 'lw_admin_email_to', $_POST['lw_admin_email_to'] );
			update_post_meta( $post->ID, 'lw_admin_email_subject', $_POST['lw_admin_email_subject'] );
			update_post_meta( $post->ID, 'lw_admin_email_body_html', $_POST['lw_admin_email_body_html'] );
			update_post_meta( $post->ID, 'lw_admin_email_body', $_POST['lw_admin_email_body'] );
			update_post_meta( $post->ID, 'lw_admin_attach_uploads', $_POST['lw_admin_attach_uploads'] );
			
			update_post_meta( $post->ID, 'lw_use_user_email', $_POST['lw_use_user_email'] );
			update_post_meta( $post->ID, 'lw_user_email_from', $_POST['lw_user_email_from'] );
			update_post_meta( $post->ID, 'lw_user_email_to', $_POST['lw_user_email_to'] );
			update_post_meta( $post->ID, 'lw_user_email_subject', $_POST['lw_user_email_subject'] );
			update_post_meta( $post->ID, 'lw_user_email_body_html', $_POST['lw_user_email_body_html'] );
			update_post_meta( $post->ID, 'lw_user_email_body', $_POST['lw_user_email_body'] );
			update_post_meta( $post->ID, 'lw_user_attach_uploads', $_POST['lw_user_attach_uploads'] );
		}
	}
}

function lw_webform_process( $template ) {
	global $post;
	global $wpdb;
	global $lw_webform_errors;
	global $lw_webform_messages;
	global $lw_webform_search;
	global $lw_webform_replace;
	global $lw_webform_use_admin_email;
	global $lw_webform_use_user_email;
	global $lw_webform_success_page;
	global $lw_webform_success_message;
	global $lw_webform_admin_attachments;
	global $lw_webform_user_attachments;

	$lw_webform_search = array();
	$lw_webform_replace = array();

	if ( is_object( $post ) && $post->post_type == 'lw_webform' ) {
		// Retrieve form params
		$postId = $post->ID;
		$lw_webform_success_page = get_post_meta( $postId, 'lw_success_page', true );
		$lw_webform_success_message = get_post_meta( $postId, 'lw_success_message', true );
		$lw_webform_use_admin_email = get_post_meta( $postId, 'lw_use_admin_email', true );
		$adminEmailFrom = ( $newPage ? get_option( 'admin_email' ) : get_post_meta( $postId, 'lw_admin_email_from', true ) );
		$adminEmailTo = get_post_meta( $postId, 'lw_admin_email_to', true );
		$adminEmailSubject = get_post_meta( $postId, 'lw_admin_email_subject', true );
		$adminEmailBodyHtml = get_post_meta( $postId, 'lw_admin_email_body_html', true );
		$adminEmailBody = get_post_meta( $postId, 'lw_admin_email_body', true );
		$adminEmailAttachUploads = get_post_meta( $postId, 'lw_admin_attach_uploads', true );
		$lw_webform_use_user_email = get_post_meta( $postId, 'lw_use_user_email', true );
		$userEmailFrom = ( $newPage ? get_option( 'admin_email' ) : get_post_meta( $postId, 'lw_user_email_from', true ) );
		$userEmailTo = get_post_meta( $postId, 'lw_user_email_to', true );
		$userEmailSubject = get_post_meta( $postId, 'lw_user_email_subject', true );
		$userEmailBodyHtml = get_post_meta( $postId, 'lw_user_email_body_html', true );
		$userEmailBody = get_post_meta( $postId, 'lw_user_email_body', true );
		$userEmailAttachUploads = get_post_meta( $postId, 'lw_user_attach_uploads', true );

		do_action( $post->post_type . '_form_setup', $post );
		
		if( $_SERVER[ 'REQUEST_METHOD' ] == 'POST' ) {
			// Check uploads
			$uploads_to_delete = array();
			if ( ! empty( $_FILES ) && is_array( $_FILES ) ) {
				inclulw_once( ABSPATH . 'wp-admin/includes/file.php' );
				
				foreach ( $_FILES as $key => $file ) {
					if ( ! empty( $file[ 'error' ] ) ) {
						$lw_webform_errors[ $key ] = __( 'File uploading error.', 'direct-edit' );
					} else {
						$result = wp_handle_upload( $file, array( 'test_form' => FALSE ) );
						
						if ( isset( $result[ 'file' ] ) ) {
							$uploads_to_delete[] = $result[ 'file' ];
							
							if ( $adminEmailAttachUploads ) {
								$lw_webform_admin_attachments[] = $result[ 'file' ];
							}
							if ( $userEmailAttachUploads ) {
								$lw_webform_user_attachments[] = $result[ 'file' ];
							}
						} else {
							$lw_webform_errors[ $key ] = __( 'File uploading error.', 'direct-edit' );
						}
					}
				}
			}

			do_action( $post->post_type . '_form_validate', $post );

			if ( empty( $lw_webform_errors ) ) {
				foreach( $_POST as $key => $value) {
					$lw_webform_search[] = '{' . $key . '}';
					$lw_webform_replace[] = $wpdb->escape( $value );
				}
				
				// Save uploaded files if they exist
				if( ! empty( $_FILES ) && is_array( $_FILES ) ) {
					inclulw_once( ABSPATH . 'wp-admin/includes/file.php' );
					
					foreach ( $_FILES as $file ) {
						$result = wp_handle_upload( $file, array( 'test_form' => FALSE ) );
						if ( $adminEmailAttachUploads )
							$lw_webform_admin_attachments[] = $result[ 'file' ];
						if ( $userEmailAttachUploads )
							$lw_webform_user_attachments[] = $result[ 'file' ];
					}
				}

				do_action( $post->post_type . '_form_action', $post );
				
				if ( empty( $lw_webform_errors ) ) {
					// Admin email
					$adminEmailFrom = str_replace( $lw_webform_search, $lw_webform_replace, $adminEmailFrom );
					$adminEmailTo = str_replace( $lw_webform_search, $lw_webform_replace, $adminEmailTo );
					$adminEmailSubject = str_replace( $lw_webform_search, $lw_webform_replace, $adminEmailSubject );
					$adminEmailBody = str_replace( $lw_webform_search, $lw_webform_replace, $adminEmailBody );

					if ( $lw_webform_use_admin_email ) {
						//if( filter_var( $adminEmailFrom, FILTER_VALIDATE_EMAIL ) && filter_var( $adminEmailTo, FILTER_VALIDATE_EMAIL ) ) {
							$blogname = wp_specialchars_decode( get_option('blogname'), ENT_QUOTES );
							$headers = 'From: ' . $blogname . ' <' . $adminEmailFrom . ">\r\n";
							if ( $adminEmailBodyHtml )
								$headers .= 'Content-type: text/html' . "\r\n";
							if ( count( $lw_webform_admin_attachments ) > 0 ) {
								wp_mail( $adminEmailTo, $adminEmailSubject, $adminEmailBody, $headers, $lw_webform_admin_attachments );
							} else {
								wp_mail( $adminEmailTo, $adminEmailSubject, $adminEmailBody, $headers );
							}
						//}
					}

					// User email
					$userEmailFrom = str_replace( $lw_webform_search, $lw_webform_replace, $userEmailFrom );
					$userEmailTo = str_replace( $lw_webform_search, $lw_webform_replace, $userEmailTo );
					$userEmailSubject = str_replace( $lw_webform_search, $lw_webform_replace, $userEmailSubject );
					$userEmailBody = str_replace( $lw_webform_search, $lw_webform_replace, $userEmailBody );

					if ( $lw_webform_use_user_email ) {
						//if( filter_var( $userEmailFrom, FILTER_VALIDATE_EMAIL ) && filter_var( $userEmailTo, FILTER_VALIDATE_EMAIL ) ) {
							$blogname = wp_specialchars_decode( get_option('blogname'), ENT_QUOTES );
							$headers = 'From: ' . $blogname . ' <' . $userEmailFrom . ">\r\n";
							if ( $userEmailBodyHtml )
								$headers .= 'Content-type: text/html' . "\r\n";
							if ( count( $lw_webform_user_attachments ) > 0 ) {
								wp_mail( $userEmailTo, $userEmailSubject, $userEmailBody, $headers, $lw_webform_user_attachments );
							} else {
								wp_mail( $userEmailTo, $userEmailSubject, $userEmailBody, $headers );
							}
						//}
					}

					// Success page redirect
					if ( $lw_webform_success_page ) {
						$link = str_replace( $lw_webform_search, $lw_webform_replace, $lw_webform_success_page );
						$link = ( strpos( $link, 'http://' ) === false && strpos( $link, 'https://' ) === false ? get_bloginfo( 'url' ) . $link : $link );
						if ( $link )
							header( 'Location: ' . $link );
					} elseif( $lw_webform_success_message ) {
						$lw_webform_messages[] = str_replace( $lw_webform_search, $lw_webform_replace, $lw_webform_success_message );
					}
				}
			}
			
			// Delete files after sending them
			foreach ( $uploads_to_delete as $upload_to_delete ) {
				@unlink( $upload_to_delete );
			}
		}
	}
	
	return $template;
}

function lw_webform_add_the_content( $content ) {
	global $post;
	global $lw_webform_errors;
	global $lw_webform_messages;
	global $lw_webform_values;
	
	if ( $post->post_type == 'lw_webform' ) {
		if ( is_dir( get_stylesheet_directory() . '/lowlevelwebforms/custom/' . $post->post_name ) && file_exists( get_stylesheet_directory() . '/lowlevelwebforms/custom/' . $post->post_name . '/single-lw_webform.php' ) ) {
			$template = get_stylesheet_directory() . '/lowlevelwebforms/custom/' . $post->post_name . '/single-lw_webform.php';
			
			if ( file_exists( dirname( $template ) . '/functions.php' ) ) {
				include dirname( $template ) . '/functions.php';
			}
			
			ob_start();
			include $template;
			$form = ob_get_contents();
			ob_end_clean();
			
			$content .= $form;
		} elseif ( get_post_meta( $post->ID, 'lw_webform_template', true ) && file_exists( get_stylesheet_directory() . '/' . get_post_meta( $post->ID, 'lw_webform_template', true ) ) ) {
			$template = get_stylesheet_directory() . '/' . get_post_meta( $post->ID, 'lw_webform_template', true );

			ob_start();
			include $template;
			$form = ob_get_contents();
			ob_end_clean();
			
			$content .= $form;
		}
	}

	return $content;
}

/*
Service functions
*/
function lw_get_login_form_permalink() {
	if( get_option( 'lw_wp_login_redirect' ) ) {
		$id = get_option( 'lw_login_form' );
		if ( file_exists( get_stylesheet_directory() . '/lowlevelwebforms/log-in.php' ) && $id && get_permalink( $id ) ) {
			return get_permalink( $id );
		} else {
			return add_query_arg( 'redirect_to', urlencode( home_url() ), home_url( 'wp-login.php' ) );
		}
	} else {
		return add_query_arg( 'redirect_to', urlencode( home_url() ), home_url( 'wp-login.php' ) );
	}
}

// Recursively copy some folder
function lw_copy( $source, $target ) {
	 if ( is_dir( $source ) ) {
		umask( 0 );
		$result = mkdir( $target, 0777 );
		if ( ! $result )
			return false;
		
		$d = dir( $source );
		while ( FALSE !== ( $entry = $d->read() ) ) {
			if ( $entry == '.' || $entry == '..' )
				continue;
			$result = lw_copy( "$source/$entry", "$target/$entry" );
			if ( ! $result )
				return false;
		}
		$d->close();
	} else {
		$result = copy( $source, $target );
		if ( ! $result )
			return false;

		chmod( $target, 0777 );
	}
	
	return true;
}

// Delete directory with files in it
function lw_rmdir( $source ) {
	if ( is_dir( $source ) ) {
		$d = dir( $source );
		while ( FALSE !== ( $entry = $d->read() ) ) {
			if ( $entry == '.' || $entry == '..' )
				continue;

			if ( is_dir( "$source/$entry" ) ) {
				lw_rmdir( "$source/$entry" );
			} else {
				unlink( "$source/$entry" );
			}
		}
		$d->close();
		rmdir( $source );
	}
}
