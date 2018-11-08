<?php

/**
 * Phost, your story
 * (c) 2018, Daniel James
 */

if ( ! defined( 'PHOST' ) ) {

	die();

}

/**
 * The setting model.
 * 
 * @package Phost
 * 
 * @since 0.1.0
 */
class Setting extends Model {

	/**
	 * The setting ID.
	 * 
	 * @since 0.1.0
	 * 
	 * @var int
	 */
	public $ID = 0;

	/**
	 * The previous post ID.
	 * 
	 * @since 0.1.0
	 * 
	 * @var int
	 */
	public $previous_ID = 0;

	/**
	 * The setting key.
	 * 
	 * @since 0.1.0
	 * 
	 * @var string
	 */
	public $setting_key = '';

	/**
	 * The setting value.
	 * 
	 * @since 0.1.0
	 * 
	 * @var string
	 */
	public $setting_value = '';

	/**
	 * The setting autoloader flag.
	 * 
	 * @since 0.1.0
	 * 
	 * @var string
	 */
	public $autoload = 'yes';

	/**
	 * Create a new setting instance.
	 * 
	 * @since 0.1.0
	 * 
	 * @param int $id The setting ID.
	 * 
	 * @return mixed
	 */
	public function __construct( $id = 0 ) {

		if ( 0 !== $id ) {

			$this->fetch( $id );

		}

		// Set the meta type.
		$this->meta_type = 'settings';

	}

	/**
	 * Fetch the setting value.
	 * 
	 * @since 0.1.0
	 * 
	 * @param mixed  $value The setting value to search for.
	 * @param string $key   The setting key to search for.
	 * 
	 * @return boolean|array
	 */
	public function fetch( $value = 0, $key = 'ID' ) {

		// Bail if we're looking for invalid columns.
		if ( 0 === $value || ! in_array( $key, array( 'ID', 'setting_key', 'setting_value' ) ) ) {

			return false;

		}

		// Create new database connection.
		$db = new Database;

		// Prepare the select statement.
		$query = $db->connection->prepare( 'SELECT * FROM ' . $db->prefix . 'settings WHERE ' . $key . ' = ? LIMIT 1' );

		// Bind the parameter to the query.
		$query->bindParam( 1, $value );

		// Execute the query.
		$query->execute();

		// Return the values.
		$result = $query->fetch();

		// We didn't find anyone, right?
		if ( false === $result ) {

			return false;

		}

		// Setup the setting model.
		$this->previous_ID = $this->ID;
		$this->ID = $result['id'];
		$this->setting_key = $result['setting_key'];
		$this->setting_value = $result['setting_value'];
		$this->autoload = $result['autoload'];

		return $result;

	}

	/**
	 * Create a new setting.
	 * 
	 * Returns the ID of the newly created setting or
	 * returns a boolean value of false if it failed.
	 * 
	 * @since 0.1.0
	 * 
	 * @return boolean|int
	 */
	public function create() {

		// Does the setting already exist?
		if ( false !== $this->exists( $this->ID ) ) {

			return false;

		}

		// Create new database connection.
		$db = new Database;

		// Prepare the insert statement.
		$query = $db->connection->prepare( 'INSERT INTO ' . $db->prefix . 'settings ( setting_key, setting_value, autoload ) VALUES ( ?, ?, ? )' );

		// Sanitise, filter and trim setting key and value.
		$this->setting_key = sanitise_text( trim( $this->setting_key ), '~[^A-Za-z0-9-_]~' );
		$this->setting_value = filter_text( trim( $this->setting_value ) );

		// Should this setting be autoloaded?
		if ( 'yes' != $this->autoload ) {

			$this->autoload = 'no';

		}

		// Bind parameters to the query.
		$query->bindParam( 1, $this->setting_key );
		$query->bindParam( 2, $this->setting_value );
		$query->bindParam( 3, $this->autoload );

		// Execute the query.
		$query->execute();

		// Set the new inserted ID.
		$this->ID = $db->connection->lastInsertId();

		return $this->ID;

	}

