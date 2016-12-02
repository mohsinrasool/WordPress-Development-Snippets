<?php
/**
 *
 * Plugin Name: New User Approve
 * Description: "New User Approval" plugin when installed along with BuddyPress sends its own format of email
 * These snippets send Approval and Deny emails as per the theme set by the BuddyPress Email Customizer
 * To make it work, 'core-user-approved' and 'core-user-denied' situations should be added and email should be set to them.
 * This plugin also disable password resetting of "New User Approval"
 * Author: Mohsin Rasool
 * Version: 1.0
 * Author URI: http://picklewagon.com/
 *
 */


/* New user approval emails set to use buddy press emails */
function own_approve_user($user_id) {

	$user = new WP_User( $user_id );

	wp_cache_delete( $user->ID, 'users' );
	wp_cache_delete( $user->data->user_login, 'userlogins' );
	$email_type = 'core-user-approved';

	do_action( 'new_user_approve_user_approved', $user );

	$unsubscribe_args = array(
		'user_id'           => $user->ID,
		'notification_type' => $email_type,
	);
	$args = array(
		'tokens' => array(
		),
	);

	bp_send_email( $email_type, $user->ID, $args );

	// change usermeta tag in database to approved
	update_user_meta( $user->ID, 'pw_user_status', 'approved' );
}

function own_deny_user( $user_id ) {
	$user = new WP_User( $user_id );

	// send email to user telling of denial
	$user_email = stripslashes( $user->user_email );

	update_user_meta( $user->ID, 'pw_user_status', 'denied' );
	$email_type = 'core-user-denied';

	$unsubscribe_args = array(
		'user_id'           => $user->ID,
		'notification_type' => $email_type,
	);
	$args = array(
		'tokens' => array(
		),
	);

	bp_send_email( $email_type, $user->ID, $args );
}

add_filter('nua_default_deny_user_message', 'nua_default_deny_user_message_own');
function nua_default_deny_user_message_own($content) {
	return bp_get_email('core-user-denied');
}

add_filter('new_user_approve_do_password_reset', 'new_user_approve_do_password_reset');
function new_user_approve_do_password_reset($bypass_password_reset) {
	return false;
}
