<?php

/**
 * Phost, your story
 * (c) 2018, Daniel James
 */

if ( ! defined( 'PHOST' ) ) {

	die();

}

/**
 * The menu model.
 * 
 * @package Phost
 * 
 * @since 0.1.0
 */
class Menu extends Model {

	/**
	 * The menu ID.
	 * 
	 * @since 0.1.0
	 * 
	 * @var int
	 */
	public $ID = 0;

	/**
	 * The previous menu ID.
	 * 
	 * @since 0.1.0
	 * 
	 * @var int
	 */
	public $previous_ID = 0;

	/**
	 * The menu name.
	 * 
	 * @since 0.1.0
	 * 
	 * @var string
	 */
	public $menu_name = '';

	/**
	 * The menu location path.
	 * 
	 * @since 0.1.0
	 * 
	 * @var string
	 */
	public $menu_location = '';

	/**
	 * The menu list.
	 * 
	 * @since 0.1.0
	 * 
	 * @var array
	 */
	public $menu_list = array();

	/**
	 * The created at timestamp.
	 * 
	 * @since 0.1.0
	 * 
	 * @var string
	 */
	public $created_at = '';

	/**
	 * The updated at timestamp.
	 * 
	 * @since 0.1.0
	 * 
	 * @var string
	 */
	public $updated_at = '';

	/**
	 * Create a new menu instance.
	 * 
	 * @since 0.1.0
	 * 
	 * @param int $id The menu ID.
	 * 
	 * @return mixed
	 */
	public function __construct( $id = 0 ) {

		if ( 0 !== $id ) {

			$this->fetch( $id );

		}

		// Set the meta type.
		$this->meta_type = 'menus';

	}

	/**
	 * Fetch the selected menu.
	 * 
	 * @since 0.1.0
	 * 
	 * @param mixed  $value The menu value to search for.
	 * @param string $key   The menu key to search for.
	 * 
	 * @return boolean|array
	 */
	public function fetch( $value = 0, $key = 'ID' ) {

		// Bail if we're looking for invalid columns.
		if ( 0 === $value || ! in_array( $key, array( 'ID', 'name', 'location' ) ) ) {

			return false;

		}

		// Create new database connection.
		$db = new Database;

		// Prepare the select statement.
		$query = $db->connection->prepare( 'SELECT * FROM ' . $db->prefix . 'menus WHERE ' . $key . ' = ? LIMIT 1' );

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

		// Setup the menu model.
		$this->previous_ID = $this->ID;
		$this->ID = $result['id'];
		$this->menu_name = $result['name'];
		$this->menu_location = $result['location'];
		$this->menu_list = json_decode( $result['list'], true );
		$this->created_at = $result['created_at'];
		$this->updated_at = $result['updated_at'];

		return $result;

	}

	/**
	 * Create a new menu.
	 * 
	 * Returns the ID of the newly created menu or
	 * returns a boolean value of false if it failed.
	 * 
	 * @since 0.1.0
	 * 
	 * @return boolean|int
	 */
	public function create() {

		// Does the menu already exist?
		if ( false !== $this->exists( $this->ID ) ) {

			return false;

		}

		// Create new database connection.
		$db = new Database;

		// Prepare the insert statement.
		$query = $db->connection->prepare( 'INSERT INTO ' . $db->prefix . 'menus ( name, location, list, created_at, updated_at ) VALUES ( ?, ?, ?, ?, ? )' );

		// Set the timestamps.
		$this->created_at = date( 'Y-m-d H:i:s' );
		$this->updated_at = date( 'Y-m-d H:i:s' );

		// Clean the text based input.
		$this->menu_name = filter_text( trim( $this->menu_name ) );
		$this->menu_location = sanitise_text( $this->menu_location, '~[^A-Za-z0-9_[-]]~' );

		// Do we have any menu items?
		if ( ! empty( $this->menu_list ) ) {

			// Loop through each and clean the values.
			foreach ( $this->menu_list as $key => $value ) {

				// Filter the text based values.
				$this->menu_list[ $key ][ 'name' ] = filter_text( $value[ 'name' ] );
				$this->menu_list[ $key ][ 'href' ] = filter_text( $value[ 'href' ] );

			}

		}

		// Convert to items to JSON.
		$this->menu_list = json_encode( $this->menu_list );

		// Bind parameters to the query.
		$query->bindParam( 1, $this->menu_name );
		$query->bindParam( 2, $this->menu_location );
		$query->bindParam( 3, $this->menu_list );
		$query->bindParam( 4, $this->created_at );
		$query->bindParam( 5, $this->updated_at );

		// Execute the query.
		$query->execute();

		// Convert items back to an array.
		$this->menu_list = json_decode( $this->menu_list, true );

		// Set the new inserted ID.
		$this->ID = $db->connection->lastInsertId();

		return $this->ID;

	}

