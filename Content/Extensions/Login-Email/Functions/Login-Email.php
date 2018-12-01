<?php

/**
 * Login Email
 * Extension for Phost
 * (c) 2018, Daniel James
 */

if ( ! defined( 'PHOST' ) ) {

	die();

}

$Login_Email = new Login_Email;

/**
 * Login Email extension class.
 */
class Login_Email {

	/**
	 * Setup the extension.
	 * 
	 * @since 1.0.0
	 * 
	 * @return void
	 */
	public function __construct() {

		// Meta event listeners.
		add_listener( 'Login-Email/install', array( __CLASS__, 'install' ) );
		add_listener( 'Login-Email/uninstall', array( __CLASS__, 'uninstall' ) );

		// Core event listeners.
		add_listener( 'core/login', array( __CLASS__, 'send' ) );

	}

	/**
	 * Install handler.
	 * 
	 * @since 1.0.0
	 * 
	 * @return boolean
	 */
	public static function install() {

		return false;

	}

	/**
	 * Uninstall handler.
	 * 
	 * @since 1.0.0
	 * 
	 * @return boolean
	 */
	public static function uninstall() {

		return false;

	}

	/**
	 * Email notification handler.
	 * 
	 * Sends an email to the user that has just been verified
	 * and logged in to their account.
	 * 
	 * @since 1.0.0
	 * 
	 * @param int    $user_id    The ID of the current user.
	 * @param string $user_email The email address of the current user.
	 * 
	 * @return boolean
	 */
	public static function send( $user_id, $user_email ) {

		// Create a user instance.
		$user = new User;

		// Fetch the user data.
		$user->fetch( $user_id );

		// Is the email address valid?
		if ( ! filter_var( $user->user_email, FILTER_VALIDATE_EMAIL ) ) {

			return false;

		}

		// Construct the email message to send.
		$message = "Hello {$user->user_fullname},<br /><br />
Someone has just logged into your account. If this was you then you don't need to do anything.<br /><br />
However if you don't recognise this log in, please secure your account.<br /><br />
<a href='" . auth_url( 'forgot/' ) . "'>Reset your password</a>.<br /><br />
Thank you,<br />" . blog_name();

		// Send the email.
		email( $user->user_email, unfilter_text( '[' . blog_name() . '] Log in notification' ), $message, true );

		return true;

	}

}
