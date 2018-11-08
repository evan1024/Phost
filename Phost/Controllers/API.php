<?php

/**
 * Phost, your story
 * (c) 2018, Daniel James
 */

if ( ! defined( 'PHOST' ) ) {

	die();

}

/**
 * The API controller.
 * 
 * @package Phost
 * 
 * @since 0.1.0
 */
class API extends Controller {

	/**
	 * The class name reference.
	 * 
	 * @since 0.1.0
	 * 
	 * @var string
	 */
	public $class = __CLASS__;

	/**
	 * Register routes for this controller.
	 * 
	 * @since 0.1.0
	 * 
	 * @return void
	 */
	public function __construct() {

		$this->get( 'api/', array( $this->class, 'heartbeat' ) );

	}

	/**
	 * The API heartbeat.
	 * 
	 * @since 0.1.0
	 * 
	 * @return mixed
	 */
	public static function heartbeat() {

		// Create API response.
		$response = array(
			'url' => api_url(),
			'status' => 200,
			'message' => 'A connection to the API was established.',
			'data' => array()
		);

		return self::json( $response );

	}

}

