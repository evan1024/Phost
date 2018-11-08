<?php

/**
 * Phost, your story
 * (c) 2018, Daniel James
 */

if ( ! defined( 'PHOST' ) ) {

	die();

}

/**
 * Database handler.
 * 
 * @package Phost
 * 
 * @since 0.1.0
 */
class Database {

	/**
	 * The current database connection.
	 * 
	 * @since 0.1.0
	 */
	public $connection;

	/**
	 * The database host.
	 * 
	 * @since 0.1.0
	 * 
	 * @var string
	 */
	public $host = '';

	/**
	 * The database name.
	 * 
	 * @since 0.1.0
	 * 
	 * @var string
	 */
	public $name = '';

	/**
	 * The database user's username.
	 * 
	 * @since 0.1.0
	 * 
	 * @var string
	 */
	public $username = '';

	/**
	 * The database user's password.
	 * 
	 * @since 0.1.0
	 * 
	 * @var string
	 */
	public $password = '';

	/**
	 * The database table prefix.
	 * 
	 * @since 0.1.0
	 * 
	 * @var string
	 */
	public $prefix = '';

	/**
	 * Database connection flag.
	 * 
	 * @since 0.1.0
	 * 
	 * @var boolean
	 */
	public $is_connected = false;

	/**
	 * Start connection to the database.
	 * 
	 * @since 0.1.0
	 * 
	 * @return boolean
	 */
	public function __construct() {

		return $this->try_connect();

	}

	/**
	 * Initialise a database connection.
	 * 
	 * @since 0.1.0
	 * 
	 * @return boolean
	 */
	public function try_connect() {

		// Check the database details.
		if ( ! defined( 'DB_HOST' ) || ! defined( 'DB_NAME' ) || ! defined( 'DB_USERNAME' ) || ! defined( 'DB_PASSWORD' ) || ! defined( 'DB_PREFIX' ) ) {

			// We don't have any details set.
			error( __METHOD__, 'Invalid database details set.', '0.1.0' );

			return false;

		}

		// Set database values.
		$this->host = DB_HOST;
		$this->port = DB_PORT;
		$this->name = DB_NAME;
		$this->username = DB_USERNAME;
		$this->password = DB_PASSWORD;
		$this->prefix = DB_PREFIX;

		// Try and connect.
		try {

			// Try to establish a database connection.
			$this->connection = new PDO( "mysql:host=" . $this->host . ";port=" . $this->port . ";dbname=" . $this->name, $this->username, $this->password );

			// Set default connection attributes.
			$this->connection->setAttribute( PDO::ATTR_CASE, PDO::CASE_LOWER );
			$this->connection->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );
			$this->connection->setAttribute( PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC );

			// Set the connection flag.
			$this->is_connected = true;

			return $this->is_connected;

		} catch ( PDOException $error ) {

			// Failed to connect to database.
			error( __METHOD__, 'Failed to connect to database', '0.1.0' );

			return false;

		}

	}

}