	/**
	 * Save the menu instance.
	 * 
	 * Saves the current menu instance to the database
	 * or creates a new menu if the ID doesn't exist.
	 * 
	 * @since 0.1.0
	 * 
	 * @return boolean|int
	 */
	public function save() {

		// Does the menu already exist?
		if ( false === $this->exists( $this->ID ) ) {

			return $this->create();

		}

		// Create new database connection.
		$db = new Database;

		// Prepare the update statement.
		$query = $db->connection->prepare( 'UPDATE ' . $db->prefix . 'menus SET name = ?, location = ?, list = ?, updated_at = ? WHERE ID = ?' );

		// Set the updated timestamp.
		$this->updated_at = date( 'Y-m-d H:i:s' );

		// Clean the text based input.
		$this->menu_name = filter_text( trim( $this->menu_name ) );
		$this->menu_location = sanitise_text( $this->menu_location, '~[^A-Za-z0-9_[-]]~' );

		// Do we have any menu items?
		if ( ! empty( $this->menu_list ) ) {

			// Loop through each and clean the values.
			foreach ( $this->menu_list as $key => $value ) {

				// Filter the text based values.
				$this->menu_list[ $key ][ 'name' ] = filter_text( $value[ 'name' ] );
				$this->menu_list[ $key ][ 'href' ] = filter_text( $value[ 'href' ] );

			}

		}

		// Convert to items to JSON.
		$this->menu_list = json_encode( $this->menu_list );

		// Bind parameters to the query.
		$query->bindParam( 1, $this->menu_name );
		$query->bindParam( 2, $this->menu_location );
		$query->bindParam( 3, $this->menu_list );
		$query->bindParam( 4, $this->updated_at );
		$query->bindParam( 5, $this->ID );

		// Execute the query.
		$query->execute();

		// Convert items back to an array.
		$this->menu_list = json_decode( $this->menu_list, true );

		return $this->ID;

	}

	/**
	 * Delete an existing menu.
	 * 
	 * Permanently deletes the current menu instance.
	 * Returns true is successful or false on failure.
	 * 
	 * @since 0.1.0
	 * 
	 * @return boolean
	 */
	public function delete() {

		// Does the menu already exist?
		if ( false === $this->exists( $this->ID ) ) {

			return false;

		}

		// Create new database connection.
		$db = new Database;

		// Prepare the delete statement.
		$query = $db->connection->prepare( 'DELETE FROM ' . $db->prefix . 'menus WHERE ID = ? LIMIT 1' );

		// Bind parameters to the query.
		$query->bindParam( 1, $this->ID );

		// Execute the query.
		$query->execute();

		return $this->reset( true );

	}

	/**
	 * Format an array of menu data into an object.
	 * 
	 * This function is for formatting arrays of data that have
	 * been fetched directly from the database and transforming
	 * it into a proper menu object.
	 * 
	 * @since 0.1.0
	 * 
	 * @param array $data The array of menu data to format.
	 * 
	 * @return void
	 */
	public function format_array( $data = array() ) {

		// Do we have anything in this array?
		if ( is_array( $data ) && ! empty( $data ) ) {

			$this->previous_ID = $this->ID;
			$this->ID = isset( $data[ 'id' ] ) ? $data[ 'id' ] : 0;
			$this->menu_name = isset( $data['name'] ) ? $data[ 'name' ] : '';
			$this->menu_location = isset( $data['location'] ) ? $data[ 'location' ] : '';
			$this->menu_list = isset( $data['list'] ) ? json_decode( $data[ 'list' ], true ) : array();
			$this->created_at = isset( $data['created_at'] ) ? $data[ 'created_at' ] : '';
			$this->updated_at = isset( $data['updated_at'] ) ? $data[ 'updated_at' ] : '';

		}

	}

	/**
	 * Check a menu exists.
	 * 
	 * @since 0.1.0
	 * 
	 * @param int $id The menu ID.
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
		$query = $db->connection->prepare( 'SELECT * FROM ' . $db->prefix . 'menus WHERE ID = ? LIMIT 1' );

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
	 * Switches to the previous menu.
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

		// Does the previous menu already exist?
		if ( false === $this->fetch( $this->previous_ID ) ) {

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
		$this->menu_name = '';
		$this->menu_location = '';
		$this->menu_list = array();
		$this->created_at = '';
		$this->updated_at = '';

		return true;

	}

}

