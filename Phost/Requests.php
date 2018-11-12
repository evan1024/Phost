<?php

/**
 * Phost, your story
 * (c) 2018, Daniel James
 */

if ( ! defined( 'PHOST' ) ) {

	die();

}

/**
 * Requests handler.
 * 
 * @package Phost
 * 
 * @since 0.1.0
 */
class Requests {

	/**
	 * The default query collection.
	 * 
	 * @since 0.1.0
	 * 
	 * @var array
	 */
	public $query = array();

	/**
	 * The current user id.
	 * 
	 * @since 0.1.0
	 * 
	 * @var int
	 */
	public $user_id = 0;

	/**
	 * The current post id.
	 * 
	 * @since 0.1.0
	 * 
	 * @var int
	 */
	public $post_id = 0;

	/**
	 * Is the current user an admin.
	 * 
	 * @since 0.1.0
	 * 
	 * @var boolean
	 */
	public $is_admin = false;

	/**
	 * Is the current user logged in.
	 * 
	 * @since 0.1.0
	 * 
	 * @var boolean
	 */
	public $is_logged_in = false;

	/**
	 * Front-end reuqest flag.
	 * 
	 * @since 0.1.0
	 * 
	 * @var boolean
	 */
	public $is_front = false;

	/**
	 * Dashboard request flag.
	 * 
	 * @since 0.1.0
	 * 
	 * @var boolean
	 */
	public $is_dashboard = false;

	/**
	 * System request flag.
	 * 
	 * @since 0.1.0
	 * 
	 * @var boolean
	 */
	public $is_system = false;

	/**
	 * Authentication request flag.
	 * 
	 * @since 0.1.0
	 * 
	 * @var boolean
	 */
	public $is_auth = false;

	/**
	 * API request flag.
	 * 
	 * @since 0.1.0
	 * 
	 * @var boolean
	 */
	public $is_api = false;

	/**
	 * The current page slug.
	 * 
	 * @since 0.1.0
	 * 
	 * @var string
	 */
	public $page = '';

	/**
	 * The current path request.
	 * 
	 * @since 0.1.0
	 * 
	 * @var string
	 */
	public $path = '';

	/**
	 * The current timezone.
	 * 
	 * @since 0.1.0
	 * 
	 * @var string
	 */
	public $timezone = '';

	/**
	 * Create the initial request data.
	 * 
	 * @since 0.1.0
	 * 
	 * @return void
	 */
	public function __construct() {

		// Add the query variables.
		$this->user_id = 0;
		$this->post_id = 0;
		$this->is_admin = false;
		$this->is_logged_in = is_logged_in();
		$this->is_secure = is_secure();
		$this->is_front = $this->get_endpoint( 'front' );
		$this->is_dashboard = $this->get_endpoint( 'dashboard' );
		$this->is_system = $this->get_endpoint( 'system' );
		$this->is_auth = $this->get_endpoint( 'auth' );
		$this->is_api = $this->get_endpoint( 'api' );
		$this->page = $this->get_endpoint();
		$this->path = get_path();

		// Set the blog timezone.
		$this->set_timezone();

		// Check if we need to upgrade to HSTS.
		$this->maybe_http_upgrade();

		// Run the automatic update chacker.
		$this->run_auto_update_checker();

	}

	/**
	 * Check for secure connections.
	 * 
	 * Checks the current connection to see if it's
	 * secure and whether strict transport security
	 * headers should be set and redirected to a
	 * HTTPS version of the page.
	 * 
	 * @todo redirect HTTP requests to HTTPS.
	 * 
	 * @since 0.1.0
	 * 
	 * @access private
	 * 
	 * @return void
	 */
	private function maybe_http_upgrade() {

		// Is HSTE requested?
		if ( 'on' == blog_setting( 'hsts' ) ) {

			// Set the HSTS header for a year.
			header( 'Strict-Transport-Security: max-age=31536000', true );

			return true;

		}

		return false;

	}

	/**
	 * Run an automatic system update check.
	 * 
	 * @todo make this work properly
	 * 
	 * @since 0.1.0
	 * 
	 * @return boolean
	 */
	public function run_auto_update_checker() {

		return false;

	}

	/**
	 * Get the current endpoint.
	 * 
	 * Returns the current endpoint the user is viewing (or trying
	 * to view) unless a value is given for the endpoint parameter
	 * in which case, a check will be performed to see if the current
	 * endpoint matches the value given. If an endpoint value is given,
	 * a boolean value will be returned.
	 * 
	 * @since 0.1.0
	 * 
	 * @param string $endpoint The endpoint to check against if provided.
	 * 
	 * @return string|boolean
	 */
	public function get_endpoint( $endpoint = '' ) {

		// Get the requested URL.
		$url = parse_url( $_SERVER['REQUEST_URI'] );

		// Convert to an array to get the endpoint.
		$dirty_parts = explode( '/', $url['path'] );

		// Create empty array for clean URL parts.
		$clean_parts = array();

		// Loop through and remove empty parts.
		foreach ( $dirty_parts as $part ) {

			// Remove the bad characters.
			$part = sanitise_text( $part, '~[^A-Za-z0-9-]~' );

			// If it's blank, don't add it.
			if ( '' != $part ) {

				$clean_parts[] = $part;

			}

		}

		// Check if the endpoint isn't for the front-end.
		if ( ! empty( $clean_parts ) && in_array( $clean_parts[0], array( 'dashboard', 'system', 'auth', 'api' ), true ) ) {

			$selected = $clean_parts[0];

		} else {

			$selected = 'front';

		}

		// Are we checking an endpoint?
		if ( '' != $endpoint ) {

			// Does the selected endpoint match the check?
			if ( $endpoint == $selected ) {

				return true;

			} else {

				return false;

			}

		}

		return $selected;

	}

	/**
	 * Set the applications timezone.
	 * 
	 * @since 0.1.0
	 * 
	 * @return string
	 */
	public function set_timezone() {

		// Get the blog timezone.
		$timezone = blog_timezone();

		// Get all timezones.
		$timezones = DateTimeZone::listIdentifiers( DateTimeZone::ALL );

		// Did we get a valid timezone?
		if ( ! in_array( $timezone, $timezones, true ) ) {

			// Set the default timezone.
			$timezone = @date_default_timezone_get();

		}

		// Set the server timezone.
		return date_default_timezone_set( $timezone );

	}

}

