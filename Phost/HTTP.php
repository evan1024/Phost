<?php

/**
 * Phost, your story
 * (c) 2018, Daniel James
 */

if ( ! defined( 'PHOST' ) ) {

	die();

}

/**
 * HTTP Request handler.
 * 
 * @package Phost
 * 
 * @since 0.1.0
 */
class HTTP {

	/**
	 * The URL to make the request.
	 * 
	 * @since 0.1.0
	 * 
	 * @var string
	 */
	public $URL = '';

	/**
	 * The array of request options.
	 * 
	 * @since 0.1.0
	 * 
	 * @var array
	 */
	public $options = array();

	/**
	 * The reeponse from the request.
	 * 
	 * @since 0.1.0
	 * 
	 * @var boolean|array
	 */
	public $response = false;

	/**
	 * Initialise the cURL request.
	 * 
	 * Creates a new request via cURL to ping an external (or
	 * internal) resource based on a number of options that can
	 * be defined. Some cURL options are explicitly set before
	 * the request is made such as RETURNTRANSFER and USERAGENT.
	 * 
	 * Accepts two parameters of `$url` and `$opts` where `$url`
	 * is the resource to be requested and `$opts` is an optional
	 * array of cURL options that will be set before the request
	 * is then made.
	 * 
	 * @since 0.1.0
	 * 
	 * @param string $url  The URL for the cURL request.
	 * @param array  $opts The cURL request options.
	 * 
	 * @return boolean
	 */
	public function __construct( $url, $opts = array() ) {

		// Create new cURL session.
		$curl = curl_init( $url );

		// Do we have any options?
		if ( ! empty( $opts ) ) {

			// Loop through the options and add them.
			foreach ( $opts as $key => $value ) {

				curl_setopt( $curl, $key, $value );

			}

		}

		// Set the default options we must include.
		curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $curl, CURLOPT_USERAGENT, 'Phost/' . blog_version() . ' (' . home_url() . ') HTTP System Request' );

		// Make the request, get the response.
		$response = curl_exec( $curl );

		// Close the cURL connection.
		curl_close( $curl );

		// Try and convert the response to JSON.
		$this->response = json_decode( $response, true );

		// Is the response a string?
		if ( false === $this->response ) {

			// The response wasn't proper JSON so set the response implicitly.
			$this->response = $response;

		}

		return true;

	}

}