	/**
	 * Save the setting instance.
	 * 
	 * Saves the current setting instance to the database
	 * or creates a new setting if the ID doesn't exist.
	 * 
	 * @since 0.1.0
	 * 
	 * @return boolean|int
	 */
	public function save() {

		// Does the setting already exist?
		if ( false === $this->exists( $this->ID ) ) {

			return $this->create();

		}

		// Create new database connection.
		$db = new Database;

		// Prepare the update statement.
		$query = $db->connection->prepare( 'UPDATE ' . $db->prefix . 'settings SET setting_key = ?, setting_value = ?, autoload = ? WHERE ID = ?' );

		// Sanitise, filter and trim setting key and value.
		$this->setting_key = sanitise_text( trim( $this->setting_key ), '~[^A-Za-z0-9-_]~' );
		$this->setting_value = filter_text( trim( $this->setting_value ) );

		// Should this setting be autoloaded?
		if ( 'yes' != $this->autoload ) {

			$this->autoload = 'no';

		}

		// Bind parameters to the query.
		$query->bindParam( 1, $this->setting_key );
		$query->bindParam( 2, $this->setting_value );
		$query->bindParam( 3, $this->autoload );
		$query->bindParam( 4, $this->ID );

		// Execute the query.
		$query->execute();

		return true;

	}

	/**
	 * Delete an existing setting.
	 * 
	 * Permanently deletes the current setting instance.
	 * Returns true is successful or false on failure.
	 * 
	 * @since 0.1.0
	 * 
	 * @return boolean
	 */
	public function delete() {

		// Does the setting already exist?
		if ( false === $this->exists( $this->ID ) ) {

			return false;

		}

		// Create new database connection.
		$db = new Database;

		// Prepare the delete statement.
		$query = $db->connection->prepare( 'DELETE FROM ' . $db->prefix . 'settings WHERE ID = ? LIMIT 1' );

		// Bind parameters to the query.
		$query->bindParam( 1, $this->ID );

		// Execute the query.
		$query->execute();

		return $this->reset( true );

	}

	/**
	 * Return all the settings.
	 * 
	 * Returns every single row from the database but is
	 * not saved into an instance variable. This function
	 * should only be called once as it could be expensive.
	 * 
	 * @since 0.1.0
	 * 
	 * @return array
	 */
	public function all() {

		// Create new database connection.
		$db = new Database;

		// Prepare the query statement.
		$query = $db->connection->prepare( 'SELECT * FROM ' . $db->prefix . 'settings' );

		// Execute the query.
		$query->execute();

		return $query->fetchAll();

	}

	/**
	 * Check a setting exists.
	 * 
	 * @since 0.1.0
	 * 
	 * @param int $id The setting ID.
	 * 
	 * @return boolean
	 */
	public function exists( $id = 0 ) {

		// Bail with an invalid id.
		if ( 0 == $id ) {

			return false;

		}

		// Create new database connection.
		$db = new Database;

		// Prepare the select statement.
		$query = $db->connection->prepare( 'SELECT * FROM ' . $db->prefix . 'settings WHERE ID = ? LIMIT 1' );

		// Bind the id to the query.
		$query->bindParam( 1, $id );

		// Execute the query.
		$query->execute();

		// Return the values.
		$result = $query->fetch();

		if ( false === $result ) {

			return false;

		}

		return true;

	}

	/**
	 * Switches to the previous setting.
	 * 
	 * Resets the current instance to the previous one
	 * based on the ID saved. If the previous ID is invalid
	 * the current instance will be reset to the defaults.
	 * 
	 * @since 0.1.0
	 * 
	 * @return boolean
	 */
	public function previous() {

		// Does the previous setting already exist?
		if ( false === $this->exists( $this->previous_ID ) ) {

			$this->reset( true );

			return false;

		}

		// Go back to the previous to object.
		$this->fetch( $this->previous_ID );

		return true;

	}

	/**
	 * Reset the current instance.
	 * 
	 * Resets the current instance to the default
	 * object values within this model. Accepts a
	 * parameter to retain the previous ID history.
	 * 
	 * @since 0.1.0
	 * 
	 * @param boolean $history Flag to keep previous ID history.
	 * 
	 * @return boolean
	 */
	public function reset( $history = false ) {

		// Should we keep the previous ID?
		if ( true === $history ) {

			$this->previous_ID = $this->ID;

		} else {

			$this->previous_ID = 0;

		}

		// Reset the objects.
		$this->ID = 0;
		$this->setting_key = '';
		$this->setting_value = '';
		$this->autoload = 'yes';

		return true;

	}

}

