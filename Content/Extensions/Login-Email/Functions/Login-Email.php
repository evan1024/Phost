<?php

/**
 * Login Email
 * Extension for Phost
 * (c) 2018, Daniel James
 */

if ( ! defined( 'PHOST' ) ) {

	die();

}

/**
 * Login Email extension class.
 */
class Login_Email {

	/**
	 * Install handler.
	 * 
	 * @since 1.0.0
	 * 
	 * @return boolean
	 */
	public static function install() {

		return true;

	}

	/**
	 * Uninstall handler.
	 * 
	 * @since 1.0.0
	 * 
	 * @return boolean
	 */
	public static function uninstall() {

		return true;

	}

	/**
	 * Event listener handler.
	 * 
	 * @since 1.0.0
	 * 
	 * @return boolean
	 */
	public static function listen() {

		return true;

	}

	/**
	 * Email notification handler.
	 * 
	 * @since 1.0.0
	 * 
	 * @return boolean
	 */
	public static function send() {

		return true;

	}

}
